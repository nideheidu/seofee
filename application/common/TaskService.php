<?php
/**
 * Created by PhpStorm.
 * User: wenwu
 * Date: 2018/12/16
 * Time: 22:33
 */
namespace app\common;
use app\common\model\CustomerAccount;
use app\common\model\Feedback;
use app\common\model\Keywords;
use app\common\model\Mingxi;
use app\common\model\KeywordsRank;
use app\common\model\Order;
use app\common\model\WebUrl;
use app\common\model\Customer;
use app\common\model\SystemConfig;
use app\common\model\Fee;
use app\common\model\RankLog;
use function GuzzleHttp\default_ca_bundle;
use function React\Promise\map;
use seo\Seo;
use think\Db;
use think\Exception;
use think\Log;
use think\Queue;
use think\facade\Config;

class TaskService{
    /**
     * @param $data
     * @param $uid
     * @return mixed
     */
    public static function add_keywords($data,$uid){
        $keywordsList=explode(',',str_replace('|',',',$data['keywords']));
        $keywordsList = array_flip(array_flip($keywordsList));
        $urlData=[
            'uid'=>$uid,
            'url'=>$data['web_url'],
            'admin_host'=>$data['admin_web_url'],
            'admin_access'=>$data['admin_username']?$data['admin_username']:'',
            'admin_password'=>$data['admin_password']?$data['admin_password']:'',
            'ftp_host'=>$data['ftp_host']?$data['ftp_host']:'',
            'ftp_port'=>$data['ftp_port']?$data['ftp_port']:'',
            'ftp_access'=>$data['ftp_username']?$data['ftp_username']:'',
            'ftp_password'=>$data['ftp_password']?$data['ftp_password']:'',
            'xiongzhang'=>$data['xiongzhang'],
        ];

        if($data['search_ngines'] ==1){
            $keywordsInfo = 'keywordsPC';
            $keywordsinfoStr ='keywords_p_c' ;
        }else{
            $keywordsInfo = 'keywordsMobile';
            $keywordsinfoStr = 'keywords_mobile';
        }
        
        $webModel = new WebUrl;
        $webModel->startTrans();
        $webid=0;

        
        // $site_settlement = $configmodel->get(['name'=>'site_settlement']);
        // $settlement_type = $site_settlement['value'];

        $configmodel = new SystemConfig;   //获取结算规则
        $settlement_type = $configmodel->alias('k')
                            ->join('customer c','c.upid = k.agent_id')
                            ->where(['c.id'=>$uid,'k.name'=>'site_settlement'])->value('value');
       
        try{

            $webObj=$webModel->get(['url'=>$urlData['url']]);

            //$kewordlist=db('keywords')->where('web_id',$webObj['id'])->select();
            
            
            
            if(!empty($webObj)){
                $kewordlist=Keywords::where(array('web_id'=>$webObj->id,'search_ngines'=>$data['search_ngines'],'keywords'=>$data['keywords']))->select()->toArray();
                $webObj = $webObj->toArray();
                if(!empty($kewordlist)){

                    $keywrodsArr = array_column($kewordlist,'keywords');
                    $keywrodsArr =array_diff($keywordsList,$keywrodsArr);

                   
                    if(empty($keywrodsArr)){
                        return ['code'=>1,'msg'=>'关键字已存在'];
                    }
                }
                $webid=$webObj['id'];
                // $webObj->save($urlData);
            }  else {
                $webModel->allowField(true)->save($urlData);
                $webid=$webModel->id;
            }

                $keywordData=[];
                $time=time();
                foreach ($keywordsList as $v){
                    if($v){
                       
                        $seo = new Seo;
                        $res = $seo->getTaskId([
                                    'url'=>$data['web_url'],
                                    'keywords' =>$v,
                                    'checkrow' =>100
                                ],$data['search_ngines']);

                        if($res){
                            if($res['code']=="0"){
                               $task_number=$res['data'];
                            }else{
                                return ['code'=>1,'msg'=>$res['msg']];
                            }
                        }
                        


                        if($settlement_type == 2 )
                        {
                            //获取用户的指数排名
                            $index_result=json_decode($seo->getBaiduIndex($v),true);

                            if( $index_result['StateCode']==1 &&$index_result['Reason']=='成功' )
                            {
                                // $baiduPcArray = explode(',', $index_result['Result']['BaiduPc']);
                                // $BaiduMobileArray = explode(',', $index_result['Result']['BaiduMobile']);
                                // $BaiduPc = $index_result['Result']['BaiduPc']?end($baiduPcArray):0;
                                // $BaiduMobile = $index_result['Result']['BaiduMobile']?end($BaiduMobileArray):0;

                                $baiduAllArray = explode(',', $index_result['Result']['BaiduAll']);   
                                $BaiduAll = $index_result['Result']['BaiduAll']?end($baiduAllArray):0;
                                $BaiduPc = $BaiduAll;
                                $BaiduMobile = $BaiduAll;

                                switch ($data['search_ngines']) {
                                    case 1:
                                        $tempindex = $BaiduPc;
                                        break;
                                    
                                    default:
                                        $tempindex = $BaiduMobile;
                                        break;
                                }

                                
                            }else
                            {
                                $tempindex = 0;
                                $BaiduPc = 0;
                                $BaiduMobile = 0;
                            }
                            //$data['fee']=db('fee')->where("minnum < $tempindex")->where("maxnum > $tempindex")->value('fee');
                            $data['fee'] = Fee::alias('f')
                                ->join('customer c','c.member_level = f.group_id')
                                ->where("f.minnum <= $tempindex")->where("f.maxnum >= $tempindex")->where("c.id = $uid")->value('fee');
                            $agent_fee = self::getAgentPrice($uid,$tempindex,$data['search_ngines']);
                            
                            $keywordData[]=[
                                'uid'=>$uid,
                                'web_url'=>$data['web_url'],
                                'web_id'=>$webid,
                                'task_number'=>$task_number,
                                'keywords'=>$v,
                                'search_ngines'=>$data['search_ngines'],
                                'current_ranking'=>0,
                                'cycle'=>$data['cycle']*30,
                                'create_time'=>$time,
                                'BaiduPc'=>$BaiduPc,
                                'BaiduMobile'=>$BaiduMobile,
                                'fee'=>$data['fee'],
                                'agent_price'=>$agent_fee,
                                'xiongzhang'=>$data['xiongzhang'],
                                //'billing_mode'=>$data['billing_mode'],
                            ];
                        }
                        else
                        {
                            

                            $keywordData[]=[
                                'uid'=>$uid,
                                'web_url'=>$data['web_url'],
                                'web_id'=>$webid,
                                'task_number'=>$task_number,
                                'keywords'=>$v,
                                'search_ngines'=>$data['search_ngines'],
                                'current_ranking'=>0,
                                'cycle'=>$data['cycle']*30,
                                'create_time'=>$time,
                                'fee'=>0,
                                'price'=>self::getPrice($uid,$data['search_ngines'],$v),
                                'agent_price'=>self::getAgentPrice($uid,0,$data['search_ngines']),
                                'xiongzhang'=>$data['xiongzhang'],
                                //'billing_mode'=>$data['billing_mode'],
                            ];
                        }
                    }
                }
                
                if(count($keywordData)>0){

                    if(Keywords::insertAll($keywordData))  //插入数据
                    {
                        $resid = Keywords::getLastInsID();
                       
                        $webModel->commit();
                        return ['code'=>0,'msg'=>'添加成功','task_id'=>$task_number,'id'=>$resid];
                    }
                    else
                    {
                        $webModel->rollback();
                        return $res;
                    }
                };
            $webModel->rollback();
            return ['code'=>1,'msg'=>'添加失败'];
        }catch (Exception $e){
            $webModel->rollback();
            return ['code'=>1,'msg'=>$e->getMessage()];
        }
    }
    
    public static function edit_keywords($data,$uid){
        $where=array();
        $where['uid'] = $uid;
        $where['status'] = [0, 5];
        
        if(isset($data['id']) && $uid){
            $where['id'] = $data['id'];
            $update_data['keywords'] = $data['keywords'];
            $update_data['web_url'] = $data['web_url'];
            $update_data['xiongzhang'] = $data['xiongzhang'];
            $update_data['cycle'] = $data['cycle'];
            $res = Keywords::where($where)->update($update_data);
            
        }

        if ($res) {
            return ['code'=>0,'msg'=>'成功'];
        }else{
            return ['code'=>1,'msg'=>'修改失败'];
        }
    }
    //删除关键字
    public static function del_keywords($data,$uid){
        $where=array();
        $where['uid'] = $uid;
        $where['status'] = [0,5];
        if(isset($data['isdel']) && $data['isdel']==1){
            $where['id'] = $data['id'];
            $res = Keywords::where($where)->delete();
        }else {
            $res = Keywords::where($where)->whereIn('id', $data['ids'])->delete();
        }

        if ($res) {
            return ['code'=>0,'msg'=>'成功'];
        }else{
            return ['code'=>1,'msg'=>'失败'];
        }
    }

