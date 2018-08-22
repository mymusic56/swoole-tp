<?php
namespace app\common\service;

class RedisKeyList{
    public static function mobileCode($mobile)
    {
        return 'sms_'.$mobile;
    }
}