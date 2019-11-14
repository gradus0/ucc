Этот проект не является классическим фреймворком.
Это набор инструментов/библиотек позволяющий создавать MVC фреймворк или иное решение

Вот пример как собрать классический фреймворк

**index.php**
```php
<?php
define("_PATH", dirname(__FILE__).'/');

include_once  _PATH . 'ucc_framework/ucc.php'; // подключаем фреймворк
include_once _PATH . 'autoload.php'; // подключение _autoload
include_once _PATH . 'core.php'; // подключение ядра

define("_DOMEN", 'http://' . $_SERVER['SERVER_NAME']. '/');

define("_DEBUG", 1);

core::run(); // запуск ядра
```

**autoload.php**
```php
<?php
class _autoload{

	private static $pathAliasInst;

	public static function load($class_name){

		if($path = self::$pathAliasInst->getPathOfNamespace($class_name)){

			$class_path = ucc\pathDirSeparator($path) . '.class.php';
			if( file_exists($class_path) ){
				include_once $class_path;
			}else{
				throw new Exception('class path '.$class_path.' not found');
			}

		}
	}

	public static function register(){
		self::$pathAliasInst = ucc::sys()->pathAlias('my_namespace');
		self::$pathAliasInst->setAliasForPath('models',_PATH.'/models');
		self::$pathAliasInst->setAliasForPath('controllers',_PATH.'/controllers');
		self::$pathAliasInst->setAliasForPath('sys',_PATH.'/sys');

		spl_autoload_register(array(__CLASS__,'load'));
	}
}
```


**core.php**
```php
<?php

class core{

	private $config;
	private $path;

	private static $_instance;

	final public static function getInstance() {
		if (self::$_instance === null) {
			self::$_instance = new self;
		}

		return self::$_instance;
	}

	static function run(){
		return self::getInstance();
	}

	// устанавливаем путь до шаблонов в шаблонизаторе
	private function set_template(){

		\ucc::sys()->headers->html_header(); // заголовок типа и кодировки

		$dirObj = ucc::sys()->dir;
		$dirObj->setPathOfAlias('theme', $this->path . 'themes/' . $this->config['theme'] );
		ucc\systems\template\templateMake::setPath( $dirObj->getPathOfAlias('theme') );
		ucc\systems\template\templateMake::setHttpPath( '/themes/' . $this->config['theme'].'/');
		\ucc::sys()->space->template_tools = new \ucc\systems\template\toolsMake;
		return ucc::sys()->template('index.php');
	}

	// установка кофнига для библиотек
	private function set_config(){

		$confObj = ucc::sys()->config;
		$confObj->set_path( $this->path.'/config' );
		$this->config = $confObj->load('main');

		if(isset( $this->config['smtp'] )){
			$this->config['smtp']['charset'] = $this->config['charset'];
			// установка конфига для отправки почты
			ucc::sys()->mail->set_config(
				array(
					'phpMailer' => $this->config['smtp']
				)
			);
		}
	}


	final public static function call_controller( $controller, $method = null ){

		if( $method === null ){
			$method = 'index';
		}

		$controller_class = "controllers\\{$controller}";

		$cont = '';

		try{

			$newObj = new $controller_class;

			if( method_exists($newObj,'_postActionName') ){
				$method .= $newObj->_postActionName();
			}else {
				$method .= 'Action';
			}

			$cont = $newObj->$method();

		}catch (\Exception $e){
			if (defined("_DEBUG") && _DEBUG)
				$cont = $e->getMessage();
		}

		if($cont=='') $cont='Страница не найдена';

		return $cont;
	}

	// стартуем запрашеваемый модуль
	private function start_modul(){

		$requestObj = \ucc::sys()->request;
		$scapeObj = \ucc::sys()->space;

		$scapeObj->tpl_load = true;
		$url_param = $requestObj->_poq('/controller/method/');

		$controller = isset($url_param['controller'])?$url_param['controller']:$this->config['default_controller'];
		$method = isset($url_param['method'])?$url_param['method']:'index';

		$main_template_obj = $this->set_template();

		$cont = self::call_controller($controller, $method);

		if( $requestObj->is_ajax() ){
			echo $cont;
		}else{
			$main_template_obj->content = $cont;
			echo $main_template_obj->load();
		}

	}


	function __construct(){

		ucc::run( _PATH . '/config' );  // ucc framework start

		ucc::sys()->dir->setPathOfAlias('project', _PATH ); // add alias

		$this->path = ucc::sys()->dir->getPathOfAlias('project');

		\_autoload::register();

		$this->set_config();
		$this->start_modul();
	}

}
```

Создайте в папке controllers файл **index.class.php**
```php
<?php
namespace controllers;

class index{

	protected $model;

	function __construct(){
		$this->model = new \models\info();
	}

	public function indexAction(){

		$data = $this->model->get_data();

		$tpl = \ucc::sys()->template('index/info.php');
		$tpl->items = $data;

		return $tpl->load();
	}
}
```


Создайте в папке models файл **info.class.php**
```php
<?php
namespace models;

class info{


	public function get_data(){

		$q = "
		SELECT
			*
		FROM
			`data`
		ORDER BY
			`name`
				ASC
		";

		return $this->db->getAll($q);
	}
}
```

На примере, запуск кортроллера и будет вызываться по таким гет параметрам
###### example.com?contoller=index&action=index
