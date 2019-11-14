<?php
/**
 * Created by gradus.
 * email: gradus0@mail.ru
 * Date: 15.11.14
 * Time: 23:03
 */

namespace ucc\library\client;

class clientMake{

	private $user_info;

	function __construct(){
		$this->user_info=array(
			'ip'=>'',
			'proxy_ip'=>null,
			'proxy_name'=>null
		);

		$this->set_info();
	}

	private function set_info(){
		if (isset($_SERVER)) {
			$this->user_info['ip'] = $_SERVER['REMOTE_ADDR'];

			if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
				$this->user_info['proxy_ip'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
			elseif (isset($_SERVER['HTTP_CLIENT_IP']))
				$this->user_info['proxy_ip'] = $_SERVER['HTTP_CLIENT_IP'];
		}
	}

	public function getIP(){
		return $this->user_info['ip'];
	}

	public function issetProxy(){
		return isset($this->user_info['proxy_ip']);
	}

	/*
		public function getProxyIp(){

		}


		public function getProxyAndIP(){

		}
	*/


}