    //客户反馈词刷新排名
    public static function admin_update_feedback_rank($data,$uid=0)
    {
        $feedback = \db('feedback')->field('id,uid,status,web_url,keywords,search_ngines')->where('id',$data['tid'])->find();
        $keywords =\db('keywords')->field('id')->where('web_url',$feedback['web_url'])->where('keywords',$feedback['keywords'])->where('search_ngines',$feedback['search_ngines'])->find();
        $data['tid'] = $keywords['id'];
        $data['feedback'] = 1;
        $res = self::system_update_keywords_rank($data,$feedback['uid']);
        if ($res['code'] == 0)
        {
            if ($res['rank'])
            {
                $update_data = [
                    'new_rank' => $res['rank'],
                    'refresh_time' => time()
                ];
                Feedback::where('id', $feedback['id'])->update($update_data);
            }
        }
        return ['code' => 0, 'msg' => '反馈词排名更新成功'];
    }
    //反馈词排名处理
    public static function admin_process_feedback_rank($data,$uid=0)
    {
        $feedback = \db('feedback')->field('id,uid,web_url,keywords,search_ngines')->where('status = 1')->where('delete_time is null')->order('id desc')->select();
        foreach ($feedback as $key => $fb)
        {
            $keywords = \db('keywords')->field('id')->where('web_url',$fb['web_url'])->where('keywords',$fb['keywords'])->where('search_ngines',$fb['search_ngines'])->find();
            $data['tid'] = $keywords['id'];
            $data['feedback'] = 1;
            $res = self::system_update_keywords_rank($data,$fb['uid']);

            if ($res['code'] == 0)
            {
                if ($res['rank'])
                {
                    $update_data = [
                        'new_rank' => $res['rank'],
                        'refresh_time' => time()
                    ];
                    Feedback::where('id', $fb['id'])->update($update_data);
                }
            }
        }
        return ['code' => 0, 'msg' => '反馈词排名更新成功'];
    }

    //反馈词状态处理-待处理
    public static function admin_process_feedback_status($data,$uid=0)
    {
        $feedback = \db('feedback')->field('id,new_rank,web_url,keywords,search_ngines')->where('status = 1')->where('delete_time is null')->order('id desc')->select();
        foreach ($feedback as $key => $fd)
        {
            if (!$fd['new_rank'])
            {
                continue;
            }
            $update_data = [
                'rank_time' =>time()
            ];
            if ($fd['new_rank'])
            {
                if ($fd['new_rank'] <= 10)
                {
                    $update_data['status'] = 3;
                    $update_data['ispay'] = 1;
                    $update_data['new_rank_customer'] = $fd['new_rank'];
                }
                else
                {
                    $update_data['status'] = 2;
                    $update_data['ispay'] = 2;
                    $update_data['new_rank_customer'] = $fd['new_rank'];
                }
            }

            Feedback::where('id', $fd['id'])->update($update_data);

            $update_data_k = [
                'current_ranking' => $fd['new_rank'],
                'rank_time' => time()
            ];
            \db('keywords')->where('web_url',$fd['web_url'])->where('keywords',$fd['keywords'])->where('search_ngines',$fd['search_ngines'])->update($update_data_k);
        }
        return ['code' => 0, 'msg' => '反馈此状态更新成功'];
    }

    //暂停关键字
    public static function admin_stop_keywords($data,$uid=0,$status = 4){
        if(is_array($data['tid']))
        {
             $ids = $data['tid'];
        }
        else
        {
            $ids = [$data['tid']];
        }
        foreach ($ids as $key => $value) {
            if($uid)
            {
                $info=db('keywords')->alias('a')
                        ->join('customer c','c.id = a.uid')
                        ->where('a.id', $value)->find();
                
                
                if($info['upid'] != $uid)
                {
                     return ['code'=>1,'msg'=>'你没有权限这样操作'];
                }
                if($info['standard'] == 0 && (time() - $info['start_time']) <  3600*24*20 )
                {
                    return ['code'=>1,'msg'=>'关键词优化未满20天，不能停止'];
                }
                if($info['standard'] >0 && $info['standard']< 30)
                {
                     return ['code'=>1,'msg'=>'关键词达标未满30天，不能停止'];
                }

                
            }
            $res=Keywords::where('id', $value)->update(['status'=>$status]);
        }
       

        return ['code'=>0,'msg'=>'操作成功'];
    }
    //任务列表
    public static function order_lists($data,$uid){
        $where=function ($query) use($data,$uid){
           // dump($data);exit;
            if(isset($data['shenhe_status']) && (!empty($data['shenhe_status']) || $data['shenhe_status']==='0')) $query->where('shenhe_status',$data['shenhe_status']);
            if(empty($data['shenhe_status'])) $query->where('shenhe_status','<>','1');
            if(isset($data['status']) && ($data['status'] || $data['status']==='0')) $query->where('status',$data['status']);
            if($uid)$query->where('uid',$uid);
        };
        $limit = $data['limit']?$data['limit']:15;
        $orderModel= new Order;
        $list=$orderModel->with(['keywordsInfo','webInfo'])->where($where)->order('id desc')->paginate($limit)->each(function ($v,$k){
            /*if($v['table_web_url']){
                $table_web_url=json_decode(json_encode($v['table_web_url']),true);
                $arr=['create_time','delete_time'];
                foreach ($table_web_url as $key=>$val){
                    if(!in_array($key,$arr) && !isset($v[$key]))$v[$key]=$val;
                }
            }
            $v['table_web_url']=null;
            return $v;*/
        });
       // $logs = \think\facade\Log::getLog();
        //dump($logs);exit;
        return $list;
    }

