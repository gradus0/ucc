<?php
/**
 * Created by gradus.
 * email: gradus0@mail.ru
 * Date: 02.02.15
 * Time: 18:05
 */


namespace ucc\library\server;

class shutdownMake{
	protected $user_func;

	function __construct(){

		\register_shutdown_function(array($this,'run_function'));

		$this->user_func = array();
	}

	function add_function($func,$arg = null){
		$this->user_func[] = array(
			'func'=>$func,
			'arg'=>$arg
		);
	}

	public function run_function(){
		foreach($this->user_func as &$val){
			if(isset($val['arg']))
				call_user_func_array($val['func'],$val['arg']);
			else
				call_user_func($val['func']);
			unset($val);
		}
	}
}