<?php

namespace app\common\model;
class Task extends Base
{
    protected $createTime = "create_time";
    protected $updateTime = false;
    protected $autoWriteTimestamp = true;

    public static function addTask($data = [])
    {
        if (empty($data)) {
            return false;

        }
        return self::save($data);

    }
}