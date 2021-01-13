<?php
/**
 * 备份文件，目前无用
 */

header("Content-Type: text/html;charset=UTF-8");
date_default_timezone_set('Asia/Shanghai');
$path = str_replace('\\','/',realpath(dirname(__FILE__).'/'))."/";
$file = $path . date("Ymd") . '_' . (isset($argv[2]) && $argv[2] ? $argv[2] : 0) . '_' . (isset($argv[3]) && $argv[3] ? $argv[3] : 0) . '_updateRanking.log';
$test = 0;
$domain = $test ? 'http://pm.cf.com.cn/' : 'http://yh.52bjw.cn/';

//调用第三方接口，判断是否为节假日
$isJiejiari = 1;
$result = file_get_contents('http://tool.bitefu.net/jiari/?d='.date("Ymd").'&info=1');
if ($result && isset($argv[2]) && $argv[2])
{
    $result = json_decode($result,true);
    if ($result['typename'] == '工作日')
    {
        $isJiejiari = 0;
    }
}

//获取需要更新的数据
$today = strtotime(date("Y-m-d 00:00:01"));
if(file_exists($file))
{
    $result = file_get_contents($file);
}
else
{
    if ($isJiejiari) //节假日更新全部
    {
        if (isset($argv[3]) && $argv[3]) //不重复更新
        {
            $result = curl_request($domain.'api/Cron/getAllTask',['where'=>"rank_time<'{$today}'",'field'=>'id']);
        }
        else
        {
            $result = curl_request($domain.'api/Cron/getAllTask',['field'=>'id']);
        }
    }
    else //工作日只更新，昨日新达标的词
    {
        if (isset($argv[3]) && $argv[3]) //不重复更新
        {
            $result = curl_request($domain.'api/Cron/getAllTask',['where'=>"current_ranking <= 10 and rank_time<'{$today}'",'field'=>'id']);
        }
        else
        {
            $result = curl_request($domain.'api/Cron/getAllTask',['where'=>"current_ranking <= 10",'field'=>'id']);
        }
    }
    $fp = fopen($file, 'w+');
    fwrite($fp, $result);
    fclose($fp);
}
$data = json_decode($result,1);

//查询并更新
if(count($data['data']) > 0)
{
    if (isset($argv[1]))
    {
        $data['data'] = array_slice($data['data'],$argv[1] * 5000,5000);
    }
    foreach ($data['data'] as $k => $v)
    {
        //更新排名
        $url = $domain.'api/Cron/updateKeywordsRank';
        $res = curl_request($url,['tid'=>$v['id']],'',0,1);
        echo "{$v['id']}:".$res."\r\n";
    }
}

if (isset($argv[1]) && $argv[1] != 19)
{
    exit;
}
unlink($file);

/*
//获取数据
$data = json_decode(curl_request($domain.'api/Cron/getRepeatedlyTask'),1);
if(count($data['data']) > 0)
{
    foreach ($data['data'] as $k => $v)
    {
        $url = $domain.'api/Cron/updateKeywordsRank';
        $res = curl_request($url,['tid'=>$v['id']]);
        if (!strstr($res,'"code":0'))
        {
            sleep(1);
            $res = curl_request($url,['tid'=>$v['id']]);
        }
        echo "{$v['id']}:".$res."\r\n";
    }
}
*/

//参数1：访问的URL，参数2：post数据(不填则为GET)，参数3：提交的$cookies,参数4：是否返回$cookies
function curl_request($url,$post='',$cookie='', $returnCookie=0, $timeout=60){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)');
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
    //curl_setopt($curl, CURLOPT_REFERER, "http://XXX");
    if($post) {
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post));
    }
    if($cookie) {
        curl_setopt($curl, CURLOPT_COOKIE, $cookie);
    }
    curl_setopt($curl, CURLOPT_HEADER, $returnCookie);
    curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec($curl);
    if (curl_errno($curl)) {
        return curl_error($curl);
    }
    curl_close($curl);
    if($returnCookie){
        list($header, $body) = explode("\r\n\r\n", $data, 2);
        preg_match_all("/Set\-Cookie:([^;]*);/", $header, $matches);
        $info['cookie']  = substr($matches[1][0], 1);
        $info['content'] = $body;
        return $info;
    }else{
        return $data;
    }
}