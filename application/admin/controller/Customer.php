<?php

namespace app\admin\controller;

use think\Request;
use app\common\model\Customer as User;
use app\common\model\Keywords;

class Customer extends Base
{
    public function getCustomerList(Request $request)
    {
        $action = $request->get('action', '', 'string');
        $where = [];
        $limit = input('limit', 20, 'int');
        $username = $request->get('username', '', 'string');
        $contacts = $request->get('contacts', '', 'string');
        $agentid = $request->get('agentid');
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
        if (!empty($agentid)) {
            $where['upid'] = $agentid;
            $agentWhere['id'] = $agentid;

        }
        $customer = new User;
        if ($action == 'agent') {
            $where['isagent'] = 1;

        } else {
            $where['isagent'] = 0;
            $agentWhere['isagent'] = 1;
            $agentArray = $customer->where($agentWhere)->select()->toArray();
            foreach ($agentArray as $key => $value) {
                $tempArray[$value['id']] = $value;

            }

        }
        $data = $customer->getListByPage($where, $limit)->toArray();
        foreach ($data['data'] as $key => $value) {
            $data['data'][$key]['last_login_time'] = date('Y-m-d H:i:s', $value['last_login_time']);
            $data['data'][$key]['create_time'] = date('Y-m-d', $value['last_login_time']);
            if ($data['data'][$key]['is_domain'] == 1)
            {
                $data['data'][$key]['is_domain'] = "是";
            }
            else
            {
                $data['data'][$key]['is_domain'] = "否";
            }

            if ($action != 'agent') {
                $data['data'][$key]['agent_name'] = isset($tempArray[$value['upid']]) ? $tempArray[$value['upid']]['username'] : '';

            }

        }
        return json(['code' => 0, 'data' => $data]);

    }

    public function addCustomer(Request $request)
    {
        $data = $request->post();
        $customer = new User;
        $data['isagent'] = 1;
        return json($customer->addUser($data));

    }

    public function editCustomer(Request $request)
    {
        $data = $request->post();
        $customer = new User();
        return json($customer->editUser($data, 'editCustomer'));

    }

    public function delCustomer(Request $request)
    {
        $data = $request->post();
        if (Keywords::where('uid', $data['id'])->where('delete_time', 'NULL')->find() || User::where('upid', $data['id'])->where('delete_time', 'NULL')->find()) {
            return ['code' => 1, 'msg' => '当前用户下有关键词或用户，不能删除'];

        } else {
            $customer = new User();
            return json($customer->delUser($data, 'editCustomer'));

        }

    }

    public function editCustomerBalance(Request $request)
    {
        $balance = $request->post('balance');
        $admin_id = $request->uid;
        $uid = $request->post('uid');
        $customer = new User();
        return json($customer->editUserBalance($uid, $balance));

    }
}