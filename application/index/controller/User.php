<?php
namespace app\index\controller;

use app\common\util\RegxTool;
use app\index\util\Response;
use Firebase\JWT\JWT;
use app\common\service\RedisService;
use app\common\service\RedisKeyList;

class User extends BaseApi{
    
    /**
     * 验证验证码的流程
     * 1.Redis或者JWT做快速验证code是否正确
     * 2.发送记录异步存数据库
     * @return \think\response\Json
     */
    public function login()
    {
        $mobile = $this->request->get('mobile');
        $code = $this->request->get('code');
        $json_token = $this->request->get('json_token');
        if (!RegxTool::is_mobile($mobile)) {
            return Response::send(config('code.error'), '手机号格式错误', []);
        }
        if (!$json_token) {
            return Response::send(config('code.error'), '参数错误', []);
        }
        
        $allow_algs = array('HS256');
        $json_token_array = (array)JWT::decode($json_token, config('business.jwt_key'), $allow_algs);
        
        if (isset($json_token_array['code']) && $code != $json_token_array['code']) {
            return Response::send(config('code.error'), '验证码错误[1]', null);
        }
        
        #获取用户信息
        $userInfo = [
            'id' => 1,
            'nickname' => '张三'
        ];
        
        #匹配Redis
        // 这样写，资源不会释放！！！
        $redis = RedisService::getInstance()->redis;
        $ori_code = $redis->get(RedisKeyList::mobileCode($mobile));
        if (!$ori_code) {
            return Response::send(config('code.error'), '验证码过期', null);
        }
        
        if ($ori_code != $code) {
            return Response::send(config('code.error'), '验证码错误[2]', null);
        }
        
        return Response::send(config('code.success'), '登录成功', $userInfo);
    }
}