<?php

namespace app\common\model;
class AdminToken extends Base
{
    protected $pk = "uid";
    protected $autoWriteTimestamp = true;
    protected $createTime = "last_login_time";
    protected $updateTime = "last_login_time";

    public static function getToken($token = '')
    {
        return self::where('access_token', $token)->value('uid');

    }
}