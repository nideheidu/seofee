<?php

namespace app\common\validate;

use think\Validate;

class Meun extends Validate
{
    protected $rule = ["\x6E\x61\x6D\x65\x7C\xE8\x8F\x9C\xE5\x8D\x95\xE5\x90\x8D" => "\x72\x65\x71\x75\x69\x72\x65\x7C\x75\x6E\x69\x71\x75\x65\x3A\x61\x75\x74\x68\x5F\x72\x75\x6C\x65", "\x75\x72\x6C\x7C\xE8\x8F\x9C\xE5\x8D\x95\x75\x72\x6C" => "\x72\x65\x71\x75\x69\x72\x65", "\x74\x69\x74\x6C\x65\x7C\xE8\x8F\x9C\xE5\x8D\x95\xE6\xA0\x87\xE9\xA2\x98" => "\x72\x65\x71\x75\x69\x72\x65",];
    protected $message = ["\x6E\x61\x6D\x65\x2E\x75\x6E\x69\x71\x75\x65" => "\xE8\x8F\x9C\xE5\x8D\x95\xE5\x90\x8D\xE5\xB7\xB2\xE5\xAD\x98\xE5\x9C\xA8",];
    protected $scene = ["\x61\x64\x64" => ["\x6E\x61\x6D\x65", "\x75\x72\x6C", "\x74\x69\x74\x6C\x65"],];

}