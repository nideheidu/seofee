<?php
/**
 * 自动更新排名，更新的数据为：1.漏更新的 2.未达标的 每天执行
 * $argv[1] 空：播报，非空：播报到钉钉对应群
 */
include_once 'base.php';
$base = new base();

//第二次更新
$today = strtotime(date("Y-m-d 00:00:01"));
for ($i=0; $i<3; $i++)
{
    $data = json_decode($base->curl_request($base->domain.'api/Cron/getAllTask',['field'=>'id','where'=>"rank_time<'{$today}' and delete_time is NULL and uid != 92 and uid != 105 and uid != 119"]),1);
    if (count($data['data']) == 0)
    {
        break;
    }
    foreach ($data['data'] as $k => $v)
    {
        //更新排名
        $url = $base->domain.'api/Cron/updateKeywordsRank';
        $res = $base->curl_request($url,['tid'=>$v['id']],'',0,1);
        echo "{$base->domain}:".$res;
        echo "\r\n";
    }
}
$base->log("第二次更新完成",'update_ranking_bak');

/*
//未达标的重跑
$data = json_decode($base->curl_request($base->domain.'api/Cron/getAllTask',['field'=>'id','where'=>"rank_time>'{$today}' and current_ranking > 10 and delete_time is NULL"]),1);
if(count($data['data']) > 0)
{
    foreach ($data['data'] as $k => $v)
    {
        //更新排名
        $url = $base->domain.'api/Cron/updateKeywordsRank';
        $res = $base->curl_request($url,['tid'=>$v['id']],'',0,1);
        echo "{$base->domain}:".$res;
        echo "\r\n";
    }
}
$base->log("未达标重跑完成",'update_ranking_bak');
*/

if (!isset($argv[1]))
{
    $url = 'https://oapi.dingtalk.com/robot/send?access_token=6a48424f222694d6c23e7dd2b2061648d73ea975d28d5a02a46f01ea6270c3d8'; //李娴组
    $content = "【计费系统】第二次更新排名已完成";
    $base->dingding_robot_curl($url, json_encode(['msgtype'=>'text','text'=>['content'=>$content]]));
    sleep(1);
//    $url = 'https://oapi.dingtalk.com/robot/send?access_token=f44f7c0e4ab013c8c5f000d77cd760c6621b60f61a5e422d64cc8095ef732838'; //金融批量词组
//    $base->dingding_robot_curl($url, json_encode(['msgtype'=>'text','text'=>['content'=>$content]]));
}