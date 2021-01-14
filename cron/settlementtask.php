<?php
/**
 * 1.管理后台-》系统设置-》手动操作-》手动结算 执行脚本
 * 2.自动结算 每天执行
 * $argv[1] 页码，每页执行2500条记录，共10页
 * $argv[2] 空：播报，非空：播报到钉钉对应群
 */
include_once 'base.php';
$base = new base();

//获取待结算数据
$bdate = strtotime(date('Y-m-d 00:00:01'));
$edate = strtotime(date('Y-m-d 23:59:59'));
$file = $base->path . date("Ymd") . '_settlementtask.log';
if(file_exists($file))
{
    $result = file_get_contents($file);
}
else
{
    $where = '';
    if (isset($argv[2]))
    {
        $where = " and uid = {$argv[2]}";
    }
    $post = ['where'=>"current_ranking >= 1 and current_ranking <= 20 and rank_time >= {$bdate} and rank_time <= {$edate}".$where];
    $result = $base->curl_request($base->domain.'api/Cron/getAllTask',$post);
    $fp = fopen($file, 'w+');
    fwrite($fp, $result);
    fclose($fp);
}
$data = json_decode($result,1);

//所有的查询一次、结算
if(count($data['data']) > 0)
{
    if (isset($argv[1]))
    {
        $data['data'] = array_slice($data['data'],$argv[1] * 2500,2500);
    }
    if ($data['data'])
    {
        $ids = array_chunk(array_column($data['data'],'id'), 10);
        foreach ($ids as $tid)
        {
            //结算
            $url = $base->domain.'api/Cron/settlementtask';
            $res = $base->curl_request($url,['tid'=>implode(",",$tid)]);
        }
        sleep(100);
        foreach ($ids as $tid)
        {
            //结算
            $url = $base->domain.'api/Cron/settlementtask';
            $res = $base->curl_request($url,['tid'=>implode(",",$tid)]);
        }
    }
}
$base->log("done",'settlementtask');

if (isset($argv[1]) && $argv[1] != 9)
{
    exit;
}
unlink($file);
if (!isset($argv[2]))
{
    $url = 'https://oapi.dingtalk.com/robot/send?access_token=6a48424f222694d6c23e7dd2b2061648d73ea975d28d5a02a46f01ea6270c3d8'; //李娴组
    $content = "【计费系统】今日排名关键词已结算";
    $base->dingding_robot_curl($url, json_encode(['msgtype'=>'text','text'=>['content'=>$content]]));
    sleep(1);
//    $url = 'https://oapi.dingtalk.com/robot/send?access_token=f44f7c0e4ab013c8c5f000d77cd760c6621b60f61a5e422d64cc8095ef732838'; //金融批量词组
//    $base->dingding_robot_curl($url, json_encode(['msgtype'=>'text','text'=>['content'=>$content]]));
}