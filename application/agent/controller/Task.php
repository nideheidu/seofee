<?php

namespace app\agent\controller;

use app\common\TaskService;
use app\common\model\Keywords;
use app\common\model\Customer as User;
use seo\Seo;
use think\Exception;
use think\Queue;
use think\Request;
use think\Validate;

class Task extends Base
{
    public function getTaskList(Request $request)
    {
        $uid = $request->uid;
        $where['uid'] = $uid;
        $status = $request->get('type', 0, 'int');
        $where['status'] = $status;
        $limit = $request->get('limit', 30, 'int');
        $keywords = new Keywords;
        $list = $keywords->where($where)->order('create_time DESC')->paginate($limit);
        return json(['code' => 0, 'data' => $list]);

    }

    public function addKeywrods(Request $request)
    {
        $data = $request->post();
        $data['uid'] = $request->uid;
        $validate = new Validate();
        $keywords = new Keywords;
        $count = $keywords->where(['uid' => $data['uid'], 'search_ngines' => $data['search_ngines'], 'keywords' => $data['keywords'], 'web_url' => $data['web_url']])->count();
        if ($count > 0) {
            return json(['code' => 1, 'msg' => '关键字与网站已添加']);

        }
        $res = Seo::addBaidupc(['keywords' => $data['keywords'], 'url' => $data['web_url']]);
        if (!$res) {
            return json(['code' => 1, 'msg' => '管家字排名查询失败~']);

        }
        if ($res['code'] != '0') {
            return json(['code' => 1, 'msg' => $res['msg']]);

        }
        $data['task_id'] = $res['data']['taskid'];
        $keywords->startTrans();
        try {
            $keywords->allowField(true)->save($data);
            $taskData = ['taskid' => $res['data']['taskid'], 'keywords' => $data['keywords'], 'url' => $data['web_url']];
            $keywords->getLastSql();
            $keywords->commit();
            return json(['code' => 0, 'msg' => '添加成功']);

        } catch (Exception $e) {
            $keywords->rollback();
            throw new \Exception($e->getMessage());

        }

    }

    public function delKeywords(Request $request)
    {
        $isDel = $request->post('isdel');
        if ($isDel == 1) {
            $del = true;

        } else {
            $del = false;

        }
        $ids = $request->post('ids/a');
        if (is_array($ids) && !empty($ids)) {
            $str = join(',', $ids);
            $where['id'] = ['in', $str];

        }
        $where['uid'] = $request->uid;
        if (Keywords::del($where, $del)) {
            return json(['code' => 0, 'msg' => '删除成功']);

        } else {
            return json(['code' => 1, 'msg' => '删除失败']);

        };

    }

    public function getcounts(Request $request)
    {
        $uid = $request->uid;
        $beginThismonth = mktime(0, 0, 0, date('m'), 1, date('y'));
        $todayEnd = strtotime(date('Y-m-d 23:59:59', time()));
        $thismonthcount = ceil(abs(($beginThismonth - $todayEnd) / 86400));
        $date['thismonth'] = array();
        for ($i = 1;
             $i < $thismonthcount + 1;
             $i++) {
            $starttime = $beginThismonth + 3600 * 24 * ($i - 1);
            $endtime = $beginThismonth + 3600 * 24 * $i;
            $count = db('mingxi')->alias('a')->join('customer c', 'c.id = a.uid')->where('a.uid =' . $uid)->where('a.create_time >' . $starttime)->where('a.create_time <' . $endtime . ' and type=3')->count();
            array_push($date['thismonth'], $count);

        }
        $nextbeginThismonth = strtotime(date('Y-m-01', strtotime('-1 month')));
        $nextEnd = strtotime(date('Y-m-t', strtotime('-1 month')));
        $tnextmonthcount = ceil(abs(($nextbeginThismonth - $nextEnd) / 86400));
        $date['nextmonth'] = array();
        for ($i = 1;
             $i < $tnextmonthcount + 1;
             $i++) {
            $starttime = $nextbeginThismonth + 3600 * 24 * ($i - 1);
            $endtime = $nextbeginThismonth + 3600 * 24 * $i;
            $count = db('mingxi')->alias('a')->join('customer c', 'c.id = a.uid')->where('a.uid =' . $uid)->where('a.create_time >' . $starttime)->where('a.create_time <' . $endtime . ' and type=3')->count();
            array_push($date['nextmonth'], $count);

        }
        return $date;

    }

    public function update_keywords_rank(Request $request)
    {
        $data = $request->param();
        $res = TaskService::system_update_keywords_rank($data, 'admin');
        return json($res);

    }

    public function settlement_keywords_rank(Request $request)
    {
        $data = $request->param();
        $res = TaskService::system_settlement_keywords_rank($data, 'admin');
        return json($res);

    }

    public function stop_keywords(Request $request)
    {
        $uid = $request->uid;
        $data = $request->param();
        if ($data['tid']) {
            $res = TaskService::admin_stop_keywords($data, $uid, 3);
            return json($res);

        } else {
            return json(['code' => 1, 'msg' => '未选择关键字']);

        }

    }

    public function admin_add_keywords(Request $request)
    {
        $data = $request->param();
        $agent_id = $request->uid;
        $uid = $data['user_id'];
        $customer = new User;
        if (!$customer->checkUser($agent_id, $uid)) {
            return json(['code' => 1, 'msg' => '你没有权限给该会员添加关键词']);

        }
        if (empty($data['web_url'])) {
            return json(['code' => 1, 'msg' => '请输入网站']);

        }
        if (empty($data['keywords'])) {
            return json(['code' => 1, 'msg' => '请输入关键字']);

        }
        $info = TaskService::add_keywords_quick($data, $data['user_id'], 1, 1);
        return json($info);

    }

    public function test(Request $request)
    {
        echo $request->uid;

    }
}