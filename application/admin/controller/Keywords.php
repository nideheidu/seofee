<?php

namespace app\admin\controller;

use app\common\model\Feedback;
use seo\Seo;
use think\Queue;
use think\Request;
use app\common\model\KeywordsRank;
use app\common\model\SystemConfig;
use app\common\model\Customer;
use app\common\model\Mingxi;
use app\common\TaskService;
use think\Db;

class Keywords extends Base
{
    public function getLists(Request $request)
    {
        $type = $request->get('type');
        $uid = $request->get('uid');
        $keywords = $request->get('keywords');
        $webUrl = $request->get('weburl', '', 'string');
        $search_ngines = $request->get('search_ngines', '');
        $limit = $request->get('limit', 30, 'int');
        $status = $request->get('status');
        if ($type == 5)
        {
            $status = 2;
        }
        elseif ($type == 6)
        {
            $status = 6;
        }
        $where = '1=1';
        if ($status) {
            if ($status == 1)
            {
                $where .= ' and a.status = ' . $status . ' and agent_status = 2';
            }
            else
            {
                $where .= ' and a.status in (' . $status . ')';
            }

        } else {
            if ($uid) {
                $where .= ' and a.uid = ' . $uid;

            } else {
                $where .= ' and a.status = 3';

            }

        }
        if ($type == 1) {
//            $limit = 999;
//            $where .= ' and a.standard = 1';
            $where .= " and a.standard=0 and a.current_ranking <= 10 and date_format(from_unixtime(a.rank_time),'%Y-%m-%d') = date_format(now(),'%Y-%m-%d')";
        }
        elseif ($type == 2)
        {
            $where .= " and a.current_ranking <= 10 and a.current_ranking > 0";
        }
        elseif ($type == 3)
        {
            $where .= " and (a.current_ranking > 10 or a.current_ranking = 0)";
        }
        if (!empty($keywords)) {
            $where .= " and (a.keywords like '%{$keywords}%' or a.web_url like '%{$keywords}%' or c.username like '%{$keywords}%' or c.contacts like '%{$keywords}%' or c.company_name like '%{$keywords}%')";

        }
        if (!empty($search_ngines)) {
            $where .= ' and a.search_ngines = ' . $search_ngines;

        }
        $model = new \app\common\model\Keywords;
        $list = \app\common\model\Keywords::getList('*,a.id as id,a.status as status,ac.username as a_username,aca.total_sum as a_total_sum,a.create_time as create_time,a.search_ngines as search_ngine_id ,c.contacts as contacts,c.id as uid,ac.id as agent_id,c.username as username ,ac.username as agent_username', $where, $limit);
        foreach ($list as $key => $value) {
            /*if ($type == 1 && !Mingxi::where(['kid' => $value['id']])->where('create_time < ' . time())->where('create_time >' . strtotime(date('Y-m-d')))->find()) {
                unset($list[$key]);
                continue 1;

            }*/
            $list[$key]['original_rank'] = $value['original_rank'] == 0 ? '101' : $value['original_rank'];
            $list[$key]['current_ranking'] = $value['current_ranking'] == 0 ? '101' : $value['current_ranking'];
            $list[$key]['create_timex'] = date('m-d H:i', strtotime($list[$key]['create_time']));
            $list[$key]['rank_timex'] = date('m-d H:i', strtotime($list[$key]['rank_time']));
            $list[$key]['change'] = $value['current_ranking'] - $value['original_rank'];
            if ($list[$key]['price'] >= 0) {
                $list[$key]['price'] = $list[$key]['price'];

            } else {
                $configmodel = new SystemConfig;
                $site_settlement = $configmodel->get(['name' => 'site_settlement']);
                $settlement_type = $site_settlement['value'];
                if ($settlement_type == 2 && $value['fee'] > 0) {
                    $list[$key]['price'] = $list[$key]['fee'];

                } else {
                    $list[$key]['price'] = Db::field('group.keyword_free')->table(['seo_customer' => 'customer', 'seo_user_group' => 'group'])->where('group.id = customer.member_level')->where('customer.id= ' . $value['uid'])->value('keyword_free');

                }

            }
        }
        return json(['code' => 0, 'data' => $list]);

    }

