<?php
/**
 * Created by PhpStorm.
 * User: longhu
 * Date: 2018/12/2
 * Time: 14:38
 */
namespace seo;

use app\common\model\RankLog;
use app\common\model\Keywords;
use app\http\middleware\PlatformCheck;
use helpers\simple_html_dom;

class Seo
{
    protected $baidu_key_pc = '';
    protected $baidu_key_mobile = '';
    protected $x360_key_pc = '';
    protected $x360_key_mobile = '';
    protected $sougou_key_pc = '';
    protected $sougou_key_mobile = '';
    protected $app_key_zhishu = '';
    public function __construct()
    {
        $this->baidu_key_pc  = config('api.baidu_pc_key');
        $this->baidu_key_mobile = config('api.baidu_mobile_key');
        $this->x360_key_pc = config('api.360_pc_key');
        $this->x360_key_mobile = config('api.360_mobile_key');
        $this->sougou_key_pc = config('api.sougou_pc_key');
        $this->sougou_key_mobile = config('api.sougou_mobile_key');
        $this->app_key_zhishu = config('api.baidu_zhihshu_key');
    }

    public  function getTaskId($param = [],$type = 1){
        return ['code'=>0,'data'=>date('His').rand(1000, 9999)];
    }
    /**
     * 提交关键字排名查询【返回taskid】
     * @param array $param
     * @return array|bool  返回数组 或则bool
     */
    // public  function getTaskId($param = [],$type = 1){  //废弃
    //     // if($type ==1){
    //     //     $url ='http://apis.5118.com/morerank/baidupc';
    //     //     $key =  $this->app_key_pc;
    //     // }else{
    //     //     $url ='http://apis.5118.com/morerank/baidumobile';
    //     //     $key =  $this->app_key_mobile;
    //     // }
    //     switch ($type) {
    //         case 1:
    //             $url ='http://apis.5118.com/morerank/baidupc';
    //             $key =  $this->app_key_pc;
    //             break;
    //         case 2:
    //             $url ='http://apis.5118.com/morerank/baidumobile';
    //             $key =  '8922714C7DEE4449BC04B26C498EAAAC';
    //             break;
    //         case 3:
    //             $url ='http://apis.5118.com/morerank/haosou';
    //             $key =  $this->x360_pc_key;
    //             break;
    //         default:
    //             $url ='http://apis.5118.com/morerank/baidupc';
    //             $key =  $this->app_key_pc;
    //             break;
    //     }
    //     if(is_array($param) && !empty($param)){
    //         $str = '';
    //         foreach ( $param as $k => $v )
    //         {
    //             $str.= "$k=" . urlencode( $v ). "&" ;
    //         }
    //         $post_data = substr($str,0,-1);
    //         // dump($post_data);exit;
    //         $res = $this->request_post($url, $post_data,$key);
    //         // dump($res);exit;
    //         if($res){
    //            $data = json_decode($res,true);
    //            //dump($res);exit;
    //            if($data['errcode'] =="0" && empty($data["errmsg"])){
    //                return ['code'=>$data['errcode'],'data'=>$data['data']['taskid']];
    //            }else{
    //                return ['code'=>$data['errcode'],'msg'=>$data['errmsg']];
    //            }
    //         }
    //     }
    //     return false;
    // }

    /**
     * 获取关键字排名详情
     * @param string $taskid
     * @return array|bool
     */
    // public  function getBaiduRank($taskid = '',$type = 1){
    //     if(empty($taskid)){
    //         return false;
    //     }
 
    //     switch ($type) {
    //         case 1:
    //             $url ='http://apis.5118.com/morerank/baidupc';
    //             $key =  $this->app_key_pc;
    //             break;
    //         case 2:
    //             $url ='http://apis.5118.com/morerank/baidumobile';
    //             $key =  $this->app_key_mobile;
    //             break;
    //         case 3:
    //             $url ='http://apis.5118.com/morerank/haosou';
    //             $key =  $this->x360_pc_key;
    //             break;
    //         default:
    //             $url ='http://apis.5118.com/morerank/baidupc';
    //             $key =  $this->app_key_pc;
    //             break;
    //     }

