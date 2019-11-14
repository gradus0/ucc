<?php
/**
 * Created by gradus.
 * email: gradus0@mail.ru
 * Date: 15.11.14
 * Time: 19:48
 */

namespace ucc\systems\session;

use ucc\systems\patterns\singlton as s_singlton;
use ucc\systems\server\serverMake as s_server;


class sessionMake extends s_singlton{

	private $session_bool;
	private $user_func;

	function __construct(){
		session_start(); // start session
		$this->session_bool = true;
		$this->user_func = array();

		$serverObj = s_server::getInstance();
		$serverObj->shutdown->add_function(array($this,'close'));
	}

	public function __set($name, $value)
	{
		$_SESSION[$name] = $value;
	}


	public function &__get($name)
	{
		if (is_scalar($_SESSION[$name])) {
			$property = $_SESSION[$name];
		} else {
			$property = & $_SESSION[$name];
		}
		return $property;
	}

	public function __isset($name)
	{
		return isset($_SESSION[$name]);
	}

	public function __unset($name)
	{
		unset($_SESSION[$name]);
	}

	public function __invoke(array $vars){
		$_SESSION = array_merge($_SESSION, $vars);
	}

	public function destroy()
	{
		if($this->session_bool){
			$_SESSION = array();
			session_destroy();
		}
	}

	public function close(){
		if($this->session_bool){
			$this->call_func_before_close();
			$this->session_bool = false;
			session_write_close();
		}
	}

	public function __destruct(){
		$this->close();
	}

	public function add_func_before_close($func,$arg = null){
		$this->user_func[] = array(
			'func'=>$func,
			'arg'=>$arg
		);
	}

	private function call_func_before_close(){

		foreach($this->user_func as &$val){
			if(isset($val['arg']))
				call_user_func_array($val['func'],$val['arg']);
			else
				call_user_func($val['func']);
			unset($val);
		}
	}
}