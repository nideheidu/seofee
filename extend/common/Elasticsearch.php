<?php
/**
 * Created by PhpStorm.
 * User: longhu
 * Date: 2018/12/11
 * Time: 9:39
 */

namespace common;


use Elasticsearch\ClientBuilder;

class Elasticsearch
{
    private $type;
    private $host;
    private $client;

    public function __construct()
    {
        $this->type  = 'longhu';
        $this->host ='127.0.0.1:9200';
        $hosts = [$this->host];
        $this->client = ClientBuilder::create()
            ->setHosts($hosts)
            ->build();
    }

    /**
     * 单文档索引
     *$params = [
     * 'index' => 'my_index',
     * 'id' => 'my_id',
     * 'body' => ['testField' => 'abc']
     * ];
     * @param $index 索引名
     * @param $id
     * @param $body
     * @return array
     */
    public function index($index, $id, $body)
    {
        $params = [
            'index' => $index,
            'refresh' => true,
            'type' => $this->type,
            'id' => $id,
            'body' => $body
        ];

        return $this->client->index($params);
    }

    /**
     * $params = [
     * 'index' => 'my_index',
     * 'id' => 'my_id',
     * 'body' => ['testField' => 'abc']
     * ];
     * @param [] $param
     * 2018-01-23 批量增加刷新索引
     */
    public function bulk($params)
    {

        return $this->client->bulk($params);
    }

    /**
     * 检查索引是否存在
     * @param $index 索引名
     * @return mixed
     */
    public function isExistIndex($index)
    {
        $params['index'] = $index;
        return $this->client->indices()->exists($params);
    }

    /**
     * 获取一个文档
     * @param $index
     * @param $id
     * @return array
     */
    public function get($index, $id)
    {
        $params = [
            'index' => $index,
            'refresh' => true,
            'type' => $this->type,
            'id' => $id
        ];
        return $this->client->get($params);
    }

    /**
     * 获取数据
     * @param $index
     * @param $id
     * @return array(3) {
                    ["name"] => string(8) "XXX"
                    ["age"] => int(30)
                    ["info"] => array(3) {
                        ["card"] => string(11) "51165656_29"
                        ["phone"] => string(6) "154546"
                        [0] => int(29)
                    }
                }
     */
    public function getSource($index, $id)
    {
        $params = [
            'index' => $index,
            'type' => $this->type,
            'id' => $id
        ];
        return $this->client->getSource($params);
    }

    /**
     * 索引
     * @param $index 索引名
     * @param $where [] 索引条件
     * @return array
     */
    public function search($index, $where)
    {
        $params = [
            'index' => $index,
            'type' => $this->type,
            'body' => [
                'query' => [
                    'match' => $where
                ]
            ]
        ];
        return $this->client->search($params);
    }

    /**
     * 组合多查询
     * @param string $index 索引
     * @param [] $body  查询条件
     * @param int $from 分页开始位置
     * @param int $size 分页数据条数
     * @return mixed
     */
    public function multi_search($index, $body, $from = 0, $size = 0, $field = [])
    {
        $params = [
            'index' => $index,
            'type' => $this->type,
            'body' => $body
        ];
        if (!empty($size)) {
            $params['from'] = $from;
            $params['size'] = $size;
        }

        if (!empty($field)) {
            $params['_source_include'] = $field;
        }

        return $this->client->search($params);
    }
}