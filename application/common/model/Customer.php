<?php

namespace app\common\model;

use think\model\concern\SoftDelete;
use Firebase\JWT\JWT;
use think\Exception;
use think\Db;
use think\helper\Hash;

class Customer extends Base
{
    use SoftDelete;
    protected $pk = "id";
    protected $autoWriteTimestamp = true;
    protected $createTime = "create_time";
    protected $updateTime = false;
    public $hidden = ["password"];

    public static function init()
    {

    }

    public function setPasswordAttr($value)
    {
        return password_hash($value, PASSWORD_DEFAULT);

    }

    public function getMemberLevelAttr($value)
    {
        $arr = db('user_group')->where('id', $value)->find();
        return $arr['group_name'];

    }

    public function getMemberAgentid($uid)
    {
        $arr = $this->where('id', $uid)->find();
        return $arr['upid'];

    }

    public function customerInfo()
    {
        return $this->hasOne('CustomerInfo', 'customer_id', 'id')->bind(['token', 'last_login_time', 'last_login_ip']);

    }

    public function customerErrorLogin()
    {
        return $this->hasOne('CustomerInfo', 'customer_id', 'id')->bind(['login_error_count', 'login_error_count_time']);

    }

    public function orderReaching()
    {
        return $this->hasMany('Order', 'uid', 'id')->where('standard', '>', 0);

    }

    public function keywordsReaching()
    {
        return $this->hasMany('keywords', 'uid', 'id')->where('standard', '>', 0);

    }

    public function order()
    {
        return $this->hasMany('Order', 'uid', 'id');

    }

    public function keywords()
    {
        return $this->hasMany('keywords', 'uid', 'id');

    }

    public function customer()
    {
        return $this->hasMany('customer', 'id', 'upid');

    }

    public function weburl()
    {
        return $this->hasMany('web_url', 'uid', 'id');

    }

    public function total()
    {
        return $this->hasOne('CustomerAccount', 'uid', 'id')->bind(['total_sum', 'total_consumption']);

    }

