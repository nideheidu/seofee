<?php

namespace app\common\model;
class UserToken extends Base
{
    public function saveToken($uid = '')
    {
        $data = $this->where('uid', $uid)->value('token');
        if (!$data) {
            $token = sha1(md5(time()) . $uid);
            $this->save(['uid' => $uid, 'token' => $token]);
            return $token;

        }
        return $data;

    }

    public static function getToken($token = '')
    {
        return self::where('token', $token)->value('uid');

    }
}