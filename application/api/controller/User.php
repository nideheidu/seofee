<?php

namespace app\api\controller;

use think\captcha\Captcha;
use app\common\model\UserGroup;
use app\common\model\Customer;
use app\common\model\CustomerAccount;
use app\common\model\Mingxi;
use app\common\model\Keywords;
use think\Db;
use think\Request;
use app\common\model\SystemConfig as System;
use app\common\model\Notice;
use app\common\model\NoticeLog;

class User extends Base
{

    public function init(Request $request)
    {

        $user = Customer::get(intval($request->uid));

        $map = [];
        $map['agent_id'] = $user['upid'];
        $map['status'] = 1;

        $data_list = System::where($map)->order('sort,id')->column('id,name,title,group,url,value,type,options,tips');
        if ($data_list) {
            foreach ($data_list as $k => &$v) {
                $config[$v['name']] = $v['value'];
            }
        } else {
            $config = config('base.');
        }
        return json(['code' => 0, 'data' => $config]);
    }

    public function getViewData(Request $request)
    {
        $server_name = $_SERVER['SERVER_NAME'];


        $map = [];
        $map['value'] = $server_name;
        $map['group'] = 'domain';
        $map['status'] = 1;

        $domain_config = System::where($map)->find();

        $agent_id = isset($domain_config['agent_id']) ? $domain_config['agent_id'] : 999999;

        if ($agent_id) {


            $agent_config = System::where(['agent_id' => $agent_id, 'group' => ['slider', 'logo', 'topmenu', 'bottommenu', 'base', 'copyright']])->order('sort,id')->column('id,name,group,value,title');
            foreach ($agent_config as $key => $value) {
                if (in_array($value['group'], ['slider', 'logo'])) {
                    $value['value'] = json_decode($value['value'], 1);
                }

                $config[$value['group']][] = $value;
            }
            foreach ($config['base'] as $k => $v) {
                if ($v['name'] == 'site_name') {
                    $config['site_name'] = $v['value'];
                }
            }
            unset($config['base']);
            foreach ($config['copyright'] as $k => $v) {
                unset($config['copyright'][$k]);
                $config['copyright'][$v['name']] = $v;
            }
            //unset($config['base']);
            //print_r($config);
        } else {
            $config = array(
                'site_name' => '速达SEO计费系统',
                'slider' => [
                    ['value' => ['image' => '/images/slider/77/f1fa13cce7e63bb00e5485b82312f5.png', 'text' => '幻灯片']],
                    ['value' => ['image' => '/images/slider/3e/4772f53b8f5d47970e43eba05b8368.png', 'text' => '幻灯片']],
                ],
                'logo' => [
                    ['value' => ['image' => '/images/logo/80/43b7169dd9c08406b60affc1cf760b.png', 'text' => '网站logo']]
                ],
                'topmenu' => [
                    ['value' => 'http://www.17s.cn', 'title' => '首页'],
                    ['value' => 'http://www.17s.cn', 'title' => '云产品'],
                    ['value' => 'http://www.17s.cn', 'title' => '数据监控']
                ],
                'bottommenu' => [
                    ['value' => 'http://www.17s.cn', 'title' => '首页'],
                    ['value' => 'http://www.17s.cn', 'title' => '云产品'],
                    ['value' => 'http://www.17s.cn', 'title' => '数据监控']
                ],
                'copyright' => [
                    'companyinfo' => ['value' => '南充速度网络有限公司 地址：南充市金融广场5栋'],
                    'copyrightinfo' => ['value' => 'Copyright © 2012-2017 17s.cn All Rights Reserved. 速达网络 版权所有'],
                    'ipcinfo' => ['value' => '蜀ICP备12345678']
                ],
            );
        }
        return json(['code' => 0, 'data' => $config]);
    }

    //获取用户基本信息
    public function info(Request $request)
    {
        //echo $request->uid;exit;
        $info = Customer::find($request->uid);
        return self::result($info);
    }

    //保存用户基本信息
    public function save_info(Request $request)
    {
        $check = ['email', 'phone', 'qq_number', 'contacts', 'company_name'];
        $param = $request->request();
        $data = [];
        foreach ($param as $k => $v) {
            if (in_array($k, $check)) {
                $data[$k] = $v;
            }
        }
        $info = Customer::where('id', $request->uid)->update($data);
        return self::result($info);
    }

