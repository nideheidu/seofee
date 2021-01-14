<?php

namespace app\common\model;

use think\facade\Config;

class RankLog extends Base
{
    protected $createTime = true;
    protected $autoWriteTimestamp = true;

    public static function addLog($taskid, $rank, $type, $action = 1)
    {
        $data['taskid'] = $taskid;
        $data['rank'] = $rank;
        $data['type'] = $type;
        $data['action'] = $action;
        if ($this->allowField(true)->save($data)) {
            return 1;

        } else {
            return 0;

        }

    }
}