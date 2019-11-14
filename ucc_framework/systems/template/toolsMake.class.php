<?php
/**
 * Created by gradus.
 * email: gradus0@mail.ru
 * Date: 13.01.15
 * Time: 22:50
 */


namespace ucc\systems\template;

use \ucc\systems\string\stringMake as s_string;

class title{

	private $arr;

	function __construct(){
		$this->arr = array();
	}

	function add($text){
		$this->arr[] = $text;
	}

	function get(){
		return $this->arr;
	}

	function merge( title $obj ){
		$this->arr = array_merge ($this->get(), $obj->get());
	}

	function html($char = ','){
		$char.=' ';
		$string = implode($char,$this->arr);
		$string = s_string::getInstance()->htmlspecialchars($string,ENT_NOQUOTES);
		return "<title>$string</title>\n";
	}


}


class description{

	private $arr;

	function __construct(){
		$this->arr = array();
	}

	function add($text){
		$this->arr[] = $text;
	}

	function get(){
		return $this->arr;
	}

	function merge( description $obj ){
		$this->arr = array_merge ($this->get(), $obj->get());
	}

	function html($char = ','){
		$char.=' ';
		$string = implode($char,$this->arr);
		$string =  s_string::getInstance()->htmlspecialchars($string,ENT_NOQUOTES);
		return "<meta name=\"description\" content=\"{$string}\">\n";
	}
}



class keywords{

	private $arr;

	function __construct(){
		$this->arr = array();
	}

	function add($text){
		$this->arr[] = $text;
	}

	function get(){
		return $this->arr;
	}

	function merge( keywords $obj ){
		$this->arr = array_merge ($this->get(), $obj->get());
	}

	function html($char = ','){
		$char.=' ';
		$string = implode($char,$this->arr);
		$string =  s_string::getInstance()->htmlspecialchars($string,ENT_NOQUOTES);
		return "<meta name=\"keywords\" content=\"{$string}\">\n";
	}

}


class js{

	private $arr;
	private $cont;

	function __construct(){
		$this->arr = array();
		$this->cont = array();
	}

	function add($file, array $conf = null, $content = null){
		if(!isset($conf['type']))
			$conf['type'] = 'text/javascript';

		$this->arr[$file] = $conf;

		if(isset($content))
			$this->cont[$file] = $content;

	}

	function get(){
		return array(
			'arr'=>$this->arr,
			'cont'=>$this->cont
		);
	}


	function merge( js $obj ){
		$old = $this->get();
		$new = $obj->get();
		$this->arr = array_merge ($old['arr'], $new['arr']);
		$this->cont = array_merge ($old['cont'], $new['cont']);
	}


	function links($path = ''){

		if($path!=''){
			$path = rtrim($path,'/');
			$path.='/';
		}

		$html = '';

		foreach($this->arr as $file => $conf){

			$content = '';

			if(isset($this->cont[$file]))
				$content = $this->cont[$file];
			else{
				$conf['src'] = $path.$file;
			}

			$attr = '';

			if(isset($conf)){
				foreach($conf as $attrName=>$attrVal){
					$attr .= " {$attrName}=\"{$attrVal}\" ";
				}
			}

			$html.= "<script {$attr}>{$content}</script>\n";
		}

		return $html;
	}
}



class css{

	private $arr;

	function __construct(){
		$this->arr = array();
	}

	function add($file, $arg = 'all'){
		if(!is_array($arg)){
			$conf = array('media'=>$arg);
		}else{
			$conf = &$arg;
		}

		$this->arr[$file] = $conf;
	}

	function get(){
		return $this->arr;
	}

	function merge( css $obj ){
		$this->arr = array_merge ($this->get(), $obj->get());
	}


	function links($path = ''){

		if($path!=''){
			$path = rtrim($path,'/');
			$path.='/';
		}

		$html = '';

		foreach($this->arr as $file => $conf){
			$pathToFile = $path.$file;

			$attr = '';

			if(isset($conf)){
				foreach($conf as $attrName=>$attrVal){
					$attr .= " {$attrName}=\"{$attrVal}\" ";
				}
			}

			$html.= "<link {$attr} rel=\"stylesheet\" type=\"text/css\" href=\"{$pathToFile}\">\n";
		}

		return $html;
	}

}

class toolsMake{

	private $obj;

	private function set_and_reset(){
		$this->obj->title = new title();
		$this->obj->description = new description();
		$this->obj->keywords = new keywords();
		$this->obj->js = new js();
		$this->obj->css = new css();
	}

	function __construct(){
		$this->obj = new \stdClass();
		$this->set_and_reset();
	}

	function __get($obj_name){
		return $this->obj->$obj_name;
	}

	function html_head($path = '', $char = ','){
		$r = '';
		$r .= $this->obj->title->html($char);
		$r .= $this->obj->description->html($char);
		$r .= $this->obj->keywords->html($char);
		$r .= $this->obj->css->links($path);
		$r .= $this->obj->js->links($path);
		return $r;
	}

	function merge( toolsMake $obj ){
		$this->obj->title->merge($obj->title);
		$this->obj->description->merge($obj->description);
		$this->obj->keywords->merge($obj->keywords);
		$this->obj->css->merge($obj->css);
		$this->obj->js->merge($obj->js);
	}

	function __clone(){
		// $this->reset();
	}

}