    public function getUserMoneyInfo(Request $request)
    {
        $uid = $request->uid;

        //今日预计消费
        $estodayfee = Keywords::where('uid',$uid)->where('status',2)->where('current_ranking <= 10 and current_ranking > 0')->where("date_format(from_unixtime(rank_time),'%Y-%m-%d') = date_format(now(),'%Y-%m-%d')")->sum('price');
        //昨日消费
        $yesterdayfee = Mingxi::where("uid",$uid)->where("change_type",2)->where("create_time >= ".strtotime(date('Y-m-d 00:00:01', strtotime('-1 day'))))->where("create_time <= ".strtotime(date('Y-m-d 23:59:59', strtotime('-1 day'))))->sum('free');
        //上月消费
        $lastmonthfee = Mingxi::where("uid",$uid)->where("change_type",2)->where("create_time >= ".strtotime(date('Y-m-01 00:00:01', strtotime('-1 month'))))->where("create_time < ".strtotime(date('Y-m-01 00:00:01')))->sum('free');
        //总消费
        $allsum = Mingxi::where('change_type', 2)->where('uid', $request->uid)->sum('free');
        //账户信息
        $info = CustomerAccount::where('uid', $request->uid)->find();
        $data['account'] = [
            [
                'title' => '今日预计消费',
                'value' => abs($estodayfee),
            ],
            [
                'title' => '昨日消费',
                'value' => abs($yesterdayfee),
            ],
            [
                'title' => '上月消费',
                'value' => abs($lastmonthfee),
            ],
            [
                'title' => '累计消费',
                'value' => abs($allsum),
            ],
            [
                'title' => '账户余额',
                'value' => floor($info['total_sum']),
            ],
        ];

        $baidupcs = [];
        //百度PC词总数
        $arr = Keywords::where('uid',$uid)->where("status in (2,6)")->where('search_ngines',1)->count();
        if ($arr)
        {
            //百度PC词新达标词总数
            $arr1 = Keywords::where('uid',$uid)->where('status',2)->where('search_ngines',1)->where("standard=0 and current_ranking <= 10 and current_ranking > 0 and date_format(from_unixtime(rank_time),'%Y-%m-%d') >= date_sub(curdate(),interval 1 day)")->count();
            //百度PC达标词总数
            $arr2 = Keywords::where('uid',$uid)->where('status',2)->where('search_ngines',1)->where("current_ranking <= 10 and current_ranking > 0")->count();
            //百度PC未达标词总数
            $arr3 = Keywords::where('uid',$uid)->where("status in (2,6)")->where('search_ngines',1)->where("current_ranking > 10 or current_ranking = 0")->count();
            //达标率
            $arr4 = $arr2 && $arr ? ($arr2/$arr*100) : 0;
            $baidupcs = [
                [
                    'title' => '百度PC优化总词数',
                    'value' => $arr,
                    'url' => '/search_ngines=1'
                ],
                [
                    'title' => '百度PC新达标词数',
                    'value' => $arr1,
                    'url' => '/search_ngines=1/standard_status=1'
                ],
                [
                    'title' => '百度PC累计达标词数',
                    'value' => $arr2,
                    'url' => '/search_ngines=1/standard_status=2'
                ],
                [
                    'title' => '百度PC未达标词数',
                    'value' => $arr3,
                    'url' => '/search_ngines=1/standard_status=3'
                ],
                [
                    'title' => '百度PC达标率',
                    'value' => floor($arr4).'%',
                ],
            ];
        }

        $baidums = [];
        //百度移动词总数
        $arr = Keywords::where('uid',$uid)->where("status in (2,6)")->where('search_ngines',2)->count();
        if ($arr)
        {
            //百度移动词新达标词总数
            $arr1 = Keywords::where('uid',$uid)->where('status',2)->where('search_ngines',2)->where("standard=0 and current_ranking <= 10 and current_ranking > 0 and date_format(from_unixtime(rank_time),'%Y-%m-%d') >= date_sub(curdate(),interval 1 day)")->count();
            //百度移动达标词总数
            $arr2 = Keywords::where('uid',$uid)->where('status',2)->where('search_ngines',2)->where("current_ranking <= 10 and current_ranking > 0")->count();
            //百度移动未达标词总数
            $arr3 = Keywords::where('uid',$uid)->where("status in (2,6)")->where('search_ngines',2)->where("current_ranking > 10 or current_ranking = 0")->count();
            //达标率
            $arr4 = $arr2 && $arr ? ($arr2/$arr*100) : 0;
            $baidums = [
                [
                    'title' => '百度M优化总词数',
                    'value' => $arr,
                    'url' => '/search_ngines=2'
                ],
                [
                    'title' => '百度M新达标词数',
                    'value' => $arr1,
                    'url' => '/search_ngines=2/standard_status=1'
                ],
                [
                    'title' => '百度M累计达标词数',
                    'value' => $arr2,
                    'url' => '/search_ngines=2/standard_status=2'
                ],
                [
                    'title' => '百度M未达标词数',
                    'value' => $arr3,
                    'url' => '/search_ngines=2/standard_status=3'
                ],
                [
                    'title' => '百度M达标率',
                    'value' => floor($arr4).'%',
                ],
            ];
        }

        $sanliulingpcs = [];
        //360PC词总数
        $arr = Keywords::where('uid',$uid)->where("status in (2,6)")->where('search_ngines',3)->count();
        if ($arr)
        {
            //360PC词新达标词总数
            $arr1 = Keywords::where('uid',$uid)->where('status',2)->where('search_ngines',3)->where("standard=0 and current_ranking <= 10 and date_format(from_unixtime(rank_time),'%Y-%m-%d') >= date_sub(curdate(),interval 1 day)")->count();
            //360PC达标词总数
            $arr2 = Keywords::where('uid',$uid)->where('status',2)->where('search_ngines',3)->where("current_ranking <= 10 and current_ranking > 0")->count();
            //360PC未达标词总数
            $arr3 = Keywords::where('uid',$uid)->where("status in (2,6)")->where('search_ngines',3)->where("current_ranking > 10 or current_ranking = 0")->count();
            //达标率
            $arr4 = $arr2 && $arr ? ($arr2/$arr*100) : 0;
            $sanliulingpcs = [
                [
                    'title' => '360PC优化总词数',
                    'value' => $arr,
                    'url' => '/search_ngines=3'
                ],
                [
                    'title' => '360PC新达标词数',
                    'value' => $arr1,
                    'url' => '/search_ngines=3/standard_status=1'
                ],
                [
                    'title' => '360PC累计达标词数',
                    'value' => $arr2,
                    'url' => '/search_ngines=3/standard_status=2'
                ],
                [
                    'title' => '360PC未达标词数',
                    'value' => $arr3,
                    'url' => '/search_ngines=3/standard_status=3'
                ],
                [
                    'title' => '360PC达标率',
                    'value' => floor($arr4).'%',
                ],
            ];
        }

        $sanliulingms = [];
        //360M词总数
        $arr = Keywords::where('uid',$uid)->where("status in (2,6)")->where('search_ngines',4)->count();
        if ($arr)
        {
            //360M词新达标词总数
            $arr1 = Keywords::where('uid',$uid)->where('status',2)->where('search_ngines',4)->where("standard=0 and current_ranking <= 10 and date_format(from_unixtime(rank_time),'%Y-%m-%d') >= date_sub(curdate(),interval 1 day)")->count();
            //360M达标词总数
            $arr2 = Keywords::where('uid',$uid)->where('status',2)->where('search_ngines',4)->where("current_ranking <= 10 and current_ranking > 0")->count();
            //360M未达标词总数
            $arr3 = Keywords::where('uid',$uid)->where("status in (2,6)")->where('search_ngines',4)->where("current_ranking > 10 or current_ranking = 0")->count();
            //达标率
            $arr4 = $arr2 && $arr ? ($arr2/$arr*100) : 0;
            $sanliulingms = [
                [
                    'title' => '360M优化总词数',
                    'value' => $arr,
                    'url' => '/search_ngines=4'
                ],
                [
                    'title' => '360M新达标词数',
                    'value' => $arr1,
                    'url' => '/search_ngines=4/standard_status=1'
                ],
                [
                    'title' => '360M累计达标词数',
                    'value' => $arr2,
                    'url' => '/search_ngines=4/standard_status=2'
                ],
                [
                    'title' => '360M未达标词数',
                    'value' => $arr3,
                    'url' => '/search_ngines=4/standard_status=3'
                ],
                [
                    'title' => '360M达标率',
                    'value' => floor($arr4).'%',
                ],
            ];
        }

        $sougoupcs = [];
        //搜狗PC词总数
        $arr = Keywords::where('uid',$uid)->where("status in (2,6)")->where('search_ngines',5)->count();
        if ($arr)
        {
            //搜狗PC词新达标词总数
            $arr1 = Keywords::where('uid',$uid)->where('status',2)->where('search_ngines',5)->where("standard=0 and current_ranking <= 10 and date_format(from_unixtime(rank_time),'%Y-%m-%d') >= date_sub(curdate(),interval 1 day)")->count();
            //搜狗PC达标词总数
            $arr2 = Keywords::where('uid',$uid)->where('status',2)->where('search_ngines',5)->where("current_ranking <= 10 and current_ranking > 0")->count();
            //搜狗PC未达标词总数
            $arr3 = Keywords::where('uid',$uid)->where("status in (2,6)")->where('search_ngines',5)->where("current_ranking > 10 or current_ranking = 0")->count();
            //达标率
            $arr4 = $arr2 && $arr ? ($arr2/$arr*100) : 0;
            $sougoupcs = [
                [
                    'title' => '搜狗PC优化总词数',
                    'value' => $arr,
                    'url' => '/search_ngines=5'
                ],
                [
                    'title' => '搜狗PC新达标词数',
                    'value' => $arr1,
                    'url' => '/search_ngines=5/standard_status=1'
                ],
                [
                    'title' => '搜狗PC累计达标词数',
                    'value' => $arr2,
                    'url' => '/search_ngines=5/standard_status=2'
                ],
                [
                    'title' => '搜狗PC未达标词数',
                    'value' => $arr3,
                    'url' => '/search_ngines=5/standard_status=3'
                ],
                [
                    'title' => '搜狗PC达标率',
                    'value' => floor($arr4).'%',
                ],
            ];
        }

        $sougoums = [];
        //搜狗M词总数
        $arr = Keywords::where('uid',$uid)->where("status in (2,6)")->where('search_ngines',6)->count();
        if ($arr)
        {
            //搜狗M词新达标词总数
            $arr1 = Keywords::where('uid',$uid)->where('status',2)->where('search_ngines',6)->where("standard=0 and current_ranking <= 10 and date_format(from_unixtime(rank_time),'%Y-%m-%d') >= date_sub(curdate(),interval 1 day)")->count();
            //搜狗M达标词总数
            $arr2 = Keywords::where('uid',$uid)->where('status',2)->where('search_ngines',6)->where("current_ranking <= 10 and current_ranking > 0")->count();
            //搜狗M未达标词总数
            $arr3 = Keywords::where('uid',$uid)->where("status in (2,6)")->where('search_ngines',6)->where("current_ranking > 10 or current_ranking = 0")->count();
            //达标率
            $arr4 = $arr2 && $arr ? ($arr2/$arr*100) : 0;
            $sougoums = [
                [
                    'title' => '搜狗M优化总词数',
                    'value' => $arr,
                    'url' => '/search_ngines=6'
                ],
                [
                    'title' => '搜狗M新达标词数',
                    'value' => $arr1,
                    'url' => '/search_ngines=6/standard_status=1'
                ],
                [
                    'title' => '搜狗M累计达标词数',
                    'value' => $arr2,
                    'url' => '/search_ngines=6/standard_status=2'
                ],
                [
                    'title' => '搜狗M未达标词数',
                    'value' => $arr3,
                    'url' => '/search_ngines=6/standard_status=3'
                ],
                [
                    'title' => '搜狗M达标率',
                    'value' => floor($arr4).'%',
                ],
            ];
        }


        $data['list'] = array_merge($baidupcs,$baidums,$sanliulingpcs,$sanliulingms,$sougoupcs,$sougoums);
        return self::result($data);
    }

