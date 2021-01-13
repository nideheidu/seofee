<?php

namespace app\common\behavior;

use app\common\model\SystemConfig;

class Base
{
    public function run()
    {
        $config = SystemConfig::getConfigData();
        foreach ($config as $key => $value) {
            config($key, $value);

        }

    }
}