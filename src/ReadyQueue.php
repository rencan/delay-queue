<?php


namespace Queue;


use Queue\Handler\AbstractHandler;

class ReadyQueue
{
    const KEY_PREFIX_READY = 'ready';

    protected $handler;

    public function __construct($handler = null)
    {
        if (!($handler instanceof AbstractHandler)) {
            throw new \InvalidArgumentException('AbstractHandler instance required');
        }

        $this->handler = $handler;
    }

    public function set($topic, $body){
        return $this->handler->setReady($topic, $body);
    }

    public function get($topic){
        return $this->handler->getReady($topic);
    }
}