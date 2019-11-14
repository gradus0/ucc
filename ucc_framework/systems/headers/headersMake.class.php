<?php
/**
 * Created by gradus.
 * email: gradus0@mail.ru
 * Date: 23.11.14
 * Time: 18:50
 */

namespace ucc\systems\headers;

use ucc\systems\session\sessionMake as s_session;
use ucc\systems\patterns\singlton as s_singlton;

use ucc\systems\config\configMake as s_config;

class headersMake extends s_singlton{

	private $config;

	function redirect($page){
		echo 'redirect to '.$page;
		s_session::close();
		exit;
	}

	function location($link){
		header('Location: '.$link);
		exit;
	}

	function __construct(){
		$this->config = s_config::getInstance(_UCC_KEY)->load('main');
	}

	function content_type($type = 'text/html' , $charset = 'utf-8'){
		header("Content-Type: {$type}; charset={$charset}");
	}

	function html_header($charset = null ){

		if(!isset($charset)) {
			$charset = $this->config['charset'];
		}

		$this->content_type('text/html', $charset);
	}


}