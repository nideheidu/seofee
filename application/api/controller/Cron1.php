<?php

namespace app\api\controller;

use app\common\model\Customer;
use app\common\model\Keywords;
use think\Request;
use think\Db;
use seo\Seo;

class Cron1 extends Base
{
    public function task()
    {
        $Customer = new Customer();
        $list = Keywords::where('status = 2 or status = 3')->select();
        $seo = new Seo();
        foreach ($list as $key => $value) {
            $seo = new Seo();
            $res = $seo->getBaiduRank($value['task_number']);
            $rank = $res['rank'];
            if ($rank) {
                Keywords::where('id', $value['id'])->update(['current_ranking' => $rank, 'rank_time' => time()]);
                if (!$value['start_time']) {
                    Keywords::where('id', $value['id'])->update(['start_time' => time()]);

                }
                if ($rank < 10) {
                    if (!$Customer->getUserBalanceToday($value['uid'], $value['keywords'], $value['web_url'])) {
                        $keyword_free = Db::field('group.keyword_free')->table(['seo_customer' => 'customer', 'seo_user_group' => 'group'])->where('group.id = customer.member_level')->where('customer.id= ' . $value['uid'])->value('keyword_free');
                        Keywords::where('id', $value['id'])->update(['standard' => $value['standard'] + 1, 'complete_time' => time()]);
                        $Customer->editUserBalanceDetail($value['uid'], -$keyword_free, $value['web_url'], $value['keywords'], '关键字排名扣费');

                    }

                }
            }
        }

    }

    public function addtask()
    {
        $Customer = new Customer();
        $seo = new Seo();
        $list = Keywords::where('status =0 or status =1 or status =2')->select();
        foreach ($list as $key => $value) {
            $res = $seo->getBaiduRank($value['task_number']);
            $rank = $res['rank'];
            if ($rank) {
                if (!$value['original_rank']) {
                    $original_rank = $rank;

                } else {
                    $original_rank = $value['original_rank'];

                }
                Keywords::where('id', $value['id'])->update(['original_rank' => $rank, 'current_ranking' => $original_rank, 'rank_time' => time()]);
                if (!$value['start_time']) {
                    Keywords::where('id', $value['id'])->update(['start_time' => time()]);

                }
                if ($rank < 10) {
                    if (!$Customer->getUserBalanceToday($value['uid'], $value['keywords'], $value['web_url'])) {
                        $keyword_free = Db::field('group.keyword_free')->table(['seo_customer' => 'customer', 'seo_user_group' => 'group'])->where('group.id = customer.member_level')->where('customer.id= ' . $value['uid'])->value('keyword_free');
                        Keywords::where('id', $value['id'])->update(['standard' => $value['standard'] + 1, 'complete_time' => time()]);
                        $Customer->editUserBalanceDetail($value['uid'], -$keyword_free, $value['web_url'], $value['keywords'], '关键字排名扣费');

                    }

                }
            }
        }
        return json(['code' => 0, 'data' => '成功']);

    }

    //手动更新排名执行脚本
    public function updatetask()
    {
        exec('ps -ef|grep "/web/seofee/cron/update_ranking.php"|grep -v grep|wc -l', $output);
        if ($output && isset($output[0]) && $output[0] > 1)
        {
            return json(['code' => 1, 'msg' => '该操作已执行，不能重复执行']);
        }
        for ($i=0; $i<20; $i++)
        {
            $command = "/usr/bin/php /web/seofee/cron/update_ranking.php {$i} >> /dev/null 2>&1 &";
            exec($command);
        }
        return json(['code' => 0, 'msg' => '正在更新中，预计需要2小时']);
    }

    //手动更新排名（今日）执行脚本
    public function updatetaskrepeat()
    {
        exec('ps -ef|grep "/web/seofee/cron/update_ranking_day.php"|grep -v grep|wc -l', $output);
        if ($output && isset($output[0]) && $output[0] > 1)
        {
            return json(['code' => 1, 'msg' => '该操作已执行，不能重复执行']);
        }
        $command = "/usr/bin/php /web/seofee/cron/update_ranking_day.php >> /dev/null 2>&1 &";
        exec($command);
        return json(['code' => 0, 'msg' => '正在更新中，预计需要1小时']);
    }

    //手动结算执行脚本
    public function settlementtask(Request $request)
    {
        exec('ps -ef|grep "/web/seofee/cron/settlementtask.php"|grep -v grep|wc -l', $output);
        if ($output && isset($output[0]) && $output[0] > 1)
        {
            return json(['code' => 1, 'msg' => '该操作已执行，不能重复执行']);
        }
        $uid = '';
        $username = $request->post("username", '');
        if ($username)
        {
            $uid = \db( 'customer')->field('id')->where('username',$username)->where('delete_time is null')->select();
            if (!$uid)
            {
                return json(['code' => 1, 'msg' => '结算失败，请确认用户名是否正确！']);
            }
            $uid = $uid[0]['id'];
        }
        for ($i=0; $i<10; $i++)
        {
            $command = "/usr/bin/php /web/seofee/cron/settlementtask.php {$i} {$uid} >> /dev/null 2>&1 &";
            exec($command);
        }
        return json(['code' => 0, 'msg' => '正在结算中，预计需要1小时']);
    }

    //补扣费执行脚本
    public function settlementtaskbak(Request $request)
    {
        exec('ps -ef|grep "/web/seofee/cron/update_ranking.php"|grep -v grep|wc -l', $output);
        if ($output && isset($output[0]) && $output[0] > 1)
        {
            return json(['code' => 1, 'msg' => '当前正在执行更新排名，请等更新完排名后再重试']);
        }
        exec('ps -ef|grep "/web/seofee/cron/settlementtask.php"|grep -v grep|wc -l', $output);
        if ($output && isset($output[0]) && $output[0] > 1)
        {
            return json(['code' => 1, 'msg' => '当前正在执行结算操作，请等结算后再重试']);
        }

        $date = $request->post("date", '');
        $username = $request->post("username", '');
        $uid = '';
        if ($username)
        {
            $uid = \db( 'customer')->field('id')->where('username',$username)->where('delete_time is null')->select();
            if (!$uid)
            {
                return json(['code' => 1, 'msg' => '补扣费失败，请确认用户名是否正确！']);
            }
            $uid = $uid[0]['id'];
        }
        if (!$date)
        {
            return json(['code' => 1, 'msg' => '补扣费失败，请输入日期']);
        }
        $day = abs((strtotime(date("Y-m-d")) - strtotime($date)) / 86400);
        $command = "/usr/bin/php /web/seofee/cron/settlementtaskbak.php {$day} {$uid} >> /dev/null 2>&1 &";
        exec($command);
        return json(['code' => 0, 'msg' => '正在补扣费中，预计需要30分钟']);
    }
}