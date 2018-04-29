<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/28/028
 * Time: 17:33
 */

namespace Trochilidae\bin\Lib\Console\CreateEntity;


class CreateEntityConfigFactory implements CreateEntityConfigFactoryInterface
{
    private $factory;
    public function __construct($factory){
        $this->factory=$factory;
    }

    public function get($fileList){
        // TODO: Implement get() method.
        return $this->factory->get($fileList);
    }
}