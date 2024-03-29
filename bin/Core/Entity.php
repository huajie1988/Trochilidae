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
    private $modelInstance=null;
    private static $instance;

    private function __construct(){

    }

    private function __clone(){
        // TODO: Implement __clone() method.
    }

    public static function getInstance(){
        if (!(self::$instance instanceof self)) {
            self::$instance = new static();
        }
        return self::$instance;
    }

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
        $this->modelInstance=null;
        return $this;
    }

    public function copy(){
        $newInstance=new Entity();
        $newInstance->model=$this->model;
        $newInstance->entity=$this->entity;
        $newInstance->modelInstance=$this->modelInstance;
        return $newInstance;
    }

    public function create(){
        return Ioc::getInstance($this->entity);
    }

    public function __call($name, $arguments)
    {

        // TODO: Implement __call() method.

        if($this->modelInstance==null)
            $this->modelInstance=Ioc::getInstance($this->model,$this->dbConfig);
        $instance=$this->modelInstance;

        if(!method_exists($instance,$name)){
            $ret = Ioc::make($instance,$this->model,$name,$arguments);
            $ret = $this->ArroytoEntity($ret);
        }else{
            $ret =  $instance->$name(...$arguments);
        }

        return $ret;

    }

    public function ArroytoEntity($result,$entity=null){

        if($this->entity==null && $entity==null){
            throw new \Exception('The entity is null');
            exit();
        }

        if($entity==null && $this->entity!=null)
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
        $this->dbConfig=Model::switchDataBase($database);
        $this->modelInstance=Ioc::getInstance($this->model,$this->dbConfig);
        return $this;
    }

    public function getMappingSql($mappingId,$params){
        list($bundle,$mappingFile,$mappingId)=Utils::explodeStringBySymbol($mappingId,'\\');
        foreach ($params as $paramKey=>$param) {
            $$paramKey=$param;
        }


        $mappingPath=Config::getOneConfig('mapping_path','site');
        $mappingFile=TROCHI.'/src/'.$bundle.$mappingPath.'/'.$mappingFile.'.xml';
        $mappings=simplexml_load_file($mappingFile);
        $sql='';
        $db='';
        foreach ($mappings as $mapping) {
            $attributes=$mapping->attributes();
            $id=((array)$attributes['id'])[0];
            if($id==$mappingId){
                $sql=((array)$mapping)[0];
                if(!empty((array)$attributes['db'])){
                    $db=((array)$attributes['db'])[0];
                }
            }
        }

        $tps=[
            '/({if:.*}[^\/]*{\/if})/',
            '/({\$[a-zA-Z_][\w]+})/'
        ];
        $tpls=[
            '/{if:(.*)}(.*)/',
            '/{else if:(.*)}/',
            '/{else:}/',
            '/{(\$[a-zA-Z_][\w]+)}/',
            '/({\/if})/'
        ];
        $trls=[
            'if (\\1){return "',
            '";}else if (\\2){return "',
            '";}else{return "',
            'return "\\1";',
            '";}'
        ];
        foreach ($tps as $tp) {
            preg_match_all($tp,$sql,$matchs);
            foreach ($matchs[0] as $match) {

                $tr=eval(preg_replace($tpls,$trls,$match));
                $sql=str_replace($match,$tr,$sql);
            }

        }

        return [
            'sql'=>$sql,
            'db'=>$db
        ];
    }

    public function doMapping($mappingId,$params,$fetch_style=0){
        $mappingSql=$this->getMappingSql($mappingId,$params);
        $sql=$mappingSql['sql'];
        $db=$mappingSql['db'];
        $dbConfig=(array)Config::getConfig('database');

        if(trim($db)==''){
            $db=current($dbConfig);
        }else{
            $db=$dbConfig[$db];
        }
        $fetch_result=\PDO::FETCH_ASSOC;
        if($fetch_style==1){
            $fetch_result=\PDO::FETCH_NUM;
        }else if ($fetch_style==2){
            $fetch_result=\PDO::FETCH_BOTH;
        }
        $db=Model::switchDataBase($db);
        $model=new Model($db);

        if(preg_match('!^SELECT!i',trim($sql))){
            return $model->query($sql)->fetchAll($fetch_result);
        }else{
            if (preg_match('!^INSERT!i',trim($sql))){
                $model->query($sql);
                return $model->id();
            }
            else
                return $model->query($sql)->rowCount();
        }

    }

    public function beginTransaction(){
        if($this->modelInstance==null)
            $this->modelInstance=Ioc::getInstance($this->model,$this->dbConfig);
        $this->modelInstance->getModelSingleton()->pdo->beginTransaction();
        return $this->modelInstance;
    }

    public function commit($modelInstance=null){
        if ($modelInstance!=null)
            $modelInstance->pdo->commit();
        else
            $this->modelInstance->getModelSingleton()->pdo->commit();
    }

    public function rollback($modelInstance=null){
        if ($modelInstance!=null)
            $modelInstance->pdo->rollback();
        else
            $this->modelInstance->getModelSingleton()->pdo->rollback();
    }

}