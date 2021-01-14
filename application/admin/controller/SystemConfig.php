<?php

namespace app\admin\controller;

use think\Request;
use app\common\model\SystemConfig as System;
use app\common\model\Notice;

class SystemConfig extends Base
{
    public function fnInit()
    {
        $map = [];
        $map['status'] = 1;
        $map['agent_id'] = 999999;
        $map['group'] = 'base';
        $data_list = System::where($map)->order('sort asc')->select();
        foreach ($data_list as $k => &$v) {
            $config[$v['name']] = $v['value'];

        }
        return json(['code' => 0, 'data' => $config]);

    }

    public function index($type = 'base')
    {
        $map = [];
        $map['status'] = 1;
        $map['agent_id'] = 999999;
        $map['group'] = ['base', 'api'];
        $data_list = System::where($map)->order('sort asc')->select();
        foreach ($data_list as $k => &$v) {
            $v['id'] = $v['name'];
            if (!empty($v['options'])) {
                $v['options'] = parse_attr($v['options']);

            }

        }
        return json(['code' => 0, 'data' => $data_list]);

    }

    public function topmenu(Request $request)
    {
        $map = [];
        $map['agent_id'] = 999999;
        $map['status'] = 1;
        $map['group'] = 'topmenu';
        $systemModel = new System;
        $data_list = System::where($map)->order('sort,id')->column('id,name,title,group,url,value,type,options,tips');
        if ($data_list) {
            foreach ($data_list as $k => &$v) {
                if (!empty($v['options'])) {
                    $v['options'] = parse_attr($v['options']);

                }

            }
        }
        for ($i = count($data_list);
             $i < 7;
             $i++) {
            $data_temp['id'] = '';
            $data_temp['name'] = '';
            $data_temp['title'] = '';
            $data_temp['value'] = '';
            $data_temp['type'] = 'input';
            $data_temp['options'] = '';
            $data_temp['tips'] = '留空则不显示，最多7个';
            array_push($data_list, $data_temp);

        }
        return json(['code' => 0, 'data' => $data_list]);

    }

    public function bottommenu(Request $request)
    {
        $map = [];
        $map['agent_id'] = 999999;
        $map['status'] = 1;
        $map['group'] = 'bottommenu';
        $systemModel = new System;
        $data_list = System::where($map)->order('sort,id')->column('id,name,title,group,url,value,type,options,tips');
        if ($data_list) {
            foreach ($data_list as $k => &$v) {
                if (!empty($v['options'])) {
                    $v['options'] = parse_attr($v['options']);

                }

            }
        }
        for ($i = count($data_list);
             $i < 9;
             $i++) {
            $data_temp['id'] = '';
            $data_temp['name'] = '';
            $data_temp['title'] = '';
            $data_temp['value'] = '';
            $data_temp['type'] = 'input';
            $data_temp['options'] = '';
            $data_temp['tips'] = '留空则不显示，最多9个';
            array_push($data_list, $data_temp);

        }
        return json(['code' => 0, 'data' => $data_list]);

    }

    public function copyright(Request $request)
    {
        $map = [];
        $map['agent_id'] = 999999;
        $map['status'] = 1;
        $map['group'] = 'copyright';
        $systemModel = new System;
        $data_list = System::where($map)->order('sort asc')->column('id,name,title,group,url,value,type,options,tips,sort');
        if (!$data_list) {
            $configData_name = ['system' => 1, 'group' => 'copyright', 'title' => '版权信息', 'name' => 'copyrightinfo', 'value' => '', 'sort' => 1, 'type' => 'input', 'options' => '', 'tips' => '底部版权信息,示例：Copyright © 2012-2017 17s.cn All Rights Reserved. 速达网络 版权所有', 'status' => 1, 'ctime' => time(), 'mtime' => time(), 'agent_id' => 999999,];
            $systemModel = new System;
            $res = $systemModel->allowField(true)->save($configData_name);
            $configData_name = ['system' => 1, 'group' => 'copyright', 'title' => '公司信息', 'name' => 'companyinfo', 'value' => '', 'sort' => 2, 'type' => 'input', 'options' => '', 'tips' => '底部公司信息,示例：某某公司 地址：某某地址', 'status' => 1, 'ctime' => time(), 'mtime' => time(), 'agent_id' => 999999,];
            $systemModel = new System;
            $res = $systemModel->allowField(true)->save($configData_name);
            $configData_name = ['system' => 1, 'group' => 'copyright', 'title' => '备案信息', 'name' => 'ipcinfo', 'value' => '', 'sort' => 3, 'type' => 'input', 'options' => '', 'tips' => '底部备案信息,示例：蜀ICP备12345678', 'status' => 1, 'ctime' => time(), 'mtime' => time(), 'agent_id' => 999999,];
            $systemModel = new System;
            $res = $systemModel->allowField(true)->save($configData_name);
            $data_list = System::where($map)->order('sort asc')->column('id,name,title,group,url,value,type,options,tips,sort');

        }
        return json(['code' => 0, 'data' => $data_list]);

    }

