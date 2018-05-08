<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/29/029
 * Time: 0:42
 */

namespace HomeBundle\Model;



use Trochilidae\bin\Core\Entity;
use Trochilidae\bin\Core\Model;
use HomeBundle\Entity\User;

class UserModel extends Model implements UserModelIfs
{
    public function save(User $user)
    {
        $this->pdo->beginTransaction();
        // TODO: Implement save() method.
//        $this->updateRelationEntity(["User.id"=>['1',2,3]],["File.id"=>['1','2','3']]);
        if(intval($user->getId())==0){
           $ret = $this->insertByEntity($user);
           $user->setId($ret);
        }else{
           $this->updateByEntity($user,['id'=>$user->getId()]);
        }
        $this->pdo->commit();
        return $user;
    }

    public function test($tt,$re){
        return $tt+$re;
    }

    public function findByUser(User $user)
    {
        // TODO: Implement findByUser() method.
    }

    public function findByUserId($id)
    {
        // TODO: Implement findByUserId() method.
    }
}