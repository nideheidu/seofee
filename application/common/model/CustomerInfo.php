<?php

namespace app\common\model;
class CustomerInfo extends Base
{
    protected $pk = "id";
    protected $createTime = "last_login_time";
    protected $updateTime = "last_login_time";

}