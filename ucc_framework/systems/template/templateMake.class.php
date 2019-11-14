<?php
/**
 * Created by gradus.
 * email: gradus0@mail.ru
 * Date: 23.11.14
 * Time: 19:45
 */

namespace ucc\systems\template;

class templateMake extends \ucc\library\template\templateMake{

	function __construct($file){
		parent::__construct(\ucc\pathDirSeparator($file));
	}

	public static function getInstance($file)
	{
		$c = __CLASS__;
		return new $c($file);
	}

}