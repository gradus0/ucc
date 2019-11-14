<?php
/**
 * Created by gradus.
 * email: gradus0@mail.ru
 * Date: 24.12.14
 * Time: 0:47
 */



namespace ucc\systems\patterns;

class singlton{

	private static $_instance = array();


	final public static function getInstance()
	{
		$calledClass = get_called_class();

		if (!isset(self::$_instance[$calledClass]))
		{
			self::$_instance[$calledClass] = new $calledClass();
		}

		return self::$_instance[$calledClass];
	}



	private function __construct() { /* ... @return Singleton */ }  // Защищаем от создания через new Singleton
	private function __clone() { /* ... @return Singleton */ }  // Защищаем от создания через клонирование
	private function __wakeup() { /* ... @return Singleton */ }  // Защищаем от создания через unserialize

}