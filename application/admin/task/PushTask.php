<?php
namespace app\admin\task;

use app\common\service\RedisKeyList;
use app\common\service\RedisService;

class PushTask{
    /**
     * 
     * @param \Swoole\Server $serv
     * @param array $data
     */
    public function execute($serv, $data)
    {
        $redis = RedisService::getInstance()->redis;
        $onlineFd = $redis->sMembers(RedisKeyList::onlineUserFdList());
        
        /*
         * 记录推送状态，每次推送花的时间，成功率是多少
         */
        foreach ($onlineFd as $v) {
            $serv->push($v, isset($data['msg']) ? $data['msg'] : json_encode($data));
        }
        
        return 'success';
    }
}