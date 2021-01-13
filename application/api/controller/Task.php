<?php

namespace app\api\controller;

use app\common\model\Customer;
use app\common\model\CustomerAccount;
use app\common\model\Mingxi;
use app\common\model\Order;
use app\common\TaskService;
use think\Db;
use think\Request;
use seo\Seo;
use think\Queue;

class Task extends Base
{
    public function keywords_lists(Request $request)
    {
        $data = $request->param();
        $list = TaskService::keywords_lists($data, $request->uid);
        return self::result($list);

    }

    public function feedback_lists(Request $request)
    {
        $data = $request->param();
        $list = TaskService::feedback_lists($data, $request->uid);
        foreach ($list as $key => $value)
        {
            if ($value['status'] == 1)
            {
                $list[$key]['new_rank'] = '';
                $list[$key]['rank_time'] = '';
            }
        }
        return self::result($list);
    }

    public function add_keywords(Request $request)
    {
        $data = $request->param();
        $data['web_url'] = formartUrl($data['web_url']);
        if (empty($data['web_url'])) {
            return json(['code' => 1, 'msg' => '请输入网站']);

        }
        if (empty($data['keywords'])) {
            return json(['code' => 1, 'msg' => '请输入关键字']);

        }
        $info = TaskService::add_keywords($data, $request->uid);
        return json($info);

    }

    public function edit_keywords(Request $request)
    {
        $data = $request->param();
        $data['web_url'] = formartUrl($data['web_url']);
        if (empty($data['web_url'])) {
            return json(['code' => 1, 'msg' => '请输入网站']);

        }
        if (empty($data['keywords'])) {
            return json(['code' => 1, 'msg' => '请输入关键字']);

        }
        $info = TaskService::edit_keywords($data, $request->uid);
        return json($info);

    }

    public function del_keywords(Request $request)
    {
        $data = $request->param();
        $info = TaskService::del_keywords($data, $request->uid);
        return self::result($info);

    }

    public function back(Request $request)
    {
        $data = $request->param();
        $info = TaskService::back($data, $request->uid);
        return self::result($info);

    }

    public function stopback(Request $request)
    {
        $data = $request->param();
        $info = TaskService::stopback($data, $request->uid);
        return self::result($info);

    }

    public function keyword_changefee(Request $request)
    {
        $data = $request->param();
        $info = TaskService::keyword_changefee($data, $request->uid);
        return self::result($info);

    }

    public function keyword_inquiry(Request $request)
    {
        $data = $request->param();
        $info = TaskService::keyword_inquiry_price($data, $request->uid);
        return self::result($info);

    }

    public function inquiry_all(Request $request)
    {
        $data = $request->param();
        $info = TaskService::keyword_inquiry_price_all($data, $request->uid);
        return self::result($info);

    }

    public function order_lists(Request $request)
    {
        $data = $request->param();
        $res = TaskService::order_lists($data, $request->uid);
        return self::result($res);

    }

    public function order_add(Request $request)
    {
        $data = $request->param();
        $res = TaskService::order_add($data, $request->uid);
        return self::result($res);

    }

    public function keywordrank(Request $request)
    {
        $data = $request->param();
        $res = TaskService::keywords_rank_lists($data, $request->uid);
        return self::result($res);

    }

    public function add_keywordrank(Request $request)
    {
        $data = $request->param();
        $res = TaskService::add_keywordrank($data, $request->uid);
        return self::result($res);

    }

    public static function keyword_submit(Request $request)
    {
        $data = $request->param();
        $res = TaskService::keyword_submit($data, $request->uid);
        return json($res);

    }

    public static function keyword_batch_submit(Request $request)
    {
        $data = $request->param();
        foreach ($data['ids'] as $key => $value) {
            $res = TaskService::keyword_submit(array('tid' => $value), $request->uid);

        }
        return json($res);

    }

    public static function keyword_submit_new(Request $request)
    {
        $data = $request->param();
        $data['data']['web_url'] = formartUrl($data['data']['web_url']);
        $res = TaskService::keyword_submit_new($data);
        return json($res);

    }

    public static function keyword_delete(Request $request)
    {
        $data = $request->param();
        $data['isdel'] = 1;
        $res = TaskService::del_keywords($data, $request->uid);
        return json($res);

    }

    public function mingxi_lists(Request $request)
    {
        $data = $request->param();
        $res = TaskService::mingxi_lists($data, $request->uid);
        return self::result($res);

    }

    public function mingxi_add_lists(Request $request)
    {
        $data = $request->param();
        $res = TaskService::mingxi_add_lists($data, $request->uid);
        return self::result($res);

    }