    public function getfeedbackLists(Request $request)
    {
        $keywords = $request->get('keywords');
        $search_ngines = $request->get('search_ngines', '');
        $limit = $request->get('limit', 30, 'int');
        $status = $request->get('status');
        $where = '1=1';
        if ($status) {
            $where .= ' and a.status = '. $status;
        }
        $taskids = [];
        if (!empty($keywords)) {
            $keywords_where = "(a.keywords like '%{$keywords}%' or a.web_url like '%{$keywords}%' or c.username like '%{$keywords}%' or c.contacts like '%{$keywords}%' or c.company_name like '%{$keywords}%')";
            $feedbackids = db('feedback')->alias('a')->join('customer c', 'c.id = a.uid')->where($keywords_where)->field('a.id')->select();
            foreach ($feedbackids as $key => $item)
            {
                $ids[] = $item['id'];
            }
        }
        if ($taskids)
        {
            $ids = implode(",", $ids);
            $where .= ' and k.id in (' . $ids . ')';
        }
        if (!empty($search_ngines)) {
            $where .= ' and k.search_ngines = ' . $search_ngines;
        }
        $list = Feedback::getList('*,a.id as id,a.uid as uid,a.status as status,a.create_time as create_time,a.refresh_time as refresh_time,a.old_rank as old_rank,a.new_rank as new_rank,a.rank_time as rank_time,a.web_url as web_url,a.search_ngines as search_ngines',$where,$limit);
        return json(['code' => 0, 'data' => $list]);
    }

    public function getStopLists(Request $request)
    {
        $keywords = $request->get('keywords');
        $webUrl = $request->get('weburl', '', 'string');
        $search_ngines = $request->get('search_ngines', '');
        $where = '1=1';
        if ($request->get('status')) {
            $where .= ' and a.status = ' . $request->get('status');

        } else {
            $where .= ' and a.status = 4';

        }
        if (!empty($keywords)) {
            $where .= " and (a.keywords like '%{$keywords}%' or a.web_url like '%{$keywords}%')";
        }
        if (!empty($search_ngines)) {
            $where .= ' and a.search_ngines = ' . $search_ngines;

        }
        $limit = $request->get('limit', 30, 'int');
        $model = new \app\common\model\Keywords;
        $list = \app\common\model\Keywords::getList('*,a.id as id,a.status as status,a.create_time as create_time', $where, $limit);
        foreach ($list as $key => $value) {
            if ($list[$key]['price'] >= 0) {
                $list[$key]['price'] = $list[$key]['price'];

            } else {
                $configmodel = new SystemConfig;
                $site_settlement = $configmodel->get(['name' => 'site_settlement']);
                $settlement_type = $site_settlement['value'];
                if ($settlement_type == 2 && $value['fee'] > 0) {
                    $list[$key]['price'] = $list[$key]['fee'];

                } else {
                    $list[$key]['price'] = Db::field('group.keyword_free')->table(['seo_customer' => 'customer', 'seo_user_group' => 'group'])->where('group.id = customer.member_level')->where('customer.id= ' . $value['uid'])->value('keyword_free');

                }

            }
        }
        return json(['code' => 0, 'data' => $list]);

    }

    public function add(Request $request)
    {
        $uid = $request->uid;
        $data['uid'] = $uid;
        $data['keywords'] = $request->post('keywords', '', 'string');
        $data['web_url'] = $request->post('web_url', '', 'string');
        $data['search_ngines'] = $request->post('search_ngines', 0, 'int');
        $data['status'] = 0;
        $data['original_rank'] = 0;
        $model = new \app\common\model\KeywordsRank();
        $postData['keywords'] = $data['keywords'];
        $postData['url'] = $data['web_url'];
        $postData['checkrow'] = 50;
        $res = Seo::addBaidupc($postData);
        if ($res) {
            if ($res['code'] == 0) {
                $data['taskid'] = $res['data']['taskid'];
                $res = $seo->getBaiduRank($data['taskid']);
                $rank = $res['rank'];
                if ($rank) {
                    $data['original_rank'] = $rank;

                }

            } else {
                return json($res);

            }

        }
        $res = $model->addTask($data);
        if ($res['code'] == 0) {
            $postData['id'] = $res['data']['id'];
            $postData['taskid'] = $data['taskid'];
            $postData['create_time'] = time();

        }
        return json($res);

    }

