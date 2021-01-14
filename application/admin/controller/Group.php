<?php

namespace app\admin\controller;

use app\common\model\UserGroup;
use app\common\model\Fee;
use think\Request;

class Group extends Base
{
    public function getList(Request $request)
    {
        $data = $request->get();
        if (isset($data['agentid'])) {
            $where = ['agent_id' => $data['agentid']];

        } else {
            $where = ['agent_id' => 0];

        }
        $limit = $request->post('limit', 100, 'int');
        $group = new UserGroup;
        $data = $group->getListByPage($where, $limit);
        return self::result($data);

    }

    public function add(Request $request)
    {
        $data = $request->post();
        $group = new UserGroup;
        return json($group->addGroup($data));

    }

    public function edit(Request $request)
    {
        $data = $request->post();
        $group = new UserGroup;
        return json($group->addGroup($data, 'edit'));

    }

    public function del(Request $request)
    {
        $data = $request->post();
        $id = $data['id'];
        $group = new UserGroup;
        return json($group->delGroup($id));

    }

    public function feelist(Request $request)
    {
        $where = ['ug.agent_id' => 0];
        $limit = $request->post('limit', 999, 'int');
        $Fee = new Fee;
        $fee_data = $Fee->getListByPage($where, $limit)->toArray();
        $where = ['agent_id' => 0];
        $limit = $request->post('limit', 999, 'int');
        $group = new UserGroup;
        $group_data = $group->getListByPage($where, $limit)->toArray();
        foreach ($fee_data['data'] as $key => $value) {
            $fee_data['data'][$key]['id'] = $value['id'] + 9999;
            $fee_data['data'][$key]['d_pid'] = $value['group_id'];

        }
        foreach ($group_data['data'] as $key2 => $value2) {
            $value2['d_pid'] = -1;
            $value2['group_id'] = $value2['id'];
            array_push($fee_data['data'], $value2);

        }
        return $fee_data;

    }

    public function feeadd(Request $request)
    {
        $data = $request->post();
        unset($data['id']);
        $Fee = new Fee;
        return json($Fee->addFee($data));

    }

    public function feedel(Request $request)
    {
        $data = $request->post();
        $data['id'] = $data['id'] - 9999;
        $id = $data['id'];
        $Fee = new Fee;
        return json($Fee->delFee($id));

    }

    public function feedit(Request $request)
    {
        $data = $request->post();
        $data['id'] = $data['id'] - 9999;
        $Fee = new Fee;
        return json($Fee->addFee($data, 'edit'));

    }

    public function getDefaultGroup()
    {
        $group = new UserGroup;
        $data = $group->getDefaultGroup();
        return self::result($data);

    }

    public function editReg(Request $request)
    {
        $data = $request->post();
        $group_id = $data['group_id'];
        if (!$group_id) {
            return ['code' => 1, 'msg' => '请选择会员组！'];

        }
        $group = new UserGroup;
        return json($group->setDefaultGroup($group_id));

    }
}