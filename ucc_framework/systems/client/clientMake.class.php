<?php
/**
 * Created by gradus.
 * email: gradus0@mail.ru
 * Date: 23.11.14
 * Time: 18:21
 */

namespace ucc\systems\client;

use ucc\library\client\clientMake as l_client;
use ucc\library\client\cookieMake as l_cookie;

use ucc\systems\patterns\singlton as s_singlton;


class clientMake extends s_singlton{

	private $info;
	private $t_clientObj;
	private $isAjax;

	public $cookie;

	function __construct(){
		$this->t_clientObj = new l_client();
		// $this->info = $this->t_clientObj->;
		$this->isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
		$this->cookie = new l_cookie();
	}

	static function isAjax(){
		return false;// $this->isAjax;
	}

	static function isAction(){
		return isset($_GET['action'])?isset($_POST['action']):false;
	}

	public function getIP(){
		return $this->t_clientObj->getIP();
	}


}