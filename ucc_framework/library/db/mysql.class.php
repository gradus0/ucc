<?php
/**
 * Created by gradus.
 * email: gradus0@mail.ru
 * Date: 10.11.14
 * Time: 17:42
 */

namespace ucc\library\db;

class mysql{

	private $db_connect;
	private $settings;

	function __construct(array $dbconfig){

		$this->settings=array(
			'encoding'=>array(
				'utf8'=>array(
					'charset'=>'utf8',
					'connection'=>'utf8_general_ci'
				)
			),
			'pconnect'=>false
		);

		$this->settings = array_merge($this->settings, $dbconfig);
	}

	function connect(){
		$ret=false;

		if($this->settings['pconnect']){
			$this->db_connect = mysql_pconnect($this->settings['host'], $this->settings['user'], $this->settings['pass']);
		}else{
			$this->db_connect = mysql_connect($this->settings['host'], $this->settings['user'], $this->settings['pass']);
		}

		if ($this->db_connect){
			$charset=isset($this->settings['charset'])?$this->settings['charset']:null;
			if(isset($charset)){
				$this->set_charset($charset);
			}

			if(!$ret=$this->db_select($this->settings['db_name'])){
				$this->error(mysql_error(),mysql_errno());
			}

		}else{
			$this->error(mysql_error(),mysql_errno());
		}

		return $ret;
	}

	function close(){
		$ret=false;
		if($this->db_connect){
			if(!$this->settings['pconnect']){
				$ret=mysql_close($this->db_connect);
			}
		}
		return $ret;
	}

	function db_select($db_name){
		return mysql_select_db($db_name, $this->db_connect);
	}


	function set_charset($encoding_name){
		if(isset($this->settings['encoding'][$encoding_name])){
			$encoding = $this->settings['encoding'][$encoding_name];
			$charset = $encoding['charset'];
			$connection = $encoding['connection'];

			$this->query("SET NAMES {$charset}");
			$this->query("SET CHARACTER SET {$charset}");
			$this->query("set character_set_client='{$charset}'");
			$this->query("set character_set_results='{$charset}'");
			$this->query("set collation_connection='{$connection}'");
		}
	}

	function query($q){
		$res=mysql_query($q);
		return $res;
	}

	function fetch_assoc($query){
		return mysql_fetch_assoc($query);
	}

	function num_rows($r){
		$res=mysql_num_rows($r);
		return $res;
	}

	function insert_id(){
		return mysql_insert_id();
	}

	function escape_string($txt){
		return mysql_real_escape_string($txt);
	}

	function searchByLike($search){
		return addcslashes($this->escape_string($search), '%_\\\'');
	}

	function searchByLikeWords($search){
		$search=preg_replace('/\s+/', ' ', trim($this->searchByLike($search)));
		$search=str_replace(' ','%',$search);
		return $search;
	}


	private function set_error($txt,$err = 0){
		$this->error[] = array(
			'message' => $txt,
			'code' => $err
		);
	}

	public function get_error(){
		$ar = array();
		foreach($this->error as $val){
			$ar[] = implode(", ", $val);
		}
		return implode("\n<br>",$ar);
	}

	private function error($txt, $err = 0)
	{
		$txt = __CLASS__.": ".$txt;
		$this->set_error($txt, $err);
		// trigger_error($err,E_USER_ERROR);
		throw new \Exception($txt);
	}

}