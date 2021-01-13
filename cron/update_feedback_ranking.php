<?php
/**
 * 客户非首页反馈词
 * 每日下午17点自动更新反馈词的排名
 * 不更新反馈状态及更新时间
 */
include_once 'base.php';
$base = new Base();

//需要更新的数据
$file = $base->path . date("Ymd") . '_update_feedback_ranking.log';
$result = $base->curl_request($base->domain.'api/Cron/getAllFeedback',['field' => 'id,keywords,web_url,search_ngines', 'where' => 'delete_time is null']);
$fp = fopen($file, 'w+');
fwrite($fp, $result);
fclose($fp);

$data = json_decode($result,1);

//查询并更新
if(count($data['data']) > 0)
{
    foreach ($data['data'] as $k => $v)
    {
        //更新排名
        $url = $base->domain.'api/Cron/updateKeywordsRank';
        $res = $base->curl_request($url,['tid'=>$v['id'],'feedback_id'=>$v['fid'],'feedback'=>1],'',0,1);
        $echo = "{$v['id']}:".$res."\r\n";
        $base->log("{$echo}",'update_feedback_ranking');
    }
}
$base->log("{$argv[1]},done",'update_feedback_ranking');

unlink($file);
if (!isset($argv[2]))
{
    $url = 'https://oapi.dingtalk.com/robot/send?access_token=6a48424f222694d6c23e7dd2b2061648d73ea975d28d5a02a46f01ea6270c3d8'; //李娴组
    $content = "【计费系统】客户反馈词排名更新已完成";
    $base->dingding_robot_curl($url, json_encode(['msgtype'=>'text','text'=>['content'=>$content]]));
    $base->log("bobao",'update_feedback_ranking');
}
