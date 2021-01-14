<?php

namespace app\admin\controller;

use think\Request;
use app\common\model\Command;
use app\common\model\CommandTemplate;

class Simulation extends Base
{
    public function addCommand(Request $request)
    {
        $data = $request->post();
        $commandData = json_decode($data['data'], 1);
        $data['isagent'] = 1;
        $command = new Command;
        return json($command->addCommand($data));

    }

    public function deleteCommand(Request $request)
    {
        $data = $request->get();
        $command = new Command;
        return json($command->deleteCommand($data['id']));

    }

    public function commandLis(Request $request)
    {
        $data = $request->get();
        $where = [];
        $limit = $request->post('limit', 50, 'int');
        $command = new Command;
        $data = $command->getListByPage($where, $limit);
        return self::result($data);

    }

    public function addTemplate(Request $request)
    {
        $data = $request->post();
        $commandData = json_decode($data['data'], 1);
        $title = $data['title'];
        $id = isset($data['id']) ? $data['id'] : 0;
        $template = new CommandTemplate;
        return json($template->addTemplate($title, $data, $id));

    }

    public function templateLis(Request $request)
    {
        $data = $request->get();
        $where = [];
        $limit = $request->post('limit', 50, 'int');
        $template = new CommandTemplate;
        $data = $template->getListByPage($where, $limit);
        return self::result($data);

    }

    public function deleteTemplate(Request $request)
    {
        $data = $request->get();
        $template = new CommandTemplate;
        return json($template->deleteTemplate($data['id']));

    }
}