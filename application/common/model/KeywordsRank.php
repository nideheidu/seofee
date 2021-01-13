<?php

namespace app\common\model;

use seo\Seo;
use think\Queue;
use think\facade\Config;

class KeywordsRank extends Base
{
    protected $createTime = "create_time";
    protected $updateTime = false;
    protected $autoWriteTimestamp = true;

    protected function getSearchNginesAttr($value)
    {
        $search_ngines = Config::get('app.search_ngines');
        return $search_ngines[$value];

    }

    protected function getCompleteTimeAttr($value)
    {
        if (empty($value)) {
            return '';

        }
        return date('Y-m-d H:i:s', $value);

    }

    public function addTask($data = [])
    {
        if (empty($data['keywords']) || empty($data['web_url'])) {
            return ['code' => 1, 'msg' => '缺少关键参数'];

        }
        $this->save($data);
        $id = $this->id;
        if ($id) {
            return ['code' => 0, 'msg' => '添加成功', 'data' => ['id' => $id]];

        }
        return ['code' => 1, 'msg' => '添加失败'];

    }

    public static function getList($field = '', $where = [], $limit = 20)
    {
        return self::field($field)->where($where)->paginate($limit);

    }

    public static function del($ids)
    {
        return self::destroy($ids);

    }
}