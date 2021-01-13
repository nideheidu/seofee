<?php

namespace app\common\model;
class Order extends Base
{
    protected $createTime = "create_time";
    protected $updateTime = false;
    protected $autoWriteTimestamp = true;

    public function keywordsInfo()
    {
        return $this->belongsTo('Keywords', 'kid', 'id')->bind('keywords,search_ngines');

    }

    public function contactsName()
    {
        return $this->belongsTo('Customer', 'uid', 'id')->bind('contacts');

    }

    public function webInfo()
    {
        return $this->belongsTo('WebUrl', 'web_id', 'id')->bind('url,admin_access,admin_password,ftp_host,ftp_port,ftp_access,ftp_password');

    }

    public function getOrderListByPage($where, $limit = 20)
    {
        $data = $this->with(['keywordsInfo', 'webInfo', 'contactsName'])->where($where)->order('create_time')->paginate($limit)->each(function ($value, $key) {
            unset($value['keywords_info']);
        });
        return $data;

    }

    public function lists($where = [])
    {
        return $this->where($where)->order('create_time')->paginate20;

    }

    public static function getThis()
    {
        return new self();

    }

    public function shenhe($orderNubmer, $status)
    {
        $shehe_status = 0;
        if ($status == 1) {
            $shehe_status = [0, 2];

        }
        if ($status == 2) {
            $shehe_status = [0, 1];

        }
        $order = $this->where('order_number', $orderNubmer)->where('shenhe_status', 'in', $shehe_status)->find();
        if (!$order) {
            return ['code' => 1, 'msg' => '订单不存在或者已审核'];

        }
        if (intval($status) == 1) $order->status = 1;
        $order->shenhe_status = intval($status);
        $order->shenhe_time = time();
        if ($order->save()) {
            return ['code' => 0, 'msg' => '操作成功'];

        }
        return ['code' => 1, 'msg' => '操作失败'];

    }

    public function addOrder($data = [])
    {
        return $this->allowField(true)->save($data);

    }

    public function editStatus($ids, $status)
    {
        if ($this->isUpdate(true)->save(['status' => $status], ['id' => $ids])) {
            echo $this->getLastSql();
            return ['code' => 0, 'msg' => '操作成功'];

        }
        return ['code' => 1, 'msg' => '操作失败'];

    }
}