<?php


namespace app\salestool\controller;


use think\Request;

class User extends Base
{
    public function getInfo(Request $request){
        $userInfo = db('customer')
            ->where('id',$request->uid)
            ->where('status',0)
            ->field('*')
            ->find();
        if(empty($userInfo)){
            return json(['code' => 1, 'data' => '用户不存在']);
        }

        return json(['code' => 0, 'data' => $userInfo]);
    }
}