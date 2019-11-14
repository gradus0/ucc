<?php
/**
 * Created by gradus.
 * email: gradus0@mail.ru
 * Date: 10.11.14
 * Time: 0:58
 */

namespace ucc\library\mail;

class smtpMake{
	private $char;
	private $ContentType;
	private $conf=array();
	private $toEmail=array();
	private $zag=false;
	private $filelog=array();
	public $fromEmail;
	public $fromName;
	public $subject;
	public $text;
	public $files=array();

	function __construct(array $conf){
		$this->conf['smtp_serv']='domen.host';
		$this->conf['user']='user';
		$this->conf['pass']='pass';
		$this->conf['email']='email_by';
		$this->conf['port'] = 25;

		$this->conf = array_merge($this->conf, $conf);

		$this->IsHTML(false);
		$this->char='windows-1251';
		$this->filelog['name']='sendmail.log';
	}

	//чистим данные что бы не создавать новый класс
	function clear(){
		$this->toEmail=array();
		$this->zag=false;
		$this->fromEmail=null;
		$this->fromName=null;
		$this->subject=null;
		$this->text=null;
		$this->files=array();
	}

	// метод добавляет адрес получателя
	function addAddress($email,$name=false){
		$email=strtolower($email);
		if(!isset($this->toEmail[$email]))
			$this->toEmail[$email]=$name;
	}

	public function _mime_types($ext = '') {
		$mimes = array(
			'hqx'   =>  'application/mac-binhex40',
			'cpt'   =>  'application/mac-compactpro',
			'bin'   =>  'application/macbinary',
			'dms'   =>  'application/octet-stream',
			'lha'   =>  'application/octet-stream',
			'lzh'   =>  'application/octet-stream',
			'exe'   =>  'application/octet-stream',
			'class' =>  'application/octet-stream',
			'psd'   =>  'application/octet-stream',
			'so'    =>  'application/octet-stream',
			'sea'   =>  'application/octet-stream',
			'dll'   =>  'application/octet-stream',
			'oda'   =>  'application/oda',
			'pdf'   =>  'application/pdf',
			'ai'    =>  'application/postscript',
			'eps'   =>  'application/postscript',
			'ps'    =>  'application/postscript',
			'smi'   =>  'application/smil',
			'smil'  =>  'application/smil',
			'mif'   =>  'application/vnd.mif',
			'xls'   =>  'application/vnd.ms-excel',
			'ppt'   =>  'application/vnd.ms-powerpoint',
			'wbxml' =>  'application/vnd.wap.wbxml',
			'wmlc'  =>  'application/vnd.wap.wmlc',
			'dcr'   =>  'application/x-director',
			'dir'   =>  'application/x-director',
			'dxr'   =>  'application/x-director',
			'dvi'   =>  'application/x-dvi',
			'gtar'  =>  'application/x-gtar',
			'php'   =>  'application/x-httpd-php',
			'php4'  =>  'application/x-httpd-php',
			'php3'  =>  'application/x-httpd-php',
			'phtml' =>  'application/x-httpd-php',
			'phps'  =>  'application/x-httpd-php-source',
			'js'    =>  'application/x-javascript',
			'swf'   =>  'application/x-shockwave-flash',
			'sit'   =>  'application/x-stuffit',
			'tar'   =>  'application/x-tar',
			'tgz'   =>  'application/x-tar',
			'xhtml' =>  'application/xhtml+xml',
			'xht'   =>  'application/xhtml+xml',
			'zip'   =>  'application/zip',
			'mid'   =>  'audio/midi',
			'midi'  =>  'audio/midi',
			'mpga'  =>  'audio/mpeg',
			'mp2'   =>  'audio/mpeg',
			'mp3'   =>  'audio/mpeg',
			'aif'   =>  'audio/x-aiff',
			'aiff'  =>  'audio/x-aiff',
			'aifc'  =>  'audio/x-aiff',
			'ram'   =>  'audio/x-pn-realaudio',
			'rm'    =>  'audio/x-pn-realaudio',
			'rpm'   =>  'audio/x-pn-realaudio-plugin',
			'ra'    =>  'audio/x-realaudio',
			'rv'    =>  'video/vnd.rn-realvideo',
			'wav'   =>  'audio/x-wav',
			'bmp'   =>  'image/bmp',
			'gif'   =>  'image/gif',
			'jpeg'  =>  'image/jpeg',
			'jpg'   =>  'image/jpeg',
			'jpe'   =>  'image/jpeg',
			'png'   =>  'image/png',
			'tiff'  =>  'image/tiff',
			'tif'   =>  'image/tiff',
			'css'   =>  'text/css',
			'html'  =>  'text/html',
			'htm'   =>  'text/html',
			'shtml' =>  'text/html',
			'txt'   =>  'text/plain',
			'text'  =>  'text/plain',
			'log'   =>  'text/plain',
			'rtx'   =>  'text/richtext',
			'rtf'   =>  'text/rtf',
			'xml'   =>  'text/xml',
			'xsl'   =>  'text/xml',
			'mpeg'  =>  'video/mpeg',
			'mpg'   =>  'video/mpeg',
			'mpe'   =>  'video/mpeg',
			'qt'    =>  'video/quicktime',
			'mov'   =>  'video/quicktime',
			'avi'   =>  'video/x-msvideo',
			'movie' =>  'video/x-sgi-movie',
			'doc'   =>  'application/msword',
			'word'  =>  'application/msword',
			'xl'    =>  'application/excel',
			'eml'   =>  'message/rfc822'
		);
		return (!isset($mimes[strtolower($ext)])) ? 'application/octet-stream' : $mimes[strtolower($ext)];
	}