    //     $post_data ='taskid='.$taskid;
    //     $res = $this->request_post($url, $post_data,$key);
    //     $json = json_decode($res);
    //     $errcode = $json->errcode; 
    //     $errmsg = $json->errmsg;     
    //     print_r($json);
    //     if ($errcode == "200104" && $errmsg =="数据采集中") {      
    //         while(true){ 
    //             sleep(1);          
    //             /*调用CURL POST函数*/         
    //             $result = $this->request_post($url, $post_data,$key); 
    //             $errcode = $json->errcode; 
    //             $errmsg = $json->errmsg;    
    //             /*errcode=0 正确返回*/  
    //             if ($errcode == "0" && $errmsg == "") {
    //                 $result = ['code'=>0,'data'=>$result];

    //                 break; 
    //             } 
    //         } 
    //     }else { 
    //         $result = ['code'=>1,'data'=>$json];
            
    //     } 
    //     if($errcode == 0)
    //     {
    //         if($result['data']->data->keywordmonitor[0]->ranks)
    //         {
    //             $rank = $result['data']->data->keywordmonitor[0]->ranks[0]->rank;
    //         }
    //         else
    //         {
    //             $rank = 101;
    //         }
    //     }
    //     else
    //     {
    //         $rank = 101;
    //     }


    //     $this->addRanklog($taskid,$rank,$type,1,$key);
        
    //     return $result;
    // }

    public function getRankingForChinaz($taskid,$url,$key='',$uid=0)   //站长之家api接口
    {
        $info = Keywords::getKeywordsForTaskid($taskid);

        $is_domain['is_domain'] = 0;
        if ($uid)
        {
            $is_domain = db('customer')->field('is_domain')->where('id',$uid)->find();
        }
        // $url = 'http://apidata.chinaz.com/CallAPI/BaiduMobileRanking';
        // $key = '32bb52c51a8d4d01960b8cf8afc429a9';
        $post_data = 'key='.$key.'&domainName='.rtrim($info['web_url'],'/').'&keyword='.$info['keywords'].'&xiongzhangID=';
        
        if($info['xiongzhang'])
        {
            $post_data .= $info['xiongzhang'];
        }
        $res = $this->chinaz_request_post($url, $post_data,$key);
        $res = json_decode($res,1);
       
        $rank = 101;
        if($res['StateCode'] == 1 && $res['Result']['Ranks'][0])
        {
            foreach ($res['Result']['Ranks'] as $one)
            {
                if ($is_domain['is_domain'] == 1) //为1时是完全匹配链接更新
                {
                    if (rtrim(preg_replace("/http\:\/\/|https\:\/\//",'',$one['Url']),'/') == rtrim($info['web_url'],'/'))
                    {
                        $rankStr = $one['RankStr'];
                        $ranks = explode("-",$rankStr);
                        $rank = ($ranks[0] - 1) * 10 + $ranks[1];
                    }
                }
                else
                {
                    $rankStr = $one['RankStr'];
                    $ranks = explode("-",$rankStr);
                    $rank = ($ranks[0] - 1) * 10 + $ranks[1];
                    if ($rank <= 10)
                    {
                        break;
                    }
                }

            }
        }
        if(!$res)
        {
            $res['Reason'] = '请求超时！';
        }
        $this->addRanklog($taskid,$rank,2,1,$key,$res['Reason']);
        return ['code'=>1,'rank'=>$rank];
    }