    /**
     * 提交审核
     * @param $data
     * @param $uid
     * @return bool
     * @throws Exception\DbException
     * @throws \think\exception\PDOException
     */
    public static function order_add($data,$uid){
        //提交审核
        $keywordModel = new Keywords;
        $keywordsInfo = $keywordModel->get(['id'=>$data['tid'],'uid'=>$uid]);
        if(empty($keywordsInfo)){
           return ['code'=>1];
        }
        $orderModel = new Order;
        $orderModel->startTrans();
        try{
            $orderModel->order_number =date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
            $orderModel->uid=$uid;
            $orderModel->web_id = $keywordsInfo->web_id;
            $orderModel->kid = $keywordsInfo->id;
            $orderModel->original_rank=$keywordsInfo->current_ranking;
            $orderModel->current_ranking = $keywordsInfo->current_ranking;
            $orderModel->standard = 0;
            $orderModel->shenhe_status = 0;
            $orderModel->status = 0;
            $orderModel->cycle = $keywordsInfo->cycle;
            $orderModel->save();
            $keywordsInfo->status=1;
            $keywordsInfo->save();
            $orderModel->commit();
            return true;
        }catch (Exception $e){
            $orderModel->rollback();
            return false;
        }
    }
    //seo关键字列表
    public static function keywords_lists($data,$uid=0){
        $order = 'id desc';
        if (isset($data['order']) && $data['order'] == 'current_ranking')
        {
            $order = 'if(current_ranking=0,101,0),current_ranking';
        }
        elseif (isset($data['order']) && $data['order'] == 'standard')
        {
            $order = 'standard DESC';
        }
        $where=array();
        $where2="1=1";
        $where3="1=1";
        $where4="1=1";
        if(isset($data['weburl']) && $data['weburl']){$where['web_url']=$data['weburl'];};
        if($uid){$where['uid']=$data['uid'];};
        if(isset($data['status'])){
            /*if (isset($data['standard_status']) && $data['standard_status'])
            {
                $data['status'] = 2;
            }*/
            $where2.=" and status in (".$data['status'] . ")";
        };
        if($data['status'] == 0){
            $where2.=" or status = 5";
        };
        if(isset($data['keywords'])){
            //$where2.=" and keywords like '%".$data['keywords']."%'";

            $where3 = [['keywords|web_url', 'like', "%".$data['keywords']."%"]];
        };
        if (isset($data['standard_status']) && $data['standard_status'] == 1)
        {
            $where4 = "standard=0 and current_ranking <= 10 and date_format(from_unixtime(rank_time),'%Y-%m-%d') = date_format(now(),'%Y-%m-%d')";
        }
        elseif (isset($data['standard_status']) && $data['standard_status'] == 2)
        {
            $where4 = "current_ranking <= 10 and current_ranking > 0";
        }
        elseif (isset($data['standard_status']) && $data['standard_status'] == 3)
        {
            $where4 = "current_ranking > 10 or current_ranking = 0";
        }

        if(isset($data['search_ngines']) && $data['search_ngines']){$where['search_ngines']=$data['search_ngines'];};
        $limit = $data['limit']?$data['limit']:15;
        $list=Keywords::where($where)->where($where2)->where($where3)->where($where4)->orderRaw($order)->paginate($limit);
        
        foreach ($list as $key => $value) {
            $list[$key]['original_rank'] = $value['original_rank']==0?'101':$value['original_rank'];
            $list[$key]['current_ranking'] = $value['current_ranking']==0?'101':$value['current_ranking'];
            $today = strtotime(date('Y-m-d',time())." 00:00:01");
            switch ($value['search_ngines']) {
                case '百度PC':
                    $search_ngines = 1;
                    break;
                case '百度移动':
                    $search_ngines = 2;
                    break;
                case '360PC':
                    $search_ngines = 3;
                    break;
                case '360移动':
                    $search_ngines = 4;
                    break;
                case '搜狗PC':
                    $search_ngines = 5;
                    break;
                case '搜狗移动':
                    $search_ngines = 6;
                    break;
                default:
                    $search_ngines = 1;
                    break;
            }
            $feedback_status = \db('feedback')->field('status')->where('web_url',$value['web_url'])->where('keywords',$value['keywords'])->where('search_ngines',$search_ngines)->where("create_time >= '{$today}'")->where('uid',$value['uid'])->find();
            if ($feedback_status['status'])
            {
                $list[$key]['feedback_status'] = $feedback_status['status'];
            }
            else
            {
                $list[$key]['feedback_status'] = 0;
                $now = strtotime(date('Y-m-d',time())." 16:00:00");
                if (time() >= $now)
                {
                    $list[$key]['feedback_status'] = 4;
                }
            }
            $upid = \db('customer')->field('upid')->where('id',$value['uid'])->find();
            $list[$key]['upid'] = $upid['upid'];
            if ($value['start_time']) {
                if (time()-($value['start_time']) >3600*24*$value['cycle']) {
                    $list[$key]['over']=1;
                }else{
                    $list[$key]['over']=0;
                }
            }else{
                $list[$key]['over']=0;
            }     
            if($list[$key]['price'] > 0){
                $list[$key]['price'] = $list[$key]['price'];
            }else
            {
                
                $settlement_type = self::getSettlementForUid($uid);
                if($settlement_type == 2 && $value['fee'] > 0)  //设置按指数结算且指数价格大于0  避免有些关键字添加时未获取指数价格扣0.00
                {
                    $list[$key]['price'] = $list[$key]['fee'];
                }
                else
                {
                    $ngines = Config::get('app.search_ngines');
                    $ngines = array_flip($ngines);
                    $list[$key]['price'] = self::getPrice($uid,$ngines[$value['search_ngines']]);
                    // $list[$key]['price'] = Db::field('group.keyword_free')
                    // ->table(['seo_customer'=>'customer','seo_user_group'=>'group'])
                    // ->where('group.id = customer.member_level')
                    // ->where('customer.id= '.$value['uid'])
                    // ->value('keyword_free');
                }
            }   
        }
        return $list;
    }
    //非首页反馈记录表
    public static function feedback_lists($data,$uid=0)
    {
        $order = 'id desc';
        $where=array();
        if($uid){$where['uid']=$data['uid'];};
        $limit = $data['limit']?$data['limit']:15;
        $list=Feedback::where($where)->orderRaw($order)->paginate($limit);
        foreach ($list as $key => $value)
        {
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
            if ($value['rank_time'])
            {
                $list[$key]['rank_time'] = date('Y-m-d H:i:s', $value['rank_time']);
            }
            if ($value['ispay'] == 1) {
                $list[$key]['ispay'] = "应扣费";
            }
            elseif ($value['ispay'] == 2) {
                $list[$key]['ispay'] = "不扣费";
            }
            else {
                $list[$key]['ispay'] = "";
            }
        }
        return $list;
    }
    public static function keyword_inquiry_price($data,$uid){
        $uid = $data['uid'];
        $search_ngines = $data['search_ngines'];
        $price=self::getPrice($uid,$search_ngines,$data['keywords']);
        return ['price'=>$price];
    }
    public static function keyword_inquiry_price_all($data,$uid){
        $uid = $data['uid'];
        $keywords = $data['keywords'];
        $web_url = $data['web_url'];
        $xiongzhang = $data['xiongzhang'];
        $data=self::getPriceall($uid,$keywords);
        foreach ($data['engines'] as $key => $value) {
            $result[$key]['keywords'] = $keywords;
            $result[$key]['price'] = $data['price'] * $value['value'] / 100;
            $result[$key]['web_url'] = $web_url;
            $result[$key]['search_ngines'] = $value['search_ngines'];
            $result[$key]['search_ngine_title'] = $value['title'];
            $result[$key]['status'] = 0;
            $result[$key]['cycle'] = 30;
        }
        return $result;
    }
    //seo关键字列表
    public static function keywords_rank_lists($data,$uid=0){
        $where=function ($query) use($data,$uid){
            if($uid)$query->where('uid',$uid);
            if(isset($data['weburl']) && $data['search_ngines'])$query->where('status',$data['status']);
            if(isset($data['search_ngines']) && $data['search_ngines'])$query->where('search_ngines',$data['search_ngines']);
        };
        $limit = $data['limit']?$data['limit']:15;
        $list=KeywordsRank::where($where)->order('id desc')->paginate($limit);
        return $list;
    }
    /**
     * 增加关键字查询
     * @param $data
     * @param $uid
     * @return mixed
     */
    public static function add_keywordrank($data,$uid){
        $keywords=explode(',',$data['keywords']);
        $urlData=[
            'uid'=>$uid,
            'web_url'       => $data['web_url'],
            'keywords'      => $data['keywords'],
            'create_time'   => time(),
            'search_ngines' => $data['search_ngines'],
            'cycle'         => $data['cycle']
        ];
        //获取taskid
        $seo = new Seo;
        $res = $seo->getTaskId([
                    'url'=>$data['web_url'],
                    'keywords' =>$data['keywords'],
                    'checkrow' =>100
                ],$data['search_ngines']);
        if($res){
            if($res['code']=="0"){
               $urlData['task_number']=$res['data'];
            }
        }
        $KeywordsModel = new KeywordsRank;
        $KeywordsModel->startTrans();
        try{
            $res=$KeywordsModel->allowField(true)->save($urlData);
            if($res){
                if($res){
                    $taskid = $res['data'];
                    $KeywordsModel->commit();
                    return ['code'=>0,'msg'=>'添加成功'];
                }else{
                    $KeywordsModel->rollback();
                    return ['code'=>1,'msg'=>'添加失败'];
                }
            }
        }catch (Exception $e){
            $KeywordsModel->rollback();
            return ['code'=>1,'msg'=>$e->getMessage()];
        }
    }
    /**
     * 增加关键字查询
     * @param $data
     * @param $uid
     * @return mixed
     */
    public static function keyword_submit($data){
        //查询用户余额
        $sysconfig=db('system_config')->where('id',47)->value('value');
        $keywordsuid=db('keywords')->where('id',$data['tid'])->value('uid');
        $customeraccount=db('customer_account')->where('uid',$keywordsuid)->value('total_sum');
        if ($sysconfig >$customeraccount) {
            return ['code'=>1,'msg'=>"余额不足".$sysconfig."元,请充值"];
        }
        $res=Keywords::where('id', $data['tid'])->update(['status'=>1,'agent_status'=>1]);
        if ($res) {
            return ['code'=>0,'msg'=>'成功'];
        }else{
            return ['code'=>1,'msg'=>'失败'];
        }
    }
    /**
     * 增加关键字查询
     * @param $data
     * @param $uid
     * @return mixed
     */
    public static function keyword_submit_new($data){ 

        $keawords['keywords'] = $data['data']['keywords'];
        $keawords['web_url'] = $data['data']['web_url'];
        $keawords['xiongzhang'] = isset($data['data']['xiongzhang'])?$data['data']['xiongzhang']:'';
        $keawords['search_ngines'] = $data['data']['search_ngines'];
        $keawords['cycle'] = 1;
        if(isset($data['action']) && $data['action'] == 'save')
        {
            return self::add_keywords_quick($keawords,$data['uid'],0,0);
        }
        else
        {
            return self::add_keywords_quick($keawords,$data['uid'],1,1);
        }
        

        
    }
    /**
     * 撤回
     * @param $data
     * @param $uid
     * @return mixed
     */
    public static function back($data){
        //查询用户余额
        $res=Keywords::where('id', $data['tid'])->update(['status'=>0]);
        if ($res) {
            return ['code'=>0,'msg'=>'成功'];
        }else{
            return ['code'=>1,'msg'=>'失败'];
        }
    }/**
     * 撤回
     * @param $data
     * @param $uid
     * @return mixed
     */
    public static function stopback($data){
        //查询用户余额
        $res=Keywords::where('id', $data['tid'])->update(['status'=>2]);
        if ($res) {
            return ['code'=>0,'msg'=>'成功'];
        }else{
            return ['code'=>1,'msg'=>'失败'];
        }
    }

    /**
     * @param $data
     * @return array
     */
    public static function feedback($data){
        switch ($data['search_ngines'])
        {
            case '百度PC':
                $search_ngines = 1;
                break;
            case '百度移动':
                $search_ngines = 2;
                break;
            case '360PC':
                $search_ngines = 3;
                break;
            case '360移动':
                $search_ngines = 4;
                break;
            case '搜狗PC':
                $search_ngines = 5;
                break;
            case '搜狗移动':
                $search_ngines = 6;
                break;
            default:
                $search_ngines = 1;
                break;
        }
        $feedbackData=[
            'uid'=>$data['uid'],
            'web_url'=>$data['web_url'],
            'keywords'=>$data['keywords'],
            'search_ngines'=>$search_ngines,
            'create_time'=>time(),
            'status'=>1,
            'old_rank'=>$data['old_rank'],
            'delete'=>'null',
        ];
        $feedback = new Feedback();
        $query = $feedback->allowField(true)->save($feedbackData);
        if ($query) {
            return ['code'=>0,'msg'=>'成功'];
        } else {
            return ['code'=>1,'msg'=>'失败'];
        }
    }
    public static function keyword_changefee($data){
        //查询用户余额
        $res=Keywords::where('id', $data['id'])->update(['fee'=>$data['fee']]);

        $Customer=new Customer();
        $info=db('keywords')->where('id', $data['id'])->find();
        if(!$Customer->getUserBalanceToday($info['uid'],$info['keywords'],$info['web_url'],$info['id']))
        {

            //扣费
            
            $Customer->editUserBalanceDetail($info['uid'],-$data['fee'],$info['web_url'],$info['keywords'],"关键字排名扣费",1,$info['id']);
            $agent_id = $Customer->getMemberAgentid($info['uid']);
                
            if($agent_id)
            {
                $res = $Customer->editUserBalanceDetail($agent_id,-$info['agent_price'],$info['web_url'],$info['keywords'],"代理商关键字排名扣费",3,$info['id']);
                if($res['code'])
                {
                    return ['code'=>1,'msg'=>$res['msg']];
                }
            }
        }
       
        if ($res) {
            return ['code'=>0,'msg'=>'成功'];
        }else{
            return ['code'=>1,'msg'=>'失败'];
        }
    }
    /**
     * 明细列表
     * @param $data
     * @param $uid
     * @return mixed
     */
    public static function mingxi_lists($data,$uid,$isagent=false){
        $limit = $data['limit']?$data['limit']:15;
//        $list=Mingxi::where("uid",$uid)->order('create_time desc')->paginate($limit);
        if (isset($data['group']) && $data['group'] == 'day')
        {
            $sql = Mingxi::where("uid",$uid)->where("change_type",2)->field("FROM_UNIXTIME(create_time,'%Y-%m-%d') as date,sum(free) as sumfree,max(create_time) as maxtime")->group("date")->order("date desc")->buildSql();
            $list = Db::table($sql)->alias('mingxi')->field("date,sumfree,maxtime")->paginate($limit);
            //当前余额
            $balance = CustomerAccount::where('uid',$uid)->field("total_sum")->find();
            //计算每日余额
            foreach ($list as $key => $l)
            {
                $balance_day = Mingxi::where("uid",$uid)->where("create_time > {$l['maxtime']}")->sum("free");
                $l['balance'] = number_format(($balance['total_sum'] - $balance_day),2);
                $list[$key] = $l;
            }
        }
        elseif (isset($data['group']) && $data['group'] == 'keyword')
        {
            $sql = Mingxi::where("uid",$uid)->where("change_type",2)->where("kid",$data['kid'])->field("FROM_UNIXTIME(create_time,'%Y-%m-%d') as date,free as sumfree")->order("date desc")->buildSql();
            $list = Db::table($sql)->alias('mingxi')->field("date,sumfree")->paginate($limit);
        }
        else
        {
            $list = Mingxi::alias('m')->leftJoin('seo_keywords k','m.kid = k.id')->field("m.id id,m.uid uid,m.kid kid,m.type type,m.change_type change_type,m.free free,m.url url,m.keywords keywords,m.remarks remarks,m.create_time create_time,if(k.search_ngines=1,'百度PC',if(k.search_ngines=2,'百度移动',if(k.search_ngines=3,'360PC',if(k.search_ngines=4,'360移动',if(k.search_ngines=5,'搜狗PC',if(k.search_ngines=6,'搜狗移动','')))))) search_ngines")->where("m.uid",$uid)->order('m.create_time desc')->paginate($limit);
        }
        return $list;
    }
    /**
     * 明细列表
     * @param $data
     * @param $uid
     * @return mixed
     */
    public static function mingxi_add_lists($data,$uid){
        $limit = $data['limit']?$data['limit']:15;
        $list=Mingxi::where("uid",$uid)->where('change_type',1)->order('id desc')->paginate($limit);
        //当前余额
        $balance = CustomerAccount::where('uid',$uid)->field("total_sum")->find();
        //计算余额
        foreach ($list as $key => $l)
        {
            $balance_day = Mingxi::where("uid",$uid)->where("create_time > {$l['create_time']}")->sum("free");
            $l['balance'] = number_format(($balance['total_sum'] - $balance_day),2);
            $list[$key] = $l;
        }
        return $list;
    }
    /**
     * 明细列表
     * @param $data
     * @param $uid
     * @return mixed
     */
    public static function change_fee($data,$uid){
        $list=explode(",", $data['id']);
        foreach ($list as $key => $value) {
            $res=Keywords::where('id', $value)->update(['fee'=>$data['fee']]);
        }
        return ['code'=>0,'msg'=>'成功'];
    }

