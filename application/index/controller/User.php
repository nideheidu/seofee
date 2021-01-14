<?php

namespace app\index\controller;

use think\Controller;
use think\Request;

class User extends Controller
{
    public function reg()
    {
        return $this->fetch();

    }

    public function login()
    {
        return $this->fetch();

    }

    public function doReg(Request $request)
    {

    }

    public function doLogin()
    {

    }
}