<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/28/028
 * Time: 18:07
 */

namespace Trochilidae\bin\Lib\Console\CreateEntity;


class CreateEntityFactory implements CreateEntityFactoryInterface
{
    private $factory;
    public function __construct($factory){
        $this->factory=$factory;
    }

    public function createEntityFile($entityJson,$filePath){
        // TODO: Implement createEntityFile() method.
        return $this->factory->createEntityFile($entityJson,$filePath);
    }

    public function createEntitySQL($entityJson,$table,$result){
        // TODO: Implement createEntitySQL() method.
        return $this->factory->createEntitySQL($entityJson,$table,$result);
    }
}