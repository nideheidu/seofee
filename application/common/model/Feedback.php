<?php


namespace app\common\model;


use think\model\concern\SoftDelete;

class Feedback extends Base
{
    use SoftDelete;
    protected $createTime = "create_time";
    protected $updateTime = false;
    protected $autoWriteTimestamp = true;

    public static function getList($field = '', $where = [], $limit = 20)
    {
        $list = self::alias('a')->field($field)->where($where)->order('a.id desc')->paginate($limit);
        foreach ($list as $key => $value) {
            if ($value['rank_time'])
            {
                $list[$key]['rank_time'] = date('Y-m-d H:i:s', $value['rank_time']);
            }
            if ($value['refresh_time'])
            {
                $list[$key]['refresh_time'] = date('Y-m-d H:i:s', $value['refresh_time']);
            }
            switch ($value['search_ngines']) {
                case 1:
                    $search_ngines = "百度PC";
                    break;
                case 2:
                    $search_ngines = "百度移动";
                    break;
                case 3:
                    $search_ngines = "360PC";
                    break;
                case 4:
                    $search_ngines = "360移动";
                    break;
                case 5:
                    $search_ngines = "搜狗PC";
                    break;
                case 6:
                    $search_ngines = "搜狗移动";
                    break;
                default:
                    $search_ngines = "百度PC";
                    break;
            }
            $list[$key]['search_ngines'] = $search_ngines;
        }
        return $list;

    }

    public static function del($where = [], $del = false)
    {
        return self::destroy(function ($query) use ($where) {
            $query->where($where);
        }, $del);

    }
}