    public function getRankingForChinaz_day($taskid,$url,$key='',$uid=0)   //站长之家api接口
    {
        $info = Keywords::getKeywordsForTaskid($taskid);
        //查询序次数
        // $testing['testing'] = 1 (非首页)
        // $testing['testing'] = 2 (首页)
        // $testing['testing'] = 3 (第三次更新)
        $testing = db('keywords')->field('testing,current_ranking')->where('task_number',$taskid)->find();

        $is_domain['is_domain'] = 0;
        if ($uid)
        {
            $is_domain = db('customer')->field('is_domain')->where('id',$uid)->find();
        }
        // $url = 'http://apidata.chinaz.com/CallAPI/BaiduMobileRanking';
        // $key = '32bb52c51a8d4d01960b8cf8afc429a9';
        $post_data = 'key='.$key.'&domainName='.rtrim($info['web_url'],'/').'&keyword='.$info['keywords'].'&xiongzhangID=';

        if($info['xiongzhang'])
        {
            $post_data .= $info['xiongzhang'];
        }
        $res = $this->chinaz_request_post($url, $post_data,$key);
        $res = json_decode($res,1);

        $rank = 101;
        if($res['StateCode'] == 1 && $res['Result']['Ranks'][0])
        {
            foreach ($res['Result']['Ranks'] as $one)
            {
                if ($is_domain['is_domain'] == 1) //为1时是完全匹配链接更新
                {
                    if (rtrim(preg_replace("/http\:\/\/|https\:\/\//",'',$one['Url']),'/') == rtrim($info['web_url'],'/'))
                    {
                        $rankStr = $one['RankStr'];
                        $ranks = explode("-",$rankStr);
                        $rank = ($ranks[0] - 1) * 10 + $ranks[1];
                    }
                }
                else
                {
                    $rankStr = $one['RankStr'];
                    $ranks = explode("-",$rankStr);
                    $rank = ($ranks[0] - 1) * 10 + $ranks[1];
                    if ($rank <= 10)
                    {
                        break;
                    }
                }

            }
        }
        if(!$res)
        {
            $res['Reason'] = '请求超时！';
        }
        //第三次直接添加，同正或同负，添加记录，否则进行第三次更新
        //第二次更新且 同为非首页 | 同为首页
        if (($testing['testing'] == 1 && $testing['current_ranking'] > 10 && $rank > 10) || ($testing['testing'] == 2 && $testing['current_ranking'] <= 10 && $rank <= 10) || $testing['testing'] == 3)
        {
            $this->addRanklog($taskid,$rank,2,1,$key,$res['Reason']);
        }

        return ['code'=>1,'rank'=>$rank,'testing'=>$testing['testing']];
    }

    public function getRankingForChinaz_xian($taskid,$url,$key='')   //站长之家api接口
    {
        $info = \db('Keywords')->alias('k')->join('web_url w', 'k.web_url = w.url','LEFT')->field('*,k.xiongzhang as xiongzhangk')->where('k.task_number', $taskid)->find();
        if ($info['xiongzhangk']) {
            $info['xiongzhang'] = $info['xiongzhangk'];

        }
//        $info = Keywords::getKeywordsForTaskid_xian($taskid);

        // $url = 'http://apidata.chinaz.com/CallAPI/BaiduMobileRanking';
        // $key = '32bb52c51a8d4d01960b8cf8afc429a9';
        $post_data = 'key='.$key.'&domainName='.rtrim($info['web_url'],'/').'&keyword='.$info['keywords'].'&xiongzhangID=';

        if($info['xiongzhang'])
        {
            $post_data .= $info['xiongzhang'];
        }
        $res = $this->chinaz_request_post_xian($url, $post_data,$key);
        $res = json_decode($res,1);

        $rank = 101;
        if($res['StateCode'] == 1 && $res['Result']['Ranks'][0])
        {
            foreach ($res['Result']['Ranks'] as $one)
            {
                if (rtrim(preg_replace("/http\:\/\/|https\:\/\//",'',$one['Url']),'/') == rtrim($info['web_url'],'/'))
                {
                    $rankStr = $one['RankStr'];
                    $ranks = explode("-",$rankStr);
                    $rank = ($ranks[0] - 1) * 10 + $ranks[1];
                }
            }
        }
        if(!$res)
        {
            $res['Reason'] = '请求超时！';
        }
        $this->addRanklog($taskid,$rank,2,1,$key,$res['Reason']);
        return ['code'=>1,'rank'=>$rank];
    }

