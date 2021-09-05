<?php


namespace Queue\Handler;

use Queue\DelayQueue;
use Queue\ReadyQueue;

class RedisHandler extends AbstractHandler
{
    private $redis_client;

    public function __construct($redis)
    {
        if (!(($redis instanceof \Predis\Client) || ($redis instanceof \Redis))) {
            throw new \InvalidArgumentException('Predis\Client or Redis instance required');
        }

        $this->redis_client = $redis;
    }

    public function setDelay($job_id, $topic, $exec_time, $body)
    {
        $data = [
            'job_id' => $job_id,
            'body' => $body
        ];

        $this->redis_client->zadd(DelayQueue::KEY_PREFIX_DELAY.':'.$topic, $exec_time, serialize($data));
    }

    public function getDelay($topic)
    {
        // TODO: 多进程获取延迟队列数据 或者 读写删造成数据不一致。
        $dateline = time();
        $key = DelayQueue::KEY_PREFIX_DELAY.':'.$topic;

        $list = $this->redis_client->zrevrangebyscore($key, $dateline, 0 ,array('withscores' => true));

        $this->redis_client->zremrangebyscore($key,0,$dateline);

        $new_list = [];

        foreach ($list as $key => $value) {
            $data = unserialize($key);
            $new_list[] = $data['body'];
        }

        return $new_list;
    }

    public function setReady($topic, $body)
    {
        $result = $this->redis_client->lpush(ReadyQueue::KEY_PREFIX_READY.':'.$topic, serialize($body));
        return empty($result) ? false : true;
    }

    public function getReady($topic)
    {
        $value = $this->redis_client->rpop(ReadyQueue::KEY_PREFIX_READY.':'.$topic);
        if (empty($value)){
            return false;
        }

        return unserialize($value);
    }
}