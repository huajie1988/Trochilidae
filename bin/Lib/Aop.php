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
    private $info=[];

    public function __construct($object,$logger){
        $this->object=$object;
        $this->logger=$logger;
        return $this;
    }

    public function __call($name, $arguments)
    {
        // TODO: Implement __call() method.
        $this->before($arguments);
        $ret=$this->object->$name(...$arguments);
        $this->after($ret);
        return $ret;
    }

    public function getObject(){
        return $this->object;
    }

    protected function before(&$arg){
        $start_time = microtime(true);
        $this->info['start_time']=$start_time;
        $this->logger->info(get_class($this->object).' start');
    }

    //ret result from do method
    protected function after(&$ret){
        $start_time=$this->info['start_time'];
        $end_time = microtime(true);
        $this->logger->info(get_class($this->object).' end',['executionTime(ms)'=>($end_time-$start_time)*1000]);
    }

    public function setInfo($key,$val){
        $this->info[$key]=$val;
    }

    public function getInfo($key){
        return $this->info[$key];
    }

}