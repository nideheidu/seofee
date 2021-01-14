<?php

namespace app\agent\controller;

use think\Request;
use app\common\model\SystemConfig as System;
use app\common\model\Notice;
use app\common\model\NoticeLog;

class SystemConfig extends Base
{
    public function fnInit(Request $request)
    {
        $map = [];
        $map['status'] = 1;
        $map['agent_id'] = 999999;
        $map['group'] = 'base';
        $data_list = System::where($map)->whereOr('agent_id', $request->uid)->order('sort asc')->select();
        foreach ($data_list as $k => $v) {
            if ($v['name'] == 'site_name' && $v['agent_id'] != $request->uid) {
                continue 1;

            }
            $config[$v['name']] = $v['value'];

        }
        return json(['code' => 0, 'data' => $config]);

    }

    public function index(Request $request)
    {
        $map = [];
        $map['agent_id'] = $request->uid;
        $map['status'] = 1;
        $map['group'] = 'base';
        $systemModel = new System;
        $data_list = System::where($map)->order('sort,id')->column('id,name,title,group,url,value,type,options,tips');
        if ($data_list) {
            foreach ($data_list as $k => &$v) {
                $v['id'] = $v['name'];
                if (!empty($v['options'])) {
                    $v['options'] = parse_attr($v['options']);

                }

            }
        } else {
            $configData_settlement = ['system' => 1, 'group' => 'base', 'title' => '结算规则', 'name' => 'site_settlement', 'value' => 1, 'sort' => 2, 'type' => 'select', 'options' => '', 'options' => '选择结算规则,会员组优先,关键字指数优先', 'tips' => '网站结算规则，使用用户组结算还是更具关键字指数结算', 'status' => 1, 'ctime' => time(), 'mtime' => time(), 'agent_id' => $request->uid,];
            $res = $systemModel->allowField(true)->save($configData_settlement);
            $configData_settlement['options'] = parse_attr($configData_settlement['options']);
            $data_list[0] = $configData_settlement;
            $configData_name = ['system' => 1, 'group' => 'base', 'title' => '网站名称', 'name' => 'site_name', 'value' => '计费系统', 'sort' => 1, 'type' => 'input', 'options' => '', 'tips' => '将显示在浏览器窗口标题等位置', 'status' => 1, 'ctime' => time(), 'mtime' => time(), 'agent_id' => $request->uid,];
            $systemModel = new System;
            $res = $systemModel->allowField(true)->save($configData_name);
            $configData_name['options'] = parse_attr($configData_name['options']);
            $data_list[1] = $configData_name;

        }
        if (count($data_list) == 1) {
            $configData_name = ['system' => 1, 'group' => 'base', 'title' => '网站名称', 'name' => 'site_name', 'value' => '计费系统', 'sort' => 1, 'type' => 'input', 'options' => '', 'tips' => '将显示在浏览器窗口标题等位置', 'status' => 1, 'ctime' => time(), 'mtime' => time(), 'agent_id' => $request->uid,];
            $res = $systemModel->allowField(true)->save($configData_name);
            $configData_name['options'] = parse_attr($configData_name['options']);
            $data_list[1] = $configData_name;

        }
        return json(['code' => 0, 'data' => $data_list]);

    }

