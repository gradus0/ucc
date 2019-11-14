<?php
/**
 * Created by gradus.
 * email: gradus0@mail.ru
 * Date: 22.11.14
 * Time: 21:17
 */


namespace ucc\systems\string;

use ucc\systems\config\configMake as s_config;

class stringMake extends \ucc\library\string\stringMake{

	private static $config;

	private static $_instance;

	public static function getInstance()
	{
		if (!isset(self::$_instance))
		{
			self::$_instance = new self;
		}

		return self::$_instance;
	}

	function __construct(){
		self::$config = s_config::getInstance(_UCC_KEY)->load('main');
	}

	/* to russian a common problem */
	public function iconvUTFtoWIN($str){
		return self::iconv("utf-8", "windows-1251",$str);
	}
	/* to russian a common problem */
	public function iconvWINtoUTF($str){
		return self::iconv("windows-1251", "utf-8",$str);
	}

	public function htmlspecialchars($string, $_ent = null ){
		return htmlspecialchars($string,$_ent, self::$config['charset']);
	}

	public function htmlsc_wq($string){ // htmlspecialchars width quote
		return $this->htmlspecialchars($string,ENT_NOQUOTES);
	}

	public function htmlsc_nq($string){ // htmlspecialchars not quote
		if(is_array($string)){
			return array_map( array($this,'htmlsc_nq') , $string );
		}else{
			return $this->htmlspecialchars($string, ENT_QUOTES);
		}
	}

	public function to_join($string , $options = 0 , $depth = 512 ){ // htmlspecialchars not quote
		return json_encode($string, $options , $depth);
	}

	public function to_input($string){ // alias method htmlsc_nq
		return $this->htmlsc_nq($string);
	}

	public function strlen($string){
		return mb_strlen($string, self::$config['charset']);
	}


	public function low($string){
		return strtolower($string);
	}

	public function upp($string){
		return strtoupper($string);
	}

	public function substr($string, $start = 0 , $length = 0){
		return mb_substr($string, $start , $length, self::$config['charset']);
	}

}