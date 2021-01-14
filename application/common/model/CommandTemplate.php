<?php

namespace app\common\model;

use think\model\concern\SoftDelete;
use Firebase\JWT\JWT;
use think\Exception;
use think\Db;
use think\helper\Hash;

class CommandTemplate extends Base
{
    use SoftDelete;
    protected $autoWriteTimestamp = true;
    protected $createTime = "create_time";
    protected $updateTime = false;

    public static function init()
    {

    }

    public function addTemplate($name, $data, $id = 0)
    {
        $insertData['name'] = $name;
        $insertData['content'] = json_encode($data['data']);
        if (!$id) {
            $insertData['create_time'] = time();

        }
        $this->startTrans();
        try {
            if ($id) {
                $this->where(['id' => $id])->update($insertData);

            } else {
                $this->save($insertData);

            }
            $this->commit();
            return ['code' => 0, 'msg' => '操作成功'];

        } catch (Exception $e) {
            $this->rollback();
            return ['code' => 1, 'msg' => '添加失败'];

        }

    }

    public function deleteTemplate($id)
    {
        $where['id'] = $id;
        if ($this->where($where)->delete()) {
            return ['code' => 0, 'msg' => '删除成功'];

        };
        return ['code' => 1, 'msg' => '删除失败'];

    }

    public function getListByPage($where = [], $limit = 20)
    {
        return $this->where($where)->order('create_time DESC')->paginate($limit);

    }
}