    //获取用户余额 今日消费 本月消费 总消费
    public function getUserMoneyInfoOld(Request $request)
    {
        // echo $request->uid;exit;
        $info = CustomerAccount::where('uid', $request->uid)->find();
        $todaysum = Mingxi::where('create_time >' . strtotime(date('Y-m-d')) . ' and create_time < ' . strtotime(date('Y-m-d', strtotime('+1 day'))))->where('change_type', 2)->where('uid', $request->uid)->sum('free');

        $allsum = Mingxi::where('change_type', 2)->where('uid', $request->uid)->sum('free');
        $monthsum = Mingxi::where('create_time >' . mktime(0, 0, 0, date('m'), 1, date('Y')) . ' and create_time < ' . mktime(23, 59, 59, date('m'), date('t'), date('Y')))->where('change_type', 2)->where('uid', $request->uid)->sum('free');
        $allcount = Keywords::where('uid', $request->uid)->count();//累计任务数
        $recivecount = Keywords::where('uid', $request->uid)->where('current_ranking <10')->where('current_ranking != 0')->count();//总达标数
        $stopcount = Keywords::where('uid', $request->uid)->where('status', 4)->count();//已停任务数
        $scurrentcount = Keywords::where('uid', $request->uid)->where('status <4')->count();//当前任务数
        $scurrentrecivecount = Keywords::where('uid', $request->uid)->where('status < 4')->where('current_ranking <10')->where('current_ranking != 0')->count();//今日达标数


        $account_info = db('CustomerAccount')->where('uid', $request->uid)->find();

        $uid = $request->uid;


        $keywordsCount = Keywords::alias('a')
            ->join('customer c', 'c.id = a.uid')
            ->where('c.id =' . $uid)
            ->count();//关键词数
        $keywordsCount_2 = Keywords::alias('a')
            ->join('customer c', 'c.id = a.uid')
            ->where('c.id =' . $uid)
            ->where('a.status', '2')->count();//优化中
        $keywordsCounttop10 = Keywords::alias('a')
            ->join('customer c', 'c.id = a.uid')
            ->where('c.id =' . $uid)
            ->where('a.current_ranking <= 10')->count();//前10
        $keywordsCount_1 = Keywords::alias('a')
            ->join('customer c', 'c.id = a.uid')
            ->where('c.id =' . $uid)
            ->where('a.status', '1')->where('a.agent_status', '2')->count();//待审核
        $keywordsCount_5 = Keywords::alias('a')
            ->join('customer c', 'c.id = a.uid')
            ->where('c.id =' . $uid)
            ->where('a.status', '5')->count();//拒绝
        $webSiteCount = db('web_url')->alias('a')
            ->join('customer c', 'c.id = a.uid')
            ->where('c.id =' . $uid)
            ->count();//网站数量

        $notice = db('notice')->alias('a')
            ->leftjoin('notice_log l', 'l.nid = a.id and l.uid = ' . $uid)
            ->where('a.group', 'in', array(1, 3))
            ->order('a.create_time desc')
            ->field('a.*,l.id as lid')
            ->limit(5)
            ->select();

        foreach ($notice as $key => $value) {
            $notice[$key]['group_name'] = $value['group'] == 1 ? '平台' : ($value['group'] == 2 ? '代理商' : "普通会员");
            $notice[$key]['create_time'] = date('Y-m-d H:i:s', $value['create_time']);

            $post_excerpt = strip_tags(htmlspecialchars_decode($value['content']));
            $post_excerpt = trim($post_excerpt);

            $patternArr = array('/\s/', '/ /');
            $replaceArr = array('', '');
            $post_excerpt = preg_replace($patternArr, $replaceArr, $post_excerpt);
            $valuex = mb_strcut($post_excerpt, 0, 140, 'utf-8');
            $notice[$key]['desction'] = $valuex;
        }


        $task = db('keywords')->alias('a')
            ->join('customer c', 'c.id = a.uid')
            ->where('c.id =' . $uid)
            ->field('a.search_ngines,count(a.search_ngines) as count')->group('a.search_ngines')->select(); //百度PC

        $taskstandard = db('keywords')->alias('a')
            ->join('customer c', 'c.id = a.uid')
            ->where('c.id =' . $uid)
            ->field('a.search_ngines,count(a.search_ngines) as count')
            ->where('a.current_ranking < 10')->group('a.search_ngines')->select(); //百度PC


        foreach ($taskstandard as $key => $value) {
            $taskstandardcount[$value['search_ngines']]['count'] = $value['count'];
        }


        foreach ($task as $key => $value) {
            $taskcount[$value['search_ngines']]['count'] = $value['count'];
        }
        // foreach ($taskcount as $key => $value) {
        //     if($taskstandardcount[$key]['count'] == 0)
        //     {
        //         $taskcount[$key]['rate'] = 0;
        //     }
        //     else
        //     {
        //         $taskcount[$key]['rate'] = round($taskstandardcount[$key]['count']/$value['count'] * 100);
        //     }

        // }

        for ($i = 1; $i < 7; $i++) {

            if (isset($taskcount[$i])) {
                if (isset($taskstandardcount[$i]['count']) == 0) {
                    $taskcount[$i]['rate'] = 0;
                } else {
                    $taskcount[$i]['rate'] = round($taskstandardcount[$i]['count'] / $taskcount[$i]['count'] * 100);
                }
            } else {
                $taskcount[$i]['count'] = 0;
                $taskcount[$i]['rate'] = 0;
            }

        }

        //记录资金明细
        $data['sum'] = $info['total_sum'];
        $data['todaysum'] = abs($todaysum);
        $data['monthsum'] = abs($monthsum);
        $data['total_consumption'] = abs($allsum);
        $data['allcount'] = $allcount;
        $data['recivecount'] = $recivecount;
        $data['stopcount'] = $stopcount;
        $data['scurrentcount'] = $scurrentcount;
        $data['scurrentrecivecount'] = $scurrentrecivecount;

        $data['account'] = array(
            [
                'title' => '我的余额',
                'value' => $info['total_sum'],
            ],
            [
                'title' => '今日预估消费',
                'value' => abs($todaysum),
            ],
            [
                'title' => '本月消费',
                'value' => abs($monthsum),
            ],
            [
                'title' => '总累计消费',
                'value' => abs($allsum),
            ],

        );
        $data['notice'] = $notice;
        $data['keywords'] = array(
//            [
//                'title' => '关键词数',
//                'value' => $keywordsCount,
//            ],
            [
                'title' => '关键词量',
                'value' => $keywordsCount_2,
            ],
            [
                'title' => '达标词量',
                'value' => $keywordsCounttop10,
            ],
//            [
//                'title' => '待审核',
//                'value' => $keywordsCount_1,
//            ],
//            [
//                'title' => '拒绝的词',
//                'value' => $keywordsCount_5,
//            ],
//            [
//                'title' => '网站数量',
//                'value' => $webSiteCount,
//            ],
        );

        $data['searchEngine'] = array(
            [
                'title' => '百度PC',
                'value' => isset($taskcount[1]['count']) ? $taskcount[1]['count'] : 0,
                'rate' => isset($taskcount[1]['rate']) ? $taskcount[1]['rate'] : 0,

            ],
            [
                'title' => '360PC',
                'value' => isset($taskcount[3]['count']) ? $taskcount[3]['count'] : 0,
                'rate' => isset($taskcount[3]['rate']) ? $taskcount[3]['rate'] : 0,
            ],
            [
                'title' => '搜狗PC',
                'value' => isset($taskcount[5]['count']) ? $taskcount[5]['count'] : 0,
                'rate' => isset($taskcount[5]['rate']) ? $taskcount[5]['rate'] : 0,
            ],
            [
                'title' => '百度移动',
                'value' => isset($taskcount[2]['count']) ? $taskcount[2]['count'] : 0,
                'rate' => isset($taskcount[2]['rate']) ? $taskcount[2]['rate'] : 0,
            ],
            [
                'title' => '360移动',
                'value' => isset($taskcount[4]['count']) ? $taskcount[4]['count'] : 0,
                'rate' => isset($taskcount[4]['rate']) ? $taskcount[4]['rate'] : 0,
            ],
            [
                'title' => '搜狗移动',
                'value' => isset($taskcount[6]['count']) ? $taskcount[6]['count'] : 0,
                'rate' => isset($taskcount[6]['rate']) ? $taskcount[6]['rate'] : 0,
            ],
        );

        $data['task'] = array(
            [
                'title' => '累计任务数',
                'value' => $allcount,
            ],
            [
                'title' => '总达标数',
                'value' => $recivecount,
            ],
            [
                'title' => '已停任务数',
                'value' => $stopcount,
            ],
            [
                'title' => '当前任务数',
                'value' => $scurrentcount,
            ],
            [
                'title' => '今日达标数',
                'value' => $scurrentrecivecount,
            ],
        );

        $data['member'] = array();

        return self::result($data);
    }

