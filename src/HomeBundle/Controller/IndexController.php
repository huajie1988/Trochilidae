<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/26/026
 * Time: 9:43
 */
namespace HomeBundle\Controller;

use Trochilidae\bin\Core\Entity;
use Trochilidae\bin\Core\Model;
use Trochilidae\bin\Core\Route;
use HomeBundle\Entity\User;
use Trochilidae\bin\Lib\Cookie;
use Trochilidae\bin\Lib\Session;

class IndexController extends BaseController
{

    public function indexAction(){
//        $this->redirect('/test/id/3');
//        print_r(Cookie::get('aa'));

        $this->render('index.html.twig@HomeBundle:Index',['string'=>'Hello World !']);
    }

    public function testAction(Route $route){

        $em=new Entity();
        /**
         * @var $a User
         */
//        $a=$em->get('User@HomeBundle')->create();
        $a=$em->get('User@HomeBundle')->switchDataBase('test')->findById(4);
        $a->setUserName('aaa');
        $a->setAvatar('这是头像');
        $a->setStatus(1);
        $a->setDescription('sdsda');
        $a->setEmail('dert');
        $a->setPassword('sqw222');
        $a->setLoginTime(date('Y-m-d H:i:s'));
        $ret=$em->save($a);

//        $em=new Entity();
//        $a=$em->get('User@HomeBundle')->findById(2);
//        $a->setUserName('aaa');
//        $a->setAvatar('这是头像2');
//        $a->setStatus(1);
//        $a->setDescription('sdsda');
//        $a->setEmail('dert');
//        $a->setPassword('sqw222');
//        $a->setLoginTime(date('Y-m-d H:i:s'));

//        $ret=$em->save($a);

//        $ret=$em->test([1,2],[3,5]);

//        $route->request('id','int')
        print_r($ret);
    }
}