    public function logo(Request $request)
    {
        $map = [];
        $map['status'] = 1;
        $map['agent_id'] = 999999;
        $map['group'] = 'logo';
        $data_list = System::where($map)->order('sort asc')->find();
        if (empty($data_list)) {
            $value['image'] = '/images/logo.png';
            $value['text'] = '网站logo';
            $configData = ['system' => 1, 'group' => 'logo', 'title' => '支付设置', 'name' => 'logo', 'value' => json_encode($value), 'sort' => 1, 'type' => 'input', 'options' => '', 'tips' => '', 'status' => 1, 'agent_id' => 999999,];
            $systemModel = new System;
            $res = $systemModel->allowField(true)->save($configData);
            $configData['id'] = $systemModel->id;
            $data_list = $configData;

        }
        if ($data_list) {
            $data_list['id'] = $data_list['name'];
            $data_list['value'] = json_decode($data_list['value'], 1);

        }
        return json(['code' => 0, 'data' => $data_list]);

    }

    public function slider(Request $request)
    {
        $map = [];
        $map['status'] = 1;
        $map['agent_id'] = 999999;
        $map['group'] = 'slider';
        $data_list = System::where($map)->order('sort asc')->select();
        if (count($data_list) < 1) {
            for ($i = 1;
                 $i <= 2;
                 $i++) {
                $value['image'] = '/images/login_pattern_' . $i . '.png';
                $value['text'] = '幻灯片';
                $configData = ['system' => 1, 'group' => 'slider', 'title' => '幻灯片', 'name' => 'slider', 'value' => json_encode($value), 'sort' => $i, 'type' => 'input', 'options' => '', 'tips' => '', 'status' => 1, 'agent_id' => 999999,];
                $systemModel = new System;
                $res = $systemModel->allowField(true)->save($configData);

            }
            $data_list = System::where($map)->order('sort asc')->select();

        }
        if (count($data_list) >= 1) {
            foreach ($data_list as $key => $value) {
                $data_list[$key]['value'] = json_decode($value['value'], 1);

            }

        }
        return json(['code' => 0, 'data' => $data_list]);

    }

    public function engines(Request $request)
    {
        $map = [];
        $map['status'] = 1;
        $map['agent_id'] = 999999;
        $map['group'] = 'engines';
        $data_list = System::where($map)->order('sort asc,id asc')->select()->toArray();
        if (empty($data_list)) {
            $engines = array(['title' => '百度PC', 'name' => 'baidupcs'], ['title' => '百度移动', 'name' => 'baidumobiles'], ['title' => '360PC', 'name' => '360pcs'], ['title' => '360移动', 'name' => '360mobiles'], ['title' => '搜狗PC', 'name' => 'sougoupcs'], ['title' => '搜狗移动', 'name' => 'sougoumobiles']);
            foreach ($engines as $key => $value) {
                $configData = ['system' => 1, 'group' => 'engines', 'title' => $value['title'], 'name' => $value['name'], 'value' => '100', 'sort' => 1, 'type' => 'input', 'options' => '', 'tips' => '%,设置' . $value['title'] . '搜索引擎的扣费百分比，如填80表示此搜索引擎优化费用为设置基础费用的80%', 'status' => 1, 'agent_id' => 999999,];
                $systemModel = new System;
                $res = $systemModel->allowField(true)->save($configData);
                $configData['id'] = $systemModel->id;
                $data_list[$key] = $configData;

            }

        }
        if ($data_list) {
            foreach ($data_list as $k => &$v) {
                $data_list[$k]['id'] = $v['name'];

            }

        }
        return json(['code' => 0, 'data' => $data_list]);

    }

