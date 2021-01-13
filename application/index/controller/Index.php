<?php

namespace app\index\controller;

use app\common\model\AdminUser;
use app\common\model\Contact;
use common\Elasticsearch;
use Elasticsearch\ClientBuilder;
use think\Controller;
use think\Db;

class Index extends Controller
{
    public function index()
    {
        Db::listen(function ($sql, $time, $explain) {
            echo $sql . ' [' . $time . 's]';
            dump($explain);
        });
        exit;
        return $this->fetch();

    }

    public function hello($name = 'ThinkPHP5')
    {
        return 'hello,' . $name;

    }

    public function add()
    {
        AdminUser::create(['username' => 'test001', 'password' => 'aa123456', 'mobile' => '15760633369', 'status' => 1, 'role_group' => '', 'role' => 0]);

    }

    public function test()
    {
        $client = new Elasticsearch();
        $index = '1998_longhu_test';
        $id = 1;
        $body = ['name' => '龙湖', 'age' => '1', 'info' => ['card' => '51165656', 'phone' => '154546']];
        $res = $client->index($index, $id, $body);
        dump($res);

    }

    public function addBulk()
    {
        $client = new Elasticsearch();
        $index = '1998_longhu_test';
        for ($i = 0;
             $i < 100;
             $i++) {
            $params['body'][] = ['index' => ['_index' => $index, '_type' => 'contacts',]];
            $params['body'][] = ['name' => '龙湖' . $i, 'age' => 1 + $i, 'info' => ['card' => '51165656_' . $i, 'phone' => '154546' . $i]];

        }
        $res = $client->bulk($params);
        dump($res);

    }

    public function getOne()
    {
        $id = 'glRsqmcBrp_WJB69nwEQ';
        $client = new Elasticsearch();
        $index = '1998_longhu_test';
        $res = $client->get($index, $id);
        dump($res);

    }

    public function getSource()
    {
        $id = 'glRsqmcBrp_WJB69nwEQ';
        $client = new Elasticsearch();
        $index = '1998_longhu_test';
        $res = $client->getSource($index, $id);
        dump($res);

    }

    public function seach()
    {
        $client = new Elasticsearch();
        $index = '1998_longhu_test';
        $where = ['name' => '龙湖',];
        $res = $client->search($index, $where);
        dump($res);

    }

    public function multi_search()
    {
        $p = input('p', 1, 'int');
        $limit = input('limit', 20, 'int');
        $start = ($p - 1) < 0 ? 0 : ($p - 1);
        $from = $start * $limit;
        $size = $p * $limit - 1;
        $client = new Elasticsearch();
        $index = '1998_longhu_test';
        $body = ['query' => ['bool' => ['must' => [['match' => ['name' => '22']]], 'filter' => []]]];
        $res = $client->multi_search($index, $body, $from, $size);
        dump($res);

    }

    public function addES()
    {
        $Contact = new Contact();
        $list = $Contact->with('contactInfo')->select();
        dump($list);

    }
}