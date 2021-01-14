<?php

namespace app\admin\controller;

use app\common\TaskService;
use app\common\model\Keywords;
use app\common\model\Customer;
use seo\Seo;
use think\Db;
use think\Exception;
use think\Queue;
use think\Request;
use think\Validate;

class Task extends Base
{
    public function getTaskList(Request $request)
    {
        $uid = $request->uid;
        $where['uid'] = $uid;
        $status = $request->get('type', 0, 'int');
        $where['status'] = $status;
        $limit = $request->get('limit', 30, 'int');
        $keywords = new Keywords;
        $list = $keywords->where($where)->order('create_time DESC')->paginate($limit);
        return json(['code' => 0, 'data' => $list]);

    }

    public function addKeywrods(Request $request)
    {
        $data = $request->post();
        $data['uid'] = $request->uid;
        $validate = new Validate();
        $keywords = new Keywords;
        $count = $keywords->where(['uid' => $data['uid'], 'search_ngines' => $data['search_ngines'], 'keywords' => $data['keywords'], 'web_url' => $data['web_url']])->count();
        if ($count > 0) {
            return json(['code' => 1, 'msg' => '关键字与网站已添加']);

        }
        $res = Seo::addBaidupc(['keywords' => $data['keywords'], 'url' => $data['web_url']]);
        if (!$res) {
            return json(['code' => 1, 'msg' => '关键字排名查询失败~']);

        }
        if ($res['code'] != '0') {
            return json(['code' => 1, 'msg' => $res['msg']]);

        }
        $data['task_id'] = $res['data']['taskid'];
        $keywords->startTrans();
        try {
            $keywords->allowField(true)->save($data);
            $taskData = ['taskid' => $res['data']['taskid'], 'keywords' => $data['keywords'], 'url' => $data['web_url']];
            $keywords->getLastSql();
            $keywords->commit();
            return json(['code' => 0, 'msg' => '添加成功']);

        } catch (Exception $e) {
            $keywords->rollback();
            throw new \Exception($e->getMessage());

        }

    }

    public function delKeywords(Request $request)
    {
        $isDel = $request->post('isdel');
        if ($isDel == 1) {
            $del = true;

        } else {
            $del = false;

        }
        $ids = $request->post('ids/a');
        if (is_array($ids) && !empty($ids)) {
            $str = join(',', $ids);
            $where['id'] = ['in', $str];

        }
        $where['uid'] = $request->uid;
        if (Keywords::del($where, $del)) {
            return json(['code' => 0, 'msg' => '删除成功']);

        } else {
            return json(['code' => 1, 'msg' => '删除失败']);

        };

    }

    public function getcounts()
    {
        $beginThismonth = mktime(0, 0, 0, date('m'), 1, date('y'));
        $todayEnd = strtotime(date('Y-m-d 23:59:59', time()));
        $thismonthcount = ceil(abs(($beginThismonth - $todayEnd) / 86400));
        $date['thismonth'] = array();
        for ($i = 1;
             $i < $thismonthcount + 1;
             $i++) {
            $starttime = $beginThismonth + 3600 * 24 * ($i - 1);
            $endtime = $beginThismonth + 3600 * 24 * $i;
            $count = db('mingxi')->where('create_time >' . $starttime)->where('create_time <' . $endtime . ' and type=1')->count();
            array_push($date['thismonth'], $count);

        }
        $nextbeginThismonth = strtotime(date('Y-m-01', strtotime('-1 month')));
        $nextEnd = strtotime(date('Y-m-t', strtotime('-1 month')));
        $tnextmonthcount = ceil(abs(($nextbeginThismonth - $nextEnd) / 86400));
        $date['nextmonth'] = array();
        for ($i = 1;
             $i < $tnextmonthcount + 1;
             $i++) {
            $starttime = $nextbeginThismonth + 3600 * 24 * ($i - 1);
            $endtime = $nextbeginThismonth + 3600 * 24 * $i;
            $count = db('mingxi')->where('create_time >' . $starttime)->where('create_time <' . $endtime . ' and type=1')->count();
            array_push($date['nextmonth'], $count);

        }
        return $date;

    }

