<?php
class HttpServer{
    private $ws;
    public function __construct($host='0.0.0.0', $port=9502)
    {
        $this->ws = new swoole_websocket_server($host, $port);
        $this->ws->set([
            'worker_num' => 2,
            'task_worker_num' => 2,
            'enable_static_handler' => true,
            'document_root' => __DIR__.'/../public',
        ]);
        //handShake非必须
        $eventList = [
            'open',/* 'handShake', */'message','workerStart','request', 'task', 'finish','close'
        ];
        foreach ($eventList as $event) {
            $this->ws->on($event, [$this, "on".ucfirst($event)]);
        }
    }
    
    /**
     * 
     * @param swoole_websocket_server  $serv
     * @param swoole_http_request  $request
     */
    public function onOpen($serv, $request)
    {
        //将用户信息存redis无序集合里面
        $redis = \app\common\service\RedisService::getInstance()->redis;
        $redis->sAdd(\app\common\service\RedisKeyList::onlineUserFdList(), $request->fd);
        echo "client:{$request->fd} connected.\n";
    }
    
    /**
     * 
     * @param swoole_websocket_server $serv
     * @param swoole_websocket_frame $frame
     */
    public function onMessage($serv, $frame)
    {
        echo "server receive client:{$frame->fd}, message: {$frame->data}";
        $serv->push($frame->fd, "server receive client message: {$frame->data}");
    }
    
    /**
     * 
     * @param swoole_websocket_server $serv
     * @param int $workerId
     */
    public function onWorkerStart($serv, $workerId)
    {
        //加载TP静载资源文件
        // 定义应用目录
        define('APP_PATH', __DIR__ . '/../application/');
        require __DIR__ . '/../thinkphp/base.php';
    }
    
    /**
     *
     * @param swoole_websocket_server $serv
     * @param int $taskId   $task_id和$src_worker_id组合起来才是全局唯一的，不同的worker进程投递的任务ID可能会有相同
     * @param int $workId   来自于哪个worker进程
     * @param mixed $data   任务的内容
     * @return string       在onTask函数中 return字符串，表示将此内容返回给worker进程。worker进程中会触发onFinish函数，表示投递的task已完成。
     * 1. task进程的onTask事件中没有调用finish方法或者return结果，worker进程不会触发onFinish
     * 2. 函数执行时遇到致命错误退出，或者被外部进程强制kill，当前的任务会被丢弃，但不会影响其他正在排队的Task
     */
    public function onTask($serv, $taskId, $workId, $data)
    {
        $class = isset($data['class']) ? $data['class'] : '';
        $method = isset($data['method']) ? $data['method'] : '';
        
        # 将错误日志记录到系统日志中，方便现实查找问题
        if ($class && $method && class_exists($class)) {
            $obj = new $class();
            if (method_exists($obj, $method)) {
                $obj->$method($serv, $data);
            }else{
                echo $class.'->'.$method.'()不存在';
            }
        }else{
            echo $class."不存在";
        }
        return 'success';
    }
    
    /**
     *
     * @param \Swoole\Http\Request $request
     * @param \Swoole\Http\Response $response
     */
    public function onRequest($request, $response)
    {
        # 记录请求日志
        
        #将请求参数赋值给TP框架
        $_GET = $request->get;
        $_POST = $request->post;
        $_FILES = $request->files;
        $_SERVER = [];
        foreach ($request->server as $k => $v) {
            $_SERVER[strtoupper($k)] = $v;
        }
        $_GET['http'] = $this->ws;
        ob_start();
        think\App::run()->send();
        $data = ob_get_contents();
        ob_clean();
        $response->header('Content-Type', 'text/html;charset=utf-8');
        if (isset($_SERVER['Content-Type'])) {
            $response->header('Content-Type', $_SERVER['Content-Type']);
        }
        $response->end($data);
    }
    
    public function start()
    {
        $this->ws->start();
    }
    
    /**
     * 进程任务结束后需要调用该回调函数
     * @param swoole_server $serv
     * @param int $task_id
     * @param string $data
     */
    public function onFinish($serv, $task_id, $data)
    {
        
    }
    /**
     * 
     * @param swoole_server $serv
     * @param int $fd
     * @param int $reactorId
     */
    public function onClose($serv, $fd, $reactorId)
    {
        //将退出的用户从在线用户中移除
        $redis = \app\common\service\RedisService::getInstance()->redis;
        $redis->srem(\app\common\service\RedisKeyList::onlineUserFdList(), $fd);
        echo "client: $fd closed \n";
    }
}

$server = new HttpServer();
$server->start();