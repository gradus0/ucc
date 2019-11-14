<?php
/**
 * Created by PhpStorm.
 * User: gradus
 * Date: 09.03.15
 * Time: 17:32
 */
namespace ucc\systems\files;

use ucc\library\files\uploadMake as l_fileUpload;
use ucc\systems\patterns\singlton as s_singlton;

class filesMake extends s_singlton{

	function getUploadFile($file_name){
		return l_fileUpload::getFile($file_name);
	}

	function getUploadFiles($files_name){
		return l_fileUpload::getFiles($files_name);
	}

}