    public function domainlist(Request $request)
    {
        //计费系统-管理员后台-OEM设置-除了“常规词账号”，删除其他的所有账号(隐藏)
        $where_lc = "c.id = 21";
        $where['group'] = 'domain';
        $limit = $request->get('limit', 30, 'int');
        $system = new System;
        $list = $system->alias('s')->join('customer c', 's.agent_id = c.id')->field('*,s.status as status,s.id as id')->where($where)->where($where_lc)->order('s.id DESC')->paginate($limit);
        return json(['code' => 0, 'data' => $list]);

    }

    public function auzdomain(Request $request)
    {
        $data = $request->get();
        $id = $data['id'];
        $system = new System;
        $where['id'] = $id;
        if ($system->where('id', $id)->update(['status' => 1])) {
            return json(['code' => 0, 'msg' => '审核成功']);

        } else {
            return json(['code' => 1, 'msg' => '审核失败']);

        }

    }

    public function editSystem(Request $request)
    {
        $data = $request->post();
        if (isset($data['access-token'])) unset($data['access-token']);
        $types = $data['type'];
        $ids = isset($data['id']) ? $data['id'] : '';
        if (!$types) return false;
        foreach ($types as $k => $v) {
            if ($v == 'switch' && !isset($ids[$k])) {
                System::where('name', $k)->update(['value' => 0]);
                continue 1;

            }
            if ($v == 'checkbox' && isset($ids[$k])) {
                $ids[$k] = json_encode($ids[$k], 1);

            }
            System::where('name', $k)->where('agent_id', 999999)->update(['value' => $ids[$k]]);

        }
        System::getConfigData('', true);
        return json(['code' => 0, 'msg' => '保存成功']);

    }

    public function addnotice(Request $request)
    {
        $data = $request->post();
        if (!$data['group'] || !$data['title'] || !$data['content']) {
            return json(['code' => 1, 'msg' => '请完善公告内容']);

        } else {
            $notice = new Notice;
            if ($data['id']) {
                $update_data['title'] = $data['title'];
                $update_data['group'] = $data['group'];
                $update_data['content'] = $data['content'];
                $update_data['is_display'] = $data['is_display'] == 1 ? 1 : 0;
                $notice->where('id', $data['id'])->update($update_data);

            } else {
                $data['create_time'] = time();
                $notice->allowField(true)->save($data);

            }

        }
        return json(['code' => 0, 'msg' => '保存成功']);

    }

    public function delnotices(Request $request)
    {
        $data = $request->get();
        $id = $data['id'];
        $notice = new Notice;
        $where['id'] = $id;
        if ($notice->destroy(function ($query) use ($where) {
            $query->where($where);
        }, false)) {
            return json(['code' => 0, 'msg' => '删除成功']);

        } else {
            return json(['code' => 1, 'msg' => '删除失败']);

        }

    }

    public function getnotices(Request $request)
    {
        $group = $request->get('group', 1, 'int');
        $where = [];
        if ($group == 2 || $group == 3) {
            $where['group'] = $group;

        }
        $limit = $request->get('limit', 30, 'int');
        $notice = new Notice;
        $list = $notice->where($where)->order('create_time DESC')->paginate($limit);
        if ($list) {
            foreach ($list as $key => $value) {
                $list[$key]['create_time'] = date('Y-m-d H:i:s', $value['create_time']);
                $list[$key]['group_id'] = $value['group'];
                $list[$key]['group'] = $value['group'] == 1 ? '平台' : ($value['group'] == 2 ? '代理商' : '普通会员');

            }

        }
        return json(['code' => 0, 'data' => $list]);

    }