    public function admin_mingxi_lists(Request $request)
    {
        $data = $request->param();
        $action = isset($data['action']) ? $data['action'] : '';
        if ($action == 3) {
            $res = TaskService::agent_mingxi_lists($data, $request->uid);

        } else {
            $isagent = $request->usertype == 2 ? true : false;
            $res = TaskService::admin_mingxi_lists($data, $request->uid, $isagent);

        }
        return self::result($res);
    }

    public function admin_recharge_lists(Request $request)
    {
        $data = $request->param();
        $action = isset($data['action']) ? $data['action'] : '';
        $isagent = $request->usertype == 2 ? true : false;
        if ($action == 2) {
            $res = TaskService::agent_recharge_lists($data, $request->uid, $isagent);
        } elseif ($action == 3) {
            $res = TaskService::agent_recharge_lists($data, $request->uid, $isagent);
        } else {
            $res = TaskService::admin_recharge_lists($data, $request->uid, $isagent);
        }
        return self::result($res);
    }

    public function admin_balance_lists(Request $request)
    {
        $data = $request->param();
        $limit = $data['limit']?$data['limit']:10;
        $where = 'c.upid != 0';
        if (isset($data['name']) && $data['name'])
        {
            $where .= " and c.username = '{$data['name']}'";
        }
        $sql = Customer::alias('c')->leftJoin('seo_customer_account a','c.id = a.uid')->leftJoin("(select s.uid,sum(s.free) recharge from seo_mingxi as s inner join seo_customer as c on s.uid = c.id where s.change_type = 1 and {$where} group by s.uid) r",'c.id = r.uid')->leftJoin("(select s.uid,sum(s.free) dayfree from seo_mingxi as s inner join seo_customer as c on s.uid = c.id where s.change_type = 2 and {$where} and s.create_time > ".strtotime(date('Y-m-d', strtotime('-1 day')))." and s.create_time < ".strtotime(date('Y-m-d'))." group by s.uid) d",'c.id = d.uid')->field("c.id,c.username,c.contacts,a.total_sum,r.recharge,d.dayfree,if(d.dayfree,CAST(a.total_sum/d.dayfree*-1 AS DECIMAL(10,2)),99999999) day")->where($where)->group('c.id')->buildSql();
        $list = Db::table($sql)->alias('customer')->field("id,username,contacts,total_sum,recharge,dayfree,day")->order($data['order'])->paginate($limit);
        foreach ($list as $key => $value)
        {
            $value['index'] = ($data['page']-1)*$data['limit']+$key+1;
            $list[$key] = $value;
        }
        return self::result($list);
    }

    public function getcounts(Request $request)
    {
        $res = TaskService::get_count($request->uid);
        return json($res);

    }

    public function keyword_stop(Request $request)
    {
        $id = $request->post('tid');
        $uid = $request->uid;
        $info = db('keywords')->alias('a')->join('customer c', 'c.id = a.uid')->where('a.uid', $uid)->where('a.id', $id)->find();
        if ($info) {
            if ($info['standard'] == 0 && (time() - $info['start_time']) < 3600 * 24 * 20) {
                return ['code' => 1, 'msg' => '关键词优化未满20天，不能停止'];

            }
            if ($info['standard'] > 0 && $info['standard'] < 30) {
                return ['code' => 1, 'msg' => '关键词达标未满30天，不能停止'];

            }
            $model = new \app\common\model\Keywords;
            $res = $model->where('id', $id)->update(['status' => 3, 'complete_time' => time()]);
            if ($res) {
                return ['code' => 0, 'msg' => '成功'];

            } else {
                return ['code' => 1, 'msg' => '失败'];

            }

        } else {
            return ['code' => 1, 'msg' => '失败'];

        }

    }

    public function add_keywords_quick(Request $request)
    {
        $data = $request->param();
        $data['web_url'] = formartUrl($data['web_url']);
        $res = TaskService::add_keywords_quick($data, $request->uid);
        return json($res);

    }

    public function update_keywords_rank(Request $request)
    {
        $data = $request->param();
        $res = $data['tid'] ? TaskService::update_keywords_rank($data, $request->uid) : TaskService::update_keywords_rank_bat($request->uid);
        return json($res);

    }

    public function changeFee(Request $request)
    {
        $data = $request->param();
        $res = TaskService::change_fee($data, $request->uid);
        return json($res);

    }

    public function get_rank_log(Request $request)
    {
        $data = $request->param();
        $id = $data['id'];
        $usertype = $request->usertype;
        if ($id) {
            return ['code' => 0, 'msg' => '成功', 'data' => TaskService::get_rank_log($id, $usertype)];

        } else {
            return ['code' => 1, 'msg' => '参数错误。'];

        };

    }

    public function feedback(Request $request)
    {
        $data = $request->param();
        $res = TaskService::feedback($data);
        return json($res);
    }
}