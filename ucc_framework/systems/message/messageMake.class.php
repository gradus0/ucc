<?php
/**
 * Created by gradus.
 * email: gradus0@mail.ru
 * Date: 15.11.14
 * Time: 18:08
 */

namespace ucc\systems\message;

use ucc\systems\session\sessionMake as s_session;
use ucc\systems\patterns\singlton as s_singlton;

class messageMake extends s_singlton{

	private $messageText;
	private $key_session;

	protected function __construct(){
		// s_session::ini();

		$this->key_session=__NAMESPACE__.' '.__CLASS__;

		$this->ini();

		$sessionObj = s_session::getInstance();
		$sessionObj->add_func_before_close(array($this,'writeMessageToSession'));
	}

	private function ini(){

		$sessionObj = s_session::getInstance();
		if( ! isset( $sessionObj->{$this->key_session} )){
			$this->messageText = array(
				'error'=>array(),
				'warning'=>array(),
				'ok'=>array()
			);
		}else{
			$this->messageText = $sessionObj->{$this->key_session};
		}
	}

	public function writeMessageToSession(){
		$sessionObj = s_session::getInstance();

		$sessionObj->{$this->key_session} = $this->messageText ;
	}

	public function setError($txtError){

		$this->messageText['error'][] = is_array($txtError)?implode(', ',$txtError):$txtError;

		$this->messageText['error'] = array_unique($this->messageText['error']);

		$this->writeMessageToSession();

	}

	public function setOk($txt){

		$this->messageText['ok'][] = is_array($txt)?implode(', ',$txt):$txt;

		$this->messageText['ok'] = array_unique($this->messageText['ok']);

		$this->writeMessageToSession();

	}

	public function getMessage($type=null){
		$ar = array();

		if(isset($type)){
			if(isset($this->messageText[$type])){
				$ar = $this->messageText[$type];
				$this->messageText[$type] = array();
			}
		}else{
			$ar = $this->messageText;
			$this->ini();
		}

		return $ar;
	}

}