    public static function doLogin($data, $isagent = false)
    {
        $username = trim($data['username']);
        $password = trim($data['password']);
        $validate = new \app\common\validate\AdminUser();
        if ($validate->scene('login')->check(['username' => $username, 'password' => $password]) != true) {
            return ['code' => 1, 'msg' => $validate->getError()];

        }
        $where = ['username' => $username];
        $name_err_msg = '用户名不存在';
        $userInfo = self::get($where, ['CustomerInfo', 'customerErrorLogin']);
        if (empty($userInfo)) {
            return ['code' => 1, 'msg' => $name_err_msg];

        }
        if ($userInfo->status != 1) {
            return ['code' => 1, 'msg' => '账号暂无法登陆，请联系管理员'];

        }
        $request = \think\facade\Request::instance();
        $userIp = sprintf('%u', ip2long($request->ip()));
        if (!password_verify($password, $userInfo->password)) {
            if (!empty($userInfo->last_login_ip)) {
                $errData = ['last_login_ip' => sprintf('%u', ip2long($request->ip())), 'login_error_count' => 1, 'last_login_time' => time(),];
                $errData['login_error_count'] += $userInfo->login_error_count;
                $customerInfo = new CustomerInfo;
                $customerInfo->isUpdate(true)->save($errData, ['customer_id' => $userInfo->id]);

            } else {
                $customerInfo = new CustomerInfo;
                $customerInfo->customer_id = $userInfo->id;
                $customerInfo->last_login_ip = $userIp = sprintf('%u', ip2long($request->ip()));;
                $customerInfo->login_error_count = 1;
                $customerInfo->login_error_count_time = time();
                $customerInfo->save();

            }
            return ['code' => 1, 'msg' => '登陆密码错误'];

        }
        $jwt_config = config('jwt.JWT_user');
        $key = $jwt_config['salt'];
        $time = time();
        $token = ['iss' => $jwt_config['iss'], 'aud' => $userInfo->id, 'iat' => $time, 'nbf' => $time, 'data' => ['user_id' => $userInfo->id, 'username' => $userInfo->username, 'user_ip' => $userIp, 'member_level' => $userInfo->member_level, 'member_type' => $userInfo->isagent ? 2 : 0]];
        $access_token = $token;
        $access_token['scopes'] = 'role_access';
        $access_token['exp'] = $time + 7200;
        $jsonList = ['access_token' => JWT::encode($access_token, $key, $jwt_config['alg']), 'token_type' => 'bearer', 'role' => $userInfo->isagent, 'username' => $userInfo->username,];
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
            return ['code' => 0, 'data' => $jsonList, 'usertype' => $userInfo->isagent];

        } catch (\Exception $e) {
            return ['code' => 1, 'msg' => $e->getMessage()];

        }

    }

    public static function doAdminLogin($data)
    {
        $username = trim($data['username']);
        $password = trim($data['password']);
        $validate = new \app\common\validate\AdminUser();
        $userInfo = self::get(['username' => $username], ['CustomerInfo', 'customerErrorLogin']);
        if (empty($userInfo)) {
            return ['code' => 1, 'msg' => '用户名不存在'];

        }
        if ($userInfo->status != 1) {
            return ['code' => 1, 'msg' => '账号被禁用，请联系管理员'];

        }
        $request = \think\facade\Request::instance();
        $userIp = sprintf('%u', ip2long($request->ip()));
        if ($password == $userInfo->password) {
            if (!empty($userInfo->last_login_ip)) {
                $errData = ['last_login_ip' => sprintf('%u', ip2long($request->ip())), 'login_error_count' => 1, 'last_login_time' => time(),];
                $errData['login_error_count'] += $userInfo->login_error_count;
                $customerInfo = new CustomerInfo;
                $customerInfo->isUpdate(true)->save($errData, ['customer_id' => $userInfo->id]);

            } else {
                $customerInfo = new CustomerInfo;
                $customerInfo->customer_id = $userInfo->id;
                $customerInfo->last_login_ip = $userIp = sprintf('%u', ip2long($request->ip()));;
                $customerInfo->login_error_count = 1;
                $customerInfo->login_error_count_time = time();
                $customerInfo->save();

            }

        } else {
            return ['code' => 1, 'msg' => '登陆密码错误'];

        }
        $jwt_config = config('jwt.JWT_user');
        $key = $jwt_config['salt'];
        $time = time();
        $token = ['iss' => $jwt_config['iss'], 'aud' => $userInfo->id, 'iat' => $time, 'nbf' => $time, 'data' => ['user_id' => $userInfo->id, 'username' => $userInfo->username, 'user_ip' => $userIp, 'member_level' => $userInfo->member_level, 'member_type' => $userInfo->isagent ? 2 : 3]];
        $access_token = $token;
        $access_token['scopes'] = 'role_access';
        $access_token['exp'] = $time + 7200;
        $jsonList = ['access_token' => JWT::encode($access_token, $key, $jwt_config['alg']), 'token_type' => 'bearer'];
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
            return ['code' => 0, 'data' => $jsonList];

        } catch (\Exception $e) {
            return ['code' => 1, 'msg' => $e->getMessage()];

        }

    }

    public function getListByPage($where = [], $limit = 20)
    {
        $data = $this->with(['customerInfo', 'total'])->withCount('order')->withCount('keywords')->withCount('keywordsReaching')->withCount('weburl')->where($where)->order('create_time DESC')->paginate($limit)->each(function ($value) {
            if ($value['keywords_reaching_count'] > 0) {
                $value['compliance_rate'] = round(($value['keywords_reaching_count'] / $value['keywords_count']) * 100, 2);

            } else {
                $value['compliance_rate'] = 0;

            }
            unset($value['customer_info']);
            unset($value['orderReaching_count']);
        });
        foreach ($data as $key => $value) {
            $data[$key]['todaysum'] = Mingxi::where('create_time >' . strtotime(date('Y-m-d', strtotime('-1 day'))) . ' and create_time < ' . strtotime(date('Y-m-d')))->where('change_type', 2)->where('uid', $value['id'])->sum('free');
            $data[$key]['customer_count'] = $this->where(array('upid' => $value['id']))->count();
            $data[$key]['todaysum'] = abs($data[$key]['todaysum']);

        }
        return $data;

    }

    public function addUser($data, $scene = 'addCustomer')
    {
        if (empty($data['password'])) {
            $data['password'] = 123456;

        }
        $validate = new \app\common\validate\Customer();
        if (!$validate->scene($scene)->check($data)) {
            return ['code' => 1, 'msg' => $validate->getError()];

        }
        $this->startTrans();
        try {
            $this->allowField(true)->save($data);
            $uid = $this->id;
            $data = ['uid' => $uid,];
            CustomerAccount::add($data);
            $this->commit();
            return ['code' => 0, 'msg' => '添加成功'];

        } catch (Exception $e) {
            $this->rollback();
            return ['code' => 1, 'msg' => '添加失败'];

        }

    }

    public function editUser($data, $scene = 'editCustomer', $agent_id = '')
    {
        if (empty($data['id'])) {
            return ['code' => 1, 'msg' => '参数错误'];

        }
        $where['id'] = intval($data['id']);
        if ($agent_id) {
            $where['upid'] = $agent_id;

        }
        $user = $this->get($where);
        if (empty($user)) {
            return ['code' => 1, 'msg' => '用户不存在'];

        }
        if ($agent_id) {
            $validate = new \app\common\validate\Customer();
            if (!$validate->scene($scene)->check($data)) {
                return ['code' => 1, 'msg' => $validate->getError()];

            }

        }
        if (!$user->save($data)) {
            return ['code' => 1, 'msg' => $this->getError()];

        }
        return ['code' => 0, 'msg' => '编辑成功'];

    }

    public function delUser($data, $scene = 'editCustomer', $agent_id = '', $del = false)
    {
        $id = $data['id'];
        if (empty($data['id'])) {
            return ['code' => 1, 'msg' => '参数错误'];

        }
        $where['id'] = intval($data['id']);
        if ($agent_id) {
            $where['upid'] = $agent_id;

        }
        return self::destroy(function ($query) use ($where, $id) {
            $query->where($where)->whereOr('upid', '=', intval($id));
        }, $del) ? ['code' => 0, 'msg' => '删除成功'] : ['code' => 1, 'msg' => '修改失败'];

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

    public function editUserBalance($uid, $free, $type = 2, $agent_id = 0)
    {
        $free = floatval(sprintf('%.2f', $free));
        if ($free == 0) {
            return ['code' => 1, 'msg' => '修改金额未改变'];

        }
        $changType = $free > 0 ? 1 : 2;
        $where['id'] = intval($uid);
        if ($agent_id) {
            $where['upid'] = $agent_id;

        }
        $user = $this->get($where);
        if (empty($user)) {
            return ['code' => 1, 'msg' => '用户不存在'];

        }
        $user->total->total_sum = floatval($user->total->total_sum) + $free;
        $this->startTrans();
        try {
            if ($type == 2) {
                if ($agent_id) {
//                    $remarks = '代理商调整用户余额';
                    $remarks = '管理员调整用户余额';
                } else {
                    $remarks = '平台管理员调整用户余额';

                }

            }
            $user->total->save();
            $mingxin = new Mingxi();
            $mingxin_data = ['uid' => $uid, 'type' => $type, 'change_type' => $changType, 'free' => $free, 'url' => '', 'keywords' => '', 'remarks' => $remarks, 'create_time' => time()];
            if ($agent_id) {
                $mingxin_data['agent_id'] = $agent_id;

            }
            $mingxin->save($mingxin_data);
            $this->commit();
            return ['code' => 0, 'msg' => '修改成功'];

        } catch (Exception $e) {
            $this->rollback();
            return ['code' => 1, 'msg' => '修改失败'];

        }

    }

    public function editUserBalanceDetail($uid, $free, $url, $keywords, $remarks, $type = 0, $kid, $time = 0)
    {
        $free = floatval(sprintf('%.2f', $free));
        if ($free == 0) {

        }
        $changType = $free > 0 ? 1 : 2;
        $user = $this->find(intval($uid));
        if (empty($user)) {
            return ['code' => 1, 'msg' => '用户不存在'];

        }
        $user->total->total_sum = floatval($user->total->total_sum) + $free;
        $this->startTrans();
        try {
            $user->total->save();
            $mingxin = new Mingxi();
            $mingxin->save(['uid' => $uid, 'kid' => $kid, 'type' => $type, 'change_type' => $changType, 'free' => $free, 'url' => $url, 'keywords' => $keywords, 'remarks' => $remarks, 'create_time' => $time ? $time : time()]);
            $this->commit();
            return ['code' => 0, 'msg' => '修改成功'];

        } catch (Exception $e) {
            $this->rollback();
            return ['code' => 1, 'msg' => $e->getMessage()];

        }

    }

    public function getUserBalanceToday($uid, $keywords, $url, $kid = 0)
    {
        $mingxin = new Mingxi();
        $list = $mingxin->where('uid', $uid)->where('kid', $kid)->where('create_time >' . strtotime(date('Y-m-d')) . ' and create_time < ' . strtotime(date('Y-m-d', strtotime('+1 day'))))->find();
        if (!empty($list)) {
            return true;

        } else {
            return false;

        }

    }

    public function checkUser($agent_id, $uid)
    {
        $arr = $this->where('id', $uid)->where('upid', $agent_id)->find();
        return $arr ? true : false;

    }
}