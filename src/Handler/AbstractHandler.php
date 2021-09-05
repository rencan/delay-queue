<?php


namespace Queue\Handler;


abstract class AbstractHandler
{
    abstract public function setDelay($job_id, $topic, $exec_time, $body);

    abstract public function getDelay($topic);

    abstract public function setReady($topic, $body);

    abstract public function getReady($topic);
}