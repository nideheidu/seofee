<?php
/**
 * Created by PhpStorm.
 * User: 龙湖
 * Date: 2018/10/18
 * Time: 10:03
 */

return [
    //登录 数据的Redis 数据库配置   1号 数据库
    'admin_login_redis'                            =>[
        'host'         => '127.0.0.1', // redis主机
        'port'         => 6379, // redis端口
        'password'     => '', // 密码
        'select'       => 1, // 操作库
        // 'expire'       => 3600, // 有效期(秒)
        'timeout'      => 0, // 超时时间(秒)
        'persistent'   => true, // 是否长连接
    ],
];