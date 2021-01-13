<?php

namespace app\common\model;

use common\Safety\Safetylogin;
use Firebase\JWT\JWT;
use think\Exception;
use think\Validate;

class AdminUser extends Base
{
    protected $pk = "uid";
    protected $createTime = "create_time";
    protected $updateTime = false;

    public function adminToken()
    {
        return $this->hasOne('AdminToken', 'uid', 'uid')->bind(['access_token', 'last_login_ip', 'last_login_time']);

    }

    public function order()
    {
        return $this->hasMany('Order', 'uid', 'uid');

    }

    public function orderDb()
    {
        return $this->hasMany('Order', 'uid', 'uid')->where('standard', '>', 0);

    }

    protected $autoWriteTimestamp = true;

    public function setPasswordAttr($value)
    {
        return password_hash($value, PASSWORD_DEFAULT);

    }

    public function setBalanceAttr($value)
    {
        return $value;

    }

    public function getLevAttr($value)
    {
        $arr = [1 => '普通会员', 2 => '高级会员'];
        return $arr[$value];

    }

    public function getUserInfo($uid, $field = 'username,mobile,role')
    {
        return $this->field($field)->find($uid);

    }

    public function doLogin($username, $pwd, $remember, $member_type = 1)
    {
        $username = trim($username);
        $password = trim($pwd);
        $validate = new \app\common\validate\AdminUser();
        if ($validate->scene('login')->check(['username' => $username, 'password' => $password]) != true) {
            return ['code' => 1, 'msg' => $validate->getError()];

        }
        $userInfo = self::get(['username' => $username], 'adminToken');
        if (empty($userInfo)) {
            return ['code' => 1, 'msg' => '用户名不存在'];

        }
        if ($userInfo->status != 1 && $username != 'admin') {
            return ['code' => 1, 'msg' => '账号被禁用，请联系管理员'];

        }
        if (!password_verify($password, $userInfo->password)) {
            return ['code' => 1, 'msg' => '登陆密码错误'];

        }
        $jwt_config = config('jwt.JWT_admin');
        $key = $jwt_config['salt'];
        $time = time();
        $request = \think\facade\Request::instance();
        $userIp = sprintf('%u', ip2long($request->ip()));
        $token = ['iss' => $jwt_config['iss'], 'aud' => $userInfo->uid, 'iat' => $time, 'nbf' => $time, 'data' => ['user_id' => $userInfo->uid, 'username' => $userInfo->username, 'user_ip' => $userIp, 'member_type' => $member_type]];
        $access_token = $token;
        $access_token['scopes'] = 'role_access';
        $access_token['exp'] = $time + 7200;
        $jsonList = ['access_token' => JWT::encode($access_token, $key, $jwt_config['alg']), 'token_type' => 'bearer'];
        try {
            if (empty($userInfo->access_token)) {
                AdminToken::create(['uid' => $userInfo->uid, 'access_token' => $jsonList['access_token'], 'last_login_ip' => $userIp]);

            } else {
                AdminToken::update(['access_token' => $jsonList['access_token'], 'last_login_ip' => $userIp], ['uid' => $userInfo->uid]);

            }
            return ['code' => 0, 'data' => $jsonList];

        } catch (\Exception $e) {
            return ['code' => 1, 'msg' => $e->getMessage()];

        }

    }

