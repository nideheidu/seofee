<?php

namespace app\admin\controller;

use app\common\model\AdminUser;
use think\captcha\Captcha;
use think\Controller;
use think\Request;

class Login extends Controller
{
    public function login(Request $request)
    {
        $username = $request->post('username', '', 'string');
        $pwd = $request->post('password', '', 'string');
        $vercode = $request->post('vercode', '', 'string');
        $remember = $request->post('remember', '');
        if (empty($vercode)) {
            return json(['code' => 0, 'msg' => '请输入验证码']);

        }
        $captcha = new Captcha();
        $userModel = new AdminUser();
        return json($userModel->doLogin($username, $pwd, $remember, 3));

    }

    public function login_out()
    {

    }
}