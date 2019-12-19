<?php

$server = new Swoole\WebSocket\Server("0.0.0.0", 9501);

$server->set([
    'log_level' => 0,
    'log_file' => '/var/log/swoole.log',
//    'daemonize' => true
]);

$server->on('open', function (Swoole\WebSocket\Server $server, $request) {
    $date = date("Y-m-d G:i:s");
    echo "[{$date}] {$request->fd} 已连接 \n";
});
$server->on('message', function (Swoole\WebSocket\Server $server, $frame) {
    $date = date("Y-m-d G:i:s");
    echo "[{$date}] 接收来自{$frame->fd}的消息，消息内容：{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish} \n";
    foreach ($server->connections as $fd) {
        if ($fd == $frame->fd) {
            continue;
        }
        if ($server->isEstablished($fd)) {
            echo "[{$date}] 发送消息给{$fd},消息内容：{$frame->data}";
            $server->push($fd, $frame->data);
        }
    }
});
$server->on('close', function ($ser, $fd) {
    $date = date("Y-m-d G:i:s");
    echo "[$date] {$fd} 断开连接 \n";
});
$server->start();