    /**
     * 修改密码
     * @param Request $request
     * @return \think\response\Json
     * @throws \think\Exception\DbException
     */
    public function editPassword(Request $request)
    {
        $uid = $request->uid;
        $oldPassword = $request->get('oldPassword', '', 'string');
        $password = $request->get('password', '', 'string');
        if (empty($password)) {
            return json(['code' => 1, 'msg' => '密码不能为空']);
        }
        $user = Customer::find($request->uid);


        if (password_verify($oldPassword, $user->password)) {
            $user->password = $password;
            if ($user->save()) {
                return json(['code' => 0, 'msg' => '修改成功']);
            } else {
                return json(['code' => 1, 'msg' => '修改失败']);
            }
        } else {
            return json(['code' => 1, 'msg' => '原密码错误']);
        }


    }

    public function doReg(Request $request)
    {
        $username = $request->post('username', '', 'string');
        $pwd = $request->post('password', '', 'string');
        $vercode = $request->post('vercode', '', 'string');
        $phone = $request->post('phone', '', 'string');
        $company = $request->post('company', '', 'string');
        $code = $request->post('code', '', 'string');
        $status = 1;
        if (empty($vercode)) {
            return json(['code' => 0, 'msg' => '请输入验证码']);
        }
        $captcha = new Captcha();
        if (!$captcha->check($vercode)) {
            return json(['code' => 0, 'msg' => '验证码错误']);
        }
        if ($code) {
            $group = new UserGroup;
            $codeInfo = $group->getCodeInfo($code);
            if ($codeInfo) {
                $data['upid'] = $codeInfo['agent_id'];
                $data['member_level'] = $codeInfo['id'];
            } else {
                return json(['code' => 0, 'msg' => '推荐码不正确，请确认后再试。']);
            }
        } else  //没有推荐码默认注册到平台下的代理账户
        {
            $group = new UserGroup;
            $codeInfo = $group->getDefaultGroup();
            if ($codeInfo) {
                $data['upid'] = $codeInfo['agent_id'];
                $data['member_level'] = $codeInfo['id'];
            } else {
                return json(['code' => 0, 'msg' => '平台未配置，暂无法注册']);
            }
        }


        $data['username'] = $username;
        $data['password'] = $pwd;
        //$data['confirm_password'] = 
        $data['contacts'] = $phone;
        $data['company_name'] = $company;

        $data['status'] = 1;

        $customer = new Customer;

        $res = $customer->addUser($data);

        if ($res['code'] == 0) {
            return json(['code' => 0, 'succsee' => 1, 'msg' => '注册成功！']);
        } else {
            return json(['code' => 0, 'msg' => $res['msg']]);
        }


        //return json(Customer::doLogin($request->param()));
    }

