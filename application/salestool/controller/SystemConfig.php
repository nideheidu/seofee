<?php

namespace app\salestool\controller;

use think\Request;
use app\common\model\SystemConfig as System;
use app\common\model\Notice;
use app\common\model\NoticeLog;

class SystemConfig extends Base
{
    public function fnInit(Request $request)
    {
        $map = [];
        $map['status'] = 1;
        $map['agent_id'] = 999999;
        $map['group'] = 'base';
        $data_list = System::where($map)->whereOr('agent_id', $request->uid)->order('sort asc')->select();
        foreach ($data_list as $k => $v) {
            if ($v['name'] == 'site_name' && $v['agent_id'] != $request->uid) {
                continue 1;

            }
            $config[$v['name']] = $v['value'];

        }
        return json(['code' => 0, 'data' => $config]);

    }
}