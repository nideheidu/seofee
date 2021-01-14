<?php

namespace app\agent\controller;

use think\Request;
use app\common\model\Customer as User;
use app\common\model\Keywords;

class Customer extends Base
{
    public function getCustomerList(Request $request)
    {
        $where = [];
        $where['upid'] = $request->uid;
        $limit = input('limit', 20, 'int');
        $username = $request->get('username', '', 'string');
        $contacts = $request->get('contacts', '', 'string');
        $email = $request->get('email', '', 'string');
        if (!empty($username)) {
            $where['username'] = $username;

        }
        if (!empty($contacts)) {
            $where['contacts'] = $contacts;

        }
        if (!empty($email)) {
            $where['email'] = $email;

        }
        $customer = new User;
        $data = $customer->getListByPage($where, $limit)->toArray();
        foreach ($data['data'] as $key => $value) {
            $data['data'][$key]['last_login_time'] = date('Y-m-d H:i:s', $value['last_login_time']);

        }
        return json(['code' => 0, 'data' => $data]);

    }

    public function addCustomer(Request $request)
    {
        $data = $request->post();
        $customer = new User;
        $data['upid'] = $request->uid;
        return json($customer->addUser($data));

    }

    public function editCustomer(Request $request)
    {
        $agent_id = $request->uid;
        $data = $request->post();
        $customer = new User();
        return json($customer->editUser($data, 'editCustomer', $agent_id));

    }

    public function editCustomerBalance(Request $request)
    {
        $agent_id = $request->uid;
        $balance = $request->post('balance');
        $uid = $request->post('uid');
        $customer = new User();
        return json($customer->editUserBalance($uid, $balance, 2, $agent_id));

    }

    public function delCustomer(Request $request)
    {
        $agent_id = $request->uid;
        $data = $request->post();
        if (Keywords::where('uid', $data['id'])->where('delete_time', 'NULL')->find()) {
            return ['code' => 1, 'msg' => '当前用户下有关键词，不能删除'];

        } else {
            $customer = new User();
            return json($customer->delUser($data, 'editCustomer', $agent_id));

        }

    }
}