    /**
     * 明细列表
     * @param $data
     * @param $uid
     * @return mixed
     */
    public static function agent_mingxi_lists($data,$uid){
       
        $where = array();
        
        $where['c.id'] = $uid;
        $where['a.change_type'] = 2;

        //2020-12-29 隐藏lc明细
        $where_lc = "c.delete_time is null and c.id != 1";

        //$list=Mingxi::order('id desc')->where($where)->paginate(10);
        $limit = $data['limit']?$data['limit']:15;
        $list=Mingxi::
            alias('a')
            ->join('customer c','c.id = a.uid')
            ->field("*,a.id as id,a.create_time as create_time")->where($where)->where('a.free','<>',0)->where($where_lc)->order('a.id desc')->paginate($limit);


        foreach ($list as $key => $value) {
            $list[$key]['uid']=db('customer')->where('id',$value['uid'])->value('username');
        }
        return $list;
    }


    /**
     * 明细列表
     * @param $data
     * @param $uid
     * @return mixed
     */
    public static function admin_mingxi_lists($data,$uid,$isagent = false){
        // $where=function ($query) use($data,$uid){
        //    // dump($data);exit;
        //     if(isset($data['name']) && !empty($data['name'])){
        //         $uid=db('customer')->where('username',$data['name'])->value('id');
        //         $query->where('uid',$uid);
        //     } 
        // };
        //print_r($where);
        $where = array();
        $where['a.change_type'] = 2;
        //$where['a.free'] = ['<>',0];
        if(isset($data['name']) && !empty($data['name'])){
            $search_uid=db('customer')->where([['username', 'like', "%".$data['name']."%"]])->value('id');
            $where['a.uid'] = $search_uid;
        } 
        if($isagent)
        {
            $where['c.upid'] = $uid;
        }
        if(isset($data['type']))
        {
            $where['a.type'] = $data['type'];
        }
        //2020-12-29 隐藏lc明细
        $where_lc = "c.delete_time is null and c.id != 1";

        //$list=Mingxi::order('id desc')->where($where)->paginate(10);
        $limit = $data['limit']?$data['limit']:10;
        $list=Mingxi::
            alias('a')
            ->join('customer c','c.id = a.uid')
            ->field("*,a.id as id,a.create_time as create_time")->where($where)->where('a.free','<>',0)->where($where_lc)->order('a.id desc')->paginate($limit);


        foreach ($list as $key => $value) {
            $list[$key]['uid']=db('customer')->where('id',$value['uid'])->value('username');
        }
        return $list;
    }

    /**
     * 明细列表
     * @param $data
     * @param $uid
     * @return mixed
     */
    public static function agent_recharge_lists($data,$uid,$isagent = false){
        $where['a.type'] = 2;
        $where['a.change_type '] = 1;
        if($isagent)
        {
            $where['c.id'] = $uid;
        }else
        {
            $where['c.upid'] = 0;
        }
        //2020-12-29 隐藏lc明细
        $where_lc = "c.delete_time is null and c.id != 1";
        
        $limit = $data['limit']?$data['limit']:10;
        $list=Mingxi::
            alias('a')
            ->join('customer c','c.id = a.uid')
            ->field("*,a.id as id")->where($where)->where($where_lc)->order('a.id desc')->paginate($limit);

        foreach ($list as $key => $value) {
            $list[$key]['uid']=db('customer')->where('id',$value['uid'])->value('username');
        }
        return $list;
    }

