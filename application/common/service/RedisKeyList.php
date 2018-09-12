<?php
namespace app\common\service;

class RedisKeyList{
    public static function mobileCode($mobile)
    {
        return 'sms_'.$mobile;
    }
    
    public static function onlineUserFdList()
    {
        return 'online_user_fd_list';
    }
    
}