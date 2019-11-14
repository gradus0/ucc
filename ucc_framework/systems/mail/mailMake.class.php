<?php

namespace ucc\systems\mail;

use ucc\library\mail\phpMailerMake as phpMailer;
use ucc\systems\patterns\singlton as s_singlton;

class mailMake extends s_singlton {
	private $phpMailerObj;
	private $config = array();

	function set_config( array $config){
		$this->config = array_merge( $this->config , $config );
	}

	function phpMailer(){

		if( $this->phpMailerObj == null )
			$this->phpMailerObj = new phpMailer( $this->config['phpMailer'] );

		return $this->phpMailerObj;
	}

}