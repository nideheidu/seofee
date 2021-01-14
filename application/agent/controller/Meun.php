<?php

namespace app\agent\controller;

use app\common\model\AuthRule;
use app\common\model\Mingxi;
use app\common\model\Keywords;
use function PHPSTORM_META\elementType;
use think\Request;
use think\Db;

class Meun extends Base
{
    public function index(Request $request)
    {
        $uid = $request->uid;
        $type = $request->param('type', 'list');
        $meun = config('menu.agent_menu');
        $tip = db('keywords')->alias('a')->join('customer c', 'c.id = a.uid')->where('c.upid', $uid)->where('a.status', 1)->where('a.agent_status', 1)->count();
        $stoptip = db('keywords')->alias('a')->join('customer c', 'c.id = a.uid')->where('c.upid', $uid)->where('a.status', 3)->where('a.agent_status', 2)->count();
        foreach ($meun as $key => $value) {
            if (isset($value['list'])) {
                foreach ($value['list'] as $k => $v) {
                    if ($v['name'] == 'seo_task_shenhe' && $tip > 0) {
                        $meun[$key]['list'][$k]['tip'] = $tip;
                        $meun[$key]['tip'] = 1;

                    }
                    if ($v['name'] == 'query_queryrank' && $stoptip > 0) {
                        $meun[$key]['list'][$k]['tip'] = $stoptip;
                        $meun[$key]['tip'] = 1;

                    }

                }
            }
        }
        if ($type == 'list') {
            return json(['code' => 0, 'data' => $meun]);

        } else {
            return json(AuthRule::getMenuList($type));

        }

    }

    public function add(Request $request)
    {
        $res = AuthRule::add();
        return json($res);

    }

    public function getUserMoneyInfo(Request $request)
    {
        $uid = $request->uid;
        $total_sum = Mingxi::where('change_type', 2)->where('uid', $uid)->sum('free');
        $todaysum = Mingxi::where('create_time >' . strtotime(date('Y-m-d')) . ' and create_time < ' . strtotime(date('Y-m-d', strtotime('+1 day'))))->where('change_type', 2)->where('uid', $uid)->sum('free');
        $monthsum = Mingxi::where('create_time >' . mktime(0, 0, 0, date('m'), 1, date('Y')) . ' and create_time < ' . mktime(23, 59, 59, date('m'), date('t'), date('Y')))->where('change_type', 2)->where('uid', $uid)->sum('free');
        $allcount = db('keywords')->where('uid', $uid)->count();
        $recivecount = Keywords::where('current_ranking <10')->where('uid', $uid)->where('current_ranking != 0')->count();
        $stopcount = Keywords::where('status', 4)->where('uid', $uid)->count();
        $scurrentcount = Keywords::where('status <4')->where('uid', $uid)->count();
        $scurrentrecivecount = Keywords::where('status < 4')->where('uid', $uid)->where('current_ranking <10')->where('current_ranking != 0')->count();
        $customersum = db('CustomerAccount')->where('uid', $uid)->sum('total_sum');
        $data['sum'] = abs($total_sum);
        $data['todaysum'] = abs($todaysum);
        $data['monthsum'] = abs($monthsum);
        $data['allcount'] = $allcount;
        $data['recivecount'] = $recivecount;
        $data['stopcount'] = $stopcount;
        $data['scurrentcount'] = $scurrentcount;
        $data['scurrentrecivecount'] = $scurrentrecivecount;
        $data['customersum'] = $customersum;
        return json(array('code' => 0, 'data' => $data));

    }

