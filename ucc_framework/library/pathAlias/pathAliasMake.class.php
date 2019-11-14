<?php
/**
 * Created by gradus.
 * email: gradus0@mail.ru
 * Date: 17.12.14
 * Time: 23:26
 */

##########
#   WARNING THIS FILE INCLUDE ../../autoload.php
##########

namespace ucc\libray\pathAlias;

class pathAliasMake{

	private $pathsAlias = array();

	public function setAliasForPath($alias,$path){
		$this->pathsAlias[$alias] = rtrim($path,'\\/');
	}

	public function getPathOfAlias($alias)
	{
		if(isset($this->pathsAlias[$alias]))
			return $this->pathsAlias[$alias];
		else
			return false;
	}

	public function getPathOfNamespace($namespace){

		$namespace = ltrim($namespace,'\\');
		if( ($pos = strpos($namespace, '\\') ) !== false ){
			$alias = substr($namespace,0,$pos);
		}else{
			$alias = $namespace; // if call namespace -> use myspace as mys;
		}

		if($path = $this->getPathOfAlias($alias)){
			$path .= substr($namespace,strlen($alias));
			return $path;
		}else{
			return false;
		}
	}

}
