<?php

namespace app\api\controller;

use app\common\model\Customer;
use app\common\model\Order;
use app\common\TaskService;
use think\Request;
use seo\Seo;
use think\Queue;

class Simulation extends Base
{
    public function getTask(Request $request)
    {
        $data = $request->param();
        $list = \app\common\model\Keywords::getSimulation('*', '', 1);
        return json(['code' => 0, 'data' => $list]);

    }
}