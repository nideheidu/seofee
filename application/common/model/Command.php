<?php

namespace app\common\model;

use think\model\concern\SoftDelete;
use Firebase\JWT\JWT;
use think\Exception;
use think\Db;
use think\helper\Hash;

class Command extends Base
{
    use SoftDelete;
    protected $autoWriteTimestamp = true;
    protected $createTime = "create_time";
    protected $updateTime = false;
    public $hidden = ["password"];

    public static function init()
    {

    }

    public function addCommand($data)
    {
        $commandData = json_decode($data['data'], 1);
        foreach ($commandData as $key => $value) {
            if (empty($value['key'])) {
                unset($commandData[$key]);

            }

        }
        $insertData['name'] = $data['title'];
        $insertData['sort'] = $data['sort'];
        $insertData['content'] = json_encode($commandData);
        if (!isset($data['id'])) {
            $insertData['create_time'] = time();

        }
        $this->startTrans();
        try {
            if (isset($data['id'])) {
                $this->where(['id' => $data['id']])->update($insertData);

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

    public function deleteCommand($id)
    {
        $where['id'] = $id;
        if ($this->where($where)->delete()) {
            return ['code' => 0, 'msg' => '删除成功'];

        };
        return ['code' => 1, 'msg' => '删除失败'];

    }

    public function getListByPage($where = [], $limit = 20)
    {
        return $this->where($where)->order('sort DESC')->paginate($limit);

    }
}