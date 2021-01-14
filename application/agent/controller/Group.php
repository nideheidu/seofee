<?php

namespace app\agent\controller;

use app\common\model\UserGroup;
use app\common\model\Fee;
use think\Request;

class Group extends Base
{
    public function getList(Request $request)
    {
        $uid = $request->uid;
        $where = ['agent_id' => $uid];
        $limit = $request->post('limit', 20, 'int');
        $group = new UserGroup;
        $data = $group->getListByPage($where, $limit);
        return self::result($data);

    }

    public function add(Request $request)
    {
        $agent_id = $request->uid;
        $data = $request->post();
        $group = new UserGroup;
        return json($group->addGroup($data, 'add', $agent_id));

    }

    public function edit(Request $request)
    {
        $agent_id = $request->uid;
        $data = $request->post();
        $group = new UserGroup;
        return json($group->addGroup($data, 'edit', $agent_id));

    }

    public function del(Request $request)
    {
        $agent_id = $request->uid;
        $data = $request->post();
        $id = $data['id'];
        $group = new UserGroup;
        return json($group->delGroup($id, $agent_id));

    }

    public function coderefresh(Request $request)
    {
        $agent_id = $request->uid;
        $data = $request->post();
        $id = $data['id'];
        $group = new UserGroup;
        return json($group->freshCode($id, $agent_id));

    }

    public function feelist(Request $request)
    {
        $uid = $request->uid;
        $where = ['ug.agent_id' => $uid];
        $limit = $request->post('limit', 20, 'int');
        $Fee = new Fee;
        $fee_data = $Fee->getListByPage($where, $limit)->toArray();
        $where = ['agent_id' => $uid];
        $limit = $request->post('limit', 20, 'int');
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
        $agent_id = $request->uid;
        $data = $request->post();
        unset($data['id']);
        $Fee = new Fee;
        return json($Fee->addFee($data, 'add', $agent_id));

    }

    public function feedit(Request $request)
    {
        $agent_id = $request->uid;
        $data = $request->post();
        $data['id'] = $data['id'] - 9999;
        $Fee = new Fee;
        return json($Fee->addFee($data, 'edit', $agent_id));

    }

    public function feedel(Request $request)
    {
        $agent_id = $request->uid;
        $data = $request->post();
        $data['id'] = $data['id'] - 9999;
        $id = $data['id'];
        $Fee = new Fee;
        return json($Fee->delFee($id, $agent_id));

    }
}