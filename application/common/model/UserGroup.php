<?php

namespace app\common\model;

use app\common\validate\Group;

class UserGroup extends Base
{
    protected $pk = "id";
    protected $autoWriteTimestamp = true;
    protected $createTime = "create_time";

    public function addGroup($data, $scene = 'add', $agent_id = 0)
    {
        $validate = new Group();
        $data['seo_free'] = $data['keyword_free'];
        if (!$validate->scene($scene)->check($data)) {
            return ['code' => 1, 'msg' => $validate->getError()];

        }
        if (empty($data['id'])) {
            unset($data['id']);
            $data['agent_id'] = $agent_id;
            if ($this->allowField(true)->save($data)) {
                return ['code' => 0, 'msg' => '添加成功'];

            };

        } else {
            $where['id'] = $data['id'];
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
        return $this->where($where)->order('create_time DESC')->paginate($limit);

    }

    public function delGroup($id, $agent_id = 0)
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

    public function freshCode($id, $agent_id = 0)
    {
        $where['id'] = $id;
        if ($agent_id) {
            $where['agent_id'] = $agent_id;

        }
        $updateData['code'] = $this->randCode(8, 2);
        if ($this->where($where)->update($updateData)) {
            return ['code' => 0, 'msg' => '更新成功'];

        };
        return ['code' => 1, 'msg' => '更新失败'];

    }

    public function getCodeInfo($code)
    {
        $info = $this->where('code', $code)->find()->toArray();
        return $info;

    }

    public function getDefaultGroup()
    {
        $info = $this->where('isdefault', '1')->find();
        if ($info) {
            $info = $info->toArray();

        }
        return $info;

    }

    public function setDefaultGroup($group_id)
    {
        $where['id'] = $group_id;
        $updateData['isdefault'] = 1;
        $this->where(['isdefault' => '1'])->update(['isdefault' => '0']);
        if ($this->where($where)->update(['isdefault' => '1'])) {
            return ['code' => 0, 'msg' => '更新成功'];

        };
        return ['code' => 1, 'msg' => '更新失败'];

    }

    private function randCode($length = 5, $type = 0)
    {
        $arr = array(1 => '0123456789', 2 => '0123456789abcdefghijklmnopqrstuvwxyz', 3 => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 4 => '~@#$%^&*(){}[]|');
        if ($type == 0) {
            array_pop($arr);
            $string = implode('', $arr);

        } elseif ($type == '-1') {
            $string = implode('', $arr);

        } else {
            $string = $arr[$type];

        }
        $count = strlen($string) - 1;
        $code = '';
        for ($i = 0;
             $i < $length;
             $i++) {
            $code .= $string[rand(0, $count)];

        }
        return $code;

    }
}