<?php
/**
 * Created by PhpStorm.
 * User: 龙湖
 * Date: 2018/10/18
 * Time: 9:50
 */

return [

    //JWT
    'JWT_admin'                     =>[
        'iss'=>'www.longhu321.com',//jwt签发者
        'typ'=>'JWT',//认证方式
        'alg'=>'HS256',//加密类型
        'exp'=>7200, //过期时间
        'salt' => '￥%￥SDSDS5454#$%%&*uhnkk5354(\\/',
        'HashToken' => 'HashLoginToken',
        'LoginToken' => 'LoginListToken',
    ],

    //JWT
    'JWT_user'                     =>[
        'iss'=>'www.longhu321.com',//jwt签发者
        'typ'=>'JWT',//认证方式
        'alg'=>'HS256',//加密类型
        'exp'=>7200, //过期时间
        'salt' => '￥%￥SDSDS5454#$%%&*uhnkk5354(\\/',
        'HashToken' => 'HashLoginToken',
        'LoginToken' => 'LoginListToken',
    ],

    'JWT_sales'                     =>[
        'iss'=>'www.longhu321.com',//jwt签发者
        'typ'=>'JWT',//认证方式
        'alg'=>'HS256',//加密类型
        'exp'=>7200, //过期时间
        'salt' => '￥%￥SDSDS5454#$%%&*uhnkk5351(\\/',
        'HashToken' => 'HashLoginToken',
        'LoginToken' => 'LoginListToken',
    ],

    //密码参数
    'Password_user'               => [
        'salt'      => '￥%￥SDSDS5454#$%%&*uhnkk5354',
        'cost'  => 14,
    ],
    //密码参数
    'Password_admin'               => [
        'salt'      => '￥%￥SDSDS5454#$%%&*uhnkk5354(\\/ddccc3434',
        'cost'  => 15,
    ],
];