    /**
     * 明细列表
     * @param $data
     * @param $uid
     * @return mixed
     */
    public static function admin_recharge_lists($data,$uid,$isagent = false){
        $where['a.type'] = 2;
        $where2 = '1=1';
        if($isagent)
        {
            $where['c.upid'] = $uid;
        }
        else
        {

            $where2.= " and c.upid != 0";
        }
        //2020-12-29 隐藏lc明细
        $where_lc = "c.delete_time is null and c.id != 1";

        $limit = $data['limit']?$data['limit']:10;
        $list=Mingxi::
            alias('a')
            ->join('customer c','c.id = a.uid')
            ->field("*,a.id as id,a.create_time as create_time")->where($where)->where($where2)->where($where_lc)->order('a.id desc')->paginate($limit);

        foreach ($list as $key => $value) {
            $list[$key]['uid']=db('customer')->where('id',$value['uid'])->value('username');
            $list[$key]['time'] = date("Y-m-d H:i:s",$value['create_time']);
            $uid = db('customer')->where('username',$value['uid'])->value('id');
            //交易前（元）
            $list[$key]['before_free'] = db('mingxi')->where('uid',$uid)->where("create_time < ".$value['create_time'])->value('free') ? db('mingxi')->where('uid',$uid)->where("create_time < ".$value['create_time'])->sum('free')+0 : 0;
            //交易后（元）
            $list[$key]['after_free'] = $list[$key]['before_free'] + $value['free'];
            $value['free'] = $value['free'] + 0;
        }
        return $list;
    }
    /**
     * 添加关键字
     * @RULE /v1/keywords/add.html
     * @param Request $request
     * @return \think\response\Json
     * @throws \think\exception\PDOException
     */
    public static function add_keywords_quick($data,$uid,$agent_status=0,$admin_status=0,$add_count = 0,$have_counted){
        $keywordsList=explode(',',str_replace('|',',',$data['keywords']));
        $keywordsList = array_flip(array_flip($keywordsList));
        
        $urlData=[
            'uid'=>$uid,
            'url'=>$data['web_url'],
            'xiongzhang'=>$data['xiongzhang'],
        ];

        if($data['search_ngines'] ==1){
            $keywordsInfo = 'keywordsPC';
            $keywordsinfoStr ='keywords_p_c' ;
        }else{
            $keywordsInfo = 'keywordsMobile';
            $keywordsinfoStr = 'keywords_mobile';
        }
        $seo = new Seo;
        
        $webModel = new WebUrl;
        $webModel->startTrans();
        $webid=0;

        $configmodel = new SystemConfig;   //获取结算规则
        $settlement_type = $configmodel->alias('k')
                            ->join('customer c','c.upid = k.agent_id')
                            ->where(['c.id'=>$uid,'k.name'=>'site_settlement'])->value('value');

        try{

            $webObj=$webModel->get(['url'=>$urlData['url']]);

            if (empty($webObj))
            {
                //旧方法 seo_web_url 表中url字段带有http做一下查询判断
                $http = "http://";
                $webObj=$webModel->get(['url'=>$http.$urlData['url']]);
            }

            if(!empty($webObj)){
                $kewordlist=Keywords::where(array('web_id'=>$webObj->id,'search_ngines'=>$data['search_ngines'],'keywords'=>$keywordsList))->field('keywords')->select()->toArray();
//                    $kewordlist=Keywords::where(array('search_ngines'=>$data['search_ngines'],'keywords'=>$keywordsList, 'delete_time'=>null))->field('keywords')->select()->toArray();

                $webObj = $webObj->toArray();
                if(!empty($kewordlist)){


                    $keywrodsArr = array_column($kewordlist,'keywords');

                    $keywrodsArr =array_diff($keywordsList,$keywrodsArr);


                    if(empty($keywrodsArr)){
                        $save = [];
                        $save[] = $data['username'];
                        $save[] = $data['keywords'];
                        $save[] = $data['web_url'];
                        $save[] = $data['search_ngines'];
                        $save[] = $data['price'];
                        $save_count = $have_counted."<br>".implode(',',$save);
//                        $msg = "已添加 {$add_count} 条,关键字:{$kewordlist[0]['keywords']},搜索引擎:{$data['search_ngines']}已存在";
                        $msg = "<br>添加失败！<br>{$save_count}<br>数据已存在！";
                        return ['code'=>1,'msg'=>$msg,'count'=>$add_count,'save_count'=>$save_count];
                    }
                }
                $webid=$webObj['id'];
                // $webObj->save($urlData);
            }  else {
                $webModel->allowField(true)->save($urlData);
                $webid=$webModel->id;
            }

                $keywordData=[];
                $time=time();
                foreach ($keywordsList as $v){
                    if($v){
                        $res = $seo->getTaskId([
                                    'url'=>$data['web_url'],
                                    'keywords' =>$v,
                                    'checkrow' =>100
                                ],$data['search_ngines']);
                        if($res){
                            if($res['code']=="0"){
                               $task_number=$res['data'];
                            }else{
                                return ['code'=>1,'msg'=>$res['msg']];
                            }
                        }


                        if($settlement_type == 2)  //if($settlement_type == 2)
                        {
                            //获取用户的指数排名
                            $index_result=json_decode($seo->getBaiduIndex($v),true);
                            if( $index_result['StateCode']==1 &&$index_result['Reason']=='成功' )
                            {
                                // $baiduPcArray = explode(',', $index_result['Result']['BaiduPc']);
                                // $BaiduMobileArray = explode(',', $index_result['Result']['BaiduMobile']);
                                // $BaiduPc = $index_result['Result']['BaiduPc']?end($baiduPcArray):0;
                                // $BaiduMobile = $index_result['Result']['BaiduMobile']?end($BaiduMobileArray):0;

                                $baiduAllArray = explode(',', $index_result['Result']['BaiduAll']);   
                                $BaiduAll = $index_result['Result']['BaiduAll']?end($baiduAllArray):0;
                                $BaiduPc = $BaiduAll;
                                $BaiduMobile = $BaiduAll;
                                switch ($data['search_ngines']) {
                                    case 1:
                                        $tempindex = $BaiduPc;
                                        break;
                                    
                                    default:
                                        $tempindex = $BaiduMobile;
                                        break;
                                }
                            }else
                            {
                                $tempindex = 0;
                                $BaiduPc = 0;
                                $BaiduMobile = 0;
                            }
                            //$data['fee']=db('fee')->where("minnum < $tempindex")->where("maxnum > $tempindex")->value('fee');
                            $data['fee'] = db('fee')->alias('f')
                                ->join('customer c','c.member_level = f.group_id')
                                ->where("f.minnum <= $tempindex")->where("f.maxnum >= $tempindex")->where("c.id = $uid")->value('fee');
                            
                            $data['fee'] = $data['fee']?$data['fee']:0;    
                            $agent_fee = self::getAgentPrice($uid,$tempindex,$data['search_ngines']);
                            $keywordData[]=[
                                'uid'=>$uid,
                                'web_url'=>$data['web_url'],
                                'web_id'=>$webid,
                                'task_number'=>$task_number,
                                'keywords'=>$v,
                                'search_ngines'=>$data['search_ngines'],
                                'current_ranking'=>0,
                                'cycle'=>$data['cycle']*30,
                                'create_time'=>$time,
                                'BaiduPc'=>$BaiduPc,
                                'BaiduMobile'=>$BaiduMobile,
                                'fee'=>$data['fee'],
                                'agent_price'=>$agent_fee,
                                'agent_status'=>$agent_status,
                                'status'=>$admin_status,
                                'xiongzhang'=>$data['xiongzhang'],
                                'start_time'=>time(),
                                //'billing_mode'=>$data['billing_mode'],
                            ];
                        }
                        else
                        {
                            $keywordData[]=[
                                'uid'=>$uid,
                                'web_url'=>$data['web_url'],
                                'web_id'=>$webid,
                                'task_number'=>$task_number,
                                'keywords'=>$v,
                                'search_ngines'=>$data['search_ngines'],
                                'current_ranking'=>0,
                                'cycle'=>$data['cycle']*30,
                                'create_time'=>$time,
                                'fee'=>0,
                                'price'=>self::getPrice($uid,$data['search_ngines'],$v),
                                'agent_price'=>self::getAgentPrice($uid,0,$data['search_ngines']),
                                'agent_status'=>$agent_status,
                                'status'=>$admin_status,
                                'xiongzhang'=>$data['xiongzhang'],
                                'start_time'=>time(),
                                //'billing_mode'=>$data['billing_mode'],
                            ];
                        }

                    }

                }
                if(count($keywordData)>0){

                    if(Keywords::insertAll($keywordData))
                    {
                        $resid = Keywords::getLastInsID();
                        if($uid != 78){
                            //闲置词库 id = 78
                            //闲置的链接被使用了，则删除   无论哪一个端口被占用，剩余的该链接闲置词都将删除
                            $model = new \app\common\model\Keywords;
                            $del_sql = "DELETE FROM `seo_keywords` WHERE ( `web_url` = ? AND `delete_time` IS NOT NULL )";
                            $model->execute($del_sql, [$data['web_url']]);
                            //例：id.m.shop.cnlist.org<=>id.shop.cnlist.org ; 此类冲突删除方法
                            if (strstr($data['web_url'],'cnlist')||strstr($data['web_url'],'qth58')||strstr($data['web_url'],'liebiao')||strstr($data['web_url'],'52bjw')||strstr($data['web_url'],'jinanfa'))
                            {
                                if (strstr($data['web_url'],'m.'))
                                {
                                    $data['web_url'] = str_replace('.m','',$data['web_url']);
                                }
                                else
                                {
                                    if (strstr($data['web_url'],'.shop'))
                                    {
                                        $data['web_url'] = str_replace('.shop','.shop.m',$data['web_url']);
                                    }
                                    elseif (strstr($data['web_url'],'.b2b'))
                                    {
                                        $data['web_url'] = str_replace('.b2b','.b2b.m',$data['web_url']);
                                    }
                                }
                                $del_sql = "DELETE FROM `seo_keywords` WHERE ( `web_url` = ? AND `delete_time` IS NOT NULL )";
                                $model->execute($del_sql, [$data['web_url']]);
                            }
                        }
                        $webModel->commit();
                        $add_count++;
                        $save_count = $have_counted ? $have_counted : "0";
//                        $msg = "添加成功,最后一条数据为：{$data['keywords']},{$data['web_url']},{$data['search_ngines']}";
                        $msg = "存在客户数据 {$save_count} 条无需添加！";
                        return ['code'=>0,'msg'=>$msg,'task_id'=>$task_number,'id'=>$resid,'count'=>$add_count];
                    }
                    else
                    {
                        $webModel->rollback();
                        return $res;
                    }
                };

                
            $webModel->rollback();
            return ['code'=>1,'msg'=>'添加失败'];
        }catch (Exception $e){
            $webModel->rollback();
            return ['code'=>1,'msg'=>$e->getMessage()];
        }
    }

