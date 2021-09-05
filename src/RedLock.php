<?php


namespace Queue;


class RedLock
{
    private $instance;

    function __construct($redis)
    {
        $this->instance = $redis;
    }

    public function lock($key, $ttl)
    {
        $token = uniqid();
        $result = $this->lockInstance($key, $token, $ttl);
        if(empty($result)){
            return false;
        }

        return $token;
    }

    public function unlock($key, $token)
    {
        $this->unlockInstance($key, $token);
    }

    private function lockInstance($resource, $token, $ttl)
    {
        return $this->instance->set($resource, $token, ['NX', 'PX' => $ttl]);
    }

    private function unlockInstance($resource, $token)
    {
        $script = '
            if redis.call("GET", KEYS[1]) == ARGV[1] then
                return redis.call("DEL", KEYS[1])
            else
                return 0
            end
        ';
        return $this->instance->eval($script, [$resource, $token], 1);
    }
}