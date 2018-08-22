<?php
namespace app\index\controller;

use app\common\service\RedisKeyList;
use app\common\util\RegxTool;
use app\index\util\Response;
use Firebase\JWT\JWT;
use app\index\task\SmsTask;
use app\common\service\SwooleRedis;

class Sms extends BaseApi
{
    public function sendCode()
    {
        $mobile = $this->request->get('mobile');
        /* @var $http \Swoole\Server */
        $http = $this->request->get('http');
        if (!RegxTool::is_mobile($mobile)) {
            return Response::send(config('code.error'), '手机号格式错误', null);
        }
        
        $code = rand(1000, 9999);
        # 发送验证码
        $data = [
            'class' => 'app\\index\\task\\SmsTask',
            'method' => 'execute',
            'mobile' => $mobile, 'code' => $code, 'time' => time(),
        ];

        #协程Redis
        // 这样写，资源不会释放！！！
        $redis = SwooleRedis::getInstance()->redis;
        $redis->set(RedisKeyList::mobileCode($mobile), $code, 300);
        /* 将短信发送以任务的方式进行 */
        $http->task($data);
        
        $token = JWT::encode($data, config('business.jwt_key'));
        
        return Response::send(config('code.success'), 'success', ['json_token'=>$token]);
    }
}