    /**
     * 添加闲置词
     * @param $data
     * @param $uid
     * @param int $agent_status
     * @param int $admin_status
     * @return array
     */
    public static function add_keywords_idle($data,$uid,$agent_status=0,$admin_status=0,&$have_count = 0,&$add_count=0){
        $keywordsList=explode(',',str_replace('|',',',$data['keywords']));
        $keywordsList = array_flip(array_flip($keywordsList));
        $model = new \app\common\model\Keywords;
        $sql = "SELECT id,delete_time FROM `seo_keywords` WHERE ( `web_url` = ? AND `keywords`=? AND search_ngines=?) OR (`web_url` = ? AND `delete_time` is null )";
        $repeat_count = 0;
        $have_url = [];
        try{
            $keywordData=[];
            $time=time();
            foreach ($keywordsList as $v){
                if($v){
                    $seo = new Seo;
                    $res = $seo->getTaskId([
                        'url'=>$data['web_url'],
                        'keywords' =>$v,
                        'checkrow' =>100
                    ],$data['search_ngines']);
                    if($res){
                        if($res['code']=="0"){
                            $task_number=$res['data'];
                        }else{
                            return ['code'=>1,'msg'=>$res['msg']];
                        }
                    }
                    $exist = $model->query($sql, [$data['web_url'],$v,$data['search_ngines'],$data['web_url']]);
                    if($exist){
                        //链接已经存在，认为添加成功
                        $repeat_count++;
                        foreach ($exist as $kk => $vv){
                            if(empty($vv['delete_time']) && !in_array($data['web_url'],$have_url)){
                                $have_url[] = $data['web_url'];
                                $have_count++;
                            }
                        }
                        continue;
                    }
                    $keywordData[]=[
                        'uid'=>$uid,
                        'web_url'=>$data['web_url'],
                        'task_number'=>$task_number,
                        'keywords'=>$v,
                        'search_ngines'=>$data['search_ngines'],
                        'current_ranking'=>0,
                        'cycle'=>$data['cycle']*30,
                        'create_time'=>$time,
                        'fee'=>0,
                        'price'=>$data['price'],
                        'agent_status'=>$agent_status,
                        'status'=>$admin_status,
                        'xiongzhang'=>$data['xiongzhang'],
                        'start_time'=>$time,
                        'delete_time'=>$time
                    ];

                }

            }
            if(count($keywordData)>0){
                if(Keywords::insertAll($keywordData))
                {
                    $add_count++;
                    $resid = Keywords::getLastInsID();
                    $msg = '存在客户链接'.$have_count.'条无需添加！';
                    return ['code'=>0,'msg'=>$msg,'task_id'=>$task_number,'id'=>$resid,'uid'=>$uid,'count'=>$add_count];
                }
                else
                {
                    return $res;
                }
            }
            $msg = '存在客户链接'.$have_count.'条条无需添加！';
            return ['code'=>1,'msg'=>$msg,'uid'=>$uid,'count'=>$add_count];
        }catch (Exception $e){
            return ['code'=>1,'msg'=>$e->getMessage()];
        }
    }
    /**
     * 更新排名 批量
     * @RULE /v1/keywords/add.html
     * @param Request $request
     * @return \think\response\Json
     * @throws \think\exception\PDOException
     */
    public static function update_keywords_rank_bat($uid){
        $keywordModel = new Keywords;
        $keywordsInfo = $keywordModel->field('*,search_ngines as search_ngines_id')->where('uid = '.$uid.' and rank_time IS NULL and status = 0')->select();


        foreach ($keywordsInfo as $key => $value) {

            if(date('d', $value->rank_time ) != date('d', time()) || ($value->current_ranking > 10 || $value->current_ranking==0) )
            {
                $seo = new Seo;
                $res = $seo -> getBaiduRank($value->task_number,$value->search_ngines_id,$uid);
                $rank = $res['rank'];
                if ($rank) {
                    
                    //更新用户排名
                    
                    $update_data = [
                        'current_ranking'   =>$rank,
                        'rank_time'         =>time()
                    ];
                    if (!$value->original_rank) {
                        $update_data['original_rank'] = $rank;
                    }
                    Keywords::where('id', $value->id)->update($update_data);

                    continue;
                    
                }
                else
                {
                    $update_data = [
                        'rank_time'         =>time()
                    ];
                    if(date('d', $value->rank_time ) != date('d', time()))
                    {
                        $update_data['current_ranking'] = 0;
                    }
                    Keywords::where('id', $value->id)->update($update_data);

                    continue;
                }
            }
            else{
                continue;
            }
        }
        
        return ['code'=>0,'msg'=>'更新成功','error'=>'1001'];
    }
    /**
     * 更新排名
     * @RULE /v1/keywords/add.html
     * @param Request $request
     * @return \think\response\Json
     * @throws \think\exception\PDOException
     */
    public static function update_keywords_rank($data,$uid){
        $keywordModel = new Keywords;
        $keywordsInfo = $keywordModel->field('*,search_ngines as search_ngines_id')->get(['id'=>$data['tid'],'uid'=>$uid]);
        
        if(empty($keywordsInfo)){
           return ['code'=>1];
        }
        // if(( time() - strtotime($keywordsInfo['create_time'])) < 60)
        // {
        //     return ['code'=>0,'msg'=>'数据更新中，请稍后再试'];
        // }
        if(date('d', $keywordsInfo->rank_time ) != date('d', time()) || ($keywordsInfo->current_ranking > 10 || $keywordsInfo->current_ranking==0) )
        {
            $seo = new Seo;
            $res = $seo -> getBaiduRank($keywordsInfo->task_number,$keywordsInfo->search_ngines_id,$uid);
            $rank = $res['rank'];
            if ($rank) {
                
                //更新用户排名
                
                $update_data = [
                    'current_ranking'   =>$rank,
                    'rank_time'         =>time()
                ];
                if (!$keywordsInfo->original_rank) {
                    $update_data['original_rank'] = $rank;
                }
                Keywords::where('id', $keywordsInfo->id)->update($update_data);

                return ['code'=>0,'msg'=>'更新成功','id'=>$keywordsInfo->id];
                
            }
            else
            {
                $update_data = [
                    'rank_time'         =>time()
                ];
                if(date('d', $keywordsInfo->rank_time ) != date('d', time()))
                {
                    $update_data['current_ranking'] = 0;
                }
                Keywords::where('id', $keywordsInfo->id)->update($update_data);
                return ['code'=>0,'msg'=>'没有排名','error'=>'1003'];
            }
        }
        else{
            return ['code'=>0,'msg'=>'今天已经更新过了','error'=>'1002'];
        }

        return ['code'=>0,'msg'=>'数据更新中','error'=>'1001'];
    }
    
     /**
     * 系统更新排名
     * @RULE /v1/keywords/add.html
     * @param Request $request
     * @return \think\response\Json
     * @throws \think\exception\PDOException
     */
    public static function system_update_keywords_rank($data,$uid){
       

        $isbatch = false;
        if(is_array($data['tid']))
        {
            $isbatch =true;
            $tid = implode(",",$data['tid']);
        }
        else
        {

            $tid = $data['tid'];
        }
        $keywordModel = new Keywords;
        $keywordsList = $keywordModel->field('*,search_ngines as search_ngines_id')->where('id','in',$tid)->select();
        if(empty($keywordsList)){
           return ['code'=>1];
        }
        foreach ($keywordsList as $key => $keywordsInfo) {
            if($isbatch && $keywordsInfo->is_submit == 1)   //批量更新时候不更新三方平台的
            {
                continue;
            }
           if(( time() - strtotime($keywordsInfo['create_time'])) < 60)
            {
                if($isbatch)   //批量更新直接跳过不返回错误信息
                {
                    continue;
                }else
                {
                    return ['code'=>0,'msg'=>'数据更新中，请稍后再试'];
                }
            }
//            if((date('d', $keywordsInfo->rank_time ) != date('d', time())) || ($keywordsInfo->current_ranking > 10 || $keywordsInfo->current_ranking!=0))  //今天没有更新过 或者已经更新单排名>10 更新
            if(true)  //全部都要更新
            {
                $seo = new Seo;
                $res = $seo -> getBaiduRank($keywordsInfo->task_number,$keywordsInfo->search_ngines_id,$keywordsInfo->uid);   //提交到三方平台就去三方平台取排名数据
                $rank = $res['rank'];
                
                if ($rank) {
                    //更新每日反馈词排名
                    if (isset($data['feedback']))
                    {
                        $updare_feedback_data = [
                          'new_rank' => $rank,
                          'refresh_time'  => time()
                        ];
                        Feedback::where('id', $data['feedback_id'])->update($updare_feedback_data);
                        continue;
                    }
                    //更新用户排名

                    $update_data = [
                        'current_ranking'   =>$rank,
                        'rank_time'         =>time()
                    ];
                    if (!$keywordsInfo->original_rank) {
                        $update_data['original_rank'] = $rank;
                    }
                    Keywords::where('id', $keywordsInfo->id)->update($update_data);
                    if($isbatch)
                    {
                        continue;
                    }else
                    {
                        return ['code'=>0,'msg'=>'更新成功','id'=>$keywordsInfo->id];
                    }
                    
                }
                else
                {
                    $update_data = [
                        'rank_time'         =>time()
                    ];
                    if(date('d', $keywordsInfo->rank_time ) != date('d', time()))
                    {
                        $update_data['current_ranking'] = 0;
                    }
                    Keywords::where('id', $keywordsInfo->id)->update($update_data);

                    if($isbatch)
                    {
                        continue;
                    }else
                    {
                        return ['code'=>0,'msg'=>'排名大于100','id'=>$keywordsInfo->id];
                    }
                }
            }
            else{
                if($isbatch)
                {
                    continue;
                }else
                {
                    return ['code'=>0,'msg'=>'今天已经更新过了','id'=>$keywordsInfo->id];
                }
            }
        }
 

        return ['code'=>0,'msg'=>'操作成功','rank'=>$rank];
    }

    /**
     * 每日系统自动更新排名（for /cron/update_ranking_new.php）
     * @RULE /cron/update_ranking_new.php
     * @param Request $request
     * @return \think\response\Json
     * @throws \think\exception\PDOException
     */
    public static function system_update_keywords_rank_day($data,$uid){
        $testing = 0;
        $isbatch = false;
        if(is_array($data['tid']))
        {
            $isbatch =true;
            $tid = implode(",",$data['tid']);
        }
        else
        {
            $tid = $data['tid'];
        }
        $keywordModel = new Keywords;
        $keywordsList = $keywordModel->field('*,search_ngines as search_ngines_id')->where('id','in',$tid)->select();
        if(empty($keywordsList)){
            return ['code'=>1];
        }
        foreach ($keywordsList as $key => $keywordsInfo) {
            if($isbatch && $keywordsInfo->is_submit == 1)   //批量更新时候不更新三方平台的
            {
                continue;
            }
            if(( time() - strtotime($keywordsInfo['create_time'])) < 60)
            {
                if($isbatch)   //批量更新直接跳过不返回错误信息
                {
                    continue;
                }else
                {
                    return ['code'=>0,'msg'=>'数据更新中，请稍后再试'];
                }
            }
            if(true)  //全部都要更新
            {
                $seo = new Seo;
                $res = $seo -> getBaiduRank_day($keywordsInfo->task_number,$keywordsInfo->search_ngines_id,$keywordsInfo->uid);   //提交到三方平台就去三方平台取排名数据
                $rank = $res['rank'];
                if ($rank) {
                    //第一次非首页
                    if ($keywordsInfo->testing == 0 && $rank > 10)
                    {
                        $testing = 1;
                    }
                    //第一次首页
                    elseif ($keywordsInfo->testing == 0 && $rank <= 10)
                    {
                        $testing = 2;
                    }
                    //第二次
                    elseif ($keywordsInfo->testing == 1 || $keywordsInfo->testing == 2)
                    {
                        $testing = 3;
                    }
                    //第三次
                    elseif ($keywordsInfo->testing == 3)
                    {
                        $testing = 0;
                    }
                    //更新用户排名
                    $update_data = [
                        'current_ranking'   =>$rank,
                        'rank_time'         =>time(),
                        'testing'           =>$testing
                    ];
                    if (!$keywordsInfo->original_rank) {
                        $update_data['original_rank'] = $rank;
                    }
                    Keywords::where('id', $keywordsInfo->id)->update($update_data);
                    if($isbatch)
                    {
                        continue;
                    }else
                    {
                        return ['code'=>0,'msg'=>'更新成功','id'=>$keywordsInfo->id, 'rank'=>$rank];
                    }
                }
                else
                {
                    $update_data = [
                        'rank_time'         =>time()
                    ];
                    if(date('d', $keywordsInfo->rank_time ) != date('d', time()))
                    {
                        $update_data['current_ranking'] = 0;
                    }
                    Keywords::where('id', $keywordsInfo->id)->update($update_data);

                    if($isbatch)
                    {
                        continue;
                    }else
                    {
                        return ['code'=>0,'msg'=>'排名大于100','id'=>$keywordsInfo->id];
                    }
                }
            }
            else{
                if($isbatch)
                {
                    continue;
                }else
                {
                    return ['code'=>0,'msg'=>'今天已经更新过了','id'=>$keywordsInfo->id];
                }
            }
        }
        return ['code'=>0,'msg'=>'操作成功','rank'=>$rank];
    }


