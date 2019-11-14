<?php

namespace ucc\library\client;

class cookieMake{

	function set( $name , $value , $expire = 0 , $path = '/' , $domain = null , $secure = false, $httponly = false ){
		return setcookie (
			$name,
			$value,
			$expire = time() + $expire ,
			$path ,
			$domain ,
			$secure ,
			$httponly
		);
	}

	public function destroy( $name = null, $path = '/' , $domain = null , $secure = false ) {
		if(isset($name)){
			if(isset($_COOKIE[$name])){
				unset($_COOKIE[$name]);
				$this->set($name , null , -1 , $path , $domain, $secure );
			}
		}else{
			foreach($_COOKIE as $key){
				unset($_COOKIE[$key]);
				$this->set($key , null , -1 , $path , $domain, $secure );
			}
		}

	}

}