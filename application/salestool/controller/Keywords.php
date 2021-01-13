<?php

namespace app\salestool\controller;

use seo\Seo;
use think\facade\Config;
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
        return self::getKeywordsList($request);
    }
    public function exportData(Request $request){
        return self::getKeywordsList($request,1);
    }
    private function getKeywordsList(Request $request, $type = 0){
        $keywords = $request->get('keywords');
        $search_ngines = $request->get('search_ngines', '');
        $page_status = $request->get('page_status', '0');
        $limit = $request->get('limit', 30, 'int');
        $search_ngines_nema = Config::get('app.search_ngines');
        $list = self::getKeywords($keywords,$search_ngines,$page_status,$search_ngines_nema,$limit,$type);
        $data = [];
        foreach ($list['data'] as $key => $value) {
            $value['current_ranking'] = $value['current_ranking'] == 0 ? '101' : $value['current_ranking'];
            $value['search_ngines'] = $search_ngines_nema[$value['search_ngines']];
            $value['delete_time'] = date('Y-m-d H:i:s',$value['delete_time']);
            $value['rank_time'] = date('Y-m-d H:i:s',$value['rank_time']);
            $data[] = $value;
        }
        $list['data'] = (array)$data;
        return json(['code' => 0, 'data' => $list]);
    }

    private function getKeywords($keywords,$search_ngines,$page_status,$search_ngines_nema,$limit,$type=0){
        $list = [];
        $db_keywords = \db('keywords')
            ->where('delete_time', 'not null')
            ->where(function ($query)use ($keywords){
                if(!empty($keywords)){
                    $query->where('keywords', 'like', "%$keywords%")
                        ->whereOr('web_url', 'like', "%$keywords%");
                }
            })
            ->where(function ($query) use ($search_ngines,$search_ngines_nema){
                if(array_key_exists($search_ngines, $search_ngines_nema)){
                    $query->where('search_ngines',$search_ngines);
                }
            })
            ->where(function ($query) use ($page_status){
                if($page_status == 1){
                    $query->where('current_ranking','>=',1);
                    $query->where('current_ranking','<=',10);
                }else if($page_status == 2){
                    $query->where('current_ranking',0);
                    $query->whereOr('current_ranking','>',10);
                }
            });
        $uids = $db_keywords->field('uid')->all();
        $uids = array_unique(array_column($uids,'uid'));
        $uids = array_values($uids);

        $uids = \db('customer')
            ->where('upid',1)//金融词账号用户
            ->whereIn('id', $uids)
            ->field('id')
            ->all();
        $uids = array_unique(array_column($uids,'id'));
        $uids = array_values($uids);
        $uids[] = 0;//闲置词库账号
        if($type){
            $temp_list = $db_keywords->whereIn('uid',$uids)->orderRaw('current_ranking !=0 desc,current_ranking,id desc')
                ->field('id,web_url,keywords,search_ngines,current_ranking,delete_time,rank_time')
                ->all();
            $list['data'] = $temp_list;
        }else{
            $list = $db_keywords->whereIn('uid',$uids)->orderRaw('current_ranking !=0 desc,current_ranking,id desc')
                ->field('id,web_url,keywords,search_ngines,current_ranking,delete_time,rank_time')
                ->paginate($limit)
                ->toArray();
        }
        return $list;
    }

    public function delKeyword(Request $request){
        $type = $request->get('type', '');
        $search_ngines_nema = Config::get('app.search_ngines');
        $list = self::getKeywords('','','',$search_ngines_nema,0,1);
        $ids = array_column($list['data'],'id');
        $del_result = 0;
        if($type == 'sunny_del_all'){
            $del_result = \db('keywords')->whereIn('id',$ids)->whereNotNull('delete_time')->delete();
        }
        return json(['code' => 0, 'data' => $ids, 'del_count'=>$del_result]);
    }
}