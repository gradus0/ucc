<?php
/**
 * Created by gradus.
 * email: gradus0@mail.ru
 * Date: 09.11.14
 * Time: 22:01
 */

namespace ucc\library\url;

class urlMake{
	private $url;
	private $url_arr;
	private $_GET;

	public function __construct(){
		$this->url_arr = array();
		$this->url = substr($_SERVER['REQUEST_URI'], 1);
		$arr = explode("/", $this->url);

		for ($i = 0; $i <=count($arr); $i++) {
			$arr[$i] = trim($arr[$i]);
			if($arr[$i] == '') unset($arr[$i]);
		}

		if(count($arr)){
			$this->url_arr = array_values($arr);
		}
	}

	function getUrl_arr(){
		return $this->url_arr;
	}
}