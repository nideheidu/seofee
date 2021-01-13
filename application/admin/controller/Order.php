<?php

namespace app\admin\controller;

use app\common\model\Feedback;
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
        $id = $request->post('tid');
        $current_ranking = $request->post('current_ranking');
        $original_rank = $request->post('original_rank');
        $uid = $request->post('uid');
        $cycle = $request->post('cycle');
        $price = $request->post('price');
        $agent_price = $request->post('agent_price');
        $xiongzhang = $request->post('xiongzhang');
        $web_url = $request->post('web_url');
        $keywords = $request->post('keywords','');

        if($keywords){
            $data['keywords'] = $keywords;

            $delete_ids = 0;
            //如果编辑修改的链接，存在于闲置词库，那么将删除闲置词库的两端数据 2020-09-07 dumaolin\
            //例：id.m.shop.cnlist.org<=>id.shop.cnlist.org ; 此类冲突删除方法
            /**
             * 能修改的，1、替换的词是闲置词库的，2、1、替换的词是同一个用户的同一条仅有m区别的链接
            要删除的，闲置词库的
             */
            $url = db('keywords')->where('id', $id)->field('web_url')->select();
            if (strstr($web_url,'cnlist')||strstr($web_url,'qth58')||strstr($web_url,'liebiao')||strstr($web_url,'52bjw')||strstr($web_url,'jinanfa')) {
                if (strstr($web_url, 'm.')) {
                    $web_url_m = str_replace('.m', '', $web_url);
                } else {
                    if (strstr($web_url, '.shop')) {
                        $web_url_m = str_replace('.shop', '.shop.m', $web_url);
                    } elseif (strstr($web_url, '.b2b')) {
                        $web_url_m = str_replace('.b2b', '.b2b.m', $web_url);
                    }
                }
                $delete_ids = db('keywords')->where('web_url', $web_url)->whereOr('web_url', $web_url_m)->select();
            }
            if ($delete_ids)
            {
                foreach ($delete_ids as $del_id)
                {
                    if (is_null($del_id['delete_time']))
                    {
                        if ($url[0]['web_url'] == $web_url || $url[0]['web_url'] == $web_url_m)
                        {
                            continue;
                        }
                        else
                        {
                            return ['code' => 1, 'msg' => '链接与其他客户重复，不可修改'];
                        }
                    }
                    else
                    {
                        db('keywords')->where('id', $del_id['id'])->delete();
                    }
                }
            }
        }
        $model = new \app\common\model\Keywords;
        $data['current_ranking'] = $current_ranking;
        $data['original_rank'] = $original_rank;
        $data['cycle'] = $cycle;
        if ($uid) {
            $data['uid'] = $uid;

        }
        $data['price'] = $price;
        $data['agent_price'] = $agent_price;
        $data['xiongzhang'] = $xiongzhang;
        $data['web_url'] = $web_url;
        if ($current_ranking)
        {
            $data['rank_time'] = time();
        }
        $res = $model->where('id', $id)->update($data);
        //手动修改
//        $res = $model->isUpdate(true)->save($data,['id',$id]);

        if ($res) {
            return ['code' => 0, 'msg' => '成功'];

        } else {
            return ['code' => 1, 'msg' => '没有变化'];

        }

    }

    //手动修改反馈词排名
    public function updatefeedback_rank(Request $request)
    {
        $id = $request->post('id');
        $new_rank = $request->post('new_rank');
        $status = $request->post('status');
        $data['new_rank'] = $new_rank;
        $data['refresh_time'] = time();
        //手动修改处理状态
        if ($status)
        {
            $data['status'] = $status;
            $data['rank_time'] = time();
            if ($status == 2)
            {
                $data['ispay'] = 2;
            }
            elseif ($status == 3)
            {
                $data['ispay'] = 1;
            }
            else
            {
                $data['ispay'] = 0;
            }
            $feedback = db('feedback')->field('keywords,web_url,search_ngines,new_rank')->where('id',$id)->find();
            if ($feedback['new_rank'])
            {
                $data_k = [
                    'current_ranking' => $feedback['new_rank'],
                    'rank_time' => time()
                ];
                db('keywords')->where('keywords',$feedback['keywords'])->where('web_url',$feedback['web_url'])->where('search_ngines',$feedback['search_ngines'])->update($data_k);
            }
        }
        $res = db('feedback')->where('id',$id)->update($data);

        if ($res) {
            return ['code' => 0, 'msg' => '成功'];

        } else {
            return ['code' => 1, 'msg' => '没有变化'];

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
        $ids = $request->post('ids', '', 'string');
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