    public function getBaiduMobileRanking($taskid)
    {
        $info = Keywords::getKeywordsForTaskid($taskid);
        $xiongzhang = $info['xiongzhang'];
        $url = 'http://apidata.chinaz.com/CallAPI/BaiduMobileRanking';
        $key = '32bb52c51a8d4d01960b8cf8afc429a9';
        $post_data = 'key='.$key.'&domainName='.rtrim($info['web_url'],'/').'&keyword='.$info['keywords'].'&xiongzhangID='.$xiongzhang;  //南充新丝路烹饪学校
        $res = $this->chinaz_request_post($url, $post_data,$key);
        $res = json_decode($res,1);
        
        $rank = 101;
        if($res['StateCode'] == 1 && $res['Result']['Ranks'][0])
        {
            $rankStr = $res['Result']['Ranks'][0]['RankStr'];
            $ranks = explode("-",$rankStr);
        
            $rank = ($ranks[0] - 1) * 10 + $ranks[1];
        }
        $data['errcode'] = 0;
        $data['errmsg'] = 0;
        $data['data'] = (object)array(
            'taskid'=>123,
            'keywordmonitor'=>array(
                (object)array(
                    'ranks'=>array(
                        (object)array(
                            'rank'=>$rank,
                            'site_url'=>'xxxx'
                        )
                    )
                )
            )
        );
        $data = (object)$data;
        $this->addRanklog($taskid,$rank,2,1,$key);
        return ['code'=>1,'data'=>$data];
    }
   