    public function update_keywords_rank(Request $request)
    {
        $data = $request->param();
        $res = TaskService::system_update_keywords_rank($data, 'admin');
        return json($res);

    }
    public function update_feedback_rank(Request $request)
    {
        $data = $request->param();
        $res = TaskService::admin_update_feedback_rank($data);
        return json($res);
    }

    public function settlement_keywords_rank(Request $request)
    {
        $agent_id = $request->uid;
        $data = $request->param();
        $res = TaskService::system_settlement_keywords_rank($data, 'admin', $agent_id);
        return json($res);

    }

    public function settlement_date_keywords_rank(Request $request)
    {
        $agent_id = $request->uid;
        $data = $request->param();
        $res = TaskService::system_date_settlement_keywords_rank($data, 'admin', $agent_id);
        return json($res);

    }

    public function stop_keywords(Request $request)
    {
        $data = $request->param();
        if ($data['tid']) {
            $res = TaskService::admin_stop_keywords($data);
            return json($res);

        } else {
            return json(['code' => 1, 'msg' => '未选择关键字']);

        }

    }

    //待处理反馈词排名处理
    public function process_feedback_rank(Request $request)
    {
        $data = $request->param();
        $res = TaskService::admin_process_feedback_rank($data);
        return json($res);
    }

    //待处理反馈词状态处理
    public function process_feedback_status(Request $request)
    {
        $data = $request->param();
        $res = TaskService::admin_process_feedback_status($data);
        return json($res);
    }

    public function admin_add_keywords(Request $request)
    {
        $data = $request->param();

        if (isset($data['wordinfo'])) //批量添加
        {
            $have_count = 0;
            $add_count = 0;
            $have_counted = '';
            foreach (explode("\n",$data['wordinfo']) as $wordinfo)
            {
                $words = explode(",",$wordinfo);
                if(count($words) != 5||empty($words[0])||empty($words[1])||empty($words[2])||empty($words[3])||$words[4]==''){
                    return json(['code' => 1, 'msg' => '添加失败，请按正确格式输入数据！']);
                }
                // 去掉url的前缀
                $web_url = preg_replace("/http\:\/\/|https\:\/\//",'',$words[2]);
                //去掉url尾部的 '/'
                //$web_url = rtrim($web_url,'/');  李娴2020-10-29内页连接可被添加进关键词表


                //根据用户名查uid
                $data['user_id'] = db('customer')->where('username', $words[0])->where('delete_time', 'null')->field('id')->find();
                $data['username'] = $words[0];
                $data['search_ngines'] = $words[3];
                $data['keywords'] = $words[1];
                $data['web_url'] = $web_url;
                $data['price'] = $words[4];
                $data['xiongzhang'] = '';
                $data['cycle'] = 1;

                if (empty($data['user_id']['id'])) {
                    return json(['code' => 1, 'msg' => '请输入用户名']);

                }
                if (empty($data['web_url'])) {
                    return json(['code' => 1, 'msg' => '请输入网站']);

                }
                if (empty($data['keywords'])) {
                    return json(['code' => 1, 'msg' => '请输入关键字']);

                }
                if($data['agent_id'] == -1){
                    //这个事添加到闲置词库
                    $data['price'] = sprintf("%.2f", $words[4]);
                    $info = TaskService::add_keywords_idle($data, 0, 2, 2,$have_count,$add_count); //添加关键词
                    $add_count = $info['count'];
                }else{
                    if($data['user_id']['id'] == 78)
                    {
                        return json(['code' => 1, 'msg' => '用户名与代理商不符']);
                    }
                    $info = TaskService::add_keywords_quick($data, $data['user_id']['id'], 2, 2,$add_count,$have_counted); //添加关键词
                    $add_count = $info['count'];
                    if (!empty($info['have_count']))
                    {
                        $have_counted = $info['have_count'];
                    }
                }
                if (!$info['code'] && $data['agent_id'] != -1)
                {
                    $model = new \app\common\model\Keywords;
                    $model->where('id', $info['id'])->update(['price'=>sprintf("%.2f", $words[4])]);
                }
            }
            return json($info);
        }

        if (empty($data['web_url'])) {
            return json(['code' => 1, 'msg' => '请输入网站']);

        }
        if (empty($data['keywords'])) {
            return json(['code' => 1, 'msg' => '请输入关键字']);

        }
        if (empty($data['user_id']['id'])) {
            return json(['code' => 1, 'msg' => '请输入用户名']);

        }
        $info = TaskService::add_keywords_quick($data, $data['user_id']['id'], 2, 2);
        return json($info);

    }

