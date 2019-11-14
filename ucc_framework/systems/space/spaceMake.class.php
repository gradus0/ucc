<?php
/**
 * Created by gradus.
 * email: gradus0@mail.ru
 * Date: 18.11.14
 * Time: 21:53
 */

namespace ucc\systems\space;

class spaceMake{

	private static $_instance;

	public static function getInstance($inst_name = 'default')
	{
		if(empty(self::$_instance[$inst_name])){
			self::$_instance[$inst_name] = new \stdClass();
		}

		return self::$_instance[$inst_name];
	}

};

