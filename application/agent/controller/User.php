<?php

namespace app\agent\controller;

use app\common\model\Customer;
use app\common\model\AdminUser;
use think\Request;
use think\Db;

class User extends Base
{
    /**
     * 获取用户详情
     * @rule : /v1/get_user_info
     * @param Request $request
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUserInfo(Request $request){
        $info=Customer::find($request->uid);
        return self::result($info);
    }

    /**
     * 修改密码
     * @param Request $request
     * @return \think\response\Json
     * @throws \think\Exception\DbException
     */
    public function editPassword(Request $request){
        $uid = $request->uid;
        $oldPassword = $request->get('oldPassword','','string');
        $password = $request->get('password','','string');
        if(empty($password)){
            return json(['code'=>1,'msg'=>'密码不能为空']);
        }
        $user = Customer::find($request->uid);

        
        if(password_verify($oldPassword, $user->password))
        {
           $user->password = $password;
           if($user->save()){
                return json(['code'=>0,'msg'=>'修改成功']);
            }else{
                return json(['code'=>1,'msg'=>'修改失败']);
            }
        }   
        else
        {
            return json(['code'=>1,'msg'=>'原密码错误']);
        }

        
    }
    /**
     * 获取类型
     * @param Request $request
     * @return \think\response\Json
     * @throws \think\Exception\DbException
     */
    public function getSelect(Request $request){
        return json(['code'=>1,'data'=>db('user_group')->where('agent_id',$request->uid)->select()]);
    }
}