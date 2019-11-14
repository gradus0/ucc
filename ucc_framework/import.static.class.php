<?php
/**
 * Created by gradus.
 * email: gradus0@mail.ru
 * Date: 25.11.14
 * Time: 15:47
 */

namespace ucc;

class importStaticClass extends \stdClass{

	private $path_import;

	private $settings;

	private static $instance;

	public static function getInstance($path = 'systems')
	{
		if (empty(self::$instance[$path]))
		{
			self::$instance[$path] = new self($path);
		}
		return self::$instance[$path];
	}

	private function __construct($path){
		$this->path_import = $path;
		$this->settings = array(
			'postfix_class' => 'Make', // $this->path_import.$this->postfix_class['make'] - full path to call class by his name
			'method_static_name' => 'getInstance'
		);
	}

	private function collectPathToClass($name,$param=null , $class_name_user = null){
		if(isset($class_name_user)){
			$class_name = $class_name_user;
		}else{
			$class_name = $name;
		}

		$class =__NAMESPACE__."\\{$this->path_import}\\{$name}\\{$class_name}{$this->settings['postfix_class']}";

		$pathStaticMethod = $class.'::'.$this->settings['method_static_name'];

		if(method_exists($class,$this->settings['method_static_name'])){
			if(isset($param)){
				return call_user_func_array($pathStaticMethod,$param);
			}else{
				// return $pathStaticMethod;
				return call_user_func($pathStaticMethod);
			}

		}else{
			return false;
		}
		// throw exception();
	}


	public function __get($name){
		return $this->collectPathToClass($name);
	}

	public function _load($path_name, $class_name , $param = null ){
		return $this->collectPathToClass($path_name , $param , $class_name);
	}

	public function __call($name,array $param){
		return $this->collectPathToClass($name,$param);
	}
}