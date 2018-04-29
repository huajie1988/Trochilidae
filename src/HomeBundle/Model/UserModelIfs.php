<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/29/029
 * Time: 0:47
 */

namespace HomeBundle\Model;



use HomeBundle\Entity\User;

interface UserModelIfs
{
    public function save(User $user);
    public function findByUser(User $user);
    public function findByUserId($id);
}