    public function del(Request $request)
    {
        $uid = $request->uid;
        $id = $request->post('ids');
        $agentid = $request->post('agent_id',0);
        $wordinfo = $request->post('wordinfo','');
        if ($agentid && $wordinfo)
        {
            $count = 0;
            $del_fail_counts = [];
            $uid = null;
            if($agentid != -1){
                $uid = $agentid;
            }
            $model = new \app\common\model\Keywords;
            foreach (explode("\n",$wordinfo) as $words)
            {
                $word = explode(",",$words);
                //例：id.m.shop.cnlist.org<=>id.shop.cnlist.org ; 此类冲突删除方法
                if (strstr($word[1],'cnlist')||strstr($word[1],'qth58')||strstr($word[1],'liebiao')||strstr($word[1],'52bjw')||strstr($word[1],'jinanfa')) {
                    if (strstr($word[1], 'm.')) {
                        $word['url'] = str_replace('.m', '', $word[1]);
                    } else {
                        if (strstr($word[1], '.shop')) {
                            $word['url'] = str_replace('.shop', '.shop.m', $word[1]);
                        } elseif (strstr($word[1], '.b2b')) {
                            $word['url'] = str_replace('.b2b', '.b2b.m', $word[1]);
                        }
                    }
                }
                else {
                    $word['url'] = $word[1];
                }
                if($agentid != -1){
                    $count_sql = "select count(`id`) as count from `seo_keywords` where `web_url`=? or `web_url`=? and `delete_time` is null ";
                    $url_query = $model->query($count_sql,[$word[1],$word['url']]);
                    $url_count =  $url_query[0]["count"];
                    if($url_count > 1 && !empty($uid)){
                        //金融词账号用户做删除
                        //如果这个链接还有别的关键词再使用，则直接删除这个关键词，不计入闲置词
                        $del_sql = "DELETE FROM `seo_keywords` WHERE (`web_url`=? AND `keywords`=? AND `search_ngines`=?)";
                        $del = $model->execute($del_sql, [$word[1],$word[0],$word[2]]);
                        if($del)
                        {
                            $count++;
                        }
                        else
                        {
                            $del_fail_counts[] = $words;
                        }
                   }else{
                        if ($url_count)
                        {
                            if (\app\common\model\Keywords::destroy(['web_url'=>$word[1],'keywords'=>$word[0],'search_ngines'=>$word[2]])){
                                $count++;
                            }
                            else
                            {
                                $del_fail_counts[] = $words;
                            }
                        }
                        else
                        {
                            $del_fail_counts[] = $words;
                        }
                   }


                }else if($agentid == -1){
                    $del_sql = "DELETE FROM `seo_keywords` WHERE (`web_url`=? AND `keywords`=? AND `search_ngines`=? AND `delete_time` is not null)";
                    $del = $model->execute($del_sql, [$word[1],$word[0],$word[2]]);
                    if($del)
                    {
                        $count++;
                    }
                    else
                    {
                        $del_fail_counts[] = $words;
                    }
                }
            }
            return ['code' => 0, 'msg' => "成功删除{$count}条！<br>失败记录：<br>".implode("<br>",$del_fail_counts)];
        }
        if (\app\common\model\Keywords::destroy($id)) {
            return ['code' => 0, 'msg' => '删除成功'];

        } else {
            return ['code' => 1, 'msg' => '删除失败'];

        };

    }

    public function domainmatch(Request $request)
    {
        $agentid = $request->post('agent_id',0);
        $wordinfo = $request->post('wordinfo','');
        if ($agentid && $wordinfo) {
            $count = 0;
            $update_fail_counts = [];
            $uid = null;
            $model = new Customer();
            foreach (explode("\n",$wordinfo) as $words)
            {
                $word = explode(",",$words);
                if (count($word) != 2)
                {
                    return json(['code' => 1, 'msg' => '添加失败，请按正确格式输入数据！']);
                }
                if ($word[1] == 1)
                {
                    $is_domain = $word[1];
                }
                elseif ($word[1] == 2)
                {
                    $is_domain = 0;
                }
                else
                {
                    return json(['code' => 1, 'msg' => '添加失败，请输入正确的匹配值！']);
                }
                $id = \db('customer')->field('id,is_domain')->where('upid',$agentid)->where('username',$word[0])->find();
                if ($id['id'])
                {
                    if ($is_domain == $id['is_domain'])
                    {
                        continue;
                    }
                    else
                    {
                        $query = $model->isUpdate(true)->save(['is_domain' => $is_domain],['id' => $id['id']]);
                        if ($query)
                        {
                            $count++;
                        }
                    }
                }
                else
                {
                    $update_fail_counts = $words;
                }
            }
            return ['code' => 0, 'msg' => "成功设置{$count}条！<br>失败记录：<br>".implode("<br>",$update_fail_counts)];
        }
    }

