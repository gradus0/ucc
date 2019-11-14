<?php
/**
 * Created by gradus.
 * email: gradus0@mail.ru
 * Date: 02.02.15
 * Time: 18:07
 */


namespace ucc\systems\server;

use ucc\systems\patterns\singlton as s_singlton;
use ucc\library\server\shutdownMake as l_shutdown;

class serverMake extends s_singlton{
	public $shutdown;

	function __construct(){
		$this->shutdown = new l_shutdown;
	}
}