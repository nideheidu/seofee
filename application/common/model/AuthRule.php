<?php

namespace app\common\model;

use app\common\validate\Meun;
use common\tree\Tree;

class AuthRule extends Base
{
    protected $autoWriteTimestamp = true;
    protected $createTime = "create_time";

    public static function getMenuList($type = 'list')
    {
        $meunList = self::order('sort')->column('id,pid,title,icon,is_nav,status,url');
        dump($meunList);
        exit;
        switch ($type) {
            case 'list':
                break 1;
            case 'select':
                break 1;
            case 'tableList':
                break 1;
            default:
                $data = ['code' => 1, 'msg' => '参数错误~'];
                break 1;

        }
        return $data;

    }

    protected static function getTreeTable($data)
    {

    }

    public static function getTreeSelect($data)
    {

    }

    public static function add()
    {
        $id = input('post.id/d');
        $data['name'] = input('post.meun_name/s');
        $data['pid'] = input('post.pid/d');
        $data['title'] = input('post.meun_title/s');
        $data['icon'] = input('post.meun_icon/s');
        $data['is_nav'] = input('post.meun_nav/d');
        $data['sort'] = input('post.meun_sort/d');
        $data['url'] = input('post.meun_url/s');
        $data['status'] = input('post.status/d');
        $data['condition'] = input('post.meun_param/s');
        $validate = new Meun();
        if (empty($id)) {
            if (!$validate->scene('add')->check($data)) {
                return ['code' => 1, 'msg' => $validate->getError()];

            }
            if (self::create($data)) {
                return ['code' => 0, 'msg' => '添加成功！'];

            }

        } else {

        }

    }
}