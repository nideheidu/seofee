<?php

namespace app\common\model;

use think\facade\Config;

class User extends Base
{
    protected $createTime = true;
    protected $updateTime = true;
    protected $autoWriteTimestamp = true;

    public static function checkAuth($token)
    {
        $uid = Config::get($token);
        if (false === $uid) {
            return false;

        } else {
            return $uid;

        }

    }

    public static function geUserData($id)
    {
        return self::where('id', $id)->find();

    }

    public static function geUserDataFd($id)
    {
        return self::where('id', $id)->value('fd');

    }
}