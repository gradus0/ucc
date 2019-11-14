<?php

namespace ucc\library\template;

class templateMake extends \stdClass
{

	private $TPLvars = array();

	private $tpl_file;

	static private $tpl_path;

	static private $http_path;

	function __construct($tpl_file = '')
	{
		$this->tpl_file = $tpl_file;
	}

	function getPath()
	{
		return self::$tpl_path;
	}

	static function setPath($path)
	{
		self::$tpl_path = $path;
	}

	static function setHttpPath($path){
		self::$http_path = $path;
	}

	static function getHttpPath(){
		return self::$http_path;
	}

	public function __set($name, $value)
	{
		$this->TPLvars[$name] = $value;
	}


	public function &__get($name)
	{
		if (is_scalar($this->TPLvars[$name])) {
			$property = $this->TPLvars[$name];
		} else {
			$property = & $this->TPLvars[$name];
		}
		return $property;
	}

	public function __isset($name)
	{
		return isset($this->TPLvars[$name]);
	}

	public function __unset($name)
	{
		unset($this->TPLvars[$name]);
	}

	public function __invoke(array $vars){
		$this->TPLvars = array_merge($this->TPLvars, $vars);
	}

	public function load()
	{
		$file = $this->getPath() . $this->tpl_file;

		foreach ($this->TPLvars as $k => $val) $$k = $val;

		ob_start();
			include $file;
			$get = ob_get_contents();
		ob_end_clean();

		return $get;
	}
}