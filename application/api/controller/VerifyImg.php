<?php

namespace app\api\controller;

use think\captcha\Captcha;
use think\Controller;

class VerifyImg extends Controller
{
    public function getVerifyImg()
    {
        $captcha = new Captcha();
        return $captcha->entry();

    }
}