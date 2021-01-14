<?php

namespace app\admin\controller;

use app\common\model\AdminUser;
use think\Request;
use think\Db;

class User extends Base
{
    public function getUserInfo(Request $request)
    {
        $uid = $request->uid;
        $userModel = new AdminUser();
        return json(['code' => 0, 'data' => $userModel->getUserInfo($uid)]);

    }

    public function editPassword(Request $request)
    {
        $uid = $request->uid;
        $oldPassword = $request->get('oldPassword', '', 'string');
        $password = $request->get('password', '', 'string');
        if (empty($password)) {
            return json(['code' => 1, 'msg' => '密码不能为空']);

        }
        $user = AdminUser::find($request->uid);
        if (password_verify($oldPassword, $user->password)) {
            $user->password = $password;
            if ($user->save()) {
                return json(['code' => 0, 'msg' => '修改成功']);

            } else {
                return json(['code' => 1, 'msg' => '修改失败']);

            }

        } else {
            return json(['code' => 1, 'msg' => '原密码错误']);

        }

    }

    public function getSelect()
    {
        return json(['code' => 1, 'data' => db('user_group')->where('agent_id', '0')->select()]);

    }
}