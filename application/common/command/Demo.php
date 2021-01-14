<?php

namespace app\common\command;

use taskphp\Utils;

class Demo
{
    public static function run()
    {
        Utils::log('demo1任务运行成功');

    }
}