<?php
namespace ucc\library\mail;

$tmp_pathByLoad = dirname(__FILE__). DIRECTORY_SEPARATOR;

require_once( $tmp_pathByLoad . 'phpMailer' . DIRECTORY_SEPARATOR . 'class.phpmailer.php' ); //путь до класса phpmailer

class phpMailerMake{
	private $config;
	private $mailObj;
	private $error;

	function __construct( array $config ){
		$this->config = $config;
		$this->mailObj = new \PHPMailer(true);
		$this->set_config();
	}

	private function set_config(){
		$this->mailObj->Host       = $this->config['host'];
		$this->mailObj->SMTPAuth   = $this->config['auth'];
		$this->mailObj->Port       = $this->config['port'];
		$this->mailObj->SMTPSecure = $this->config['secure'];
		$this->mailObj->Username   = $this->config['user'];
		$this->mailObj->Password   = $this->config['pass'];
		$this->mailObj->CharSet    = $this->config['charset'];
		$this->mailObj->IsSMTP();
	}

	function last_error(){
		return $this->error;
	}

	function getMailerObj(){
		return $this->mailObj;
	}

	function send(array $param){
		$t = false;

		try {

			$param['to'] = (array)$param['to'];

			foreach($param['to'] as $val){
				$val = (array)$val;
				if( ! isset ( $val[1] ) )
					$val[1] = null;
				$this->mailObj->AddAddress( $val[0] , $val[1] );
			}

			$this->error = null;

			if( ! isset( $param['from'] ) ){
				$param['from'] = $this->config['email'];
			}

			$param['from'] = (array)$param['from'];

			if( ! isset ( $param['from'][1] ) )
				$param['from'][1] = null;

			$this->mailObj->SetFrom($param['from'][0], $param['from'][1]); // from email, name

			if(isset($param['isHtml']))
				$this->mailObj->isHtml( $param['isHtml'] );

			$this->mailObj->Subject = $param['subject'];
			$this->mailObj->MsgHTML( $param['message'] );

			$t = $this->mailObj->Send();

		} catch (\phpmailerException $e) {
			$this->error = $e->errorMessage();
		} catch (\Exception $e) {
			$this->error = $e->getMessage();
		}

		return $t;
	}

}