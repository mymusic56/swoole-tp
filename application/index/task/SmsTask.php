<?php
namespace app\index\task;

use app\common\service\sms\AliSms;

class SmsTask
{
    public function execute($serv, $data)
    {
        #发送验证码
//         AliSms::send($mobile, $code, $msg);

//         file_put_contents('/windows/www/swoole-tp5.0/swoole/task_test.txt', json_encode($data).PHP_EOL, FILE_APPEND);
        return 'success';
    }
}