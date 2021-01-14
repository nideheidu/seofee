<?php

namespace app\console;

use app\common\model\Message;
use app\common\model\User;
use app\tool\Tool;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\Cache;

class WebSocket extends Command
{
    protected static $token;
    protected $host = "\x30\x2E\x30\x2E\x30\x2E\x30";
    protected $port = 10000;
    protected $serverType = "\x73\x6F\x63\x6B\x65\x74";
    protected static $uid = "";
    protected $option = ["\x77\x6F\x72\x6B\x65\x72\x5F\x6E\x75\x6D" => 4, "\x64\x61\x65\x6D\x6F\x6E\x69\x7A\x65" => false, "\x62\x61\x63\x6B\x6C\x6F\x67" => 128, "\x64\x69\x73\x70\x61\x74\x63\x68\x5F\x6D\x6F\x64\x65" => 2, "\x68\x65\x61\x72\x74\x62\x65\x61\x74\x5F\x63\x68\x65\x63\x6B\x5F\x69\x6E\x74\x65\x72\x76\x61\x6C" => 5, "\x68\x65\x61\x72\x74\x62\x65\x61\x74\x5F\x69\x64\x6C\x65\x5F\x74\x69\x6D\x65" => 100,];
    protected $server;

    protected function configure()
    {
        $this->setName('websocket:start')->setDescription('Start Web Socket Server!');

    }

    protected function execute(Input $input, Output $output)
    {
        $this->server = new \swoole_websocket_server($this->host, $this->port);
        $this->server->on('Open', [$this, 'onOpen']);
        $this->server->on('Message', [$this, 'onMessage']);
        $this->server->on('Close', [$this, 'onClose']);
        $this->server->start();

    }

    public function onOpen(\swoole_websocket_server $server, \swoole_http_request $request)
    {
        $fd = $request->fd;
        $token = $request->get['token'] ?? '';
        if (!$token) {
            $server->push($fd, Tool::showAjax(['status' => 3, 'message' => '请先登录!']));
            $server->close($fd);
            return;

        }
        $userData = Cache::get($token);
        if (!$userData) {
            $server->push($fd, Tool::showAjax(['status' => 2, 'message' => '用户身份无效,请重新登录!']));
            $server->close($fd);
            return;

        }
        if (self::$uid == $userData['uid']) {
            $server->push($fd, Tool::showAjax(['status' => 2, 'message' => '非法操作!']));
            $server->close($fd);

        }
        self::$uid = $request->get['friendId'];
        $server->bind($fd, $userData['uid']);
        $messages = Message::getMessage($userData['uid']);
        (new User())->where('id', $userData['uid'])->update(['fd' => $request->fd]);
        $arr = $messages->toArray();
        sort($arr);
        $server->push($fd, Tool::showAjax(['status' => 1, 'message' => 'success', 'data' => $arr]));

    }

    public function onMessage(\swoole_websocket_server $server, \swoole_websocket_frame $frame)
    {
        echo 'receive from ' . $frame->fd . ':' . $frame->data . ',opcode:' . $frame->opcode . ',fin:' . $frame->finish . '
';
        $server->push($frame->fd, 'this is server');

    }

    public function onClose($server, $fd)
    {
        echo 'client ' . $fd . ' closed
';

    }
}