    public function getAdminUserMoneyInfo(Request $request)
    {
        $uid = $request->uid;
        $total_sum = Mingxi::alias('a')->where('a.uid =' . $uid)->where('a.change_type', 2)->sum('free');
        $user_total_sum = Mingxi::alias('a')->join('customer c', 'c.id = a.uid')->where('c.upid =' . $uid)->where('a.change_type', 2)->sum('free');
        $todaysum = Mingxi::alias('a')->where('a.uid =' . $uid)->where('a.create_time >' . strtotime(date('Y-m-d')) . ' and a.create_time < ' . strtotime(date('Y-m-d', strtotime('+1 day'))))->where('a.change_type', 2)->sum('free');
        $ser_todaysum = Mingxi::alias('a')->join('customer c', 'c.id = a.uid')->where('c.upid =' . $uid)->where('a.create_time >' . strtotime(date('Y-m-d')) . ' and a.create_time < ' . strtotime(date('Y-m-d', strtotime('+1 day'))))->where('a.change_type', 2)->sum('free');
        $monthsum = Mingxi::alias('a')->where('a.uid =' . $uid)->where('a.create_time >' . mktime(0, 0, 0, date('m'), 1, date('Y')) . ' and a.create_time < ' . mktime(23, 59, 59, date('m'), date('t'), date('Y')))->where('a.change_type', 2)->sum('free');
        $user_monthsum = Mingxi::alias('a')->join('customer c', 'c.id = a.uid')->where('c.upid =' . $uid)->where('a.create_time >' . mktime(0, 0, 0, date('m'), 1, date('Y')) . ' and a.create_time < ' . mktime(23, 59, 59, date('m'), date('t'), date('Y')))->where('a.change_type', 2)->sum('free');
        //我的上个月消费  新增
        $last_monthsum = Mingxi::alias('a')->where('a.uid = ' . $uid)->where('a.create_time >' . strtotime(date('Y-m-01 00:00:00',strtotime('-1 month'))) . ' and a.create_time < ' . strtotime(date("Y-m-d 23:59:59", strtotime(-date('d').'day'))))->where('a.change_type', 2)->sum('free');
        //会员上个月消费  新增
        $last_user_monthsum = Mingxi::alias('a')->join('customer c', 'c.id = a.uid')->where('c.upid =' . $uid)->where('a.create_time >' . strtotime(date('Y-m-01 00:00:00',strtotime('-1 month'))) . ' and a.create_time < ' . strtotime(date("Y-m-d 23:59:59", strtotime(-date('d').'day'))))->where('a.change_type', 2)->sum('free');
        $allcount = db('keywords')->alias('a')->join('customer c', 'c.id = a.uid')->where('c.upid =' . $uid)->count();
        $recivecount = Keywords::alias('a')->join('customer c', 'c.id = a.uid')->where('c.upid =' . $uid)->where('a.current_ranking <10')->where('a.current_ranking != 0')->count();
        $stopcount = Keywords::alias('a')->join('customer c', 'c.id = a.uid')->where('c.upid =' . $uid)->where('a.status', 4)->count();
        $scurrentcount = Keywords::alias('a')->join('customer c', 'c.id = a.uid')->where('c.upid =' . $uid)->where('a.status <4')->count();
        $scurrentrecivecount = Mingxi::alias('a')->join('customer c', 'c.id = a.uid')->where('c.upid =' . $uid)->where('a.type =1 ')->where('a.create_time < ' . time())->where('a.create_time >' . strtotime(date('Y-m-d')))->count();
        $scurrentrecivecountnew = Mingxi::alias('a')->join('keywords k', 'k.id = a.kid')->join('customer c', 'c.id = a.uid')->where('k.standard = 1 ')->where('c.upid =' . $uid)->where('a.type =1 ')->where('a.create_time < ' . time())->where('a.create_time >' . strtotime(date('Y-m-d')))->count();
        $customersum = db('CustomerAccount')->alias('a')->join('customer c', 'c.id = a.uid')->where('c.upid =' . $uid)->sum('a.total_sum');
        $account_info = db('CustomerAccount')->where('uid', $request->uid)->find();
        $customercount = db('customer')->where('upid', $uid)->where('status', 1)->where('delete_time', 'null')->count();
        $customercount_0 = db('customer')->where('upid', $uid)->where('status', 0)->where('delete_time', 'null')->count();
        $keywordsCount = Keywords::alias('a')->join('customer c', 'c.id = a.uid')->where('c.upid =' . $uid)->count();
        $keywordsCount_2 = Keywords::alias('a')->join('customer c', 'c.id = a.uid')->where('c.upid =' . $uid)->where('a.status', '2')->count();
        $keywordsCounttop10 = Keywords::alias('a')->join('customer c', 'c.id = a.uid')->where('c.upid =' . $uid)->where('a.current_ranking < 10')->count();
        $keywordsCount_1 = Keywords::alias('a')->join('customer c', 'c.id = a.uid')->where('c.upid =' . $uid)->where('a.status', '1')->where('a.agent_status', '2')->count();
        $keywordsCount_5 = Keywords::alias('a')->join('customer c', 'c.id = a.uid')->where('c.upid =' . $uid)->where('a.status', '5')->count();
        $webSiteCount = db('web_url')->alias('a')->join('customer c', 'c.id = a.uid')->where('c.upid =' . $uid)->count();
        $task = db('keywords')->alias('a')->join('customer c', 'c.id = a.uid')->where('c.upid =' . $uid)->field('a.search_ngines,count(a.search_ngines) as count')->group('a.search_ngines')->select();
        $taskstandard = db('keywords')->alias('a')->join('customer c', 'c.id = a.uid')->where('c.upid =' . $uid)->field('a.search_ngines,count(a.search_ngines) as count')->where('a.current_ranking < 10')->group('a.search_ngines')->select();
        $notice = db('notice')->alias('a')->leftjoin('notice_log l', 'l.nid = a.id and l.uid = ' . $uid)->where('a.group', 'in', array(1, 2))->order('a.create_time desc')->field('a.*,l.id as lid')->limit(5)->select();
        foreach ($notice as $key => $value) {
            $notice[$key]['group_name'] = $value['group'] == 1 ? '平台' : ($value['group'] == 2 ? '代理商' : '普通会员');
            $notice[$key]['create_time'] = date('Y-m-d H:i:s', $value['create_time']);
            $post_excerpt = strip_tags(htmlspecialchars_decode($value['content']));
            $post_excerpt = trim($post_excerpt);
            $patternArr = array('/\s/', '/ /');
            $replaceArr = array('', '');
            $post_excerpt = preg_replace($patternArr, $replaceArr, $post_excerpt);
            $valuex = mb_strcut($post_excerpt, 0, 140, 'utf-8');
            $notice[$key]['desction'] = $valuex;

        }
        foreach ($taskstandard as $key => $value) {
            $taskstandardcount[$value['search_ngines']]['count'] = $value['count'];

        }
        $taskcount = array();
        foreach ($task as $key => $value) {
            $taskcount[$value['search_ngines']]['count'] = $value['count'];

        }
        foreach ($taskcount as $key => $value) {
            if (isset($taskstandardcount[$key]['count']) == 0) {
                $taskcount[$key]['rate'] = 0;

            } else {
                $taskcount[$key]['rate'] = round($taskstandardcount[$key]['count'] / $value['count'] * 100);

            }

        }
        $data['sum'] = abs($total_sum);
        $data['user_sum'] = abs($user_total_sum);
        $data['todaysum'] = abs($todaysum);
        $data['user_todaysum'] = abs($ser_todaysum);
        $data['monthsum'] = abs($monthsum);
        $data['user_monthsum'] = abs($user_monthsum);
        $data['last_monthsum'] = abs($last_monthsum);
        $data['last_user_monthsum'] = abs($last_user_monthsum);
        $data['allcount'] = $allcount;
        $data['recivecount'] = $recivecount;
        $data['stopcount'] = $stopcount;
        $data['scurrentcount'] = $scurrentcount;
        $data['scurrentrecivecount'] = $scurrentrecivecount;
        $data['customersum'] = $customersum;
        $data['total_account'] = $account_info['total_sum'];
        $data['account'] = array(['title' => '我的余额', 'value' => $account_info['total_sum'],], ['title' => '我的总消费', 'value' => abs($total_sum),], ['title' => '我的日消费', 'value' => abs($todaysum),], ['title' => '我的月消费', 'value' => abs($monthsum),],['title' => '我的上个月消费', 'value' => abs($last_monthsum)], ['title' => '会员余额', 'value' => $customersum,], ['title' => '会员总消费', 'value' => abs($user_total_sum),], ['title' => '会员日消费', 'value' => abs($ser_todaysum),], ['title' => '会员月消费', 'value' => abs($user_monthsum),],['title' => '会员上个月消费', 'value' => abs($last_user_monthsum)],);
        $data['notice'] = $notice;
        $data['keywords'] = array(['title' => '关键词数', 'value' => $keywordsCount,], ['title' => '优化中', 'value' => $keywordsCount_2,], ['title' => '排名前10', 'value' => $keywordsCounttop10,], ['title' => '待审核', 'value' => $keywordsCount_1,], ['title' => '拒绝的词', 'value' => $keywordsCount_5,], ['title' => '网站数量', 'value' => $webSiteCount,],);
        $data['searchEngine'] = array(['title' => '百度PC', 'value' => isset($taskcount[1]['count']) ? $taskcount[1]['count'] : 0, 'rate' => isset($taskcount[1]['rate']) ? $taskcount[1]['rate'] : 0,], ['title' => '360PC', 'value' => isset($taskcount[3]['count']) ? $taskcount[3]['count'] : 0, 'rate' => isset($taskcount[3]['rate']) ? $taskcount[3]['rate'] : 0,], ['title' => '搜狗PC', 'value' => isset($taskcount[5]['count']) ? $taskcount[5]['count'] : 0, 'rate' => isset($taskcount[5]['rate']) ? $taskcount[5]['rate'] : 0,], ['title' => '百度移动', 'value' => isset($taskcount[2]['count']) ? $taskcount[2]['count'] : 0, 'rate' => isset($taskcount[2]['rate']) ? $taskcount[2]['rate'] : 0,], ['title' => '360移动', 'value' => isset($taskcount[4]['count']) ? $taskcount[4]['count'] : 0, 'rate' => isset($taskcount[4]['rate']) ? $taskcount[4]['rate'] : 0,], ['title' => '搜狗移动', 'value' => isset($taskcount[6]['count']) ? $taskcount[6]['count'] : 0, 'rate' => isset($taskcount[6]['rate']) ? $taskcount[6]['rate'] : 0,],);
        $data['task'] = array(['title' => '累计任务数', 'value' => $allcount,], ['title' => '总达标数', 'value' => $recivecount,], ['title' => '已停任务数', 'value' => $stopcount,], ['title' => '当前任务数', 'value' => $scurrentcount,], ['title' => '今日达标数', 'value' => $scurrentrecivecount,], ['title' => '今日新达标', 'value' => $scurrentrecivecountnew,],);
        $data['member'] = array(['title' => '会员数量', 'value' => $customercount,], ['title' => '待审核会员', 'value' => $customercount_0,]);
        return json(array('code' => 0, 'data' => $data));

    }
}