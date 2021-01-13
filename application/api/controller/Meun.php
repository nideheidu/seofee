<?php

namespace app\api\controller;

use app\common\model\AuthRule;
use app\common\model\Keywords;
use function PHPSTORM_META\elementType;
use think\Request;

class Meun extends Base
{
    public function index(Request $request)
    {
        $uid = $request->uid;
        $type = $request->param('type', 'list');
        $meun = config('menu.customers_menu');
        $upid = db('customer')->field('upid')->where('id',$uid)->find();
        if ($upid['upid'] == 1)
        {
            unset($meun[1]['list'][1]);
        }
        $keywords = new Keywords;
        $tip = $keywords->where(['status' => 0, 'uid' => $uid,])->count();
        $auditing = $keywords->where(['status' => 1, 'uid' => $uid,])->count();
        $failed = $keywords->where(['status' => 3, 'uid' => $uid,])->count();
        foreach ($meun as $key => $value) {
            if (isset($value['list'])) {
                foreach ($value['list'] as $k => $v) {
                    if ($v['name'] == 'task-add' && $tip > 0) {
                        $meun[$key]['list'][$k]['tip'] = $tip;
                        $meun[$key]['tip'] = 1;

                    }
                    if ($v['name'] == 'task-list-2' && $auditing > 0) {
                        $meun[$key]['list'][$k]['tip'] = $auditing;
                        $meun[$key]['tip'] = 1;

                    }
                    if ($v['name'] == 'task-list-3' && $failed > 0) {
                        $meun[$key]['list'][$k]['tip'] = $failed;
                        $meun[$key]['tip'] = 1;

                    }

                }
            }
        }
        if ($type == 'list') {
            return json(['code' => 0, 'data' => $meun]);

        } else {
            return json(AuthRule::getMenuList($type));

        }

    }
}