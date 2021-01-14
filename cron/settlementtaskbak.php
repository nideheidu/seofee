<?php
/**
 * 管理后台-》系统设置-》手动操作-》补扣费 执行脚本
 * $argv[1] 补扣前$argv[1]的费用
 * $argv[2] 空：播报，非空：播报到钉钉对应群
 */

//昨天结算出问题，今天补结算脚本。思路：用今天的结算补到昨天的结算中。1.先跑今天的排名 2.先结算今天的 3.把今天的结算的时间改为昨天（如果昨天该词已结算，则不改）
include_once 'base.php';
$base = new base();

$db_config = include_once dirname(__FILE__) . '/../config/database.php';
$db = mysqli_connect($db_config['hostname'],$db_config['username'],$db_config['password'],$db_config['database']); //连接数据库
if (mysqli_connect_errno($db)) {
    die("连接 MySQL 失败: " . mysqli_connect_error());
}
mysqli_query($db,"set names utf8"); //数据库编码格式
$day = 1;
if (isset($argv[1]) && $argv[1])
{
    $day = $argv[1];
}
$where = '';
if (isset($argv[2]) && $argv[2])
{
    $uid = $argv[2];
    $where = " and uid = {$uid}";
}
//获取昨日所有结算数据
$btime = strtotime(date("Y-m-d 00:00:01", strtotime("-{$day} day")));
$etime = strtotime(date("Y-m-d 23:59:59", strtotime("-{$day} day")));
$today = strtotime(date("Y-m-d 00:00:01"));
$query = mysqli_query($db,"select kid from seo_mingxi where create_time >= {$btime} and create_time <= {$etime} and type in (1,3) and change_type = 2".$where);
$yestoday_kids = [];
while($row = mysqli_fetch_array($query))
{
    $yestoday_kids[] = $row['kid'];
}

//获取今日新增的词
$query = mysqli_query($db,"select id from seo_keywords where create_time >= {$btime} and status = 2".$where);
$newkids = [];
while($row = mysqli_fetch_array($query))
{
    $newkids[] = $row['id'];
}

//获取今日所有结算数据
$query = mysqli_query($db,"select id,kid from seo_mingxi where create_time >= {$today} and type in (1,3) and change_type = 2".$where);
while($row = mysqli_fetch_array($query))
{
    if (in_array($row['kid'],$yestoday_kids) || in_array($row['kid'],$newkids)) //过滤掉昨天已经结算的 和 今日新增的
    {
        continue;
    }

    $query1 = mysqli_query($db,"update seo_mingxi set create_time = {$btime} where id = {$row['id']} and type in (1,3) and change_type = 2".$where);
    if ($query1)
    {
        echo "{$row['id']}更新成功\n";
    }
    else
    {
        echo "{$row['id']}更新失败\n";
    }
}
mysqli_free_result($result);
mysqli_close($db);
echo "done";

if (!isset($argv[2]))
{
    $url = 'https://oapi.dingtalk.com/robot/send?access_token=6a48424f222694d6c23e7dd2b2061648d73ea975d28d5a02a46f01ea6270c3d8';
    $content = "【计费系统】".date("Y-m-d", strtotime("-{$day} day"))."日费用已补扣";
    $base->dingding_robot_curl($url, json_encode(['msgtype'=>'text','text'=>['content'=>$content]]));
}