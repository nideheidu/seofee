<?php

namespace app\admin\controller;

use think\Request;
use think\Db;
use app\common\model\WebUrl as Web;

class WebUrl extends Base
{
    public function getWebUrlList(Request $request)
    {
        $where = [];
        $url = $request->get('url', '', 'string');
        $uid = $request->get('uid');
        if (!empty($url)) {
            $where[] = ['url', 'like', '%' . $url . '%'];

        }
        if (!empty($uid)) {
            $where[] = ['uid', '=', $uid];

        }
        $limit = $request->get('limit', 30, 'int');
        $web = new Web;
        $data = $web->getListByPage($where, $limit)->toArray();
        foreach ($data['data'] as $key => $value) {
            $list = db('keywords')->where('web_id', $value['id'])->select();
            $data['data'][$key]['keywordslists'] = array();
            foreach ($list as $k => $v) {
                array_push($data['data'][$key]['keywordslists'], $v['keywords']);

            }

        }
        return json(['code' => 0, 'data' => $data]);

    }

    public function updatetask(Request $request)
    {
        $id = $request->post('tid');
        $xiongzhang = $request->post('xiongzhang');
        $model = new \app\common\model\WebUrl;
        $data['xiongzhang'] = $xiongzhang;
        $res = $model->where('id', $id)->update($data);
        return ['code' => 0, 'msg' => '成功'];

    }
}