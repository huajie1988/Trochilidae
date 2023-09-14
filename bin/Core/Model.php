<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/26/026
 * Time: 16:06
 */

namespace Trochilidae\bin\Core;


use Medoo\Medoo;
use Trochilidae\bin\Common\Utils;
use Trochilidae\bin\Lib\ModelSingleton;

class Model
{
    private $table;
    private $entity;
    private $modelSingleton;
    public function __construct($otherOptions = null)
    {

        $database=current((array)Config::getConfig('database'));

        $options=$this::switchDataBase($database,$otherOptions);
        $this->modelSingleton=ModelSingleton::getInstance($options);


        $reflection = new \ReflectionClass($this);
        $class=explode('\\',$reflection->getName());
        $doc=$reflection->getDocComment();
        preg_match('!@Table\s+(.+)\s+!',$doc,$match);
        if(isset($match[1])){
            $this->table=trim($match[1]);
        }else
            $this->table=str_replace('Model','',$class[count($class)-1]);

    }

    public static function switchDataBase($database,$otherOptions=[]){

        $options=(array)$database;

        if($otherOptions!=null){
            foreach ($otherOptions as $key=>$otherOption) {
                $options[$key]=$otherOption;
            }
        }

        return $options;
    }

    private function throwError(){
        $error=$this->modelSingleton->errorInfo;

        if($error!==null){
            throw new \Exception("The SQL ".$this->last().' is error,error code is '.$error[0].' error msg is '.$error[count($error)-1]);
            exit();
        }
    }

    public function __call($name, $arguments)
    {
        $result=true;
        if(preg_match('!findBy(\w+)!',$name,$match)){
            $arguments=current($arguments);

            if(!is_array($arguments)){
                $arguments=[strtolower($match[1])=>$arguments];
            }
            $result = current($this->__find($arguments));
        }

        if(preg_match('!updateBy(\w+)!',$name,$match)){
            $where=[strtolower($match[1])=>$arguments[0]];
            $result=$this->__update($arguments[1],$where);
            $result = is_array($result)?current($result):$result;
        }

        if(preg_match('!delBy(\w+)!',$name,$match)){
            $arguments=current($arguments);

            if(!is_array($arguments)){
                $arguments=[strtolower($match[1])=>$arguments];
            }
            $result = $this->__del($arguments);
            $result = is_array($result)?current($result):$result;
        }

        $this->throwError();

        if($result==false)
            $result=null;
        return $result;
    }

    public function add($data){
        return $this->modelSingleton->insert($this->table,$data);
    }

    public function __update($data,$where){
        return $this->modelSingleton->update($this->table,$data,$where);
    }

    public function __find($where){
        return $this->modelSingleton->select($this->table,'*',$where);
    }

    public function __del($where){
        return $this->modelSingleton->delete($this->table,$where);
    }

    public function find($col,$where,$join=null){

        if($join==null){
            $ret=$this->modelSingleton->select($this->table,$col,$where);
        }else{
            $ret=$this->modelSingleton->select($this->table,$join,$col,$where);
        }
        $this->throwError();

        return $ret;
    }

    public function findOne($col,$where,$join=null){
        return current($this->find($col,$where,$join));
    }

    public function getTable(){
        return $this->table;
    }

    public function updateRelationEntity($sourceData,$relationData){

        $sourceKey=current(array_keys($sourceData));
        $relationKey=current(array_keys($relationData));
        $sourceValue=$sourceData[$sourceKey];
        $relationValue=$relationData[$relationKey];

        list($sourceTable,$sourceField)=Utils::explodeStringBySymbol($sourceKey,'.');
        list($relationTable,$relationField)=Utils::explodeStringBySymbol($relationKey,'.');
        $table=$sourceTable.'_'.$relationTable;
        //fitter some do not find data
        $sourceData=$this->modelSingleton->select($sourceTable,$sourceField,[$sourceField=>$sourceValue]);
        $relationData=$this->modelSingleton->select($relationTable,$relationField,[$relationField=>$relationValue]);

        $this->modelSingleton->delete($table,[($sourceTable.'_'.$sourceField)=>$sourceValue]);

        foreach ($sourceData as $sourceDatum) {
            foreach ($relationData as $relationDatum) {
                $this->modelSingleton->insert($table,[
                    ($sourceTable.'_'.$sourceField)=>$sourceDatum,
                    ($relationTable.'_'.$relationField)=>$relationDatum,
                ]);
            }
        }

        $this->throwError();

        return true;
    }

    public function updateByEntity($data, $where = null){
        $oldData=$this->findOne('*',$where);
        if ($oldData===false) return 0;
        $entity=Entity::getInstance();
        $data=$entity->EntityToArray($data);
        $data=array_diff($data,$oldData);
        if(empty($data))
            return 0;
        $ret  = parent::update($this->table, $data, $where); // TODO: Change the autogenerated stub
        $this->throwError();

        return $ret->rowCount();
    }

    public function insertByEntity($datas){
        $entity=Entity::getInstance();
        $datas=$entity->EntityToArray($datas);
        parent::insert($this->table, $datas); // TODO: Change the autogenerated stub
        $this->throwError();
        return $this->modelSingleton->id();
    }

    public function deleteByEntity($where){
        $ret = parent::delete($this->table, $where); // TODO: Change the autogenerated stub
        $this->throwError();
        return $ret;
    }

    public function getModelSingleton(){
        return $this->modelSingleton;
    }
}