<?php
/**
 * Created by gradus.
 * email: gradus0@mail.ru
 * Date: 14.01.15
 * Time: 21:02
 */

namespace ucc\systems\request;

use ucc\systems\patterns\singlton as s_singlton;
use ucc\systems\string\stringMake as s_string;

use ucc\systems\config\configMake as s_config;

class requestMake extends s_singlton{

	private $date;

	function __construct(){
		$this->date = array();

		$this->date['_POST'] = $_POST;
		$this->date['_GET'] = $_GET;
		$this->date['_COOKIE'] = $_COOKIE;
		$this->date['_REQUEST'] = $_REQUEST;

		if(function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()){
			$this->stripslashes_all($this->date);
		}

		$this->urldecode_all($this->date['_GET']);

		$this->trim_all($this->date);


		$this->date['_URI'] = array();
		$this->date['is_ajax'] = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH']==='XMLHttpRequest';
		$this->date['is_flash'] = isset($_SERVER['HTTP_USER_AGENT']) && (stripos($_SERVER['HTTP_USER_AGENT'],'Shockwave')!==false || stripos($_SERVER['HTTP_USER_AGENT'],'Flash')!==false);

		$path_uri = urldecode(parse_url($_SERVER['REQUEST_URI'])['path']);
		$uri_arr = explode("/", substr($path_uri, 1) );

		foreach($uri_arr as $key=> &$val){
			$val = trim($val);
			if($val == '') unset($uri_arr[$key]);
		}

		if(count($uri_arr)){
			$this->date['_URI'] = array_values($uri_arr);
		}

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
				$date = rawurldecode($date);
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

	function _REQUEST($name, $default_value = null){
		return isset($this->date['_REQUEST'][$name])?$this->date['_REQUEST'][$name]:$default_value;
	}

	function _GET($name, $default_value = null){
		return isset($this->date['_GET'][$name])?$this->date['_GET'][$name]:$default_value;
	}

	function _POST($name, $default_value = null){
		return isset($this->date['_POST'][$name])?$this->date['_POST'][$name]:$default_value;
	}

	function _COOKIE($name, $default_value = null){
		return isset($this->date['_COOKIE'][$name])?$this->date['_COOKIE'][$name]:$default_value;
	}

	function is_ajax(){
		return $this->date['is_ajax'];
	}

	function is_flash(){
		return $this->date['is_flash'];
	}

	function _poq($user_url, $type = '/'){ // poq - path OR query
		$r = array();

		$slashes = $type=='/';
		$user_url = trim($user_url,' /');
		$user_url_arr = explode('/',$user_url);

		if($slashes){
			foreach($user_url_arr as $key=>$var_name){
				$var_name = trim($var_name);
				if($var_name == '#' ) continue;
				if(isset($this->date['_URI'][$key])){
					$r[$var_name] = $this->date['_URI'][$key];
				}else{
					$r[$var_name] = null;
				}
			}
		}else{
			foreach($user_url_arr as $var_name){
				$var_name = trim($var_name);
				if($var_name == '#' ) continue;
				$r[$var_name] = $this->_GET($var_name);
			}
		}

		return $r;
	}

}
