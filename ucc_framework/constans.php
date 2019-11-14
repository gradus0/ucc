<?php
/**
 * Created by gradus.
 * email: gradus0@mail.ru
 * Date: 09.11.14
 * Time: 21:48
 */

// namespace ucc;

/*

class define{
	private $consts;

}
*/

// system
define("_UCC_FRAMEWORK_NAMESPACE", __NAMESPACE__);
define("_UCC_PATH_SELF", dirname($_SERVER['PHP_SELF']));
define("_UCC_KEY", md5('ucc'));
define("_UCC_DOC_ROOT", $_SERVER['DOCUMENT_ROOT']);
define("_UCC_DOMEN", $_SERVER['SERVER_NAME']);
define("_UCC_TIME", time());

define("_UCC_PATH", dirname(__FILE__));

/*

// systems
define("_CONFIG_PATH", 'config'.DIRECTORY_SEPARATOR);

// user using constans
define("_PATH_TEMPLATE", 'themes/');

// user using constans
define("_PATH_AUTOLOAD", dirname(__FILE__).DIRECTORY_SEPARATOR);

	*/