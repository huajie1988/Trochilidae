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

class Model extends Medoo
{
    private $table;
    private $entity;
    public function __construct($otherOptions = null)
    {

        $database=current((array)Config::getConfig('database'));
        $this->entity=new Entity();
        $options=$this::switchDataBase($database,$otherOptions);
        parent::__construct($options);

        $reflection = new \ReflectionClass($this);
        $class=explode('\\',$reflection->getName());
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
        $error=$this->error();
        if($error[1]!=''){
            throw new \Exception("The SQL ".$this->last().' is error,error code is '.$error[0].' error msg is '.$error[count($error)-1]);
            exit();
        }
    }

    public function __call($name, $arguments)
    {
        $arguments=current($arguments);

        if(preg_match('!findBy(\w+)!',$name,$match)){
            if(!is_array($arguments)){
                $arguments=[strtolower($match[1])=>$arguments];
            }
        }

        $result = current($this->__find($arguments));

        $this->throwError();
        if($result==false)
            $result=null;
        return $result;
    }

    public function __find($where){
        return $this->select($this->table,'*',$where);
    }

    public function find($col,$where,$join=null){

        if($join==null){
            $ret=$this->select($this->table,$col,$where);
        }else{
            $ret=$this->select($this->table,$join,$col,$where);
        }
        $this->throwError();

        return $ret;
    }

    public function findOne($col,$where,$join=null){
        return current($this->find($col,$where,$join));
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
        $sourceData=$this->select($sourceTable,$sourceField,[$sourceField=>$sourceValue]);
        $relationData=$this->select($relationTable,$relationField,[$relationField=>$relationValue]);

        $this->delete($table,[($sourceTable.'_'.$sourceField)=>$sourceValue]);

        foreach ($sourceData as $sourceDatum) {
            foreach ($relationData as $relationDatum) {
                $this->insert($table,[
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
        $data=$this->entity->EntityToArray($data);
        $data=array_diff($data,$oldData);
        if(empty($data))
            return 0;
        $ret  = parent::update($this->table, $data, $where); // TODO: Change the autogenerated stub
        $this->throwError();

        return $ret->rowCount();
    }

    public function insertByEntity($datas){
        $datas=$this->entity->EntityToArray($datas);
        parent::insert($this->table, $datas); // TODO: Change the autogenerated stub
        $this->throwError();
        return $this->id();
    }

    public function deleteByEntity($where){
        $ret = parent::delete($this->table, $where); // TODO: Change the autogenerated stub
        $this->throwError();
        return $ret;
    }

}