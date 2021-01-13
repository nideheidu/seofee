<?php
/**
 * 管理后台-》系统设置-》手动操作-》手动更新排名（今日） 执行脚本
 * $argv[1] 空：播报，非空：播报到钉钉对应群
 */
include_once 'base.php';
$base = new base();
$time = strtotime(date('Y-m-d 00:00:01'));
//获取数据
$data = json_decode($base->curl_request($base->domain.'api/Cron/getAllTask',['where'=>'delete_time is NULL and create_time >= '.strtotime(date('Y-m-d 00:00:01'))]),1);
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
echo "done";

if (!isset($argv[1]))
{
    $url = 'https://oapi.dingtalk.com/robot/send?access_token=6a48424f222694d6c23e7dd2b2061648d73ea975d28d5a02a46f01ea6270c3d8';
    $content = "【计费系统】手动更新排名（今日）已完成";
    $base->dingding_robot_curl($url, json_encode(['msgtype'=>'text','text'=>['content'=>$content]]));
}