<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/8/008
 * Time: 18:07
 */

namespace Trochilidae\bin\Lib;


class Aop
{

    private $object=null;
    private $logger=null;

    public function __construct($object,$logger){
        $this->object=$object;
        $this->logger=$logger;
        return $this;
    }

    public function __call($name, $arguments)
    {
        // TODO: Implement __call() method.
        $start_time = microtime(true);
        $this->logger->addInfo(get_class($this->object).' start');
        $ret=$this->object->$name(...$arguments);
        $end_time = microtime(true);
        $this->logger->addInfo(get_class($this->object).' end',['executionTime(ms)'=>($end_time-$start_time)*1000]);
        return $ret;
    }

    public function getObject(){
        return $this->object;
    }
}