	public function IsHTML($ishtml = true) {
		if ($ishtml) $this->ContentType = 'text/html';
		else $this->ContentType = 'text/plain';
	}

	function attachment($file,$name=null,$id=null){
		if(!file_exists($file) || !$fp = fopen($file, "rb")) return false;
		$fname=basename($file);
		$this->files[] = array('txt'=>chunk_split(base64_encode(fread($fp, filesize($file)))),
			'type'=>$this->_mime_types(pathinfo($fname, PATHINFO_EXTENSION)),
			'name'=>isset($name)?$name:$fname,
			'id'=>$id);
		fclose($fp);
		return true;
	}

	function head($email,$name=false){

		$uniq_id = md5(uniqid(time()));
		$boundary="--------".uniqid();
		$head="Date: ".date("D, j M Y G:i:s")." +0400\r\n";
		$head.="Return-Path: ".(isset($this->conf['email'])?$this->conf['email']:$this->fromEmail)."\r\n";
		$head.="From: ";
		if(isset($this->fromName)) $head.="=?".$this->char."?B?".base64_encode($this->fromName)."?= ";
		$head.="<".$this->fromEmail.">\r\n";
		$head.="To: ".($name?"=?".$this->char."?B?".base64_encode($name)."?= ":'')."<".$email.">\r\n";
		$head.="Reply-To: =?".$this->char."?B?".base64_encode($this->fromName)."?= <".$this->fromEmail.">\r\n";
		$head.="Subject: =?".$this->char."?B?".base64_encode($this->subject)."?=\r\n";
		$head.="Message-ID: <".$uniq_id."@".$_SERVER['SERVER_NAME'].">\r\n";
		$head.="X-Priority: 1\r\n";
		$head.="X-Mailer: WEB-Portal Mailer\r\n";
		$head.="MIME-Version: 1.0\r\n";

		if(!$this->zag) {
			if(empty($this->files)) {
				$this->zag="Content-Type: multipart/alternative; boundary=\"".$boundary."\"\r\n\r\n\r\n";
				$this->zag.="--".$boundary."\r\n";
				$this->zag.="Content-Type: ".$this->ContentType."; charset=\"".$this->char."\"\r\n";
				$this->zag.="Content-Transfer-Encoding: base64\r\n\r\n";
				$this->zag.=chunk_split(base64_encode($this->text))."\r\n\r\n\r\n";
				$this->zag.="--".$boundary."--\r\n";
			}
			else {
				$this->zag="Content-Type: multipart/mixed; boundary=\"".$boundary."\"\r\n";
				$this->zag.="--".$boundary."\r\n";
				$this->zag.="Content-Type: ".$this->ContentType."; charset=\"".$this->char."\"\r\n";
				$this->zag.="Content-Transfer-Encoding: base64\r\n\r\n".chunk_split(base64_encode($this->text))."\r\n\r\n";
				foreach($this->files as $file) {
					$this->zag.="--".$boundary."\r\n";
					$this->zag.="Content-Type: ".$file['type']."; name=\"".$file['name']."\"\r\n";
					$this->zag.="Content-Transfer-Encoding: base64\r\n";
					if(isset($file['id'])) $this->zag.="Content-ID: <".$file['id'].">\r\n";
					else $this->zag.="Content-Disposition: attachment; filename=\"".$file['name']."\"\r\n";
					$this->zag.="\r\n".$file['txt']."\r\n";
				}
				$this->zag.="--".$boundary."--\r\n";
			}
		}
		return $head.$this->zag;
	}

	function auth($user,$pass){
		$this->conf['user']=$user;
		$this->conf['pass']=$pass;
	}

