<?php

namespace app\common\validate;

use think\Validate;

class Keywords extends Validate
{
    protected $rule = ["\x6B\x65\x79\x77\x6F\x72\x64\x73\x7C\xE5\x85\xB3\xE9\x94\xAE\xE5\xAD\x97" => "\x72\x65\x71\x75\x69\x72\x65",];
    protected $message = [];
    protected $scene = [];

}