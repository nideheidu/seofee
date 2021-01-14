<?php

namespace app\common\model;
class Fee extends Base
{
    protected $pk = "id";
    protected $autoWriteTimestamp = true;
    protected $createTime = "create_time";

    public function addFee($data, $scene = 'add', $agent_id = 0)
    {
        if (empty($data['id'])) {
            unset($data['id']);
            if ($agent_id != 0) {
                $data['agent_id'] = $agent_id;

            }
            if ($this->allowField(true)->save($data)) {
                return ['code' => 0, 'msg' => '添加成功'];

            };

        } else {
            $where = ['id' => $data['id']];
            if ($agent_id) {
                $where['agent_id'] = $agent_id;

            }
            unset($data['id']);
            if ($this->isUpdate(true)->allowField(true)->save($data, $where)) {
                return ['code' => 0, 'msg' => '修改成功'];

            };

        }
        return ['code' => 1, 'msg' => '操作失败'];

    }

    public function getListByPage($where = [], $limit = 20)
    {
        $list = $this->alias('a')->join('user_group ug', 'a.group_id = ug.id')->where($where)->field('* ,a.id as id')->order('a.minnum asc')->paginate($limit);
        return $list;

    }

    public function delFee($id, $agent_id = 0)
    {
        $where['id'] = $id;
        if ($agent_id) {
            $where['agent_id'] = $agent_id;

        }
        if ($this->where($where)->delete()) {
            return ['code' => 0, 'msg' => '删除成功'];

        };
        return ['code' => 1, 'msg' => '删除失败'];

    }
}