	function logwrite($type=true,$txt=false){
		if($type==='start'){
			$flog=fopen($this->filelog['name'], 'ab');
			if($flog) {
				fwrite($flog, "\n".date('Y-m-d H:i:s')."\n");
				$this->filelog['res']=$flog;
				// return true;
			}
			// else return false;
		}elseif($type==='end'){
			if(isset($this->filelog['res'])) fclose($this->filelog['res']);
		}else{
			if(isset($this->filelog['res'])) fwrite($this->filelog['res'], $txt);
		}
	}

	function send(){
		$ok=true; // статус отправки
		$this->logwrite('start'); // открываем файл лога
/*
		// Создаем сокет
		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		if ($socket < 0) {
			$this->logwrite(true,'socket_create() failed: '.socket_strerror(socket_last_error())."\n");
			$ok=false;
		}else{
			// Соединяем сокет к серверу
			if(!socket_connect($socket, 'https://' . $this->conf['smtp_serv'], $this->conf['port'])){
				$ok=false;
				$this->logwrite(true,'socket_connect() failed: '.socket_strerror(socket_last_error())."\n");
			}
		}
*/
		$socket = fsockopen('ssl://' . $this->conf['smtp_serv'], 465, $errno, $errstr, 50);
var_dump($errno); var_dump($errstr);
		if ($socket) { // if ($ok) {
			// Читаем информацию о сервере
			$this->read_smtp_answer($socket);

			// Приветствуем сервер
			$this->write_smtp_response($socket, 'EHLO cdep.ru');
			$code=$this->read_smtp_answer($socket); // ответ сервера
			if($code != 250) $ok=false;

			if(isset($this->conf['user'])){
				// Делаем запрос авторизации
				$this->write_smtp_response($socket, 'AUTH PLAIN');
				$code=$this->read_smtp_answer($socket); // ответ сервера
				if($code != 334) $ok=false;
var_dump($code); exit;
				// Отравляем логин пароль
				$this->write_smtp_response($socket, base64_encode($this->conf['user']."\0".$this->conf['user']."\0".$this->conf['pass']));
				$code=$this->read_smtp_answer($socket); // ответ сервера
				if($code != 235) $ok=false;
			}

			foreach($this->toEmail as $email=>$name){
				$header=$this->head($email,$name);
				$size_msg=strlen($header);

				// Задаем адрес отправителя
				$this->write_smtp_response($socket, "MAIL FROM:<".(isset($this->conf['email'])?$this->conf['email']:$this->fromEmail)."> SIZE=".$size_msg);
				$code=$this->read_smtp_answer($socket); // ответ сервера
				if($code != 250) $ok=false;

				// Задаем адрес получателя
				$this->write_smtp_response($socket, "RCPT TO:<".$email.">");
				$code=$this->read_smtp_answer($socket); // ответ сервера
				if($code != 250 AND $code != 251) $ok=false;
				// Готовим сервер к приему данных
				$this->write_smtp_response($socket, 'DATA');
				$code=$this->read_smtp_answer($socket); // ответ сервера
				if($code != 354) $ok=false;
				// Отправляем данные
				$this->write_smtp_response($socket, $header."\r\n.");
				$code=$this->read_smtp_answer($socket); // ответ сервера
				if($code != 250) $ok=false;
			}

			// Отсоединяемся от сервера
			$this->write_smtp_response($socket, 'QUIT');
			$this->read_smtp_answer($socket); // ответ сервера
		}

		if (isset($socket)) {
			socket_close($socket); //закрываем сокет
		}

		$this->logwrite('end'); //закрываем файл
		$this->clear(); //очищаем данные
		return $ok; // отдаём статус отправки
	}

	private $i=0;
	// Функция для чтения ответа сервера. Выбрасывает исключение в случае ошибки
	function read_smtp_answer($socket) {
		//$read = socket_read($socket, 1024);
		$this->i++;
		$read = fgets($socket, 1024);
		if($this->i == 3){
			while (!feof($socket)) {
				$read .= @fgets($socket, 128);
			}
			var_dump($read); exit;
		}
		$this->logwrite(true,$read);
		return substr($read,0,3);
	}

	// Функция для отправки запроса серверу
	function write_smtp_response($socket, $msg) {
		$msg = $msg."\r\n";
		// socket_write($socket, $msg, strlen($msg));
		fwrite($socket,$msg);
	}
}


/*
$test= new mailSend();
$test->fromEmail='';
$test->addAddress('');
$test->fromName='Интернет-портал ГАС «Правосудие»';
$test->subject='Ответ на обращение';
$test->text='<table><tr><td>hello</td></tr><tr><td><img src="cid:images"></td></tr></table>';
$test->attachment('userimages/photo/cdepfoto_03.jpg',null,'images'); // (file,namefale=null,idcid)
$test->IsHTML(); // on html
$test->send();
*/