<?php
/**
 * Created by gradus.
 * email: gradus0@mail.ru
 * Date: 10.11.14
 * Time: 17:45
 */

namespace ucc\systems\config;

use ucc\systems\patterns\multiSinglton as s_ms;

class configMake extends s_ms{

	private $config_array=array();

	private $config_path;

	function set_path($path){
		$this->config_path = \ucc\pathDirSeparator_r($path);
	}

	function load($config_name)
	{
		$config_name = strtolower($config_name);


		if(!isset($this->config_array[$config_name])){
			$config_path = $this->config_path.$config_name.'.conf.php';

			if (is_file($config_path))
			{
				$config = include $config_path;
			}

			if((empty($config)) || (!empty($config) && !is_array($config))){
				$config=array();
			}

			$this->config_array[$config_name] = $config;

		}

		return $this->config_array[$config_name];
	}

	private function set_config($config_name,$new_config=array()){
		$config_name = strtolower($config_name);
		$this->config_array[$config_name] = $new_config;
	}

	function replace_config($config_name,$new_config=array())
	{
		$old_config = $this->load($config_name);
		$set_config = array_merge($old_config, $new_config);
		$this->set_config($config_name,$set_config);
	}
}