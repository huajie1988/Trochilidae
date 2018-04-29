<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/28/028
 * Time: 17:39
 */

namespace Trochilidae\bin\Lib\Console\CreateEntity;


class CreateEntityConfigByJSON implements CreateEntityConfigFactoryInterface
{
    public function get($filePath)
    {
        // TODO: Implement get() method.
        $file=$this->readfile($filePath);
        return json_decode($file);
    }

    private function readFile($filePath)
    {
        $file=file_get_contents($filePath);
        return $file;
    }
}