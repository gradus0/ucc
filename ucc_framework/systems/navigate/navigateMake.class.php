<?php
/**
 * Created by PhpStorm.
 * User: gradus
 * Date: 26.02.15
 * Time: 0:44
 */
namespace ucc\systems\navigate;

use ucc\systems\patterns\singlton as s_singlton;
use ucc\library\navigate as n_navigate;

class navigateMake extends s_singlton{

	function pagination($max_count = 0 , $count = 0 , $count_void = 3, $current_num = 1){
		return n_navigate\navigateMake::pagination($max_count , $count , $count_void , $current_num );
	}

	function tree(array $array = array() , array $param = array()){
		return new n_navigate\treeMake($array , $param);
	}
}