    public function notice(Request $request)
    {
        $data = $request->get();
        $id = $data['id'];
        $where['id'] = $id;
        $notice = new Notice;
        $res = $notice->where($where)->find();
        if ($res) {
            $insert_data['uid'] = $request->uid;
            $insert_data['nid'] = $id;
            $insert_data['create_time'] = time();
            $noticelog = new NoticeLog;
            $noticelog->allowField(true)->save($insert_data);
        }
        return json(['code' => 0, 'notice' => $res]);
    }

    public function recharge(Request $request)
    {
        $user = Customer::get(intval($request->uid));
        $map = [];
        $map['status'] = 1;
        $map['agent_id'] = $user['upid'];;
        $map['group'] = 'recharge';

        $data_list = System::where($map)->order('sort asc')->find();

        if (empty($data_list)) {
            $value['image'] = "/images/1562053100.png";
            $value['text'] = "识别二维码联系在线客服充值！";
            $configData = [
                'system' => 1,
                'group' => 'recharge',
                'title' => "支付设置",
                'name' => 'recharge',
                'value' => json_encode($value),
                'sort' => 1,
                'type' => 'input',
                'options' => '',
                'tips' => '',
                'status' => 1,
                'agent_id' => $request->uid,
            ];


            $systemModel = new System;
            $res = $systemModel->allowField(true)->save($configData);
            $configData['id'] = $systemModel->id;
            $data_list = $configData;


        }
        if ($data_list) {

            $data_list['id'] = $data_list['name'];
            $data_list['value'] = json_decode($data_list['value'], 1);
        }
        // 模块配置
        return json(['code' => 0, 'data' => $data_list]);
    }
}