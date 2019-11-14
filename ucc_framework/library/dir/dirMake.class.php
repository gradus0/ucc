<?php
namespace ucc\library\dir;


class dirMake{
	private $_alias = array();

	public function setPathOfAlias( $alias, $path ){
		$this->_alias[ $alias ] = str_replace(array('\\','/'), DIRECTORY_SEPARATOR, rtrim($path,'\\/') );
	}

	public function getPathOfAlias( $alias , $post_separator = true){
		if(isset( $this->_alias[ $alias ] )){
			return $this->_alias[ $alias ] . ($post_separator ? DIRECTORY_SEPARATOR : '' );
		}else{
			return false;
		}
	}

}
