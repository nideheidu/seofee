<?php

namespace app\agent\controller;

use app\common\model\Mingxi;
use seo\Seo;
use think\Queue;
use think\Request;
use app\common\model\KeywordsRank;
use app\common\model\SystemConfig;
use app\common\TaskService;
use think\Db;

class Keywords extends Base
{
    public function getLists(Request $request)
    {
        $type = $request->get('type');
        $uid = $request->uid;
        $keywords = $request->get('keywords');
        $webUrl = $request->get('weburl', '', 'string');
        $search_ngines = $request->get('search_ngines', '');
        $limit = $request->get('limit', 30, 'int');
        $status = $request->get('status');
        $where = '1=1';
        if ($status) {
            /*if ($type)
            {
                $status = 2;
            }*/
            if ($status === '2,6')
            {
                $where .= ' and a.status in (2,6)';
            }
            elseif ($status === 1)
            {
                $where .= ' and (a.agent_status = 1 or a.agent_status = 2) and (a.status = 1 )';
            }
            elseif ($status === 2 || $status === 5)
            {
                $where .= ' and a.status = ' . $status;
            }
            else
            {
                $where .= ' and a.agent_status = ' . $status;
            }
        } else {
            $where .= ' and a.status = 3';

        }
        if ($type == 1) {
//            $limit = 999;
            $where .= " and a.current_ranking <= 10 and a.current_ranking > 0";

        }
        elseif ($type == 2) {
//            $limit = 999;
//            $where .= ' and a.standard = 1';
            $where .= " and a.standard=0 and a.current_ranking <= 10 and date_format(from_unixtime(a.rank_time),'%Y-%m-%d') = date_format(now(),'%Y-%m-%d')";
        }
        elseif ($type == 3)
        {
            $where .= " and (a.current_ranking > 10 or a.current_ranking = 0)";
        }
        if (!empty($keywords)) {
            $where .= " and (a.keywords like '%{$keywords}%' or a.web_url like '%{$keywords}%' or c.username like '%{$keywords}%' or c.contacts like '%{$keywords}%' or c.company_name like '%{$keywords}%')";

        }
        if (!empty($search_ngines)) {
            $where .= ' and a.search_ngines = ' . $search_ngines;

        }
        $where .= ' and c.upid = ' . $uid;
        $model = new \app\common\model\Keywords;
        $list = \app\common\model\Keywords::getList('*,a.id as id,a.status as status,ac.username as a_username,aca.total_sum as a_total_sum,a.create_time as create_time,a.search_ngines as search_ngine_id ,c.contacts as contacts,c.id as uid,ac.id as agent_id,c.username as username ,ac.username as agent_username', $where, $limit);
        $tesk = new TaskService();
        $settlement_type = $tesk->getSettlementForAid($uid);
        foreach ($list as $key => $value) {
            /*if ($type == 1 && !Mingxi::where(['kid' => $value['id']])->where('create_time < ' . time())->where('create_time >' . strtotime(date('Y-m-d')))->find()) {
                unset($list[$key]);
                continue 1;

            }*/
            $list[$key]['original_rank'] = $value['original_rank'] == 0 ? '101' : $value['original_rank'];
            $list[$key]['current_ranking'] = $value['current_ranking'] == 0 ? '101' : $value['current_ranking'];
            if ($list[$key]['price'] > 0) {
                $list[$key]['price'] = $list[$key]['price'];

            } else {
                if ($settlement_type == 2 && $value['fee'] > 0) {
                    $list[$key]['price'] = $list[$key]['fee'];

                } else {
                    $list[$key]['price'] = Db::field('group.keyword_free')->table(['seo_customer' => 'customer', 'seo_user_group' => 'group'])->where('group.id = customer.member_level')->where('customer.id= ' . $value['uid'])->value('keyword_free');

                }

            }
        }
        return json(['code' => 0, 'data' => $list]);

    }

