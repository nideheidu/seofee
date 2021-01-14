<?php

namespace app\api\controller;

use app\common\model\Customer;
use think\captcha\Captcha;
use think\helper\Hash;
use think\Request;
use app\http\middleware\Check;

class Login extends Base
{
    public function doLogin(Request $request)
    {
        $username = $request->post('username', '', 'string');
        $pwd = $request->post('password', '', 'string');
        $remember = $request->post('remember', '');
        $captcha = new Captcha();
        return json(Customer::doLogin($request->param()));

    }

    public function adminLogin(Request $request)
    {
        $token = $request->post('token', '', 'string');
        $id = $request->post('id', '', 'string');
        $userinfo = db('customer')->Where('id', $id)->find();
        $Check = new Check();
        $result = $Check->checkToken($token);
        if (!empty($result->data) && $userinfo['upid'] == $result->aud) {
            return json(Customer::doAdminLogin($userinfo));

        } else {
            return json(['code' => 1, 'msg' => '请先登录']);

        }

    }
}