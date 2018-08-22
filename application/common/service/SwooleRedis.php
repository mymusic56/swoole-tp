<?php
namespace app\common\service;

/**
 * 这样写，资源不会释放！！！
 * @author zhangshengji
 *
 */
class SwooleRedis{
    
    public $redis = null;
    
    private static $_instance = null;
    
    private function __construct()
    {
        $this->redis = new \Swoole\Coroutine\Redis();
        $this->redis->connect(config('redis.host'), config('redis.port'));
        if (config('redis.isAuth') === true) {
            $this->redis->auth(config('redis.auth'));
        }
    }
    
    public static function getInstance()
    {
        if (!self::$_instance || !(self::$_instance instanceof SwooleRedis)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    
}