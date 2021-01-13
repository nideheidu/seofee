<?php

namespace app\common\model;
class CustomerAccount extends Base
{
    public static function add($data)
    {
        return self::create($data);

    }
}