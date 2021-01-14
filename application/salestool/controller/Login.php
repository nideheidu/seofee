<?php

namespace app\salestool\controller;

use app\common\model\Customer;
use app\common\model\CustomerInfo;
use think\Request;
use Firebase\JWT\JWT;

class Login extends Base
{
    public function doLogin(Request $request){
        $data = $request->param();
        $username = trim($data['username']);
        $password = trim($data['password']);

        $where = [
            'username' => $username,
            'status' => 0
        ];
        $name_err_msg = '用户名不存在';
        $userInfo = db('customer')
            ->where('username',$username)
            ->where('status',0)
            ->field('*')
            ->find();
        if (empty($userInfo)) {
            return json(['code' => 1, 'msg' => $name_err_msg]);
        }
        $userInfo = (object)$userInfo;
        $userIp = sprintf('%u', ip2long($request->ip()));
        if (!password_verify($password, $userInfo->password)) {
            if (!empty($userInfo->last_login_ip)) {
                $errData = [
                    'last_login_ip' => sprintf('%u', ip2long($request->ip())),
                    'login_error_count' => 1,
                    'last_login_time' => time()
                    ];
                $errData['login_error_count'] += $userInfo->login_error_count;
                $customerInfo = new CustomerInfo();
                $customerInfo->isUpdate(true)->save($errData, ['customer_id' => $userInfo->id]);
            } else {
                $customerInfo = new CustomerInfo;
                $customerInfo->customer_id = $userInfo->id;
                $customerInfo->last_login_ip = $userIp = sprintf('%u', ip2long($request->ip()));;
                $customerInfo->login_error_count = 1;
                $customerInfo->login_error_count_time = time();
                $customerInfo->save();

            }
            return json(['code' => 1, 'msg' => '登陆密码错误']);

        }
        $jwt_config = config('jwt.JWT_sales');
        $key = $jwt_config['salt'];
        $time = time();
        $token = [
            'iss' => $jwt_config['iss'],
            'aud' => $userInfo->id,
            'iat' => $time,
            'nbf' => $time,
            'data' => [
                'user_id' => $userInfo->id,
                'username' => $userInfo->username,
                'user_ip' => $userIp,
                'member_level' => $userInfo->member_level,
                'member_type' => $userInfo->isagent ? 2 : 0
            ]
        ];
        $access_token = $token;
        $access_token['scopes'] = 'role_access';
        $access_token['exp'] = $time + 7200;
        $jsonList = [
            'access_token' => JWT::encode($access_token, $key, $jwt_config['alg']),
            'token_type' => 'bearer',
            'role' => $userInfo->isagent,
            'username' => $userInfo->username
        ];
        try {
            if (!empty($userInfo->last_login_ip)) {
                $errData = ['token' => $jsonList['access_token'], 'login_error_count' => 0, 'last_login_ip' => $userIp, 'last_login_time' => time(),];
                $customerInfo = new CustomerInfo;
                $customerInfo->isUpdate(true)->save($errData, ['customer_id' => $userInfo->id]);

            } else {
                $customerInfo = new CustomerInfo;
                $customerInfo->customer_id = $userInfo->id;
                $customerInfo->last_login_ip = $userIp = sprintf('%u', ip2long($request->ip()));;
                $customerInfo->login_error_count = 0;
                $customerInfo->last_login_time = time();
                $customerInfo->token = $jsonList['access_token'];
                $customerInfo->save();

            }
            return json(['code' => 0, 'data' => $jsonList, 'usertype' => $userInfo->isagent]);

        } catch (\Exception $e) {
            return json(['code' => 1, 'msg' => $e->getMessage()]);

        }
    }

}