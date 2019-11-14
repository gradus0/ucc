<?php
/**
 * Created by gradus.
 * email: gradus0@mail.ru
 * Date: 14.01.15
 * Time: 21:02
 */

namespace ucc\systems\gpcf;

use ucc\systems\patterns\singlton as s_singlton;
use ucc\systems\string\stringMake as s_string;

use ucc\systems\config\configMake as s_config;

class gpcfMake extends s_singlton{

	private $date;

	function __construct(){
		$this->date = array();
		$this->date['post'] = $_POST;
		$this->date['get'] = $_GET;
		$this->date['cookie'] = $_COOKIE;
		$this->date['files'] = $_FILES;

		if(get_magic_quotes_gpc()){
			$this->stripslashes_all($this->date['post']);
			$this->stripslashes_all($this->date['get']);
			$this->stripslashes_all($this->date['cookie']);
		}

		$this->urldecode_all($this->date['post']);
		$this->urldecode_all($this->date['get']);
		$this->urldecode_all($this->date['cookie']);

		$this->trim_all($this->date['post']);
		$this->trim_all($this->date['get']);
		$this->trim_all($this->date['cookie']);

	}

	function stripslashes_all(&$date){
		array_walk_recursive($date,
			function (&$date){
				$date = stripslashes($date);
			}
		);
	}

	function urldecode_all(&$date){
		array_walk_recursive($date,
			function (&$date){
				$date = urldecode($date);
			}
		);
	}

	function trim_all(&$date){
		array_walk_recursive($date,
			function (&$date){
				$date = s_string::trim($date);
			}
		);
	}

	function textForInput($string){
		return s_string::htmlreplacechars($string);
	}

	function issetForInput($var, $str){
		return $this->textForInput(isset($var)?$var:$str);
	}

	function __get($type){
		return $this->date[$type];
	}
}