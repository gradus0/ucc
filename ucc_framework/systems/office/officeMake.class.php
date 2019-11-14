<?php
namespace ucc\systems\office;

use ucc\library\office\excel\excelMake as excel;
use ucc\systems\patterns\singlton as s_singlton;

class officeMake extends s_singlton{
	private $excel;

	function __construct(){
		$this->excel = new excel();
	}
}