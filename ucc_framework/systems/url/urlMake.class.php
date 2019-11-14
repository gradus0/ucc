<?php
/**
 * Created by gradus.
 * email: gradus0@mail.ru
 * Date: 22.11.14
 * Time: 19:59
 */

namespace ucc\systems\url;

use ucc\library\url\urlMake as l_url;
use ucc\systems\string\stringMake as s_string;
use ucc\systems\patterns\singlton as s_singlton;

class urlMake extends s_singlton{

	private $objurlTools;

	private $urlArr;

	function __construct(){
		$this->objurlTools = new l_url;
		$this->urlArr = $this->objurlTools->getUrl_arr();
	}


	function getUrl($user_url,$type = '/'){
		$r = array();

		$slashes = $type=='/';
		$user_url = trim($user_url,' /');
		$user_url_arr = explode('/',$user_url);

		if($slashes){
			foreach($user_url_arr as $key=>$var_name){
				if(isset($user_url_arr[$key])){
					$r[$var_name] = $user_url_arr[$key];
				}else{
					$r[$var_name] = null;
				}
			}
		}else{
			foreach($user_url_arr as $key=>$var_name){
				if(isset($_GET[$var_name])){
					$r[$var_name] = $_GET[$var_name];
				}else{
					$r[$var_name] = null;
				}
			}
		}

		return $r;
	}



/*
	function getUrl($path = ''){
		if($path!=''){
			$useArrUrl = explode('/',$path);
			$useArrUrl = array_map('s_string::trim',$useArrUrl);
		}
	}
*/
}