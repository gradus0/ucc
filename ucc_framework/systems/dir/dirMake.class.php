<?php

namespace ucc\systems\dir;

use ucc\library\dir\dirMake as t_dir;

class dirMake{

	private static $_instance;

	public static function getInstance($inst_name = 'default')
	{
		if(empty(self::$_instance[$inst_name])){
			self::$_instance[$inst_name] = new t_dir;
		}

		return self::$_instance[$inst_name];
	}

}