    public function logout($access_token, $UserData, $type = 'mysql')
    {
        $RedisLogin = new RedisLogin(config('admin_login_redis'));
        if (!$RedisLogin->logout($access_token, $type)) {
            Log::addLog(['id' => $UserData['id'], 'info' => $type . '退出登录失败：redis中没用数据'], 1);
            return ['code' => 1, 'msg' => '请稍后再尝试[utoo1]'];

        }
        if ($type == 'redis') {
            return ['code' => 0, 'msg' => '退出登录成功'];

        }
        $Token = Token::get(['login_access_token' => $access_token]);
        if (!$Token) {
            Log::addLog(['id' => $UserData['id'], 'info' => $type . '退出登录失败：mysql中没用数据'], 1);
            return ['code' => 1, 'msg' => '请稍后再尝试[utoo2]'];

        }
        $Token->login_access_token_time = date('Y-m-d H:i:s', strtotime($Token->login_access_token_time) - config('JWT_admin.exp'));
        if (!$Token->save()) {
            Log::addLog(['id' => $UserData['id'], 'info' => $type . '退出登录失败：更新登录数据错误'], 1);
            return ['code' => 1, 'msg' => '请稍后再尝试[utoo3]'];

        }
        Log::addLog(['id' => $UserData['id'], 'info' => $type . '退出登录成功'], 0);
        return ['code' => 0, 'msg' => '退出登录成功'];

    }

    public function addUser($data, $scene = 'add')
    {
        $validate = new \app\common\validate\AdminUser();
        if (!$validate->scene($scene)->check($data)) {
            return ['code' => 1, 'msg' => $validate->getError()];

        }
        if (!$this->allowField(true)->save($data)) {
            return ['code' => 1, 'msg' => $this->getError()];

        }
        return ['code' => 0, 'msg' => '添加成功'];

    }

    public function editUser($data, $scene = 'edit')
    {
        if (empty($data['uid'])) {
            return ['code' => 1, 'msg' => '参数错误'];

        }
        $user = $this->get(intval($data['uid']));
        if (empty($user)) {
            return ['code' => 1, 'msg' => '用户不存在'];

        }
        $validate = new \app\common\validate\AdminUser();
        if (!$validate->scene($scene)->check($data)) {
            return ['code' => 1, 'msg' => $validate->getError()];

        }
        if (!$user->save($data)) {
            return ['code' => 1, 'msg' => $this->getError()];

        }
        return ['code' => 0, 'msg' => '编辑成功'];

    }

    public function setUserPassword($data, $scene = true)
    {
        if (empty($data['uid'])) {
            return ['code' => 1, 'msg' => '参数错误'];

        }
        $user = $this->get(intval($data['uid']));
        if (empty($user)) {
            return ['code' => 1, 'msg' => '用户不存在'];

        }
        if ($scene) {
            if (empty($data['ys_passwrod'])) {
                return ['code' => 1, 'msg' => '原来密码不能为空'];

            }

        }
        $validate = new \app\common\validate\AdminUser();
        if (!$validate->scene('password')->check($data)) {
            return ['code' => 1, 'msg' => $validate->getError()];

        }
        if ($user->password == password_hash($data['password'], 1)) {
            return ['code' => 1, 'msg' => '修改的密码和以前的密码一样'];

        }
        $user->password = $data['password'];
        if (!$user->save()) {
            return ['code' => 1, 'msg' => '修改失败'];

        }
        return ['code' => 0, 'msg' => '修改成功'];

    }

    public function editUserBalance($uid, $free, $type = 0)
    {
        $free = floatval(sprintf('%.2f', $free));
        if ($free == 0) {
            return ['code' => 1, 'msg' => '修改金额未改变'];

        }
        $changType = $free > 0 ? 1 : 2;
        $user = $this->find(intval($uid));
        if (empty($user)) {
            return ['code' => 1, 'msg' => '用户不存在'];

        }
        $user->balance = floatval($user->balance) + $free;
        $this->startTrans();
        try {
            $user->save();
            $mingxin = new Mingxi();
            $mingxin->save(['uid' => $uid, 'type' => $type, 'change_type' => $changType, 'free' => $free, 'url' => '', 'keywords' => '', 'remarks' => '', 'create_time' => time()]);
            $this->commit();
            return ['code' => 0, 'msg' => '修改成功'];

        } catch (Exception $e) {
            $this->rollback();
            return ['code' => 1, 'msg' => '修改失败'];

        }

    }
}