<?php
/**
 * 2020-10-10
 * 闲置词库
 * 每周日下午1点开始执行自动更新排名
 */
include_once 'base.php';
$base = new base();

//获取需要更新的数据
$file = $base->path . date("Ymd") . '_updateRankingWeekend.log';
if(file_exists($file))
{
    $result = file_get_contents($file);
}
else
{
    //判断是不是周日  (linux操作系统以设置,故取消php的过滤 2020-10-28)
    /*$date = date("l");
    if ($date != "Sunday")
    {
        echo "闲置词库关键词更新时间为周日13点开始";
        exit;
    }*/
    $today = date('Y-m-d')." 00:00:01";
    $str_today = strtotime($today);
    $result = $base->curl_request($base->domain.'api/Cron/getAllTaskbak',['field'=>'id','where'=>'delete_time is not null and rank_time < '.$str_today]);
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
        $url = $base->domain.'api/Cron/updateKeywordsRank_xian';
        $res = $base->curl_request($url,['tid'=>$v['id']],'',0,1);
        echo "{$v['id']}:".$res."\r\n";
    }
}
$base->log("{$argv[1]},done",'update_ranking_weekend');

if (isset($argv[1]) && $argv[1] != 19)
{
    exit;
}
unlink($file);
if (!isset($argv[2]))
{
    $url = 'https://oapi.dingtalk.com/robot/send?access_token=6a48424f222694d6c23e7dd2b2061648d73ea975d28d5a02a46f01ea6270c3d8'; //李娴组
    $content = "【计费系统】闲置词库更新排名已完成";
    $base->dingding_robot_curl($url, json_encode(['msgtype'=>'text','text'=>['content'=>$content]]));
    sleep(1);
//    $url = 'https://oapi.dingtalk.com/robot/send?access_token=f44f7c0e4ab013c8c5f000d77cd760c6621b60f61a5e422d64cc8095ef732838'; //金融批量词组
//    $base->dingding_robot_curl($url, json_encode(['msgtype'=>'text','text'=>['content'=>$content]]));
    $base->log("bobao",'update_ranking');
}