    public function admin_edit_keywords(Request $request)
    {
        $data = $request->param();
        if (isset($data['edittype']) && ($data['edittype'] == 'rank' || $data['edittype'] == 'operable'))
        {
            $counts = [];
            foreach (explode("\n",$data['wordinfo']) as $wordinfo)
            {
                $words = explode(",",$wordinfo);
                if ($data['edittype'] == 'rank') //批量修改排名
                {
                    $update_data['current_ranking'] = $words[3];
                    $update_data['rank_time'] = time();
                    //为修改词后可现实最新修改时间，默认将批量修改的词的 “可操作性” 为 “可操作”  即原不可操作词status=6，修改后默认为可操作status=2
                    $update_data['status'] = 2;
                }
                elseif ($data['edittype'] == 'operable') //批量修改可操作性
                {
                    if ($words[3] == -1) //设置为不需要更新排名
                    {
                        $update_data['status'] = 6;
                    }
                    elseif ($words[3] == 0) //设置为需要更新排名
                    {
                        $update_data['status'] = 2;
                    }
                    else
                    {
                        $counts[] = $wordinfo;
                        continue;
                    }
                }
                else
                {
                    $counts[] = $wordinfo;
                    continue;
                }
                if (Db::name("keywords")->where(['keywords'=>$words[0],'web_url'=>$words[1],'search_ngines'=>$words[2]])->update($update_data))
                {
                }
                else
                {
                    $counts[] = $wordinfo;
                }
            }
            return ['code' => 0, 'msg' => "操作成功！失败记录：".implode("<br>",$counts)];
        }

        if (empty($data['id'])) {
            return json(['code' => 1, 'msg' => '请选择关键词！']);
        }

        //批量修改价格
        if (isset($data['edittype']) && $data['edittype'] == 'price')
        {
            $update_data['price'] = $data['price'];
            if (Keywords::where(['id' => explode(",",$data['id'])])->update($update_data))
            {
                return ['code' => 0, 'msg' => '修改成功'];
            }
            return ['code' => 1, 'msg' => '修改失败'];
        }

        if (empty($data['keywords'])) {
            return json(['code' => 1, 'msg' => '请输入关键字']);
        }
        if (empty($data['user_id'])) {
            return json(['code' => 1, 'msg' => '请选择用户']);
        }
        if (empty($data['web_url'])) {
            return json(['code' => 1, 'msg' => '请输入网址']);
        }
        $keywords = new Keywords;
        $update_data['uid'] = $data['user_id'];
        $update_data['keywords'] = $data['keywords'];
        $update_data['web_url'] = $data['web_url'];
        $update_data['xiongzhang'] = $data['xiongzhang'];
        $update_data['original_rank'] = $data['original_rank'];
        $update_data['current_ranking'] = $data['current_ranking'];
        $update_data['price'] = $data['price'];
        $update_data['agent_price'] = $data['agent_price'];
        $update_data['cycle'] = $data['cycle'];
        if ($keywords->where(['id' => $data['id']])->update($update_data)) {
            return ['code' => 0, 'msg' => '修改成功'];

        }
        return ['code' => 1, 'msg' => '修改失败'];
    }

    //批量修改排名-反馈词
    public function admin_edit_feedback(Request $request)
    {
        $data = $request->param();
        $counts = [];
        foreach (explode("\n",$data['wordinfo']) as $wordinfo)
        {
            $words = explode(",",$wordinfo);
            if (count($words) != 4)
            {
                return ['code'=>1,'msg'=>'请按正确格式输入'];
            }
            $update_data_fb = [
                'new_rank' => $words[3],
                'refresh_time' => time()
            ];
            $res = \db('feedback')->where('web_url',$words[1])->where('keywords',$words[0])->where('search_ngines',$words[2])->where('status',1)->update($update_data_fb);
            if (!$res)
            {
                $counts[] = $wordinfo;
            }
        }
        return ['code' => 0, 'msg' => "操作成功！失败记录：".implode("<br>",$counts)];
    }

    public function get_user(Request $request)
    {
        return json(['code' => 1, 'msg' => '未选择关键字']);

    }

    public function test(Request $request)
    {
        echo $request->uid;

    }
}