<?php

namespace app\agent\controller;

use app\common\model\Keywords;
use think\Exception;
use think\Request;

class Order extends Base
{
    public function getTaskOrderList(Request $request)
    {
        $where = [];
        $status = $request->get('shenhe_status', 0, 'int');
        $keyword = $request->get('keyword', '', 'string');
        $search_ngines = $request->get('search_ngines', '');
        $url = $request->get('url', '', 'string');
        $limit = $request->get('limit', 30, 'int');
        if ($status >= 0) {
            $where['shenhe_status'] = intval($status);

        } else {
            $where['shenhe_status'] = ['<>', '1'];

        }
        $where['status'] = 0;
        if (!empty($keyword) || !empty($search_ngines)) {
            $kModel = new Keywords;
            $map['status'] = 1;
            if (!empty($keyword)) {
                $map['keywords'] = $keyword;

            }
            if (!empty($search_ngines)) {
                $map['search_ngines'] = $search_ngines;

            }
            $ids = $kModel->where($map)->column('id');
            $where['kid'] = join(',', $ids);

        }
        if (!empty($url)) {
            $webMode = new \app\common\model\WebUrl;
            $weid = $webMode->where(['url' => $url])->value('id');
            $where['web_id'] = $weid;

        }
        $order = new \app\common\model\Order;
        $list = $order->getOrderListByPage($where, $limit);
        return json(['code' => 0, 'data' => $list]);

    }

    public function getOrderTasks(Request $request)
    {
        $where['shenhe_status'] = 1;
        $status = $request->get('status', 1, 'int');
        if ($status >= 0) {
            $where['status'] = intval($status);

        } else {
            $where['status'] = ['in', '1,2'];

        }
        $keyword = $request->get('keyword', '', 'string');
        $search_ngines = $request->get('search_ngines', '');
        $url = $request->get('url', '', 'string');
        if (!empty($keyword) || !empty($search_ngines)) {
            $kModel = new Keywords;
            $map['status'] = 1;
            if (!empty($keyword)) {
                $map['keywords'] = $keyword;

            }
            if (!empty($search_ngines)) {
                $map['search_ngines'] = $search_ngines;

            }
            $ids = $kModel->where($map)->column('id');
            $where['kid'] = join(',', $ids);

        }
        if (!empty($url)) {
            $webMode = new \app\common\model\WebUrl;
            $weid = $webMode->where(['url' => $url])->value('id');
            $where['web_id'] = $weid;

        }
        $limit = $request->get('limit', 30, 'int');
        $order = new \app\common\model\Order;
        $list = $order->getOrderListByPage($where, $limit);
        return json(['code' => 0, 'data' => $list]);

    }

    public function addTaskOrder($uid, $original_rank, $current_ranking, $type = 1)
    {
        $uid = $request->uid;
        $kid = $request->post('kid', 0, 'int');
        $orderData = ['kid' => $kid, 'uid' => $uid, 'order_number' => date('Ymd') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8), 'kid' => $tid, 'original_rank' => $original_rank, 'current_ranking' => 0, 'standard' => 0, 'type' => $type, 'create_time' => time()];
        $order = new \app\common\model\Order;
        $order->startTrans();
        try {
            $order->save($orderData);
            Keywords::update(['status' => 1], ['id' => $tid]);
            $order->commit();
            return json(['code' => 0, 'msg' => '提交成功']);

        } catch (Exception $e) {
            $order->rollback();
            return json(['code' => 1, 'msg' => '提交失败']);

        }

    }

    public function updatetask(Request $request)
    {
        $uid = $request->uid;
        $id = $request->post('tid');
        $current_ranking = $request->post('current_ranking');
        $original_rank = $request->post('original_rank');
        $cycle = $request->post('cycle');
        $price = $request->post('price');
        $model = new \app\common\model\Keywords;
        $where = 'a.id = ' . $id;
        $where .= ' and c.upid = ' . $uid;
        $keywords_info = $model->findTask('*,a.id as id,a.status as status', $where);
        if ($keywords_info) {
            $data['current_ranking'] = $current_ranking;
            $data['original_rank'] = $original_rank;
            $data['cycle'] = $cycle;
            $data['price'] = $price;
            $data['rank_time'] = time();
            $data['start_time'] = time();
            $res = $model->where('id', $id)->update($data);
            if ($res) {
                return ['code' => 0, 'msg' => '成功'];

            } else {
                return ['code' => 1, 'msg' => '失败'];

            }

        } else {
            return ['code' => 1, 'msg' => '失败'];

        }

    }

    public function getTaskList(Request $request)
    {

    }

    public function shenheOrder(Request $request)
    {
        $orderNumber = $request->post('order_number', '', 'string');
        $shenhe_status = $request->post('shenhe_status', 0, 'int');
        if (empty($orderNumber)) {
            return json(['code' => 1, 'msg' => '参数错误']);

        }
        if (!in_array($shenhe_status, [1, 2])) {
            return json(['code' => 1, 'msg' => '参数错误']);

        }
        $order = new \app\common\model\Order;
        return json($order->shenhe($orderNumber, $shenhe_status));

    }

    public function editStatus(Request $request)
    {
        $ids = $request->post('ids', '');
        if (!is_array($ids)) {
            $ids = [$ids];

        }
        $type = $request->post('type', '', 'string');
        if (empty($ids)) {
            return json(['code' => 1, '请选择数据']);

        }
        if (empty($type) || !in_array($type, ['stop', 'start'])) {
            return json(['code' => 1, '请选择操作类型']);

        }
        $arr['stop'] = 2;
        $arr['start'] = 1;
        $order = new \app\common\model\Order;
        return json($order->editStatus($ids, $arr[$type]));

    }

    public function editRanking()
    {
        $where['status'] = 1;
        $where['shenhe_status'] = 1;
        $order = new \app\common\model\Order;
        $list = $order->where($where)->whereTime('update_time', '<=', time() - 12 * 3600)->limit(100)->column('web_id');
        $web_id = array_unique($list);
        $web = new \app\common\model\WebUrl;
        $data = [];
        $PClisst = $web->with('keywordsPC')->where('id', 'in', join(',', $web_id))->select()->toArray();
        foreach ($PClisst as $key => $value) {
            $data[$key]['url'] = $value['url'];
            $keywords = array_column($value['keywords_p_c'], 'keywords');
            $data[$key]['keywords'] = join('|', $keywords);
            $data[$key]['search_status'] = 1;

        }
        $ModelList = $web->with('keywordsMobile')->where('id', 'in', join(',', $web_id))->select()->toArray();
        $data1 = [];
        foreach ($ModelList as $key => $value) {
            if (!empty($value['keywords_mobile'])) {
                $data1[$key]['url'] = $value['url'];
                $keywords = array_column($value['keywords_mobile'], 'keywords');
                $data1[$key]['keywords'] = join('|', $keywords);
                $data1[$key]['search_status'] = 1;

            }

        }
        $data = array_merge($data, $data1);
        foreach ($data as $key => $val) {

        }

    }
}