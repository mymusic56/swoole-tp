<?php
namespace app\admin\controller;

use think\Controller;
use app\index\util\Response;
use app\common\service\SwooleRedis;
use app\common\service\RedisKeyList;

class Live extends Controller{
    
    public function index()
    {
        $this->assign('title', '消息发布平台');
        return $this->fetch('admin@live/index');
    }
    
    /**
     * 推送通知消息
     */
    public function push()
    {
        $msg = $this->request->param('msg');
        # 添加任务
        /* @var $http \Swoole\Server */
        $http = $this->request->param('http');
        $data = [
            'class' => 'app\\admin\\task\\PushTask',
            'method' => 'execute',
            'msg' => $msg
        ];
        $taskNo = $http->task($data);
        
        if ($taskNo) {
            return Response::send(1, 'success', $msg);
        }
        return Response::send(-1, 'task send error', $msg);
    }
}