    public function topmenu(Request $request)
    {
        $map = [];
        $map['agent_id'] = $request->uid;
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
        $map['agent_id'] = $request->uid;
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
        $map['agent_id'] = $request->uid;
        $map['status'] = 1;
        $map['group'] = 'copyright';
        $systemModel = new System;
        $data_list = System::where($map)->order('sort asc')->column('id,name,title,group,url,value,type,options,tips,sort');
        if (!$data_list) {
            $configData_name = ['system' => 1, 'group' => 'copyright', 'title' => '版权信息', 'name' => 'copyrightinfo', 'value' => '', 'sort' => 1, 'type' => 'input', 'options' => '', 'tips' => '底部版权信息,示例：Copyright © 2012-2017 17s.cn All Rights Reserved. 速达网络 版权所有', 'status' => 1, 'ctime' => time(), 'mtime' => time(), 'agent_id' => $request->uid,];
            $systemModel = new System;
            $res = $systemModel->allowField(true)->save($configData_name);
            $configData_name = ['system' => 1, 'group' => 'copyright', 'title' => '公司信息', 'name' => 'companyinfo', 'value' => '', 'sort' => 2, 'type' => 'input', 'options' => '', 'tips' => '底部公司信息,示例：某某公司 地址：某某地址', 'status' => 1, 'ctime' => time(), 'mtime' => time(), 'agent_id' => $request->uid,];
            $systemModel = new System;
            $res = $systemModel->allowField(true)->save($configData_name);
            $configData_name = ['system' => 1, 'group' => 'copyright', 'title' => '备案信息', 'name' => 'ipcinfo', 'value' => '', 'sort' => 3, 'type' => 'input', 'options' => '', 'tips' => '底部备案信息,示例：蜀ICP备12345678', 'status' => 1, 'ctime' => time(), 'mtime' => time(), 'agent_id' => $request->uid,];
            $systemModel = new System;
            $res = $systemModel->allowField(true)->save($configData_name);
            $data_list = System::where($map)->order('sort asc')->column('id,name,title,group,url,value,type,options,tips,sort');

        }
        return json(['code' => 0, 'data' => $data_list]);

    }

