<?php


namespace Queue;

use Queue\Handler\AbstractHandler;

class DelayQueue
{
    const KEY_PREFIX_DELAY = 'delay';

    protected $handler;

    public function __construct($handler = null)
    {
        if (!($handler instanceof AbstractHandler)) {
            throw new \InvalidArgumentException('AbstractHandler instance required');
        }

        $this->handler = $handler;
    }

    public function set($topic, $exec_time, $body){
        return $this->handler->setDelay($this->_create_id(), $topic, $exec_time, $body);
    }

    public function get($topic){
        return $this->handler->getDelay($topic);
    }

    /**
     * 生产唯一标识,根据微秒
     */
    private function _create_id() {
        $sn = 'TKID';
        $sn .= floor(microtime(true) * 10000);
        $sn .= mt_rand(100000, 999999);
        return $sn;
    }
}