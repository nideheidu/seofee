<?php
/**
 * Created by PhpStorm.
 * User: yuxun
 * Date: 2020/7/7
 * Time: 18:46
 */

header("Content-Type: text/html;charset=UTF-8");
date_default_timezone_set('Asia/Shanghai');
$db_config = include_once dirname(__FILE__) . '/../config/database.php';
$db = mysqli_connect($db_config['hostname'],$db_config['username'],$db_config['password'],$db_config['database']); //连接数据库
if (mysqli_connect_errno($db)) {
    die("连接 MySQL 失败: " . mysqli_connect_error());
}
mysqli_query($db,"set names utf8"); //数据库编码格式

//开始处理seo_keywords表的Web_Url字段的字符串格式
//去掉Web_Url的网址前缀和尾部的'/'
/*$query = mysqli_query($db,"SELECT id,Web_Url FROM pm.seo_keywords");
while($web_url = mysqli_fetch_array($query))
{
    $url = preg_replace("/http\:\/\/|https\:\/\//",'',$web_url[1]);
    $url = rtrim($url,'/');
    $result = mysqli_query($db,"UPDATE seo_keywords SET pm.seo_keywords.web_url = '{$url}' WHERE pm.seo_keywords.id = {$web_url[0]}");
}*/

//2020-07-14 闲置词库 错误的uid更改
/*$query = mysqli_query($db,"select id from seo_keywords where search_ngines = 2 and uid = 78");
while ($uid = mysqli_fetch_array($query))
{
    $result = mysqli_query($db,"UPDATE seo_keywords SET seo_keywords.uid = 0 WHERE pm.seo_keywords.id = pm.seo_keywords.uid");
}*/

//2020-07-29 批量添加关键词
/*$str = "客户测试1,按天十倍配资测试,191100000.shop.cnlist.org,1,5
客户测试1,蚌埠股票配资测试,240947000.shop.cnlist.org,1,5
客户测试1,宝利配资测试,234611000.shop.cnlist.org,1,5
客户测试1,宝尚配资测试,177216000.shop.cnlist.org,1,5
客户测试1,北海配资开户测试,1762064000.shop.cnlist.org,1,5
客户测试1,北海在线配资测试,1781707000.shop.cnlist.org,1,5
客户测试1,北京股票配资测试,58270000.shop.cnlist.org,1,5
客户测试1,北京配资公司测试,115452000.shop.cnlist.org,1,5";
$arr = explode("\r\n",$str);
$num = 0;
foreach ($arr as $key => $one)
{
    $num++;
    $arr1 = explode(",",$one);
    $uid = mysqli_query($db,"SELECT id FROM pm.seo_customer WHERE username = '{$arr1[0]}'");
    while ($uid = mysqli_fetch_array($uid))
    {
        $userid = $uid['id'];
    }
    if (!$userid)
    {
        echo "用户{$arr1[0]}不存在！";
        continue;
    }
    $keywordsList=explode(',',str_replace('|',',',$arr1[1]));
    $keywordsList = array_flip(array_flip($keywordsList));
    // 去掉url的前缀
    $web_url = preg_replace("/http\:\/\/|https\:\/\//",'',$arr1[2]);
    //去掉url尾部的 '/'
    $web_url = rtrim($web_url,'/');

    $task_number = date('His').rand(1000, 9999);
    $time=time();

    $webid = mysqli_query($db,"SELECT id FROM pm.seo_web_url WHERE url = '{$web_url}'");
    if(!empty($webid))
    {
        while ($webid = mysqli_fetch_array($webid))
        {
            $web_id = $webid['id'];
            $kewordlist = mysqli_query($db,"SELECT FROM pm.seo_keywords WHERE web_id = {$web_id} AND search_ngines = {$arr1[3]} AND keywords = '{$arr1[1]}'");
            if(!empty($kewordlist)){
                $keywrodsArr = array_column($kewordlist,'keywords');
                $keywrodsArr =array_diff($keywordsList,$keywrodsArr);
                if(empty($keywrodsArr)){
                    echo "{$arr1[0]},{$arr1[1]},{$arr1[2]},{$arr1[3]} 已存在";
                    continue;
                }
            }
        }
    }
    else
    {
        $query = mysqli_query($db,"INSERT INTO pm.seo_web_url (url, uid, create_time) VALUES ('{$web_url}',{$uid},{$time})");
        $webid = mysqli_query($db,"SELECT max(id) FROM pm.seo_web_url");
        while ($webid = mysqli_fetch_all($webid))
        {
            $web_id = $webid['id'];
        }
    }

    $result = mysqli_query($db,"INSERT INTO pm.seo_keywords (uid, web_url, task_number, keywords, create_time, search_ngines, current_ranking, status, agent_status, cycle, price, agent_price, web_id, start_time) VALUES ({$userid},'{$web_url}',{$task_number},'{$arr1[1]}',{$time},{$arr1[3]},0,2,2,30,{$arr1[4]},{$arr1[4]},{$web_id},{$time})");
    if ($result)
    {
        echo "第 {$num} 条添加成功";
    }
}*/

//2020-08-04 闲置词库和金融词库共有的词；删除闲置词库的
$query = mysqli_query($db,"SELECT id,web_url FROM pm.seo_keywords WHERE (uid = 0 or uid = 78) AND delete_time is not NULL"); //查出闲置词库的词
while ($xianzhi = mysqli_fetch_array($query))
{
    if (strstr($xianzhi['web_url'],'cnlist')||strstr($xianzhi['web_url'],'qth58')||strstr($xianzhi['web_url'],'liebiao')||strstr($xianzhi['web_url'],'52bjw')||strstr($xianzhi['web_url'],'jinanfa'))
    {
        if (strstr($xianzhi['web_url'],'m.'))
        {
            $xianzhi['web_url'] = str_replace('.m','',$xianzhi['web_url']);
        }
        else
        {
            if (strstr($xianzhi['web_url'],'.shop'))
            {
                $xianzhi['web_url'] = str_replace('.shop','.shop.m',$xianzhi['web_url']);
            }
            elseif (strstr($xianzhi['web_url'],'.b2b'))
            {
                $xianzhi['web_url'] = str_replace('.b2b','.b2b.m',$xianzhi['web_url']);
            }
        }
    }

    //查出既在闲置词库又在金融词库的词
    $yonghusql = mysqli_query($db,"SELECT id FROM pm.seo_keywords WHERE web_url = '{$xianzhi['web_url']}' AND delete_time is NULL");
    while ($yonghu = mysqli_fetch_array($yonghusql))
    {
        //如果存在，删除闲置词库多余的词
        if ($yonghu)
        {
            $del_query = mysqli_query($db,"DELETE FROM pm.seo_keywords WHERE pm.seo_keywords.id = {$xianzhi['id']}");
            if ($del_query)
            {
                echo "编号{$xianzhi['id']}删除成功\n";
            }
            else
            {
                echo "编号{$xianzhi['id']}删除失败\n";
            }
            break;
        }
    }
}


mysqli_free_result($result);
mysqli_close($db);
echo "done";