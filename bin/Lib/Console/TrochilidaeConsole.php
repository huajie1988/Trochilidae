<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/28/028
 * Time: 12:16
 */

namespace Trochilidae\bin\Lib\Console;

use Trochilidae\bin\Core\Model;
use Trochilidae\bin\Lib\Console\CreateEntity\CreateEntityConfigFactory;
use Trochilidae\bin\Lib\Console\CreateEntity\CreateEntityFactory;

class TrochilidaeConsole
{

    public	function run($argv,$argc){

        if($argc==1){
            $this->help();
        }

        $op=$argv[1];
        if(method_exists($this,$op)){
            unset($argv[0]);
            unset($argv[1]);
            $this->$op(array_values($argv));
        }

    }

    private function help(){
        $sting=<<<HELP
        createEntity <path|file>
        updateDataBase
        clear
        suggest
HELP;

        print_r($sting);
        exit();
    }

    public function clear(){
        $this->doClear(TROCHICONSOLE.'/../storage',false);
    }

    private function doClear($dirname, $self = true) {
        if (!file_exists($dirname)) {
            return false;
        }
        if (is_file($dirname) || is_link($dirname)) {
            return unlink($dirname);
        }
        $dir = dir($dirname);
        if ($dir) {
            while (false !== $entry = $dir->read()) {
                if ($entry == '.' || $entry == '..') {
                    continue;
                }
                $this->doClear($dirname . '/' . $entry);
            }
        }
        $dir->close();
        $self && rmdir($dirname);
    }

    public function updateDataBase($argv){
        $sqlFile=TROCHICONSOLE.'/../storage/framework/sql/current.sql';
        $sql=file_get_contents($sqlFile);
        $model=new Model();
        $model->query($sql)->fetchAll();
    }


    public	function createEntity($argv){
        $path=current($argv);

        if (!is_dir($path) && !is_file($path)) {
            print_r('The params not found');
            exit();
        }

        if(is_file($path)){
            $fileList[]=$path;
        }else{
            foreach(glob($path . '/*') as $filename){
                $fileList[]=$filename;
            }
        }

        $entitySQLList=[];
        $model=new Model();
        $tableList=$model->query('show tables')->fetchAll();
        $tables=[];
        foreach ($tableList as $table) {
            $tables[]=$table[0];
        }

        foreach ($fileList as $key => $filePath) {
            # code...
            $extension = pathinfo($filePath)['extension'];
            $basename = strtolower(str_replace('.'.$extension,'',basename($filePath)));

            if(!in_array($extension,['json','yml','xml'])){
                continue;
            }

            $class='Trochilidae\bin\Lib\Console\CreateEntity\CreateEntityConfigBy'.strtoupper($extension);
            if(!class_exists($class)){
                print_r('The Class '.$class.' not find');
                exit();
            }
            $cecf=new CreateEntityConfigFactory(new $class);
            $entityJson= $cecf->get($filePath);
            $class='Trochilidae\bin\Lib\Console\CreateEntity\CreateEntityBy'.strtoupper($extension);
            if(!class_exists($class)){
                print_r('The Class '.$class.' not find');
                exit();
            }

            $result=[];
            if(in_array($basename,$tables)){
                $result=$model->select($basename,'*');
            }

            $cef=new CreateEntityFactory(new $class);
            $entitySQLList[]=$cef->createEntitySQL($entityJson,$basename,$result,$filePath);
            $entityClass=$cef->createEntityFile($entityJson,$filePath);
            $entityClassFile=pathinfo($filePath)['dirname'].'/'.ucfirst($basename).'.php';
            if(is_file($entityClassFile)){
                rename($entityClassFile,$entityClassFile.'~');
            }
            file_put_contents($entityClassFile,$entityClass);
        }
        $storage=TROCHICONSOLE.'/../storage/framework/sql';
        if(!is_dir($storage)){
            mkdir($storage,0777,true);
        }
        $entitySQLListTxt=join(PHP_EOL,$entitySQLList);
        if(is_file($storage.'/current.sql')){
            rename($storage.'/current.sql',$storage.'/'.time().'.sql');
        }
        file_put_contents($storage.'/current.sql',$entitySQLListTxt);

    }

    public function suggest(){
        $script=TROCHICONSOLE.'/Lib/Console/phpcpd.phar';
        $checkPath=TROCHI.'/src';
        $storage=TROCHICONSOLE.'/../storage';
        if(!is_dir($storage)){
            mkdir($storage,0777,true);
        }

        $output = shell_exec('php '.$script.' '.$checkPath);
        $script=TROCHICONSOLE.'/Lib/Console/phpmd.phar';
        $output .= shell_exec('php '.$script.' '.$checkPath.' text codesize,unusedcode,naming');
        file_put_contents($storage.'/suggest.txt',$output);
    }

}