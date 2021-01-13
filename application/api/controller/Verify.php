<?php

namespace app\api\controller;

use think\captcha\Captcha;

class Verify
{
    public function index()
    {
        $config = ['fontSize' => 30, 'length' => 4, 'useNoise' => false,];
        $captcha = new Captcha($config);
        return $captcha->entry();

    }
}