<?php
namespace HomeBundle\Entity;
class User {
	private $id;
	private $user_name;
	private $password;
	private $avatar;
	private $description;
	private $email;
	private $login_time;
	private $status;

	public function getId(){
		return $this->id;
	}

	public function setId($id){
		$this->id = $id;
	}

	public function getUserName(){
		return $this->user_name;
	}

	public function setUserName($user_name){
		$this->user_name = $user_name;
	}

	public function getPassword(){
		return $this->password;
	}

	public function setPassword($password){
		$this->password = $password;
	}

	public function getAvatar(){
		return $this->avatar;
	}

	public function setAvatar($avatar){
		$this->avatar = $avatar;
	}

	public function getDescription(){
		return $this->description;
	}

	public function setDescription($description){
		$this->description = $description;
	}

	public function getEmail(){
		return $this->email;
	}

	public function setEmail($email){
		$this->email = $email;
	}

	public function getLoginTime(){
		return $this->login_time;
	}

	public function setLoginTime($login_time){
		$this->login_time = $login_time;
	}

	public function getStatus(){
		return $this->status;
	}

	public function setStatus($status){
		$this->status = $status;
	}

}