    public function edittemplate(Request $request)
    {
        $data = $request->post();
        $uid = 999999;
        $where['agent_id'] = $uid;
        if ($data['image_logo']) {
            $where_log['group'] = 'logo';
            $value['image'] = $data['image_logo'];
            $value['text'] = '网站logo';
            System::where($where)->where($where_log)->update(['value' => json_encode($value)]);

        }
        if ($data['image_slider0'] && $data['image_slider_id0']) {
            $where_slide['id'] = $data['image_slider_id0'];
            $value['image'] = $data['image_slider0'];
            $value['text'] = '幻灯片';
            System::where($where)->where($where_slide)->update(['value' => json_encode($value)]);

        }
        if ($data['image_slider1'] && $data['image_slider_id1']) {
            $where_slide['id'] = $data['image_slider_id1'];
            $value['image'] = $data['image_slider1'];
            $value['text'] = '幻灯片';
            System::where($where)->where($where_slide)->update(['value' => json_encode($value)]);

        }
        foreach ($data['topmenuname'] as $key => $value) {
            if ($data['topmenuname'][$key] && $data['topmenuvalue'][$key]) {
                $where_top['name'] = 'topmenu';
                $where_top['agent_id'] = 999999;
                if ($data['topmenuid'][$key]) {
                    $where_top['id'] = $data['topmenuid'][$key];
                    System::where($where_top)->update(['value' => $data['topmenuvalue'][$key], 'title' => $data['topmenuname'][$key]]);

                } else {
                    $configData = ['system' => 1, 'group' => 'topmenu', 'title' => $data['topmenuname'][$key], 'name' => 'topmenu', 'value' => $data['topmenuvalue'][$key], 'sort' => 1, 'type' => 'input', 'options' => '', 'tips' => '', 'status' => 1, 'tips' => '留空则不显示，最多7个', 'agent_id' => 999999,];
                    $systemModel = new System;
                    $res = $systemModel->allowField(true)->save($configData);

                }

            }
            if (!$data['topmenuname'][$key] && !$data['topmenuname'][$key] && $data['topmenuid'][$key]) {
                System::destroy($data['topmenuid'][$key]);

            }

        }
        foreach ($data['bottommenuname'] as $key => $value) {
            if ($data['bottommenuname'][$key] && $data['bottommenuvalue'][$key]) {
                $where_top['name'] = 'bottommenu';
                $where_top['agent_id'] = 999999;
                if ($data['bottommenuid'][$key]) {
                    $where_top['id'] = $data['bottommenuid'][$key];
                    System::where($where_top)->update(['value' => $data['bottommenuvalue'][$key], 'title' => $data['bottommenuname'][$key]]);

                } else {
                    $configData = ['system' => 1, 'group' => 'bottommenu', 'title' => $data['bottommenuname'][$key], 'name' => 'bottommenu', 'value' => $data['bottommenuvalue'][$key], 'sort' => 1, 'type' => 'input', 'options' => '', 'tips' => '', 'status' => 1, 'tips' => '留空则不显示，最多7个', 'agent_id' => 999999,];
                    $systemModel = new System;
                    $res = $systemModel->allowField(true)->save($configData);

                }

            }
            if (!$data['bottommenuname'][$key] && !$data['bottommenuvalue'][$key] && $data['bottommenuid'][$key]) {
                System::destroy($data['bottommenuid'][$key]);

            }

        }
        $ids = isset($data['id']) ? $data['id'] : '';
        foreach ($ids as $k => $v) {
            $where['id'] = $k;
            $where['agent_id'] = 999999;
            System::where($where)->update(['value' => $ids[$k]]);

        }
        return json(['code' => 0, 'msg' => '保存成功']);

    }

    public function upload(Request $request)
    {
        $file = request()->file('file');
        if ($file == null) {
            $this->error('请选择图片!');

        }
        $saveFolder = $request->get('type', 'recharge', 'string');
        $info = $file->rule('md5')->move('./images/' . $saveFolder . '/');
        $saveName = $info->getsaveName();
        $str = '/images/' . $saveFolder . '/' . $saveName;
        $url = strtr($str, '\\', '/');
        return json(['code' => 0, 'url' => $url]);

    }
}