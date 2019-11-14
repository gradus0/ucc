<?php
/**
 * Created by gradus.
 * email: gradus0@mail.ru
 * Date: 24.12.14
 * Time: 0:28
 */


namespace ucc\systems\patterns;

class multiSinglton{

	private static $_instance;


	final public static function getInstance($inst_name = 'default')
	{
		$calledClass = get_called_class();

		if (!isset(self::$_instance[$calledClass]))
		{
			self::$_instance[$calledClass] = array();
		}

		if (!isset(self::$_instance[$calledClass][$inst_name]))
		{
			self::$_instance[$calledClass][$inst_name] = new $calledClass();
		}

		return self::$_instance[$calledClass][$inst_name];
	}



	private function __construct() { /* ... @return Singleton */ }  // Защищаем от создания через new Singleton
	private function __clone() { /* ... @return Singleton */ }  // Защищаем от создания через клонирование
	private function __wakeup() { /* ... @return Singleton */ }  // Защищаем от создания через unserialize

}