    /**
     * 周末闲置词库更新排名
     * @RULE /v1/keywords/add.html
     * @param Request $request
     * @return \think\response\Json
     * @throws \think\exception\PDOException
     */
    public static function system_update_keywords_rank_xian($data,$uid){


        $isbatch = false;
        if(is_array($data['tid']))
        {
            $isbatch =true;
            $tid = implode(",",$data['tid']);
        }
        else
        {

            $tid = $data['tid'];
        }
        $keywordModel = Keywords::withTrashed();
        $keywordsList = $keywordModel->field('*,search_ngines as search_ngines_id')->where('id','in',$tid)->select();
        if(empty($keywordsList)){
            return ['code'=>1];
        }
        foreach ($keywordsList as $key => $keywordsInfo) {
            if($isbatch && $keywordsInfo->is_submit == 1)   //批量更新时候不更新三方平台的
            {
                continue;
            }
            if(( time() - strtotime($keywordsInfo['create_time'])) < 60)
            {
                if($isbatch)   //批量更新直接跳过不返回错误信息
                {
                    continue;
                }else
                {
                    return ['code'=>0,'msg'=>'数据更新中，请稍后再试'];
                }
            }
//            if((date('d', $keywordsInfo->rank_time ) != date('d', time())) || ($keywordsInfo->current_ranking > 10 || $keywordsInfo->current_ranking!=0))  //今天没有更新过 或者已经更新单排名>10 更新
            if(true)  //全部都要更新
            {
                $seo = new Seo;
                $res = $seo -> getBaiduRank_xian($keywordsInfo->task_number,$keywordsInfo->search_ngines_id);   //提交到三方平台就去三方平台取排名数据
                $rank = $res['rank'];

                if ($rank) {

                    //更新用户排名

                    $update_data = [
                        'current_ranking'   =>$rank,
                        'rank_time'         =>time()
                    ];
                    if (!$keywordsInfo->original_rank) {
                        $update_data['original_rank'] = $rank;
                    }
                    Keywords::withTrashed()->where('id', $keywordsInfo->id)->update($update_data);
                    if($isbatch)
                    {
                        continue;
                    }else
                    {
                        return ['code'=>0,'msg'=>'更新成功','id'=>$keywordsInfo->id];
                    }

                }
                else
                {
                    $update_data = [
                        'rank_time'         =>time()
                    ];
                    if(date('d', $keywordsInfo->rank_time ) != date('d', time()))
                    {
                        $update_data['current_ranking'] = 0;
                    }
                    Keywords::where('id', $keywordsInfo->id)->update($update_data);

                    if($isbatch)
                    {
                        continue;
                    }else
                    {
                        return ['code'=>0,'msg'=>'排名大于100','id'=>$keywordsInfo->id];
                    }
                }
            }
            else{
                if($isbatch)
                {
                    continue;
                }else
                {
                    return ['code'=>0,'msg'=>'今天已经更新过了','id'=>$keywordsInfo->id];
                }
            }
        }


        return ['code'=>0,'msg'=>'操作成功'];
    }

     /**
     * 系统手动结算
     * @RULE /v1/keywords/add.html
     * @param Request $request
     * @return \think\response\Json
     * @throws \think\exception\PDOException
     */
    public static function system_settlement_keywords_rank($data,$uid){
        $keywordModel = new Keywords;
        $Customer=new Customer();
        

        $isbatch = false;
        if(is_array($data['tid']))
        {
            $isbatch =true;
            $tid = implode(",",$data['tid']);
        }
        else
        {
            
            $tid = $data['tid'];
        }
        $keywordsList = $keywordModel->where('id','in',$tid)->select();



        $configmodel = new SystemConfig;
        $site_settlement = $configmodel->get(['name'=>'site_settlement']);
        $settlement_type = $site_settlement['value'];

        if(empty($keywordsList)){
           return ['code'=>0,'msg'=>'未获取到数据'];
        }

        foreach ($keywordsList as $key => $value) {
            if($value['current_ranking'] >= 1 && $value['current_ranking'] <= 10)  //今天没有更新过 或者已经更新单排名>10 更新
            {
                if(!$Customer->getUserBalanceToday($value['uid'],$value['keywords'],$value['web_url'],$value['id']))
                {
                    //获取用户的扣费金额
                    if($value['price'] > 0 )   //price字段优先，再更具系统设置 使用指数扣费或者用户等级扣费
                    {
                        $keyword_free = $value['price'];

                    }
                    else
                    {
                        if($settlement_type==2 && $value['fee'] > 0)
                        {
                            $keyword_free=$value['fee'];

                        }else
                        {
                            $keyword_free = Db::field('group.keyword_free')
                            ->table(['seo_customer'=>'customer','seo_user_group'=>'group'])
                            ->where('group.id = customer.member_level')
                            ->where('customer.id= '.$value['uid'])
                            ->value('keyword_free');

                        }
                    }
                    Keywords::where('id', $value['id'])->update([
                        'standard'              =>$value['standard']+1,
                        'complete_time'         =>time()
                    ]);
                    $Customer->editUserBalanceDetail($value['uid'],-$keyword_free,$value['web_url'],$value['keywords'],"关键字排名扣费",1,$value['id']);

                    $agent_id = $Customer->getMemberAgentid($value['uid']);
                    
                    if($agent_id)
                    {
                        $res = $Customer->editUserBalanceDetail($agent_id,-$value['agent_price'],$value['web_url'],$value['keywords'],"代理商关键字排名扣费",3,$value['id']);
                        if($res['code'])
                        {
                            return ['code'=>1,'msg'=>$res['msg']];
                        }
                    }
                    if(!$isbatch)
                    {
                        return ['code'=>0,'msg'=>'结算成功'];
                    }
                    
                }
                else 
                {
                    
                    if(!$isbatch)
                    {
                       return ['code'=>0,'msg'=>'该关键词今天已经计算过'];
                    }
                }
            }
            else{
                
                if(!$isbatch)
                {
                   return ['code'=>0,'msg'=>'排名未到前10不结算'];
                }
            }
        }

        return ['code'=>0,'msg'=>'操作成功'];

        
    }

    /**
     * 指定时间手动结算
     * @RULE /v1/keywords/add.html
     * @param Request $request
     * @return \think\response\Json
     * @throws \think\exception\PDOException
     */
    public static function system_date_settlement_keywords_rank($data,$uid){
        $keywordModel = new Keywords;
        $Customer=new Customer();
        

        $id = $data['id'];
        $complete_time = $data['date']?strtotime($data['date']):time();

        $price =  $data['price'];
        $agent_price =  $data['agent_price'];
        $agent_id =  $data['agent_id'];

        $keywords = $keywordModel->where('id',$id)->find();
        if($keywords)
        {

            Keywords::where('id', $id)->update([
                        'standard'              =>$keywords['standard']+1,
                        'complete_time'         =>$complete_time
                    ]);

            $Customer->editUserBalanceDetail($keywords['uid'],-$price,$keywords['web_url'],$keywords['keywords'],"关键字排名扣费",1,$keywords['id'],$complete_time);

            $res = $Customer->editUserBalanceDetail($agent_id,-$agent_price,$keywords['web_url'],$keywords['keywords'],"代理商关键字排名扣费",3,$keywords['id'],$complete_time);
                        
            return ['code'=>0,'msg'=>'操作成功'];
        }
        else
        {
            return ['code'=>1,'msg'=>'操作失败'];
        }
        

        
    }
    /**
     * 统计
     * @RULE /v1/keywords/add.html
     * @param Request $request
     * @return \think\response\Json
     * @throws \think\exception\PDOException
     */
    public static function get_count($uid){
        $beginThismonth = mktime(0,0,0,date('m'),1,date('y'));  //这个月初始的时间戳
        $todayEnd= strtotime(date('Y-m-d 23:59:59', time()));//今天时间戳
        $thismonthcount=ceil(abs(($beginThismonth-$todayEnd)/(3600*24)));//这个月的天数
        $date['thismonth']=array();
        for ($i=1; $i <$thismonthcount+1 ; $i++) { 
            $starttime=$beginThismonth+3600*24*($i-1);
            $endtime=$beginThismonth+3600*24*$i;
            $count=db('mingxi')->where("create_time >$starttime")->where('uid',$uid)->where("create_time <$endtime and type = 1")->count();
            array_push($date['thismonth'], $count);
        }
        $nextbeginThismonth=strtotime(date('Y-m-01', strtotime('-1 month')));
        $nextEnd=strtotime(date('Y-m-t', strtotime('-1 month')));
        $tnextmonthcount=ceil(abs(($nextbeginThismonth-$nextEnd)/(3600*24)));//下个月的天数
        $date['nextmonth']=array();
        for ($i=1; $i <$tnextmonthcount+1 ; $i++) { 
            $starttime=$nextbeginThismonth+3600*24*($i-1);
            $endtime=$nextbeginThismonth+3600*24*$i;
            $count=db('mingxi')->where("create_time >$starttime")->where('uid',$uid)->where("create_time <$endtime and type = 1")->count();
            array_push($date['nextmonth'], $count);
        }
        $date['code']=0;
        return $date;
    }

