<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/28/028
 * Time: 23:47
 */

namespace Trochilidae\bin\Core;



use Trochilidae\bin\Common\Utils;
use Trochilidae\bin\Lib\Ioc;

class Entity
{
    private $entity=null;
    private $model;
    private $dbConfig=[];
    public function get($target){
        if(strstr('@',$target)){
            throw new \Exception('The  '.$target.' incorrectly formatting');
            exit();
        }

        list($entityName,$bundleName)=Utils::explodeStringBySymbol($target);

        if(trim($bundleName)=='' || trim($entityName)==''){
            throw new \Exception('The  '.$target.' incorrectly formatting');
            exit();
        }
        $this->entity=$bundleName.'\\Entity\\'.ucfirst($entityName);
        $this->model=$bundleName.'\\Model\\'.ucfirst($entityName).'Model';
        return $this;
    }

    public function create(){
        return Ioc::getInstance($this->entity);
    }

    public function __call($name, $arguments)
    {

        // TODO: Implement __call() method.

        $instance=Ioc::getInstance($this->model,$this->dbConfig);
        if(!method_exists($instance,$name)){
            $ret = Ioc::make($instance,$this->model,$name,$arguments);
            $ret = $this->ArroytoEntity($ret);
        }else{
            $ret =  $instance->$name(...$arguments);
        }

        return $ret;

    }

    public function ArroytoEntity($result,$entity=null){

        if($this->entity==null){
            throw new \Exception('The entity is null');
            exit();
        }

        if($entity==null)
            $entity=$this->entity;

        $instance=Ioc::getInstance($entity);
        $utilsInstance=Utils::getInstance();
        $flag=false;

//        if(is_null($result))
//            return $instance;

        if(!is_array($result))
            return $result;

        foreach ($result as $key=>$item) {
            $key=$utilsInstance::convertUnderline($key);
            $method='set'.$key;

            if(method_exists($instance,$method)){
                $flag=true;
                $instance->{$method}($item);
            }
        }

        return $flag?$instance:$result;
    }

    public function EntityToArray($entity){

        $class = new \ReflectionClass($entity);
        $properties=$class->getProperties();
        $array=[];
        foreach ($properties as $propertie) {
            $name=$propertie->getName();
            $method='get'.Utils::convertUnderline($name);
            $array[$name]=$entity->$method();
        }
        return $array;
    }

    public function switchDataBase($dbConfig){
        $database=Config::getOneConfig($dbConfig,'database');
        $this->dbConfig=Model::switchDataBase($database,[]);
        return $this;
    }
}