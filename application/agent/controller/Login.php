<?php

namespace app\agent\controller;

use app\common\model\AdminUser;
use app\common\model\Customer;
use think\captcha\Captcha;
use think\Controller;
use think\Request;
use app\http\middleware\Check;

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
        return json(Customer::doLogin($request->param(), true));

    }

    public function adminLogin(Request $request)
    {
        $token = $request->post('token', '', 'string');
        $id = $request->post('id', '', 'string');
        $userinfo = db('customer')->Where('id', $id)->find();
        $Check = new Check();
        $result = $Check->checkToken($token);
        if (!empty($result->data) && $userinfo['isagent']) {
            return json(Customer::doAdminLogin($userinfo));

        } else {
            return json(['code' => 1, 'msg' => '请先登录']);

        }

    }

    public function login_out()
    {

    }
}