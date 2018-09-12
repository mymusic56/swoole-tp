<?php
namespace app\index\util;

use think\response\Json;

class Response
{
    public static function send($status, $msg, $data)
    {
        $_SERVER['Content-Type'] = 'application/json;charset=utf-8';
        return new Json(['status' => $status, 'msg' => $msg, 'result' => $data]);
    }
}