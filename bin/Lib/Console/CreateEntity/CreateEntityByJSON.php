<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/28/028
 * Time: 18:11
 */

namespace Trochilidae\bin\Lib\Console\CreateEntity;


class CreateEntityByJSON implements CreateEntityFactoryInterface
{
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
        $entityJsonKeys=array_keys($entityJson);

        foreach ($entityJsonKeys as $entityJsonKey) {
            $entityClassText.="\t".'private $'.$entityJsonKey.';'.PHP_EOL;
        }
        $entityClassText.=PHP_EOL;
        foreach ($entityJson as $field => $info) {
            $fieldList=explode('_',$field);
            $fields='';
            foreach ($fieldList as $item) {
                $fields[]=ucfirst($item);
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

    public function createEntitySQL($entityJson,$table,$result){
        // TODO: Implement createEntitySQL() method.
        # code...
        $sql="DROP TABLE IF EXISTS `$table`;CREATE TABLE `$table`(";
        $keyList=[];
        $sqlField=[];
        $entityJson=(array)$entityJson;
        $entityJsonKeys=array_keys($entityJson);

        foreach ($entityJson as $field => $info) {
            if (isset($info->key)) {
                # code...
                $keyList[]=$field;
            }
            $sqlField[]=$this->getCreateTableField($field,$info);

        }
        $sql.=join($sqlField,',');
        if(count($keyList)>0)
            $sql.=',PRIMARY KEY ('.join($keyList,',').')';
        $sql.=");\n";

        $sql.=$this->createRecoverySQL($entityJsonKeys,$result);
        $sql=str_replace('%TABLE%',$table,$sql);

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
            $sql.='NOT NULL';
        }

        return $sql;
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