    public static function getPrice($uid,$search_ngines = 1,$keywords = '')  //获取用户的价格
    {
        $engines = array('1'=>'baidupcs', '2'=>'baidumobiles', '3'=>'360pcs', '4'=>'360mobiles', '5'=>'sougoupcs', '6'=>'sougoumobiles');
        $Customer=new Customer();
        $upid = $Customer->getMemberAgentid($uid);
        $configmodel = new SystemConfig;
        $engine_config = $configmodel->where(array('agent_id'=>$upid,'name'=>$engines[$search_ngines]))->find();
        if($engine_config)
        {
            $agent_price_ratio = $engine_config['value'] / 100;
        }
        else
        {
            $agent_price_ratio = 1;
        }

        $agent_price = self::getCustomerPrice($uid,$keywords);

        return $agent_price_ratio > 0 ? round($agent_price * $agent_price_ratio,2) : $agent_price;
    }
     public static function getPriceall($uid,$keywords)
    {
        $engines = array('baidupcs'=>'1', 'baidumobiles'=>'2', '360pcs'=>'3', '360mobiles'=>'4', 'sougoupcs'=>'5', 'sougoumobiles'=>'6');
        $Customer=new Customer();
        $upid = $Customer->getMemberAgentid($uid);
        $configmodel = new SystemConfig;
        $engine_config = $configmodel->field('group,title,name,value')->where(array('agent_id'=>$upid,'group'=>'engines'))->select();

        $agent_price = self::getCustomerPrice($uid,$keywords);
        
        foreach ($engine_config as $key => $value) {
            if($value['value'] <= 0)
            {
                unset($engine_config[$key]);
            }
            $engine_config[$key]['search_ngines'] = $engines[$value['name']];
        }
        $data['price'] = $agent_price;
        $data['engines'] = $engine_config;
        return $data;
    }
    public static function getCustomerPrice($uid,$keywords){
        $configmodel = new SystemConfig;
        $Customer=new Customer();
        $agent_id = $Customer->getMemberAgentid($uid);

        $site_settlement = $configmodel->get(['name'=>'site_settlement','agent_id'=>$agent_id]);
        $settlement_type_system = $site_settlement['value'];   //会员结算规则
        
        if($settlement_type_system == 2)  //指数结算
        {
            
            $seo = new Seo;
            $index_result=json_decode($seo->getBaiduIndex($keywords),true);

           
            if( $index_result['StateCode']==1 &&$index_result['Reason']=='成功' )
            {

                $baiduAllArray = explode(',', $index_result['Result']['BaiduAll']);   
                $BaiduAll = $index_result['Result']['BaiduAll']?end($baiduAllArray):0;
                $BaiduPc = $BaiduAll;
                $BaiduMobile = $BaiduAll;
                $index = $BaiduPc;
            }else
            {
                $index = 0;
            }




            $customer_price = Fee::alias('f')
                ->field('f.fee as keyword_free')
                ->join('customer c','c.member_level = f.group_id')
                ->where("f.minnum <= $index")->where("f.maxnum >= $index")->where("c.id = ".$uid)->value('fee');
            
            if(!$customer_price) //未在设置范围内 或 未设置
            {
                $customer_price = Db::field('group.keyword_free')
                    ->table(['seo_customer'=>'customer','seo_user_group'=>'group'])
                    ->where('group.id = customer.member_level ')  // and group.agent_id = \'null\'
                    ->where('customer.id= '.$uid)
                    ->value('keyword_free');
            }
        }
        else
        {
            $customer_price = Db::field('group.keyword_free')
                ->table(['seo_customer'=>'customer','seo_user_group'=>'group'])
                ->where('group.id = customer.member_level ')  // and group.agent_id = \'null\'
                ->where('customer.id= '.$uid)
                ->value('keyword_free');
        }

        return $customer_price;
    }
    public static function getAgentPrice($uid,$tempindex=0,$search_ngines = 1)   //用户id ，指数 ,搜索引擎  //代理商的价格
    {
        $engines = array('1'=>'baidupcs', '2'=>'baidumobiles', '3'=>'360pcs', '4'=>'360mobiles', '5'=>'sougoupcs', '6'=>'sougoumobiles');
        $Customer=new Customer();
        $upid = $Customer->getMemberAgentid($uid);
        if($upid)
        {
            $configmodel = new SystemConfig;
            $system_config = $configmodel->where(array('agent_id'=>999999,'name'=>'site_settlement'))->find();
            $engine_config = $configmodel->where(array('agent_id'=>999999,'name'=>$engines[$search_ngines]))->find();
            $agent_price_ratio = $engine_config['value'] / 100;
            
            $agent_price = 0;
            switch ($system_config['value']) {
                case 1:
                    $agent_price = Db::field('group.keyword_free')
                        ->table(['seo_customer'=>'customer','seo_user_group'=>'group'])
                        ->where('group.id = customer.member_level ')  // and group.agent_id = \'null\'
                        ->where('customer.id= '.$upid)
                        ->value('keyword_free');
                    
                    break;
                case 2:

                    
                    $agent_price = Fee::alias('f')
                            ->join('customer ca','ca.member_level = f.group_id')
                            ->join('customer c','c.upid = ca.id')
                            ->where("f.minnum <= $tempindex")->where("f.maxnum >= $tempindex")->field("ca.fee as fee")->where("c.id = ".$uid)->value('fee');
                    
                    //$agent_price = db('fee')->where("minnum < $tempindex")->where("maxnum > $tempindex")->value('fee');

                    
                    
                    break;
                default:
                    # code...
                    break;
            }
            if(empty($agent_price))  //空就按会员组扣费
            {
                $agent_price = Db::field('group.keyword_free')
                        ->table(['seo_customer'=>'customer','seo_user_group'=>'group'])
                        ->where('group.id = customer.member_level ')  // and group.agent_id = \'null\'
                        ->where('customer.id= '.$upid)
                        ->value('keyword_free');
            }
            return $agent_price_ratio>0?$agent_price * $agent_price_ratio:$agent_price;
        }else
        {
            return 0;
        }
        
    }

    public static function get_rank_log($id,$usertype,$limit=45) //默认查询最近30条
    {
        $log_list = RankLog::alias('log')
                    ->join('keywords k','k.task_number = log.taskid')
                    
                    ->field("*,log.create_time as create_time")
                    ->where("k.id = ".$id)->limit($limit)->order('log.create_time desc')->select()->toArray();
        $data = [];
        $date = [];

        $log_list = array_reverse($log_list); 

        foreach ($log_list as $key => $value) {

           $data[$key] = $value['result']>100?100:$value['result'];
           $date[$key] = $value['create_time'];
        }
        return ['data'=>$data,'date'=>$date];
    }

     public static function getSettlementForUid($uid)
    {
        $configmodel = new SystemConfig;
        $settlement_type = $configmodel->alias('k')
            ->join('customer c','c.upid = k.agent_id')
            ->where(['c.id'=>$uid,'k.name'=>'site_settlement'])->value('value');

        return $settlement_type;
    }     
    public static function getSettlementForAid($aid)
    {
        $configmodel = new SystemConfig;
        $settlement_type = $configmodel->alias('k')
            ->join('customer c','c.id = k.agent_id')
            ->where(['c.id'=>$aid,'k.name'=>'site_settlement'])->value('value');

        return $settlement_type;
    } 
   
   


    public static function request_post($url = '', $param = '') {
        if (empty($url) || empty($param)) {
            return false;
        }
        $postUrl = $url;
        $curlPost = $param;
        $ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
        curl_setopt($ch, CURLOPT_URL,$postUrl);//抓取指定网页
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        $data = curl_exec($ch);//运行curl
        curl_close($ch);
        return $data;
    }
    // public function getAgentPrice($id)
    // {
    //     $keywords_info = Customer::alias('a')
    //                   ->join('keywords k','k.uid = a.id')
    //                   ->where('k.id = '.$id)
    //                   ->find();
    //     if($keywords_info['upid'])
    //     {
    //         $configmodel = new SystemConfig;
    //         $system_config = $configmodel->where(array('agent_id'=>999999,'name'=>'site_settlement'))->find();
    //         $agent_price = 0;
    //         switch ($system_config['value']) {
    //             case 1:
    //                 $agent_price = Db::field('group.keyword_free')
    //                     ->table(['seo_customer'=>'customer','seo_user_group'=>'group'])
    //                     ->where('group.id = customer.member_level ')  // and group.agent_id = \'null\'
    //                     ->where('customer.id= '.$keywords_info['upid'])
    //                     ->value('keyword_free');
                    
    //                 break;
    //             case 2:
    //                 $tempindex = $keywords_info['search_ngines'] == 1? $keywords_info['BaiduPc']:$keywords_info['BaiduMobile'];

                    
    //                 $agent_price = db('fee')->alias('f')
    //                         ->join('customer ca','ca.member_level = f.group_id')
    //                         ->join('customer c','c.upid = ca.id')
    //                         ->where("f.minnum <= $tempindex")->where("f.maxnum >= $tempindex")->field("ca.fee as fee")->where("c.id = ".$keywords_info['uid'])->value('fee');
                    
    //                 //$agent_price = db('fee')->where("minnum < $tempindex")->where("maxnum > $tempindex")->value('fee');

                    
                    
    //                 break;
    //             default:
    //                 # code...
    //                 break;
    //         }

    //         return $agent_price;
    //     }else
    //     {
    //         return 0;
    //     }
        
    // }
}