<?php

namespace app\common\validate;

use think\Validate;

class Task extends Validate
{
    protected $rule = ["\x6B\x69\x64" => "\x72\x65\x71\x75\x69\x72\x65", "\x75\x69\x64" => "\x72\x65\x71\x75\x69\x72\x65", "\x74\x61\x73\x6B\x5F\x69\x64" => "\x72\x65\x71\x75\x69\x72\x65", "\x74\x79\x70\x65" => "\x72\x65\x71\x75\x69\x72\x65"];
    protected $message = [];
    protected $scene = [];

}