    public  function getBaiduRank($taskid = '',$type = 1,$uid){
        if(empty($taskid)){
            return false;
        }
 
        switch ($type) {
            case 1:
                $url ='http://apidata.chinaz.com/CallAPI/BaiduPcRanking';
                $key =  $this->baidu_key_pc;
                break;
            case 2:
                $url ='http://apidata.chinaz.com/CallAPI/BaiduMobileRanking';
                $key =  $this->baidu_key_mobile;
                break;
            case 3:
                $url ='http://apidata.chinaz.com/CallAPI/SoPcRanking';
                $key =  $this->x360_key_pc;
                break;
            case 4:
                $url ='http://apidata.chinaz.com/CallAPI/SoMobileRanking';
                $key =  $this->x360_key_mobile;
                break;
            case 5:
                $url ='http://apidata.chinaz.com/CallAPI/SogouPcRanking';
                $key =  $this->sougou_key_pc;
                break;
            case 6:
                $url ='http://apidata.chinaz.com/CallAPI/SogouMobileRanking';
                $key =  $this->sougou_key_mobile;
                break;
            default:
                $url ='http://apidata.chinaz.com/CallAPI/BaiduPcRanking';
                $key =  $this->baidu_key_pc;
                break;
        }

        
        $result = $this->getRankingForChinaz($taskid,$url,$key,$uid);

        //360移动端因接口查询结果严重不准确，所以需要自行查询
        if ($type == 4 && $result['rank'] > 100)
        {
            $info = Keywords::getKeywordsForTaskid($taskid);
            $checkurl = str_replace("http://",'',rtrim($info['web_url'],'/'));
            $header = [
                ':authority: m.so.com',
                ':method: GET',
                ':scheme: https',
                'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
                'accept-encoding: gzip, deflate, br',
                'accept-language: zh-CN,zh;q=0.9,en;q=0.8',
                'cache-control: no-cache',
                'pragma: no-cache',
                'sec-fetch-dest: document',
                'sec-fetch-mode: navigate',
                'sec-fetch-site: none',
                'sec-fetch-user: ?1',
                'upgrade-insecure-requests: 1',
                'user-agent: Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1'
            ];
            for ($i=1; $i<11; $i++)
            {
                $posturl = "https://m.so.com/s?q=".urlencode($info['keywords']);
                if ($i > 1)
                {
                    $posturl .= "&pn={$i}";
                }
                $page = $this->abuyun($posturl,'','',$header,'','gzip');
                if (!$page)
                {
                    $ch = curl_init();//初始化
                    curl_setopt($ch, CURLOPT_URL, $posturl);//访问的URL
                    curl_setopt($ch, CURLOPT_HEADER, false);//设置不需要头信息
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);//只获取页面内容，但不输出
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//https请求 不验证证书
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);//https请求 不验证HOST
                    curl_setopt($ch,CURLOPT_ENCODING,'gzip');//百度返回的内容进行了gzip压缩,需要用这个设置解析
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);//curl模拟头部信息
                    $page = curl_exec($ch);//执行请求
                    curl_close($ch);//关闭curl，释放资
                }
                if (!$page)
                {
                    continue;
                }
                $html = new simple_html_dom;
                $html->load($page);
                $dom = $html->find('.res-site-url');
                if ($dom)
                {
                    foreach ($dom as $key => $d)
                    {
                        if (str_replace(" ",'',$d->text()) == $checkurl)
                        {
                            $result['rank'] = ($i-1) * 10 + $key + 1;
                            break 2;
                        }
                    }
                }
            }
        }

        return $result;
    }

    public  function getBaiduRank_day($taskid = '',$type = 1,$uid){
        if(empty($taskid)){
            return false;
        }

        switch ($type) {
            case 1:
                $url ='http://apidata.chinaz.com/CallAPI/BaiduPcRanking';
                $key =  $this->baidu_key_pc;
                break;
            case 2:
                $url ='http://apidata.chinaz.com/CallAPI/BaiduMobileRanking';
                $key =  $this->baidu_key_mobile;
                break;
            case 3:
                $url ='http://apidata.chinaz.com/CallAPI/SoPcRanking';
                $key =  $this->x360_key_pc;
                break;
            case 4:
                $url ='http://apidata.chinaz.com/CallAPI/SoMobileRanking';
                $key =  $this->x360_key_mobile;
                break;
            case 5:
                $url ='http://apidata.chinaz.com/CallAPI/SogouPcRanking';
                $key =  $this->sougou_key_pc;
                break;
            case 6:
                $url ='http://apidata.chinaz.com/CallAPI/SogouMobileRanking';
                $key =  $this->sougou_key_mobile;
                break;
            default:
                $url ='http://apidata.chinaz.com/CallAPI/BaiduPcRanking';
                $key =  $this->baidu_key_pc;
                break;
        }


        $result = $this->getRankingForChinaz_day($taskid,$url,$key,$uid);

        //360移动端因接口查询结果严重不准确，所以需要自行查询
        if ($type == 4 && $result['rank'] > 100)
        {
            $info = Keywords::getKeywordsForTaskid($taskid);
            $checkurl = str_replace("http://",'',rtrim($info['web_url'],'/'));
            $header = [
                ':authority: m.so.com',
                ':method: GET',
                ':scheme: https',
                'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
                'accept-encoding: gzip, deflate, br',
                'accept-language: zh-CN,zh;q=0.9,en;q=0.8',
                'cache-control: no-cache',
                'pragma: no-cache',
                'sec-fetch-dest: document',
                'sec-fetch-mode: navigate',
                'sec-fetch-site: none',
                'sec-fetch-user: ?1',
                'upgrade-insecure-requests: 1',
                'user-agent: Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1'
            ];
            for ($i=1; $i<11; $i++)
            {
                $posturl = "https://m.so.com/s?q=".urlencode($info['keywords']);
                if ($i > 1)
                {
                    $posturl .= "&pn={$i}";
                }
                $page = $this->abuyun($posturl,'','',$header,'','gzip');
                if (!$page)
                {
                    $ch = curl_init();//初始化
                    curl_setopt($ch, CURLOPT_URL, $posturl);//访问的URL
                    curl_setopt($ch, CURLOPT_HEADER, false);//设置不需要头信息
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);//只获取页面内容，但不输出
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//https请求 不验证证书
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);//https请求 不验证HOST
                    curl_setopt($ch,CURLOPT_ENCODING,'gzip');//百度返回的内容进行了gzip压缩,需要用这个设置解析
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);//curl模拟头部信息
                    $page = curl_exec($ch);//执行请求
                    curl_close($ch);//关闭curl，释放资
                }
                if (!$page)
                {
                    continue;
                }
                $html = new simple_html_dom;
                $html->load($page);
                $dom = $html->find('.res-site-url');
                if ($dom)
                {
                    foreach ($dom as $key => $d)
                    {
                        if (str_replace(" ",'',$d->text()) == $checkurl)
                        {
                            $result['rank'] = ($i-1) * 10 + $key + 1;
                            break 2;
                        }
                    }
                }
            }
        }

        return $result;
    }

    public  function getBaiduRank_xian($taskid = '',$type = 1){
        if(empty($taskid)){
            return false;
        }

        switch ($type) {
            case 1:
                $url ='http://apidata.chinaz.com/CallAPI/BaiduPcRanking';
                $key =  $this->baidu_key_pc;
                break;
            case 2:
                $url ='http://apidata.chinaz.com/CallAPI/BaiduMobileRanking';
                $key =  $this->baidu_key_mobile;
                break;
            case 3:
                $url ='http://apidata.chinaz.com/CallAPI/SoPcRanking';
                $key =  $this->x360_key_pc;
                break;
            case 4:
                $url ='http://apidata.chinaz.com/CallAPI/SoMobileRanking';
                $key =  $this->x360_key_mobile;
                break;
            case 5:
                $url ='http://apidata.chinaz.com/CallAPI/SogouPcRanking';
                $key =  $this->sougou_key_pc;
                break;
            case 6:
                $url ='http://apidata.chinaz.com/CallAPI/SogouMobileRanking';
                $key =  $this->sougou_key_mobile;
                break;
            default:
                $url ='http://apidata.chinaz.com/CallAPI/BaiduPcRanking';
                $key =  $this->baidu_key_pc;
                break;
        }


        $result = $this->getRankingForChinaz_xian($taskid,$url,$key);

        //360移动端因接口查询结果严重不准确，所以需要自行查询
//        if ($type == 4 && $result['rank'] > 100)
//        {
//            $info = Keywords::getKeywordsForTaskid($taskid);
//            $checkurl = str_replace("http://",'',rtrim($info['web_url'],'/'));
//            $header = [
//                ':authority: m.so.com',
//                ':method: GET',
//                ':scheme: https',
//                'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
//                'accept-encoding: gzip, deflate, br',
//                'accept-language: zh-CN,zh;q=0.9,en;q=0.8',
//                'cache-control: no-cache',
//                'pragma: no-cache',
//                'sec-fetch-dest: document',
//                'sec-fetch-mode: navigate',
//                'sec-fetch-site: none',
//                'sec-fetch-user: ?1',
//                'upgrade-insecure-requests: 1',
//                'user-agent: Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1'
//            ];
//            for ($i=1; $i<11; $i++)
//            {
//                $posturl = "https://m.so.com/s?q=".urlencode($info['keywords']);
//                if ($i > 1)
//                {
//                    $posturl .= "&pn={$i}";
//                }
//                $page = $this->abuyun($posturl,'','',$header,'','gzip');
//                if (!$page)
//                {
//                    $ch = curl_init();//初始化
//                    curl_setopt($ch, CURLOPT_URL, $posturl);//访问的URL
//                    curl_setopt($ch, CURLOPT_HEADER, false);//设置不需要头信息
//                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);//只获取页面内容，但不输出
//                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//https请求 不验证证书
//                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);//https请求 不验证HOST
//                    curl_setopt($ch,CURLOPT_ENCODING,'gzip');//百度返回的内容进行了gzip压缩,需要用这个设置解析
//                    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);//curl模拟头部信息
//                    $page = curl_exec($ch);//执行请求
//                    curl_close($ch);//关闭curl，释放资
//                }
//                if (!$page)
//                {
//                    continue;
//                }
//                $html = new simple_html_dom;
//                $html->load($page);
//                $dom = $html->find('.res-site-url');
//                if ($dom)
//                {
//                    foreach ($dom as $key => $d)
//                    {
//                        if (str_replace(" ",'',$d->text()) == $checkurl)
//                        {
//                            $result['rank'] = ($i-1) * 10 + $key + 1;
//                            break 2;
//                        }
//                    }
//                }
//            }
//        }

        return $result;
    }

    /**
     * 获取百度指数排名详情
     * @param string $taskid
     * @return array|bool
     */
    public  function getBaiduIndex($keyword){

        $url ='http://apidata.chinaz.com/CallAPI/BaiduIndex?key='.$this->app_key_zhishu.'&keyword='.$keyword;
        $res = file_get_contents($url);
        return $res;
    }

    /**
     * 模拟post进行url请求
     * @param string $url
     * @param string $param
     * @return bool|mixed
     */
    protected  function request_post($url = '', $param = '',$key) {
        if (empty($url) || empty($param)) {
            return false;
        }
        $header[] = "Content-type:application/x-www-form-urlencoded";
        /*输入你要调用API的APIKEY*/
        $header[] = "Authorization:APIKEY".' '.$key;
//        $header['Content-type'] = 'Content-type:application/x-www-form-urlencoded';
//        $header['Authorization'] = self::APP_KEY_PC;
        $postUrl = $url;
        $curlPost = $param;
        $ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
        curl_setopt($ch, CURLOPT_URL,$postUrl);//抓取指定网页
        curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        $data = curl_exec($ch);//运行curl
        curl_close($ch);

        return $data;
    }



    protected  function chinaz_request_post($url = '', $param = '') {
        if (empty($url) || empty($param)) {
            return false;
        }
        $header[] = "Content-type:application/x-www-form-urlencoded";
        /*输入你要调用API的APIKEY*/
//        $header['Content-type'] = 'Content-type:application/x-www-form-urlencoded';
//        $header['Authorization'] = self::APP_KEY_PC;
        $postUrl = $url;
        $curlPost = $param;
        $ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
        curl_setopt($ch, CURLOPT_URL,$postUrl);//抓取指定网页
        curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        $data = curl_exec($ch);//运行curl
        curl_close($ch);

        return $data;
    }


    protected  function chinaz_request_post_xian($url = '', $param = '') {
        if (empty($url) || empty($param)) {
            return false;
        }
        $header[] = "Content-type:application/x-www-form-urlencoded";
        /*输入你要调用API的APIKEY*/
//        $header['Content-type'] = 'Content-type:application/x-www-form-urlencoded';
//        $header['Authorization'] = self::APP_KEY_PC;
        $postUrl = $url;
        $curlPost = $param;
        $ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
        curl_setopt($ch, CURLOPT_URL,$postUrl);//抓取指定网页
        curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        $data = curl_exec($ch);//运行curl
        curl_close($ch);

        return $data;
    }


    public function curl_get($url){

        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($curl);     //返回api的json对象
        //关闭URL请求
        curl_close($curl);
        return $data;    //返回json对象
    }

    private function addRanklog($taskid,$result,$type,$action=1,$key,$reason='')
    {
        $rankData['taskid'] = $taskid;
        $rankData['result'] = $result;
        $rankData['type'] = $type;
        $rankData['action'] = $action;
        $rankData['key'] = $key;
        $rankData['message'] = $reason;

        $rankLog = new RankLog();
        return $rankLog->allowField(true)->save($rankData);
    }

    //阿布云使用方法
    protected function abuyun($url, $data='', $cookie='', $header=[], $cookiejar='', $encoding='', $optheader=0)
    {
        // 代理服务器
        $proxyServer = "http://proxy.abuyun.com:9020";
        // 隧道身份信息
        $proxyUser   = "H6W746DVZI90236D";
        $proxyPass   = "B2207B69ED923EB9";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // 设置代理服务器
        curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
        curl_setopt($ch, CURLOPT_PROXY, $proxyServer);
        // 设置隧道验证信息
        curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_PROXYUSERPWD, "{$proxyUser}:{$proxyPass}");

        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_HEADER, $optheader);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($encoding)
        {
            curl_setopt($ch,CURLOPT_ENCODING,$encoding);//百度返回的内容进行了gzip压缩,需要用这个设置解析
        }
        if ($data)
        {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);//要提交的信息
        }
        if ($cookie)
        {
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);//COOKIE
        }
        if ($header)
        {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
        if ($cookiejar)
        {
            curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiejar); //保存网页cookie
        }
        $page = curl_exec($ch);
        curl_close($ch);
        return $page;
    }
}