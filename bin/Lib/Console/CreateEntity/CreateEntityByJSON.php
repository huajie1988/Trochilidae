<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/28/028
 * Time: 18:11
 */

namespace Trochilidae\bin\Lib\Console\CreateEntity;


use Trochilidae\bin\Common\Utils;

class CreateEntityByJSON implements CreateEntityFactoryInterface
{
    private $table='';
    private $entityJson='';
    public function createEntityFile($entityJson,$filePath){

        // TODO: Implement createEntityFile() method.
        $namespace=pathinfo($filePath)['dirname'];
        $namespace=str_replace('/','\\',$namespace);
        $namespace=str_replace(realpath(TROCHI.'/src').'\\','',$namespace);

        $fileName=pathinfo($filePath)['filename'];

        $entityJson=(array)$entityJson;
        $entityClassText='<?php'.PHP_EOL;
        $entityClassText.='namespace '.$namespace.';'.PHP_EOL;
//        $entityClassText.='use Trochilidae\\bin\\Core\\Entity;'.PHP_EOL;
        $entityClassText.='class '.ucfirst($fileName).' {'.PHP_EOL;
//        $entityJsonKeys=array_keys($entityJson);

        foreach ($entityJson as $field => $info) {
            if(in_array($info->type,['o2m'])){
                continue;
            }
            $entityClassText.="\t".'private $'.$field.';'.PHP_EOL;
        }
        $entityClassText.=PHP_EOL;
        foreach ($entityJson as $field => $info) {
            $fieldList=explode('_',$field);
            $fields='';
            foreach ($fieldList as $item) {
                $fields[]=ucfirst($item);
            }
            if(in_array($info->type,['o2m'])){
                continue;
            }
            $fieldName=join('',$fields);
            $entityClassText.="\t".'public function get'.$fieldName.'(){'.PHP_EOL."\t\t".'return $this->'.$field.';'.PHP_EOL."\t".'}'.PHP_EOL;
            $entityClassText.=PHP_EOL;
            $entityClassText.="\t".'public function set'.$fieldName.'($'.$field.'){'.PHP_EOL."\t\t".'$this->'.$field.' = $'.$field.';'.PHP_EOL."\t".'}'.PHP_EOL;
            $entityClassText.=PHP_EOL;

        }

        $entityClassText.='}'.PHP_EOL;
        return $entityClassText;
    }

    public function createEntitySQL($entityJson,$table,$result,$filePath){
        // TODO: Implement createEntitySQL() method.
        # code...
        $this->table=$table;
        $sql="DROP TABLE IF EXISTS `$table`;CREATE TABLE `$table`(";
        $keyList=[];
        $sqlField=[];
        $entityJson=(array)$entityJson;
        $entityJsonKeys=array_keys($entityJson);
        $this->entityJson=$entityJson;
        $relationSQL=[];
        foreach ($entityJson as $field => $info) {
            if (isset($info->key)) {
                # code...
                $keyList[]=$field;
            }
            if(in_array($info->type,['o2m','o2o'])){
                $ret=$this->getCreateRelationTableField($field,$info,$filePath);
                if(!empty($ret['filedSQL']))
                    $sqlField[]=$ret['filedSQL'];
                if(!empty($ret['tableSQL']))
                    $relationSQL[]=$ret['tableSQL'];
            }else{
                $sqlField[]=$this->getCreateTableField($field,$info);
            }

        }
        $sql.=join($sqlField,',');
        if(count($keyList)>0)
            $sql.=',PRIMARY KEY ('.join($keyList,',').')';
        $sql.=");\n";

        $sql.=$this->createRecoverySQL($entityJsonKeys,$result);
        $sql=str_replace('%TABLE%',$table,$sql);
        $sql.=join('',$relationSQL);

        return $sql;
    }

    private function getCreateTableField($field,$info)
    {

        $sql=$field.' '.$info->type;

        if(isset($info->length)){
            $sql.='('.$info->length.')';
        }


        if(isset($info->auto)){
            $sql.=' AUTO_INCREMENT';
        }

        if(isset($info->not_null)){
            $sql.=' NOT NULL';
        }

        return $sql;
    }

    private function getCreateRelationTableField($field,$info,$filePath){
        list($table,$field)=Utils::explodeStringBySymbol($info->target,'.');

        $file=file_get_contents(pathinfo($filePath)['dirname'].'/'.$table.'.json');
        $file=(array)json_decode($file);
        $infoTmp=$file[$field];
        $infoTarget['type']=$infoTmp->type;
        $infoTarget['not_null']=isset($infoTmp->not_null)?true:false;
        if(isset($infoTmp->length))
            $infoTarget['length']=$infoTmp->length;
        $infoTarget=(object)$infoTarget;

        if($info->type=='o2m'){
            $infoTmp=$this->entityJson[$field];
            $infoSource['type']=$infoTmp->type;
            $infoSource['not_null']=isset($infoTmp->not_null)?true:false;
            if(isset($infoTmp->length))
                $infoSource['length']=$infoTmp->length;
            $infoSource=(object)$infoSource;

            $new_table=strtolower($this->table.'_'.$table);

            $tableSQL="DROP TABLE IF EXISTS `$new_table`;CREATE TABLE `$new_table`(";
            $tableSQL.=$this->getCreateTableField(strtolower($this->table.'_'.$field),$infoSource).',';
            $tableSQL.=$this->getCreateTableField(strtolower($table.'_'.$field),$infoTarget);
            $tableSQL.=");".PHP_EOL;
            $sql='';
        }elseif ($info->type=='o2o'){
            $tableSQL='';
            $sql=$this->getCreateTableField(strtolower($table.'_'.$field),$infoTarget);
        }

        return [
            'tableSQL'=>$tableSQL,
            'filedSQL'=>$sql,
        ];
    }

    private function createRecoverySQL($fields,$results){

        $sql='';
        foreach ($results as $result) {
            $insertField=[];
            $insertValue=[];
            foreach ($fields as $field) {
                if(isset($result[$field])){
                    $insertField[]=$field;
                    $insertValue[]="'".$result[$field]."'";
                }
            }
            $sql.='INSERT INTO %TABLE% ('.join(',',$insertField).') VALUES ('.join(',',$insertValue).');'.PHP_EOL;
        }

        return $sql;

    }

}