    public function getStopLists(Request $request)
    {
        $uid = $request->uid;
        $keywords = $request->get('keywords');
        $webUrl = $request->get('weburl', '', 'string');
        $search_ngines = $request->get('search_ngines', '');
        $where = '1=1';
        if ($request->get('status')) {
            $where .= ' and a.status = ' . $request->get('status');

        } else {
            $where .= ' and a.status = 4';

        }
        if (!empty($keywords)) {
            $where .= " and (a.keywords like '%{$keywords}%' or a.web_url like '%{$keywords}%')";

        }
        if (!empty($search_ngines)) {
            $where .= ' and a.search_ngines = ' . $search_ngines;

        }
        $where .= ' and c.upid = ' . $uid;
        $limit = $request->get('limit', 30, 'int');
        $model = new \app\common\model\Keywords;
        $list = \app\common\model\Keywords::getList('*,a.id as id,a.status as status,a.create_time as create_time', $where, $limit);
        foreach ($list as $key => $value) {
            if ($list[$key]['price'] > 0) {
                $list[$key]['price'] = $list[$key]['price'];

            } else {
                $configmodel = new SystemConfig;
                $site_settlement = $configmodel->get(['name' => 'site_settlement']);
                $settlement_type = $site_settlement['value'];
                if ($settlement_type == 2 && $value['fee'] > 0) {
                    $list[$key]['price'] = $list[$key]['fee'];

                } else {
                    $list[$key]['price'] = Db::field('group.keyword_free')->table(['seo_customer' => 'customer', 'seo_user_group' => 'group'])->where('group.id = customer.member_level')->where('customer.id= ' . $value['uid'])->value('keyword_free');

                }

            }
        }
        return json(['code' => 0, 'data' => $list]);

    }

    public function add(Request $request)
    {
        $uid = $request->uid;
        $data['uid'] = $uid;
        $data['keywords'] = $request->post('keywords', '', 'string');
        $data['web_url'] = $request->post('web_url', '', 'string');
        $data['search_ngines'] = $request->post('search_ngines', 0, 'int');
        $data['status'] = 0;
        $data['original_rank'] = 0;
        $model = new \app\common\model\KeywordsRank();
        $postData['keywords'] = $data['keywords'];
        $postData['url'] = $data['web_url'];
        $postData['checkrow'] = 50;
        $res = Seo::addBaidupc($postData);
        if ($res) {
            if ($res['code'] == 0) {
                $data['taskid'] = $res['data']['taskid'];
                $res = $seo->getBaiduRank($data['taskid']);
                $rank = $res['rank'];
                if ($rank) {
                    $data['original_rank'] = $rank;

                }

            } else {
                return json($res);

            }

        }
        $res = $model->addTask($data);
        if ($res['code'] == 0) {
            $postData['id'] = $res['data']['id'];
            $postData['taskid'] = $data['taskid'];
            $postData['create_time'] = time();

        }
        return json($res);

    }

    public function del(Request $request)
    {

    }

    public function submit(Request $request)
    {
        $agent_uid = $request->uid;
        $id = $request->post('tid');
        $current_ranking = $request->post('current_ranking');
        $original_rank = $request->post('original_rank');
        $cycle = $request->post('cycle');
        $price = $request->post('price');
        $web_url = $request->post('web_url');
        $model = new \app\common\model\Keywords;
        $data['agent_status'] = 2;
        $data['current_ranking'] = $current_ranking;
        $data['original_rank'] = $original_rank;
        $data['cycle'] = $cycle;
        $data['price'] = $price;
        $data['web_url'] = $web_url;
        $data['xiongzhang'] = $request->post('xiongzhang');
        $data['start_time'] = time();
        $res = $model->alias('k')->join('customer c', 'c.id = k.uid')->where('c.upid', $agent_uid)->where('k.id', $id)->update($data);
        if ($res) {
            return ['code' => 0, 'msg' => '成功'];

        } else {
            return ['code' => 1, 'msg' => '失败'];

        }

    }

    public function examine(Request $request)
    {
        $agent_uid = $request->uid;
        $tid = $request->post('tid');
        if (is_array($tid)) {
            $isbatch = true;
            $tid = implode(',', $tid);

        } else {
            $tid = $tid;

        }
        $model = new \app\common\model\Keywords;
        if ($request->post('action') == 'refuse') {
            $data['k.status'] = 5;

        } else {
            $data['agent_status'] = 2;

        }
        $data['start_time'] = time();
        $res = $model->alias('k')->join('customer c', 'c.id = k.uid')->where('c.upid', $agent_uid)->where('k.id', 'in', $tid)->update($data);
        if ($res) {
            return ['code' => 0, 'msg' => '成功'];

        } else {
            return ['code' => 1, 'msg' => '失败'];

        }

    }

    public function submitstop(Request $request)
    {
        $id = $request->post('tid');
        $model = new \app\common\model\Keywords;
        $res = $model->where('id', $id)->update(['agent_status' => 4, 'complete_time' => time()]);
        if ($res) {
            return ['code' => 0, 'msg' => '成功'];

        } else {
            return ['code' => 1, 'msg' => '失败'];

        }

    }

    public function submitrefuse(Request $request)
    {
        $id = $request->post('tid');
        $model = new \app\common\model\Keywords;
        $res = $model->where('id', $id)->update(['status' => 5, 'start_time' => time()]);
        if ($res) {
            return ['code' => 0, 'msg' => '成功'];

        } else {
            return ['code' => 1, 'msg' => '失败'];

        }

    }
}