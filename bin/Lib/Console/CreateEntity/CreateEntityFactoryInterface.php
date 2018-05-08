<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/28/028
 * Time: 18:05
 */

namespace Trochilidae\bin\Lib\Console\CreateEntity;


interface CreateEntityFactoryInterface
{
    public function createEntityFile($entityJson,$filePath);
    public function createEntitySQL($entityJson,$table,$result,$filePath);
}