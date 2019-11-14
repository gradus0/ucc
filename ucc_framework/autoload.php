<?php

$file_t_pathAlias = ucc\pathDirSeparator(_UCC_PATH.'/library/pathAlias/pathAliasMake.class.php');
$file_s_pathAlias = ucc\pathDirSeparator(_UCC_PATH.'/systems/pathAlias/pathAliasMake.class.php');

include_once $file_t_pathAlias; // include tools class for work alias of path
include_once $file_s_pathAlias; // include systems class for work tools class

use ucc\systems\pathAlias\pathAliasMake as s_pathAlias;

class ucc_autoload{

	private static $pathAliasInst;

	public static function load($class_name){

		if($path = self::$pathAliasInst->getPathOfNamespace($class_name)){

			$class_path = ucc\pathDirSeparator($path) . '.class.php';

			if(file_exists($class_path)){
				include_once $class_path;
			}else{

				try{

					if(method_exists('ucc\systems\log\logMake','getInstance')){
						$logObj = ucc\systems\log\logMake::getInstance(); // fatal error if not class
						$logObj->fatal_error('class path '.$class_path.' not found',__FILE__,__LINE__);
					}

				}catch (Exception $e){
					ucc\fatal_error($e->getMessage(),$e->getFile(),$e->getLine()); // stop recursion autoload log file
				}

				throw new Exception($class_path.' not found');
			}
		}
	}

	public static function register(){

		self::$pathAliasInst = s_pathAlias::getInstance();

		self::$pathAliasInst->setAliasForPath('ucc',_UCC_PATH);

		spl_autoload_register(array(__CLASS__,'load'));

	}

	public static function unregister(){
		spl_autoload_register(array(__CLASS__,'load'));
	}

}