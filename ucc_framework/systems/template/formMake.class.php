<?php
/**
 * Created by gradus.
 * email: gradus0@mail.ru
 * Date: 23.01.15
 * Time: 15:48
 */

namespace ucc\systems\template;

use ucc\systems\string\stringMake as s_string;
use ucc\systems\patterns\singlton as s_singlton;

class formMake extends s_singlton{

	public function str_to_input($string){
		return s_string::getInstance()->htmlspecialchars($string, ENT_QUOTES);
	}

}