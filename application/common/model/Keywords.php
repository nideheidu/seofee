<?php

namespace app\common\model;

use think\model\concern\SoftDelete;
use think\Db;
use think\facade\Config;
use app\common\model\CommandTemplate;

class Keywords extends Base
{
    use SoftDelete;
    protected $createTime = "create_time";
    protected $updateTime = false;
    protected $autoWriteTimestamp = true;

    public function TableWebUrl()
    {
        return $this->hasOne('WebUrl', 'id', 'web_id');

    }

    public function Order()
    {
        return $this->hasOne('Order', 'kid', 'id');

    }

    protected function getSearchNginesAttr($value)
    {
        $search_ngines = Config::get('app.search_ngines');
        return $search_ngines[$value];

    }

    public static function getTaskInfo($id = 0)
    {
        if (!$id) return false;
        return self::get($id);

    }

    public static function getWebIdAttr($value)
    {
        return db('WebUrl')->where('id', $value)->find();

    }

    public static function getKeywordsForTaskid($taskid)
    {
        $data = self::alias('k')->join('web_url w', 'k.web_url = w.url', 'LEFT')->field('*,k.xiongzhang as xiongzhangk')->where('k.task_number', $taskid)->find();
        if ($data['xiongzhangk']) {
            $data['xiongzhang'] = $data['xiongzhangk'];

        }
        return $data;

    }

    public static function getKeywordsForTaskid_xian($taskid)
    {
        $data = \db('Keywords')->alias('k')->join('web_url w', 'k.web_url = w.url','LEFT')->field('*,k.xiongzhang as xiongzhangk')->where('k.task_number', $taskid)->find();
//        $data = self::alias('k')->join('web_url w', 'k.web_url = w.url', 'LEFT')->field('*,k.xiongzhang as xiongzhangk')->where('k.task_number', $taskid)->find();
        if ($data['xiongzhangk']) {
            $data['xiongzhang'] = $data['xiongzhangk'];

        }
        return $data;

    }

    public static function getTaskListByPage($field = '', $where = [], $limit = 20)
    {
        return self::where($where)->order('create_time DESC')->paginate($limit);

    }

    public static function getList($field = '', $where = [], $limit = 20)
    {
        $field = $field . ',c.company_name as company_name';
        $list = self::alias('a')->join('customer c', 'c.id = a.uid')->join('customer_account ca', 'ca.uid = a.uid')->join('customer ac', 'ac.id = c.upid')->join('customer_account aca', 'aca.uid = ac.id')->field($field)->where($where)->order('a.id desc')->paginate($limit);
        foreach ($list as $key => $value) {
            $list[$key]['rank_time'] = date('Y-m-d H:i:s', $value['rank_time']);

        }
        return $list;

    }

    public static function getSimulation($field = '', $where = [], $limit = 20)
    {
        $kaywords = self::field($field)->where($where)->order('id desc')->paginate($limit);
        $simulationTemplate = CommandTemplate::orderRaw('rand()')->limit1->select();
        foreach ($kaywords as $key => $value) {
            $keywords = $value['keywords'];
            $url = 'http://' . $value['web_url'];
            $xiongzhang = $value['xiongzhang'];
            $content = str_replace('{$keywords}', $keywords, $simulationTemplate[0]['content']);
            $content = str_replace('{$url}', $url, $content);
            $content = str_replace('{$xiongzhang}', $xiongzhang, $content);
            $simulationTask[$key] = self::formartcommand($content);

        }
        return $simulationTask;

    }

    public static function findTask($field = '', $where = [])
    {
        $list = self::alias('a')->join('customer c', 'c.id = a.uid')->join('customer_account ca', 'ca.uid = a.uid')->field($field)->where($where)->order('a.id desc')->find();
        return $list;

    }

    public static function del($where = [], $del = false)
    {
        return self::destroy(function ($query) use ($where) {
            $query->where($where);
        }, $del);

    }

    public static function formartcommand($str)
    {
        $data = json_decode(json_decode($str, 1), 1);
        $restr = '';
        foreach ($data as $key => $value) {
            $str = '';
            foreach ($value as $k => $v) {
                if ($k != 0) {
                    $str .= ',';

                }
                $str .= '"' . $v['key'] . '":"' . $v['value'] . '"';

            }
            $restr .= '{' . $str . '}';

        }
        return $restr;

    }
}