<?php
/**
 * Created by gradus.
 * email: gradus0@mail.ru
 * Date: 17.12.14
 * Time: 23:26
 */

##########
#   WARNING THIS FILE INCLUDE ../../autoload.php
##########


namespace ucc\systems\pathAlias;

use ucc\libray\pathAlias\pathAliasMake as t_pathAlias;

class pathAliasMake{

	private static $_instance;

	public static function getInstance($inst_name = 'default')
	{
		if(empty(self::$_instance[$inst_name])){
			self::$_instance[$inst_name] = new t_pathAlias;
		}

		return self::$_instance[$inst_name];
	}

}
