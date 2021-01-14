<?php
/**
 * 1.自动更新排名 每天执行
 * $argv[1] 页码，每页执行5000条记录，共20页
 * $argv[2] 空：播报，非空：播报到钉钉对应群
 *
 * 2020-01-12 16:54 更新
 * 第一次->首->第二次->首 =>首
 * 第一次->首->第二次->非首->第三次->结果
 * 第一次->非首->第二次->首->第三次->结果
 * 第一次->非首->第二次->非首 =>非首
 */
include_once 'base.php';
$base = new base();

//获取需要更新的数据
$file = $base->path . date("Ymd") . '_updateRanking_new.log';
if(file_exists($file))
{
    $result = file_get_contents($file);
}
else
{
    $result = $base->curl_request($base->domain.'api/Cron/getAllTask',['field'=>'id']);
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
        //第一次
        $url = $base->domain.'api/Cron/updateKeywordsRank_day';
        $res = $base->curl_request($url,['tid'=>$v['id']],'',0,1);
        echo "{$v['id']}:".$res."\r\n";
    }
}
$base->log("{$argv[1]},done",'update_ranking_new');

if (isset($argv[1]) && $argv[1] != 5)
{
    exit;
}
unlink($file);
if (!isset($argv[2]))
{
    $url = 'https://oapi.dingtalk.com/robot/send?access_token=6a48424f222694d6c23e7dd2b2061648d73ea975d28d5a02a46f01ea6270c3d8'; //李娴组
    $content = "【计费系统】每日自动更新排名已完成";
    $base->dingding_robot_curl($url, json_encode(['msgtype'=>'text','text'=>['content'=>$content]]));
    sleep(1);
    $base->log("bobao",'update_ranking_new');
}
