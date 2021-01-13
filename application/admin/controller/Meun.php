<?php

namespace app\admin\controller;

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
        $type = $request->param('type', 'list');
        $meun = config('menu.admin_menu');
        $keywords = new Keywords;
        $tip = $keywords->where(['status' => 1, 'agent_status' => 2,])->count();
        $stoptip = $keywords->where(['status' => 3, 'agent_status' => 2,])->count();
        foreach ($meun as $key => $value) {
            if (isset($value['list'])) {
                foreach ($value['list'] as $k => $v) {
                    if ($v['name'] == 'seo_task_shenhe' && $tip > 0) {
                        $meun[$key]['list'][$k]['tip'] = $tip;
                        $meun[$key]['tip'] = 1;

                    }
                    if ($v['name'] == 'seo_queryrank' && $stoptip > 0) {
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
        $recivecount = Keywords::where('current_ranking <=10')->where('uid', $uid)->where('current_ranking != 0')->count();
        $stopcount = Keywords::where('status', 4)->where('uid', $uid)->count();
        $scurrentcount = Keywords::where('status <4')->where('uid', $uid)->count();
        $scurrentrecivecount = Keywords::where('status < 4')->where('uid', $uid)->where('current_ranking <=10')->where('current_ranking != 0')->count();
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
        $total_sum = Mingxi::alias('m')->join('customer c', 'c.id = m.uid')->where('c.upid', '=0')->where('m.change_type', 2)->sum('free');
        $todaysum = Mingxi::alias('m')->join('customer c', 'c.id = m.uid')->where('c.upid', '=0')->where('m.create_time >' . strtotime(date('Y-m-d')) . ' and m.create_time < ' . strtotime(date('Y-m-d', strtotime('+1 day'))))->where('change_type', 2)->sum('free');
        $monthsum = Mingxi::alias('m')->join('customer c', 'c.id = m.uid')->where('c.upid', '=0')->where('m.create_time >' . mktime(0, 0, 0, date('m'), 1, date('Y')) . ' and m.create_time < ' . mktime(23, 59, 59, date('m'), date('t'), date('Y')))->where('change_type', 2)->sum('free');
        $allcount = db('keywords')->count();
        $recivecount = Keywords::where('current_ranking <=10')->where('current_ranking != 0')->count();
        $stopcount = Keywords::where('status', 4)->count();
        $scurrentcount = Keywords::where('status <4')->count();
        $scurrentrecivecount = Mingxi::where('type =1 ')->where('create_time < ' . time())->where('create_time >' . strtotime(date('Y-m-d')))->count();
        $newscurrentrecivecount = Mingxi::alias('m')->join('keywords k', 'k.id = m.kid')->where('k.standard = 1 ')->where('m.create_time < ' . time())->where('m.create_time >' . strtotime(date('Y-m-d')))->where('k.status = 2')->count();
        $customersum = db('CustomerAccount')->alias('a')->join('customer c', 'c.id = a.uid')->where('c.upid', '=0')->sum('total_sum');
        $agentcount = db('customer')->where('isagent', '1')->count();
        $customercount = db('customer')->where('isagent', '0')->count();
        $keywordsCount = db('keywords')->count();
        $keywordsCount_2 = db('keywords')->where('status', '2')->count();
        $keywordsCounttop10 = db('keywords')->where('current_ranking <= 10')->count();
        $keywordsCount_1 = db('keywords')->where('status', '1')->where('agent_status', '2')->count();
        $keywordsCount_5 = db('keywords')->where('status', '5')->count();
        $webSiteCount = db('web_url')->count();
        $task = db('keywords')->field('search_ngines,count(search_ngines) as count')->group('search_ngines')->select();
        $taskstandard = db('keywords')->field('search_ngines,count(search_ngines) as count')->where('current_ranking <= 10')->group('search_ngines')->select();
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
        $data['todaysum'] = abs($todaysum);
        $data['monthsum'] = abs($monthsum);
        $data['allcount'] = $allcount;
        $data['recivecount'] = $recivecount;
        $data['stopcount'] = $stopcount;
        $data['scurrentcount'] = $scurrentcount;
        $data['scurrentrecivecount'] = $scurrentrecivecount;
        $data['customersum'] = $customersum;
        $data['account'] = array(['title' => '总消费', 'value' => abs($total_sum),], ['title' => '日消费', 'value' => abs($todaysum),], ['title' => '月消费', 'value' => abs($monthsum),], ['title' => '用户总额', 'value' => $customersum,],);
        $data['keywords'] = array(['title' => '关键词数', 'value' => $keywordsCount,], ['title' => '优化中', 'value' => $keywordsCount_2,], ['title' => '排名前10', 'value' => $keywordsCounttop10,], ['title' => '待审核', 'value' => $keywordsCount_1,], ['title' => '拒绝的词', 'value' => $keywordsCount_5,], ['title' => '网站数量', 'value' => $webSiteCount,],);
        if (count($taskcount) > 0) {
            $data['searchEngine'] = array(['title' => '百度PC', 'value' => isset($taskcount[1]['count']) ? $taskcount[1]['count'] : 0, 'rate' => isset($taskcount[1]['rate']) ? $taskcount[1]['rate'] : 0,], ['title' => '360PC', 'value' => isset($taskcount[3]['count']) ? $taskcount[3]['count'] : 0, 'rate' => isset($taskcount[3]['rate']) ? $taskcount[3]['rate'] : 0,], ['title' => '搜狗PC', 'value' => isset($taskcount[5]['count']) ? $taskcount[5]['count'] : 0, 'rate' => isset($taskcount[5]['rate']) ? $taskcount[5]['rate'] : 0,], ['title' => '百度移动', 'value' => isset($taskcount[2]['count']) ? $taskcount[2]['count'] : 0, 'rate' => isset($taskcount[2]['rate']) ? $taskcount[2]['rate'] : 0,], ['title' => '360移动', 'value' => isset($taskcount[4]['count']) ? $taskcount[4]['count'] : 0, 'rate' => isset($taskcount[4]['rate']) ? $taskcount[4]['rate'] : 0,], ['title' => '搜狗移动', 'value' => isset($taskcount[6]['count']) ? $taskcount[6]['count'] : 0, 'rate' => isset($taskcount[6]['rate']) ? $taskcount[6]['rate'] : 0,],);

        } else {
            $data['searchEngine'] = array(['title' => '百度PC', 'value' => 0, 'rate' => 0,], ['title' => '360PC', 'value' => 0, 'rate' => 0,], ['title' => '搜狗PC', 'value' => 0, 'rate' => 0,], ['title' => '百度移动', 'value' => 0, 'rate' => 0,], ['title' => '360移动', 'value' => 0, 'rate' => 0,], ['title' => '搜狗移动', 'value' => 0, 'rate' => 0,],);

        }
        $data['task'] = array(['title' => '累计任务数', 'value' => $allcount,], ['title' => '总达标数', 'value' => $recivecount,], ['title' => '已停任务数', 'value' => $stopcount,], ['title' => '当前任务数', 'value' => $scurrentcount,], ['title' => '今日达标数', 'value' => $scurrentrecivecount,], ['title' => '今日新达标数', 'value' => $newscurrentrecivecount,],);
        $data['member'] = array(['title' => '代理商', 'value' => $agentcount,], ['title' => '普通会员', 'value' => $customercount,],);
        return json(array('code' => 0, 'data' => $data));

    }
}