<?php
/**
 * Created by gradus.
 * email: gradus0@mail.ru
 * Date: 24.11.14
 * Time: 0:31
 */

class ucc{
	private static $sysClassReturnObj;

	private static $_instance;

	public static function getInstance($conf = null) {
		if (self::$_instance === null) {
			self::$_instance = new self($conf);
		}

		return self::$_instance;
	}

	function __construct($conf = null){
		$this->set_exception_handler();

		ucc_autoload::register();
		// $this->start_user_core();
		self::$sysClassReturnObj = ucc\importStaticClass::getInstance();
		ucc::sys()->config(_UCC_KEY)->set_path($conf);
		ucc::sys()->dir->setPathOfAlias('webroot', dirname($_SERVER['SCRIPT_FILENAME']) );
		ucc::sys()->dir->setPathOfAlias('ucc', dirname(__FILE__) );
	}

	static function sys(){
		return self::$sysClassReturnObj;
	}

	static function run($conf = null){
		return self::getInstance($conf);
	}

	function set_exception_handler(){
		set_exception_handler('ucc\exception_handler');
	}


}




