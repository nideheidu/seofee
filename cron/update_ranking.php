<?php
/**
 * 1.管理后台-》系统设置-》手动操作-》手动更新排名（全部） 执行脚本
 * 2.自动更新排名 每天执行
 * $argv[1] 页码，每页执行5000条记录，共20页
 * $argv[2] 空：播报，非空：播报到钉钉对应群
 *
 * 2020-09-18 新更新排名规则
 * 第一次更新（6点开始）：3天内排名进过前50名的所有词（昨日计费系统最新排名前50和接口排名表3天内排名前50去重后数据）——钉钉播报
 * 第一次补跑（接着第一次更新）：3天内排名进过前50名，第一遍更新后为未达标词
 * 第二次更新（9点30分开始）：更新时间不是当天的所有数据——钉钉播报
 */
include_once 'base.php';
$base = new base();

//获取需要更新的数据
$file = $base->path . date("Ymd") . '_updateRanking.log';
if(file_exists($file))
{
    $result = file_get_contents($file);
}
else
{
    $result = $base->curl_request($base->domain.'api/Cron/getAllTasknew',['field'=>'id']);
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
        $url = $base->domain.'api/Cron/updateKeywordsRank';
        $res = $base->curl_request($url,['tid'=>$v['id']],'',0,1);
        echo "{$v['id']}:".$res."\r\n";
    }
}
$base->log("{$argv[1]},done",'update_ranking');

if (isset($argv[1]) && $argv[1] != 5)
{
    exit;
}
unlink($file);
if (!isset($argv[2]))
{
    $url = 'https://oapi.dingtalk.com/robot/send?access_token=6a48424f222694d6c23e7dd2b2061648d73ea975d28d5a02a46f01ea6270c3d8'; //李娴组
    $content = "【计费系统】第一次更新排名已完成";
    $base->dingding_robot_curl($url, json_encode(['msgtype'=>'text','text'=>['content'=>$content]]));
    sleep(1);
//    $url = 'https://oapi.dingtalk.com/robot/send?access_token=f44f7c0e4ab013c8c5f000d77cd760c6621b60f61a5e422d64cc8095ef732838'; //金融批量词组
//    $base->dingding_robot_curl($url, json_encode(['msgtype'=>'text','text'=>['content'=>$content]]));
    $base->log("bobao",'update_ranking');
}

//补跑
$data = json_decode($base->curl_request($base->domain.'api/Cron/getAllTasknew',['field'=>'id', 'where'=>"delete_time is null and current_ranking > 10 and uid != 92 and uid != 105 and uid != 119"]),1);
if(count($data['data']) > 0)
{
    foreach ($data['data'] as $k => $v)
    {
        $url = $base->domain.'api/Cron/updateKeywordsRank';
        $res = $base->curl_request($url,['tid'=>$v['id']]);
        if (!strstr($res,'"code":0'))
        {
            sleep(1);
            $res = $base->curl_request($url,['tid'=>$v['id']]);
        }
        echo "{$v['id']}:".$res."\r\n";
    }
}