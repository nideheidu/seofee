<?php

namespace app\common\model;
class WebUrl extends Base
{
    protected $pk = "id";
    protected $autoWriteTimestamp = true;
    protected $createTime = "create_time";
    protected $updateTime = false;

    public function keywords()
    {
        return $this->hasMany('Keywords', 'web_id', 'id');

    }

    public function iskeywords($vlaue, $type)
    {
        return $this->hasMany('Keywords', 'web_id', 'id')->where('keywords', 'in', $vlaue)->where('search_ngines', $type);

    }

    public function keywordsPC()
    {
        halt($this->hasMany('Keywords', 'web_id', 'id')->where('search_ngines', 1));
        return $this->hasMany('Keywords', 'web_id', 'id')->where('search_ngines', 1);

    }

    public function keywordsMobile()
    {
        return $this->hasMany('Keywords', 'web_id', 'id')->where('search_ngines', 2);

    }

    public function userName()
    {
        return $this->belongsTo('Customer', 'uid', 'id')->bind(['contacts', 'company_name', 'member_level']);

    }

    public function getListByPage($where, $limit = 20)
    {
        return $this->with('userName')->withCount('keywords')->where($where)->order('create_time DESC')->paginate($limit);

    }

    public function addWebUrl($data, $uid)
    {

    }
}