<?php
namespace app\admin\controller;

use think\Controller;
use app\index\util\Response;

class Live extends Controller{
    
    public function index()
    {
        $this->assign('title', '消息发布平台');
        return $this->fetch();
    }
    
    /**
     * 推送通知消息
     */
    public function push()
    {
        $msg = $this->request->post('msg');
        return Response::send(1, 'success', $msg);
    }
}