<?php
/**
 * Created by gradus.
 * email: gradus0@mail.ru
 * Date: 15.11.14
 * Time: 22:55
 */

namespace ucc\systems\date;

class dateMake{

	private static $_instance;

	public static function getInstance() {
		if (self::$_instance === null) {
			self::$_instance = new self;
		}

		return self::$_instance;
	}

	public function dateByServMess(){
		return date('d.m.Y G:i:s');
	}

	public function time(){
		return time();
	}

	public function datetime(){
		return date('Y-m-d H:i:s');
	}
}