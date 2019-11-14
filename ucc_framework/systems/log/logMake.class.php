<?php
/**
 * User: gradus
 * Date: 13.11.14
 * Time: 19:34
 */

namespace ucc\systems\log;

use ucc\systems\config\configMake as s_config;
use ucc\systems\message\messageMake as s_message;
use ucc\systems\date\dateMake as s_date;
use ucc\library\client\clientMake as l_client;

class logMake{

	private $config;

	private $info;

	private static $_instance;

	public static function getInstance() {
		if (self::$_instance === null) {
			self::$_instance = new self;
		}
		return self::$_instance;
	}

	function __construct(){
		$this->config = s_config::getInstance(_UCC_KEY)->load('log');
		if( empty($this->config) )
			$this->config['write'] = false;
		else{
			$this->config['log_file_error'] = \ucc\pathDirSeparator($this->config['log_file_error']);

			$dateObj = s_date::getInstance();
			$clientObj = new l_client();

			$this->info = array(
				'date'=>$dateObj->dateByServMess(),
				'user_ip'=>$clientObj->getIP()
			);
		}
	}

	private function error_catch($e){
		return $e->getMessage();
	}

	function error($text){
		if(is_array($text)){
			$text = implode(', ',$text);
		}elseif($text instanceof \Exception){
			$text = $this->error_catch($text);
		}
		$this->write($text);
	}

	function fatal_error($text){
		$this->write('fatal error: '.$text);
	}

	function write($text){

		$s_message = s_message::getInstance();

		if($this->config['write']){
			if(is_writable($this->config['log_file_error'])){
				$fb = fopen($this->config['log_file_error'], "a+bt");
				if($fb){
					$text='('.$this->info['date'].' , '.$this->info['user_ip'].') '.$text."\n";
					if (fwrite($fb, $text) === FALSE) {
						$s_message->setError(__CLASS__.' file log not write');
					}
				}else{
					$s_message->setError(__CLASS__.' file log not read/creat');
				}
			}else{
				$s_message->setError(__CLASS__.' file log not is write');
			}
		}
	}

}