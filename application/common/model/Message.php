<?php

namespace app\common\model;
class Message extends Base
{
    public static function getMessage($uid = 0)
    {
        return self::whereOr('uid', $uid)->whereOr('fid', $uid)->order('id', 'desc')->limit20->select();

    }
}