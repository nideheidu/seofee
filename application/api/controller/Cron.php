<?php

namespace app\api\controller;

use app\common\model\Customer;
use app\common\model\Keywords;
use app\common\model\SystemConfig;
use app\common\TaskService;
use Elasticsearch\Endpoints\Indices\Upgrade\Post;
use think\Request;
use think\Db;
use seo\Seo;
use app\common\model\Fee;

class Cron extends Base
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
                if (!$value['original_rank']) {
                    $original_rank = $rank;

                } else {
                    $original_rank = $value['original_rank'];

                }
                Keywords::where('id', $value['id'])->update(['current_ranking' => $rank, 'rank_time' => time()]);
                if (!$value['start_time']) {
                    Keywords::where('id', $value['id'])->update(['start_time' => time()]);

                }
                if ($rank < 10) {
                    if (!$Customer->getUserBalanceToday($value['uid'], $value['keywords'], $value['web_url'], $value['id'])) {
                        $keyword_free = $value['fee'];
                        Keywords::where('id', $value['id'])->update(['standard' => $value['standard'] + 1, 'complete_time' => time()]);
                        $Customer->editUserBalanceDetail($value['uid'], -$keyword_free, $value['web_url'], $value['keywords'], '关键字排名扣费', 1, $value['id']);
                        $agent_id = $Customer->getMemberAgentid($value['uid']);
                        if ($agent_id) {
                            $Customer->editUserBalanceDetail($agent_id, -$value['agent_price'], $value['web_url'], $value['keywords'], '代理商关键字排名扣费', 3, $value['id']);

                        }

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
                $update_data = ['current_ranking' => $result[0]->rank, 'rank_time' => time()];
                if (!$value['original_rank']) {
                    $update_data['original_rank'] = $result[0]->rank;

                }
                Keywords::where('id', $value['id'])->update($update_data);
                if (!$value['start_time']) {
                    Keywords::where('id', $value['id'])->update(['start_time' => time()]);

                }
                if ($result[0]->rank < 10) {
                    if (!$Customer->getUserBalanceToday($value['uid'], $value['keywords'], $value['web_url'], $value['id'])) {
                        $keyword_free = $value['fee'];
                        Keywords::where('id', $value['id'])->update(['standard' => $value['standard'] + 1, 'complete_time' => time()]);
                        $Customer->editUserBalanceDetail($value['uid'], -$keyword_free, $value['web_url'], $value['keywords'], '关键字排名扣费', 1, $value['id']);
                        $agent_id = $Customer->getMemberAgentid($value['uid']);
                        if ($agent_id) {
                            $Customer->editUserBalanceDetail($agent_id, -$value['agent_price'], $value['web_url'], $value['keywords'], '代理商关键字排名扣费', 3, $value['id']);

                        }

                    }
                }
            }

        }
        return json(['code' => 0, 'msg' => '成功']);

    }

    //更新排名（全部）
    public function updatetask(Request $request)
    {
        sleep(1);
        $Customer = new Customer();
        $seo = new Seo();
        if ($request->get('page')) {
            $pageNumber = 10;

        } else {
            $pageNumber = 999;

        }
        $type = $request->get('type');
        if ($type == 'original')
        {
            $where = "rank_time = '' ";
        }
        else
        {
            $where = 'status =2 or status =3';

        }
        $total = Keywords::where($where)->count();
        $list = Keywords::where($where)->paginate($pageNumber);
        $count = 0;
        foreach ($list as $key => $value) {
            if (date('d', $value['rank_time']) == date('d', time()) && ($value['current_ranking'] <= 10 && $value['current_ranking'] != 0)) {
                continue 1;
            }
            $res = $seo->getBaiduRank($value['task_number'],$value['search_ngines']);
            $rank = $res['rank'];
            if ($rank) {
                $update_data = ['current_ranking' => $rank, 'rank_time' => time()];
                if (!$value['original_rank']) {
                    $update_data['original_rank'] = $rank;

                }
                if (!$value['start_time']) {
                    $update_data['start_time'] = time();

                }
                Keywords::where('id', $value['id'])->update($update_data);

            } else {
                $update_data = ['rank_time' => time()];
                Keywords::where('id', $value['id'])->update($update_data);

            }
            $count++;
        }
        return json(['code' => 0, 'msg' => '成功', 'count' => $count, 'total' => $total]);

    }

    //更新排名（今日）
    public function updatetaskrepeat(Request $request)
    {
        sleep(1);
        $Customer = new Customer();
        $seo = new Seo();
        if ($request->get('page')) {
            $pageNumber = 5;

        } else {
            $pageNumber = 999;

        }
        $type = $request->get('type');
        $where = 'status =2 or status =3';
        $total = Keywords::where($where)->where('create_time >= '.strtotime(date('Y-m-d 00:00:01')))->count();
        $list = Keywords::where($where)->where('create_time >= '.strtotime(date('Y-m-d 00:00:01')))->paginate($pageNumber);
        $count = 0;
        foreach ($list as $key => $value) {
            if (date('d', $value['rank_time']) == date('d', time()) && ($value['current_ranking'] <= 10 && $value['current_ranking'] != 0)) {
                continue 1;
            }
            $res = $seo->getBaiduRank($value['task_number'],$value['search_ngines'],$value['uid']);
            $rank = $res['rank'];
            if ($rank) {
                $update_data = ['current_ranking' => $rank, 'rank_time' => time()];
                if (!$value['original_rank']) {
                    $update_data['original_rank'] = $rank;

                }
                if (!$value['start_time']) {
                    $update_data['start_time'] = time();

                }
                Keywords::where('id', $value['id'])->update($update_data);

            } else {
                $update_data = ['rank_time' => time()];
                Keywords::where('id', $value['id'])->update($update_data);

            }
            $count++;
        }
        return json(['code' => 0, 'msg' => '成功', 'count' => $count, 'total' => $total]);
    }

    public function settlementtask(Request $request)
    {
        $date = $request->post("date", '');
        $tid = $request->post("tid", '');
        if (!$date)
        {
            $date = date("Y-m-d");
        }

        $Customer = new Customer();
        if ($tid)
        {
            $list = Db::table('seo_keywords')->field('*')->where('status =2')->where('current_ranking >= 1')->where('current_ranking <= 20')->where('rank_time >= ' . strtotime($date.' 00:00:00'))->where('rank_time <= ' . strtotime($date.' 23:59:59'))->where("id in ({$tid})")->order('id desc')->select();
        }
        else
        {
            $list = Db::table('seo_keywords')->field('*')->where('status =2')->where('current_ranking >= 1')->where('current_ranking <= 20')->where('rank_time >= ' . strtotime($date.' 00:00:00'))->where('rank_time <= ' . strtotime($date.' 23:59:59'))->order('id desc')->select();
        }
        $count = 0;
        foreach ($list as $key => $value) {
            if ($value['current_ranking'] >= 1 && $value['current_ranking'] <= 20 && !$Customer->getUserBalanceToday($value['uid'], $value['keywords'], $value['web_url'], $value['id'])) {
                $agent_id = $Customer->getMemberAgentid($value['uid']);
                $fee_data = $this->getTaskFee($value['uid'], $agent_id, $value['current_ranking'], $value['search_ngines'], $value['BaiduPc']);
                if ($value['price'] >= 0 && $value['current_ranking'] <= 10) {
                    $fee_data['customer_fee'] = $value['price'];

                }
                if ($value['agent_price'] > 0 && $value['current_ranking'] <= 10) {
                    $fee_data['agent_fee'] = $value['agent_price'];

                }
                if ($fee_data['customer_fee'] >= 0) {
                    if ($value['current_ranking'] <= 10) {
                        Keywords::where('id', $value['id'])->update(['standard' => $value['standard'] + 1, 'complete_time' => time()]);

                    }
                    $Customer->editUserBalanceDetail($value['uid'], -$fee_data['customer_fee'], $value['web_url'], $value['keywords'], '关键字排名扣费', 1, $value['id']);
                    $count++;

                }
                if ($agent_id && $fee_data['agent_fee'] > 0) {
                    $Customer->editUserBalanceDetail($agent_id, -$fee_data['agent_fee'], $value['web_url'], $value['keywords'], '代理商关键字排名扣费', 3, $value['id']);

                }

            }
        }
        return json(['code' => 0, 'msg' => '成功', 'count' => $count]);

    }

    public function updateKeywordsRank(Request $request)
    {
        $data = $request->param();
        $res = TaskService::system_update_keywords_rank($data, 'admin');
        return json($res);

    }

    public function updateKeywordsRank_day(Request $request)
    {
        $data = $request->param();
        $res = TaskService::system_update_keywords_rank_day($data, 'admin');
        return json($res);

    }

    public function updateKeywordsRank_xian(Request $request)
    {
        $data = $request->param();
        $res = TaskService::system_update_keywords_rank_xian($data, 'admin');
        return json($res);

    }

    public function getAllTask(Request $request)
    {
        $field = $request->post("field", '*');
        $where = $request->post("where", '');
        if ($where)
        {
            $list = Keywords::where('status = 2 and delete_time is null')->field($field)->where($where)->order('id DESC')->select();
        }
        else
        {
            $list = Keywords::where('status = 2 and delete_time is null')->field($field)->order('id DESC')->select();
        }
        $result = [];
        foreach ($list as $key => $value)
        {
            $result[$key]['id'] = $value['id'];
        }
        return json(['code' => 0, 'data' => $result]);

    }

    public function getAllTaskbak(Request $request)
    {
        $field = $request->post("field", '*');
        $where = $request->post("where", '');
        if ($where)
        {
            $list = \db('keywords')->where('status =2')->field($field)->where($where)->order('id DESC')->select();
        }
        else
        {
            $list = \db('keywords')->where('status =2')->field($field)->order('id DESC')->select();
        }
        $result = [];
        foreach ($list as $key => $value)
        {
            $result[$key]['id'] = $value['id'];
        }
        return json(['code' => 0, 'data' => $result]);

    }

    public function getAllTasknew(Request $request)
    {
        $field = $request->post("field", '*');
        $where = $request->post("where", '');

        $six = strtotime(date('Y-m-d 06:00:00'));
        $seven = strtotime(date('Y-m-d 07:40:00'));
        $result = [];
        $ids = [];
        if ($where)
        {
            $list_keywords = \db('keywords')->where('status = 2')->field($field)->where('rank_time >= '.$six.' and rank_time <= '.$seven)->where($where)->order('id DESC')->select();

            foreach ($list_keywords as $key => $value)
            {
                $result[$key]['id'] = $value['id'];
                $ids[] = $value['id'];
            }
        }
        else
        {
            $list_keywords = \db('keywords')->where('status = 2')->field($field)->where('current_ranking <= 50')->where('delete_time is null')->where('uid != 92 and uid != 105 and uid != 119')->order('id DESC')->select();

            foreach ($list_keywords as $key => $value)
            {
                $result[$key]['id'] = $value['id'];
                $ids[] = $value['id'];
            }

            $num = count($list_keywords);

            $list_rank = Db::field('keywords.id')->table(['seo_keywords' => 'keywords', 'seo_rank_log' => 'rank'])->where('keywords.task_number = rank.taskid')->where('keywords.status = 2')->where('rank.create_time >= ' . (strtotime(date('Y-m-d')) - 3 * 24 * 60 * 60))->where('rank.result <= 50')->where('keywords.delete_time is null')->group('keywords.id')->select();
            foreach ($list_rank as $key => $value)
            {
                $key_rank = $num + $key + 1;
                if (!in_array($value['id'],$ids))
                {
                    $result[$key_rank]['id'] = $value['id'];
                }
            }
        }

        return json(['code' => 0, 'data' => $result]);

    }

    public function getRepeatedlyTasknew(Request $request)
    {
        $where = $request->post("where", '');
        $list = Db::field('keywords.id')->table(['seo_keywords' => 'keywords', 'seo_rank_log' => 'rank'])->where('keywords.task_number = rank.taskid')->where('keywords.status = 2')->where('keywords.current_ranking > 10')->where('rank.create_time >= ' . (strtotime(date('Y-m-d')) - 3 * 24 * 60 * 60))->where('keywords.rank_time >= ' . strtotime(date('Y-m-d')))->where('rank.result <= 50')->where($where)->group('keywords.id')->select();
        foreach ($list as $key => $value) {
            $result[$key]['id'] = $value['id'];

        }
        return json(['code' => 0, 'data' => $result]);

    }

    public function geNewTask()
    {
        $list = Keywords::where('rank_time', 'null')->where('status', 0)->select();
        $result = [];
        foreach ($list as $key => $value) {
            $result[$key]['id'] = $value['id'];

        }
        return json(['code' => 0, 'data' => $result]);

    }

    public function getRepeatedlyTask(Request $request)
    {
        $where = $request->post("where", '');
        $list = Db::field('keywords.id')->table(['seo_keywords' => 'keywords', 'seo_rank_log' => 'rank'])->where('keywords.task_number = rank.taskid')->where('keywords.status = 2')->where('keywords.current_ranking = 101')->where('rank.create_time >= ' . (strtotime(date('Y-m-d')) - 3 * 24 * 60 * 60))->where('rank.result < 20')->where($where)->group('keywords.id')->select();
        foreach ($list as $key => $value) {
            $result[$key]['id'] = $value['id'];

        }
        return json(['code' => 0, 'data' => $result]);

    }

    private function getTaskFee($uid, $agent_id, $rank, $search_ngines = 1, $index = 0)
    {
        $engines = array('1' => 'baidupcs', '2' => 'baidumobiles', '3' => '360pcs', '4' => '360mobiles', '5' => 'sougoupcs', '6' => 'sougoumobiles');
        $configmodel = new SystemConfig;
        $site_settlement = $configmodel->get(['name' => 'site_settlement', 'agent_id' => 999999]);
        $settlement_type_platform = $site_settlement['value'];
        ($site_settlement);
        $site_settlement = $configmodel->get(['name' => 'site_settlement', 'agent_id' => $agent_id]);
        $settlement_type_system = $site_settlement['value'];
        $engine_config_system = $configmodel->where(array('agent_id' => $agent_id, 'name' => $engines[$search_ngines]))->find();
        if ($engine_config_system) {
            $user_price_ratio = $engine_config_system['value'] / 100;

        } else {
            $user_price_ratio = 1;

        }
        $engine_config_platform = $configmodel->where(array('agent_id' => 999999, 'name' => $engines[$search_ngines]))->find();
        if ($engine_config_platform) {
            $agent_price_ratio = $engine_config_platform['value'] / 100;

        } else {
            $agent_price_ratio = 1;

        }
        if ($settlement_type_system == 2 && $index) {
            $keyword_free_customer = $this->getIndexFeeForUid($uid, $user_price_ratio, $index);

        } else {
            $keyword_free_customer = $this->getGroupFeeForUid($uid, $user_price_ratio);

        }
        if ($settlement_type_platform == 2 && $index) {
            $keyword_free_agent = $this->getIndexFeeForUid($agent_id, $agent_price_ratio, $index);

        } else {
            $keyword_free_agent = $this->getGroupFeeForUid($agent_id, $agent_price_ratio);

        }
        if ($rank <= 10) {
            $taskFee = array('agent_fee' => $keyword_free_agent['keyword_free'], 'customer_fee' => $keyword_free_customer['keyword_free']);

        } else {
            $taskFee = array('agent_fee' => $keyword_free_agent['keyword_free2'], 'customer_fee' => $keyword_free_customer['keyword_free2']);

        }
        return $taskFee;

    }

    private function getGroupFeeForUid($uid, $ratio)
    {
        $data = Db::field('group.keyword_free,group.keyword_free2')->table(['seo_customer' => 'customer', 'seo_user_group' => 'group'])->where('group.id = customer.member_level')->where('customer.id= ' . $uid)->find();
        if ($data) {
            foreach ($data as $key => $value) {
                $data[$key] = $value * $ratio;

            }
            return $data;

        } else {
            return 0;

        }
        return $data;

    }

    private function getIndexFeeForUid($uid, $ratio, $index)
    {
        $data = Fee::alias('f')->field('f.fee as keyword_free,f.fee2 as keyword_free2')->join('customer c', 'c.member_level = f.group_id')->where('f.minnum <= ' . $index)->where('f.maxnum >= ' . $index)->where('c.id = ' . $uid)->find();
        if ($data) {
            $data = $data->toArray();
            foreach ($data as $key => $value) {
                $data[$key] = $value * $ratio;

            }

        } else {
            $data = $this->getGroupFeeForUid($uid, $ratio);

        }
        return $data;

    }

    public function getAllFeedback(Request $request)
    {
        $field = $request->get('field', '*');
        $where = $request->get('where', '');
        if ($where)
        {
            $feedback = \db('feedback')->where('status',1)->field($field)->where($where)->order('id desc')->select();
        }
        else
        {
            $feedback = \db('feedback')->where('status',1)->field($field)->order('id desc')->select();
        }
        $result = [];
        foreach ($feedback as $key => $value)
        {
            $keywordid = \db('keywords')->field('id')->where('keywords',$value['keywords'])->where('web_url',$value['web_url'])->where('search_ngines',$value['search_ngines'])->find();
            if (!$keywordid['id'])
            {
                continue;
            }
            $result[$key]['id'] = $keywordid['id'];
            $result[$key]['fid'] = $value['id'];
        }
        return json(['code' => 0, 'data' => $result]);
    }
}