    public function engines(Request $request)
    {
        $map = [];
        $map['status'] = 1;
        $map['agent_id'] = $request->uid;
        $map['group'] = 'engines';
        $data_list = System::where($map)->order('sort asc')->select()->toArray();
        if (empty($data_list)) {
            $engines = array(['title' => '百度PC', 'name' => 'baidupcs'], ['title' => '百度移动', 'name' => 'baidumobiles'], ['title' => '360PC', 'name' => '360pcs'], ['title' => '360移动', 'name' => '360mobiles'], ['title' => '搜狗PC', 'name' => 'sougoupcs'], ['title' => '搜狗移动', 'name' => 'sougoumobiles']);
            foreach ($engines as $key => $value) {
                $configData = ['system' => 1, 'group' => 'engines', 'title' => $value['title'], 'name' => $value['name'], 'value' => '100', 'sort' => 1, 'type' => 'input', 'options' => '', 'tips' => '%,设置' . $value['title'] . '搜索引擎的扣费百分比，如填80表示此搜索引擎优化费用为设置基础费用的80%', 'status' => 1, 'agent_id' => $request->uid,];
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

    public function editSystem(Request $request)
    {
        $data = $request->post();
        if (isset($data['access-token'])) unset($data['access-token']);
        $types = $data['type'];
        $ids = isset($data['id']) ? $data['id'] : '';
        if (!$types) return false;
        foreach ($types as $k => $v) {
            $where['name'] = $k;
            $where['agent_id'] = $request->uid;
            if ($v == 'switch' && !isset($ids[$k])) {
                System::where($where)->update(['value' => 0]);
                continue 1;

            }
            if ($v == 'checkbox' && isset($ids[$k])) {
                $ids[$k] = json_encode($ids[$k], 1);

            }
            System::where($where)->update(['value' => $ids[$k]]);

        }
        System::getConfigData('', true);
        return json(['code' => 0, 'msg' => '保存成功']);

    }

    public function notice(Request $request)
    {
        $data = $request->get();
        $id = $data['id'];
        $where['id'] = $id;
        $notice = new Notice;
        $res = $notice->where($where)->find();
        if ($res) {
            $insert_data['uid'] = $request->uid;
            $insert_data['nid'] = $id;
            $insert_data['create_time'] = time();
            $noticelog = new NoticeLog;
            $noticelog->allowField(true)->save($insert_data);

        }
        return json(['code' => 0, 'notice' => $res]);

    }

    public function recharge(Request $request)
    {
        $map = [];
        $map['status'] = 1;
        $map['agent_id'] = $request->uid;
        $map['group'] = 'recharge';
        $data_list = System::where($map)->order('sort asc')->find();
        if (empty($data_list)) {
            $value['image'] = '/images/1562053100.png';
            $value['text'] = '识别二维码联系在线客服充值！';
            $configData = ['system' => 1, 'group' => 'recharge', 'title' => '支付设置', 'name' => 'recharge', 'value' => json_encode($value), 'sort' => 1, 'type' => 'input', 'options' => '', 'tips' => '', 'status' => 1, 'agent_id' => $request->uid,];
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

    public function domain(Request $request)
    {
        $map = [];
        $map['agent_id'] = $request->uid;
        $map['group'] = 'domain';
        $data_list = System::where($map)->order('sort asc')->find();
        if (empty($data_list)) {
            $data_list = ['system' => 1, 'group' => 'domain', 'title' => '域名', 'name' => 'domain', 'value' => '', 'sort' => 1, 'type' => 'input', 'options' => '', 'tips' => '设置访问的域名，需联系管理员审核通过后生效，修改后需再次审核，请谨慎操作。', 'status' => 0, 'agent_id' => $request->uid,];
            $systemModel = new System;
            $res = $systemModel->allowField(true)->save($data_list);

        }
        return json(['code' => 0, 'data' => $data_list]);

    }

    public function logo(Request $request)
    {
        $map = [];
        $map['status'] = 1;
        $map['agent_id'] = $request->uid;
        $map['group'] = 'logo';
        $data_list = System::where($map)->order('sort asc')->find();
        if (empty($data_list)) {
            $value['image'] = '/images/logo.png';
            $value['text'] = '网站logo';
            $configData = ['system' => 1, 'group' => 'logo', 'title' => '支付设置', 'name' => 'logo', 'value' => json_encode($value), 'sort' => 1, 'type' => 'input', 'options' => '', 'tips' => '', 'status' => 1, 'agent_id' => $request->uid,];
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
        $map['agent_id'] = $request->uid;
        $map['group'] = 'slider';
        $data_list = System::where($map)->order('sort asc')->select();
        if (count($data_list) < 1) {
            for ($i = 1;
                 $i <= 2;
                 $i++) {
                $value['image'] = '/images/login_pattern_' . $i . '.png';
                $value['text'] = '幻灯片';
                $configData = ['system' => 1, 'group' => 'slider', 'title' => '幻灯片', 'name' => 'slider', 'value' => json_encode($value), 'sort' => $i, 'type' => 'input', 'options' => '', 'tips' => '', 'status' => 1, 'agent_id' => $request->uid,];
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

    public function editrecharge(Request $request)
    {
        $data = $request->post();
        $uid = $request->uid;
        $where['agent_id'] = $uid;
        $where['group'] = 'recharge';
        $value['image'] = $data['image'];
        $value['text'] = $data['text'];
        System::where($where)->update(['value' => json_encode($value)]);
        return json(['code' => 0, 'msg' => '保存成功']);

    }

    public function edittemplate(Request $request)
    {
        $data = $request->post();
        $uid = $request->uid;
        $where['agent_id'] = $uid;
        if ($data['domain']) {
            $where_domain['group'] = 'domain';
            if (!System::where($where)->where($where_domain)->where(['value' => $data['domain']])->find()) {
                System::where($where)->where($where_domain)->update(['value' => $data['domain'], 'status' => 0]);

            }

        }
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
                $where_top['agent_id'] = $request->uid;
                if ($data['topmenuid'][$key]) {
                    $where_top['id'] = $data['topmenuid'][$key];
                    System::where($where_top)->update(['value' => $data['topmenuvalue'][$key], 'title' => $data['topmenuname'][$key]]);

                } else {
                    $configData = ['system' => 1, 'group' => 'topmenu', 'title' => $data['topmenuname'][$key], 'name' => 'topmenu', 'value' => $data['topmenuvalue'][$key], 'sort' => 1, 'type' => 'input', 'options' => '', 'tips' => '', 'status' => 1, 'tips' => '留空则不显示，最多7个', 'agent_id' => $request->uid,];
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
                $where_top['agent_id'] = $request->uid;
                if ($data['bottommenuid'][$key]) {
                    $where_top['id'] = $data['bottommenuid'][$key];
                    System::where($where_top)->update(['value' => $data['bottommenuvalue'][$key], 'title' => $data['bottommenuname'][$key]]);

                } else {
                    $configData = ['system' => 1, 'group' => 'bottommenu', 'title' => $data['bottommenuname'][$key], 'name' => 'bottommenu', 'value' => $data['bottommenuvalue'][$key], 'sort' => 1, 'type' => 'input', 'options' => '', 'tips' => '', 'status' => 1, 'tips' => '留空则不显示，最多7个', 'agent_id' => $request->uid,];
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
            $where['agent_id'] = $request->uid;
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