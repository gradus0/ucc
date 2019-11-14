<?php
/**
 * Created by gradus.
 * email: gradus0@mail.ru
 * Date: 22.11.14
 * Time: 21:10
 */

namespace ucc\library\string;

class stringMake{

	static function trim($str){
		return \trim($str);
	}

	static function iconv($in_charset, $out_charset, $str){
		// return mb_convert_encoding($str, $out_charset, $in_charset);
		return \iconv($in_charset, $out_charset, $str);
	}

}