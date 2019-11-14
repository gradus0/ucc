<?php
/**
 * Created by gradus.
 * email: gradus0@mail.ru
 * Date: 10.11.14
 * Time: 17:48
 */

namespace ucc\systems\db;

use ucc\library\db as t_db;
use ucc\systems\config\configMake as s_config;

class dbMake{
	private static $instance=array(); // by singlton

	private static $config; // global (all) config db

	private $obj_db; // db object to return for work

	private $config_connect; // this db config to connect

	private static $db_types = array(
		'mysql',
		'mysqli',
	);

	private static $db_types_check = array();

	public static function getInstance($db_name = null)
	{

		if(!count(self::$db_types_check)){
			foreach(self::$db_types as $db_type){
				self::$db_types_check[$db_type] = (bool) @ini_get_all($db_type);
			}
		}

		self::$config=s_config::getInstance()->load('db');


		if(!isset($db_name)){
			if(isset(self::$config['default']))
				$db_name = self::$config['default'];
			else
				$db_name = key(self::$config['db']);
		}

		$db_conf=isset(self::$config['db'][$db_name])?self::$config['db'][$db_name]:null;

		if(isset($db_conf)){

			if (empty(self::$instance[$db_name]))
			{
				self::$instance[$db_name] = new self($db_conf);

			}
		}

		if(isset(self::$instance[$db_name])){
			return self::$instance[$db_name];
		}else{
			return null;
		}

	}

	private function check_type_db($db_name_type){
		return (isset(self::$db_types_check[$db_name_type]) && self::$db_types_check[$db_name_type]);
	}


	private function __construct($db_conf)
	{
		$this->config_connect = $db_conf;
		$this->obj_db = false;

		$type_db = $this->getTypeDb();

		if($this->check_type_db($type_db)){
			$path_db_class = 'ucc\library\db\\'.$type_db;
			if(class_exists($path_db_class)){
				$this->obj_db = new $path_db_class($this->config_connect);
			}else{
				throw new \Exception(' db '.$type_db.' not class');
			}
		}else{
			throw new \Exception(' db '.$type_db.' not server');
		}

	}

	public function getTypeDb(){
		return $this->config_connect['type'];
	}

	public function connect(){
		# status (bool) connect in db
		$s = false;

		if($this->obj_db){
			# this is new throw in db class by method connect
			$s = $this->obj_db->connect();
		}

		return $s;
	}

	public function get_obj_db(){
		return $this->obj_db;
	}
}