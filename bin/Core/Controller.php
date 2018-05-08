<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/26/026
 * Time: 10:01
 */

namespace Trochilidae\bin\Core;


use Trochilidae\bin\Common\Utils;
use Trochilidae\bin\Lib\Http;
use Trochilidae\bin\Lib\Ioc;

class Controller
{
   protected $request=null;
   protected function __before(){
   }

    protected function __after(){
    }

    public function __run($classFileName,$methodName,Route $route){
        $className='\\'.str_replace('/','\\',$classFileName);

        $params=[
            $route
        ];


        $instance=Ioc::getInstance($className);
        if(!method_exists($instance,$methodName)){
            throw new \Exception('The method '.$methodName.' not find');
            exit();
        }
        $instance->__before();
        Ioc::make($instance,$className,$methodName,$params);
        $instance->__after();
    }

    public function render($tpl,$data){
        echo $this->renderOnly($tpl,$data);
    }

    public function renderOnly($tpl,$data,$isReplease=false){
       if(!$isReplease){
           list($tplName,$pathName)=Utils::explodeStringBySymbol($tpl);
           if($tplName=='' || $pathName==''){
               throw new \Exception('The tpl'.$tpl.' incorrectly formatting');
               exit();
           }

           if(strpos($pathName,':')>0){
               list($bundleName,$BundlepathName)=Utils::explodeStringBySymbol($pathName,":");

               if($bundleName=='' || $BundlepathName==''){
                   throw new \Exception('The tpl'.$tpl.' incorrectly formatting');
                   exit();
               }
           }else{
               $bundleName=$pathName;
               $BundlepathName='';
           }


           $mid_path=Config::getOneConfig('view_mid_path','site');

           $tplPath=APP.'/'.$bundleName.$mid_path;

           $tplFile=($BundlepathName?'/':'').$BundlepathName.'/'.$tplName;

           if(!is_file($tplPath.'/'.$tplFile)){
               throw new \Exception('The tpl file '.$tplPath.'not found');
               exit();
           }
       }else{
           $pathInfo=pathinfo($tpl);
           $tplPath=$pathInfo['dirname'];
           $tplFile='/'.$pathInfo['basename'];
       }

        $loader = new \Twig_Loader_Filesystem($tplPath);
        $twig = new \Twig_Environment($loader, array(
            'cache' => TROCHI.'/storage/framework/twig',
            'debug'=>DEBUG
        ));

        $template = $twig->load($tplFile);
        return $template->render($data);
    }

    public function redirect($url){
       ob_clean();
       header('Location:'.$url);
       exit();
    }

    public function ajaxResponse($code=10000,$msg='',$responseCode=200,$data=[]){
       Http::response($responseCode,json_encode([
           'code'=>$code,
           'msg'=>$msg,
           'data'=>$data,
       ]),['Content-Type'=>'application/json']);
       exit();
    }
}