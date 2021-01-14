<?php

namespace app\salestool\controller;

use app\common\model\AuthRule;
use app\common\model\Mingxi;
use app\common\model\Keywords;
use function PHPSTORM_META\elementType;
use think\Request;
use think\Db;

class Meun extends Base
{
    public function index(Request $request)
    {
        $type = $request->param('type', 'list');
        $meun = config('menu.servicetool_menu');
        if ($type == 'list') {
            return json(['code' => 0, 'data' => $meun]);

        } else {
            return json(AuthRule::getMenuList($type));

        }

    }

}