    public function huifu(Request $request)
    {
        $agentid = $request->post('agent_id',0);
        $wordinfo = $request->post('wordinfo','');
        if ($wordinfo)
        {
            $count = 0;
            $update_fail_counts = [];
            $uid = null;
            foreach (explode("\n",$wordinfo) as $key => $words)
            {
                $word = explode(",",$words);
                if (count($word) != 1)
                {
                    return json(['code' => 1, 'msg' => '添加失败，请按正确格式输入数据！']);
                }

                $customer = \db('customer')->field('id,delete_time')->where('username',$word[0])->where('upid',$agentid)->find();
                if ($customer['id'])
                {
                    if ($customer['delete_time'] == null)
                    {
                        $update_fail_counts[$key] = $words;
                        continue;
                    }
                    $update_data['delete_time'] = null;
                    $query = \db('customer')->where('id',$customer['id'])->update($update_data);
                    if ($query)
                    {
                        $count++;
                    }
                }
                else
                {
                    $update_fail_counts[$key] = $words;
                }
            }
            return ['code' => 0, 'msg' => "成功设置{$count}条！<br>失败记录：<br>".implode("<br>",$update_fail_counts)];
        }
    }

    public function submit(Request $request)
    {
        $id = $request->post('tid');
        $current_ranking = $request->post('current_ranking');
        $original_rank = $request->post('original_rank');
        $cycle = $request->post('cycle');
        $price = $request->post('price');
        $xiongzhang = $request->post('xiongzhang');
        $agent_price = $request->post('agent_price');
        $model = new \app\common\model\Keywords;
        $taskService = new TaskService();
        $data['status'] = 2;
        $data['current_ranking'] = $current_ranking;
        $data['original_rank'] = $original_rank;
        $data['cycle'] = $cycle;
        $data['price'] = $price;
        $data['xiongzhang'] = $xiongzhang;
        $data['agent_price'] = $agent_price;
        $data['start_time'] = time();
        $res = $model->where('id', $id)->update($data);
        if ($res) {
            return ['code' => 0, 'msg' => '成功'];

        } else {
            return ['code' => 1, 'msg' => '失败'];

        }

    }

    public function examine(Request $request)
    {
        $tid = $request->post('tid');
        if (is_array($tid)) {
            $isbatch = true;
            $tid = implode(',', $tid);

        } else {
            $tid = $tid;

        }
        $model = new \app\common\model\Keywords;
        if ($request->post('action') == 'refuse') {
            $data['status'] = 5;

        } else {
            $data['status'] = 2;

        }
        $data['start_time'] = time();
        $res = $model->where('id', 'in', $tid)->update($data);
        if ($res) {
            return ['code' => 0, 'msg' => '成功'];

        } else {
            return ['code' => 1, 'msg' => '失败'];

        }

    }

    public function submitstop(Request $request)
    {
        $id = $request->post('tid');
        $model = new \app\common\model\Keywords;
        $res = false;
        if ($id) {
            $res = $model->whereIn('id', $id)->update(['status' => 4, 'complete_time' => time()]);

        }
        if ($res) {
            return ['code' => 0, 'msg' => '成功'];

        } else {
            return ['code' => 1, 'msg' => '失败'];

        }

    }

    public function refusestop(Request $request)
    {
        $id = $request->post('tid');
        $model = new \app\common\model\Keywords;
        $res = $model->where('id', $id)->update(['status' => 2, 'complete_time' => time()]);
        if ($res) {
            return ['code' => 0, 'msg' => '成功'];

        } else {
            return ['code' => 1, 'msg' => '失败'];

        }

    }

    public function submitrefuse(Request $request)
    {
        $id = $request->post('tid');
        $model = new \app\common\model\Keywords;
        $res = $model->where('id', $id)->update(['status' => 5, 'start_time' => time()]);
        if ($res) {
            return ['code' => 0, 'msg' => '成功'];

        } else {
            return ['code' => 1, 'msg' => '失败'];

        }

    }

    public function Editweburl()
    {
        $web_urls = Db::query("SELECT id,Web_Url FROM pm.seo_keywords");
        foreach ($web_urls as $web_url) {
            $url = preg_replace("/http\:\/\/|https\:\/\//",'',$web_url['Web_Url']);
            $url = rtrim($url,'/');
            Db::execute("UPDATE seo_keywords SET pm.seo_keywords.web_url = '{$url}' WHERE pm.seo_keywords.id = {$web_url['id']}");
        }
    }
}