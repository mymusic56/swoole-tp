<?php
namespace app\index\util;

use think\response\Json;

class Response
{
    public static function send($status, $msg, $data)
    {
        return new Json(['status' => $status, 'msg' => $msg, 'result' => $data]);
    }
}