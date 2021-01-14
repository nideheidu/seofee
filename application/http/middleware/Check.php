<?php

namespace app\http\middleware;

use Firebase\JWT\JWT;
use think\facade\Request;

class Check
{
    private static $msg;

    public function handle($request, \Closure $next)
    {
        $token = Request::header('access-token');
        if (empty($token)) {
            return json(['code' => 1001, 'msg' => '请先登录']);

        }
        $user = self::checkToken($token);
        if (!$user) {
            return json(['code' => 1001, 'msg' => self::$msg]);

        }
        if ($user->data->member_type <= 0) {
            return json(['code' => 1001, 'msg' => '权限错误']);

        }
        if (isset($user->data->member_type)) {
            $member_type = $user->data->member_type;

        } else {
            $member_type = '';

        }
        $request->uid = $user->data->user_id;
        $request->username = $user->data->username;
        $request->usertype = $member_type;
        return $next($request);

    }

    public function checkAuth()
    {

    }

    public static function checkToken($token = null)
    {
        if (empty($token)) {
            self::$msg = 'token is null';
            return false;

        }
        $jwt_config = config('jwt.JWT_admin');
        $key = $jwt_config['salt'];
        try {
            JWT::$leeway = 60;
            $decoded = JWT::decode($token, $key, ['HS256']);
            return $decoded;

        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            self::$msg = $e->getMessage();
            return false;

        } catch (\Firebase\JWT\BeforeValidException $e) {
            self::$msg = $e->getMessage();
            return false;

        } catch (\Firebase\JWT\ExpiredException $e) {
            self::$msg = $e->getMessage();
            return false;

        } catch (Exception $e) {
            self::$msg = $e->getMessage();
            return false;

        }

    }
}