<?php
/**
 * Created by PhpStorm.
 * User: gradus
 * Date: 16.02.15
 * Time: 23:39
 */

namespace ucc\systems\convert;

use ucc\systems\patterns\singlton as s_singlton;

class data{
	private $data;

	function __construct($data){
		$this->data = $data;
	}

	function json(){
		return json_decode($this->data,true);
	}

	function show($type){
		if($type == 'json'){
			header('Content-Type: application/json');
			echo $this->json();
		}else{

		}
	}

	function end($type){
		$this->show($type);
		exit;
	}
}

class convertMake extends s_singlton{


	function set($data){
		return new data($data);
	}



}