<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/26/026
 * Time: 9:43
 */
namespace HomeBundle\Controller;

use Trochilidae\bin\Core\Controller;
use Trochilidae\bin\Lib\Http;


class IndexController extends Controller
{

    public function indexAction(){
        $this->render('index.html.twig@HomeBundle:Index',['projectName'=>'Trochilidae']);
    }
}