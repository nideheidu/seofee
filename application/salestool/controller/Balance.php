<?php


namespace app\salestool\controller;

use think\Request;
use think\Db;
use app\common\model\Customer;

class Balance extends Base
{
    public function balanceList(Request $request)
    {
        $data = $request->param();
        $limit = $data['limit']?$data['limit']:10;
        //金融词账号代理商
        $where = 'c.upid = 1';
        if (isset($data['name']) && $data['name'])
        {
            $where .= " and c.username = '{$data['name']}'";
        }
        $sql = Customer::alias('c')->leftJoin('seo_customer_account a','c.id = a.uid')->leftJoin("(select s.uid,sum(s.free) recharge from seo_mingxi as s inner join seo_customer as c on s.uid = c.id where s.change_type = 1 and {$where} group by s.uid) r",'c.id = r.uid')->leftJoin("(select s.uid,sum(s.free) dayfree from seo_mingxi as s inner join seo_customer as c on s.uid = c.id where s.change_type = 2 and {$where} and s.create_time > ".strtotime(date('Y-m-d', strtotime('-1 day')))." and s.create_time < ".strtotime(date('Y-m-d'))." group by s.uid) d",'c.id = d.uid')->field("c.id,c.username,c.contacts,a.total_sum,r.recharge,d.dayfree,if(d.dayfree,CAST(a.total_sum/d.dayfree*-1 AS DECIMAL(10,2)),99999999) day")->where($where)->group('c.id')->buildSql();
        $list = Db::table($sql)->alias('customer')->field("id,username,contacts,total_sum,recharge,dayfree,day")->order($data['order'])->paginate($limit);
        foreach ($list as $key => $value)
        {
            $value['index'] = ($data['page']-1)*$data['limit']+$key+1;
            $list[$key] = $value;
        }
        return self::result($list);
    }
}