<?php
/**
 * @author col.shrapnel@gmail.com
 * @link http://phpfaq.ru/safemysql
 *
 * $name = $db->getOne('SELECT name FROM table WHERE id = ?i',$_GET['id']);
 * $data = $db->getInd('id','SELECT * FROM ?n WHERE id IN ?a','table', array(1,2));
 * $data = $db->getAll("SELECT * FROM ?n WHERE mod=?s LIMIT ?i",$table,$mod,$limit);
 *
 * $ids = $db->getCol("SELECT id FROM tags WHERE tagname = ?s",$tag);
 * $data = $db->getAll("SELECT * FROM table WHERE category IN (?a)",$ids);
 *
 * $data = array('offers_in' => $in, 'offers_out' => $out);
 * $sql = "INSERT INTO stats SET pid=?i,dt=CURDATE(),?u ON DUPLICATE KEY UPDATE ?u";
 * $db->query($sql,$pid,$data,$data);
 *
 * if ($var === NULL) {
 * $sqlpart = "field is NULL";
 * } else {
 * $sqlpart = $db->parse("field = ?s", $var);
 * }
 * $data = $db->getAll("SELECT * FROM table WHERE ?p", $bar, $sqlpart);
 *
 */
namespace ucc\library\db;


class mysqli {

	private $db_connect;
	private $settings;
	private $stats;
	private $error;

	const RESULT_ASSOC = MYSQLI_ASSOC;
	const RESULT_NUM = MYSQLI_NUM;

	function __construct(array $dbconfig)
	{

		$this->error = array();

		$this->settings=array(
			'charset'=>'utf8',
			'port' => NULL,
			'socket' => NULL,
			'pconnect'=>false
		);

		$this->settings = array_merge($this->settings, $dbconfig);

	}

	function connect(){

		$ret=false;

			if ($this->settings['pconnect'])
			{
				$host = "p:".$this->settings['host'];
			}else{
				$host = $this->settings['host'];
			}

			$this->db_connect = @ mysqli_connect($host, $this->settings['user'], $this->settings['pass'], $this->settings['db_name'], $this->settings['port'], $this->settings['socket']);

			$connect_err = mysqli_connect_error();

			if ($connect_err)
			{
				$this->error($connect_err,mysqli_connect_errno());

			}else{
				mysqli_set_charset($this->db_connect, $this->settings['charset']) or $this->error(mysqli_error($this->db_connect),mysqli_errno($this->db_connect));
				$ret = true;
			}

		return $ret;
	}




	public function query()
	{
		return $this->rawQuery($this->prepareQuery(func_get_args()));
	}
	/**
	 * Conventional function to fetch single row.
	 *
	 * @param resource $result - myqli result
	 * @param int $mode - optional fetch mode, RESULT_ASSOC|RESULT_NUM, default RESULT_ASSOC
	 * @return array|FALSE whatever mysqli_fetch_array returns
	 */
	public function fetch_array($result,$mode=self::RESULT_ASSOC)
	{
		return mysqli_fetch_array($result, $mode);
	}

	public function fetch_assoc($result){
		return mysqli_fetch_assoc($result);
	}

	function close(){
		$ret=false;
		if($this->db_connect){
			if(!$this->settings['pconnect']){
				$ret=mysqli_close($this->db_connect);
			}
		}
		return $ret;
	}

	function db_select($db_name){
		return mysqli_select_db($db_name, $this->db_connect);
	}


	function num_rows($r){
		return mysqli_num_rows($r);
	}

	function insert_id(){
		return mysqli_insert_id($this->db_connect);
	}

	function escape_string($txt){

		if( ! is_int($txt) ){
			$txt = mysqli_real_escape_string($this->db_connect,$txt);
		}

		return $txt;
	}

	/**
	 * Conventional function to get number of affected rows.
	 *
	 * @return int whatever mysqli_affected_rows returns
	 */
	public function affectedRows()
	{
		return mysqli_affected_rows ($this->db_connect);
	}

	/**
	 * Conventional function to free the resultset.
	 */
	public function free($result)
	{
		mysqli_free_result($result);
	}
	/**
	 * Helper function to get scalar value right out of query and optional arguments
	 *
	 * Examples:
	 * $name = $db->getOne("SELECT name FROM table WHERE id=1");
	 * $name = $db->getOne("SELECT name FROM table WHERE id=?i", $id);
	 *
	 * @param string $query - an SQL query with placeholders
	 * @param mixed $arg,... unlimited number of arguments to match placeholders in the query
	 * @return string|FALSE either first column of the first row of resultset or FALSE if none found
	 */
	public function getOne()
	{
		$query = $this->prepareQuery(func_get_args());
		if ($res = $this->rawQuery($query))
		{
			$row = $this->fetch_assoc($res);

			if (is_array($row)) {
				return reset($row);
			}

			$this->free($res);
		}
		return FALSE;
	}
	/**
	 * Helper function to get single row right out of query and optional arguments
	 *
	 * Examples:
	 * $data = $db->getRow("SELECT * FROM table WHERE id=1");
	 * $data = $db->getOne("SELECT * FROM table WHERE id=?i", $id);
	 *
	 * @param string $query - an SQL query with placeholders
	 * @param mixed $arg,... unlimited number of arguments to match placeholders in the query
	 * @return array|FALSE either associative array contains first row of resultset or FALSE if none found
	 */
	public function getRow()
	{
		$query = $this->prepareQuery(func_get_args());
		if ($res = $this->rawQuery($query)) {
			$ret = $this->fetch_assoc($res);
			$this->free($res);
			return $ret;
		}
		return FALSE;
	}

	/**
	 * Helper function to get all the rows of resultset right out of query and optional arguments
	 *
	 * Examples:
	 * $data = $db->getAll("SELECT * FROM table");
	 * $data = $db->getAll("SELECT * FROM table LIMIT ?i,?i", $start, $rows);
	 *
	 * @param string $query - an SQL query with placeholders
	 * @param mixed $arg,... unlimited number of arguments to match placeholders in the query
	 * @return array enumerated 2d array contains the resultset. Empty if no rows found.
	 */
	public function getAll()
	{
		$ret = array();
		$query = $this->prepareQuery(func_get_args());
		if ( $res = $this->rawQuery($query) )
		{
			while($row = $this->fetch_assoc($res))
			{
				$ret[] = $row;
			}
			$this->free($res);
		}
		return $ret;
	}

	/**
	 * Helper function to get all the rows of resultset into indexed array right out of query and optional arguments
	 *
	 * Examples:
	 * $data = $db->getInd("id", "SELECT * FROM table");
	 * $data = $db->getInd("id", "SELECT * FROM table LIMIT ?i,?i", $start, $rows);
	 *
	 * @param string $index - name of the field which value is used to index resulting array
	 * @param string $query - an SQL query with placeholders
	 * @param mixed $arg,... unlimited number of arguments to match placeholders in the query
	 * @return array - associative 2d array contains the resultset. Empty if no rows found.
	 */
	public function getInd()
	{
		$args = func_get_args();
		$index = array_shift($args);
		$query = $this->prepareQuery($args);
		$ret = array();
		if ( $res = $this->rawQuery($query) )
		{
			while($row = $this->fetch_assoc($res))
			{
				$ret[$row[$index]] = $row;
			}
			$this->free($res);
		}
		return $ret;
	}

	/**
	 * Helper function to get a dictionary-style array right out of query and optional arguments
	 *
	 * Examples:
	 * $data = $db->getIndCol("name", "SELECT name, id FROM cities");
	 *
	 * @param string $index - name of the field which value is used to index resulting array
	 * @param string $query - an SQL query with placeholders
	 * @param mixed $arg,... unlimited number of arguments to match placeholders in the query
	 * @return array - associative array contains key=value pairs out of resultset. Empty if no rows found.
	 */
	public function getIndCol()
	{
		$args = func_get_args();
		$index = array_shift($args);
		$query = $this->prepareQuery($args);
		$ret = array();
		if ( $res = $this->rawQuery($query) )
		{
			while($row = $this->fetch_array($res))
			{
				$key = $row[$index];
				unset($row[$index]);
				$ret[$key] = reset($row);
			}
			$this->free($res);
		}
		return $ret;
	}
	/**
	 * Function to parse placeholders either in the full query or a query part
	 * unlike native prepared statements, allows ANY query part to be parsed
	 *
	 * useful for debug
	 * and EXTREMELY useful for conditional query building
	 * like adding various query parts using loops, conditions, etc.
	 * already parsed parts have to be added via ?p placeholder
	 *
	 * Examples:
	 * $query = $db->parse("SELECT * FROM table WHERE foo=?s AND bar=?s", $foo, $bar);
	 * echo $query;
	 *
	 * if ($foo) {
	 * $qpart = $db->parse(" AND foo=?s", $foo);
	 * }
	 * $data = $db->getAll("SELECT * FROM table WHERE bar=?s ?p", $bar, $qpart);
	 *
	 * @param string $query - whatever expression contains placeholders
	 * @param mixed $arg,... unlimited number of arguments to match placeholders in the expression
	 * @return string - initial expression with placeholders substituted with data.
	 */
	public function parse()
	{
		return $this->prepareQuery(func_get_args());
	}

	/**
	 * Function to get last executed query.
	 *
	 * @return string|NULL either last executed query or NULL if were none
	 */
	public function lastQuery()
	{
		$last = end($this->stats);
		return $last['query'];
	}
	/**
	 * Function to get all query statistics.
	 *
	 * @return array contains all executed queries with timings and errors
	 */
	public function getStats()
	{
		return $this->stats;
	}
	/**
	 * private function which actually runs a query against Mysql server.
	 * also logs some stats like profiling info and error message
	 *
	 * @param string $query - a regular SQL query
	 * @return mysqli result resource or FALSE on error
	 */
	private function rawQuery($query)
	{
		$start = microtime(TRUE);
		$res = mysqli_query($this->db_connect, $query);
		$timer = microtime(TRUE) - $start;
		$this->stats[] = array(
			'query' => $query,
			'start' => $start,
			'timer' => $timer,
		);
		if (!$res)
		{
			$error = mysqli_error($this->db_connect);
			$error_code = mysqli_errno($this->db_connect);
			end($this->stats);
			$key = key($this->stats);
			$this->stats[$key]['error'] = $error;
			$this->cutStats();
			$this->error($error,$error_code);
		}
		$this->cutStats();
		return $res;
	}


	private function prepareQuery($args)
	{
		$query = '';
		$raw = array_shift($args);
		$array = preg_split('~(\?[nsiuapl])~u',$raw,null,PREG_SPLIT_DELIM_CAPTURE);
		$anum = count($args);
		$pnum = floor(count($array) / 2);
		if ( $pnum != $anum )
		{
			$this->error("Number of args ($anum) doesn't match number of placeholders ($pnum) in [$raw]");
		}
		foreach ($array as $i => $part)
		{
			if ( ($i % 2) == 0 )
			{
				$query .= $part;
				continue;
			}
			$value = array_shift($args);
			switch ($part)
			{
				case '?n':
					$part = $this->escapeIdent($value);
					break;
				case '?s':
					$part = $this->escapeStringText($value);
					break;
				case '?i':
					$part = $this->escapeInt($value);
					break;
				case '?a':
					$part = $this->createIN($value);
					break;
				case '?u':
					$part = $this->createSET($value);
					break;
				case '?l':
					$part = $this->escapeLike($value);
					break;
				case '?p':
					$part = $value;
					break;
			}
			$query .= $part;
		}
		return $query;
	}


	private function escapeInt($value)
	{
		if ($value === NULL)
		{
			return 'NULL';
		}
		if(!is_numeric($value))
		{
			$this->error("Integer (?i) placeholder expects numeric value, ".gettype($value)." given");
			return FALSE;
		}
		if (is_float($value))
		{
			$value = number_format($value, 0, '.', ''); // may lose precision on big numbers
		}
		return $value;
	}


	public function escapeStringText($value)
	{
		if ($value === NULL)
		{
			return 'NULL';
		}
		return	"'".$this->escape_string($value)."'";
	}

	private function escapeIdent($value)
	{
		if ($value)
		{
			return "`".str_replace("`","``",$value)."`";
		} else {
			$this->error("Empty value for identifier (?n) placeholder");
		}
	}

	private function createIN($data)
	{
		if (!is_array($data))
		{
			$this->error("Value for IN (?a) placeholder should be array");
			return;
		}
		if (!$data)
		{
			return 'NULL';
		}
		$query = $comma = '';
		foreach ($data as $value)
		{
			$query .= $comma.$this->escapeStringText($value);
			$comma = ",";
		}
		return $query;
	}
	private function createSET($data)
	{
		if (!is_array($data))
		{
			$this->error("SET (?u) placeholder expects array, ".gettype($data)." given");
			return;
		}
		if (!$data)
		{
			$this->error("Empty array for SET (?u) placeholder");
			return;
		}
		$query = $comma = '';
		foreach ($data as $key => $value)
		{
			$query .= $comma.$this->escapeIdent($key).'='.$this->escapeStringText($value);
			$comma = ",";
		}
		return $query;
	}


	function escapeLike($search){
		return addcslashes($this->escape_string($search), '%_');
	}

	#what ????? like ' word1 word2 ' - replace - like '%word1%word2%'
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




	/**
	 * On a long run we can eat up too much memory with mere statsistics
	 * Let's keep it at reasonable size, leaving only last 100 entries.
	 */
	private function cutStats()
	{
		if ( count($this->stats) > 100 )
		{
			reset($this->stats);
			$first = key($this->stats);
			unset($this->stats[$first]);
		}
	}



	/**
	 * function to implement whitelisting feature
	 * sometimes we can't allow a non-validated user-supplied data to the query even through placeholder
	 * especially if it comes down to SQL OPERATORS
	 *
	 * Example:
	 *
	 * $order = $db->whiteList($_GET['order'], array('name','price'));
	 * $dir = $db->whiteList($_GET['dir'], array('ASC','DESC'));
	 * if (!$order || !dir) {
	 * throw new http404(); //non-expected values should cause 404 or similar response
	 * }
	 * $sql = "SELECT * FROM table ORDER BY ?p ?p LIMIT ?i,?i"
	 * $data = $db->getArr($sql, $order, $dir, $start, $per_page);
	 *
	 * @param string $iinput - field name to test
	 * @param array $allowed - an array with allowed variants
	 * @param string $default - optional variable to set if no match found. Default to false.
	 * @return string|FALSE - either sanitized value or FALSE
	 */
	public function whiteList($input,$allowed,$default=FALSE)
	{
		$found = array_search($input,$allowed);
		return ($found === FALSE) ? $default : $allowed[$found];
	}
	/**
	 * function to filter out arrays, for the whitelisting purposes
	 * useful to pass entire superglobal to the INSERT or UPDATE query
	 * OUGHT to be used for this purpose,
	 * as there could be fields to which user should have no access to.
	 *
	 * Example:
	 * $allowed = array('title','url','body','rating','term','type');
	 * $data = $db->filterArray($_POST,$allowed);
	 * $sql = "INSERT INTO ?n SET ?u";
	 * $db->query($sql,$table,$data);
	 *
	 * @param array $input - source array
	 * @param array $allowed - an array with allowed field names
	 * @return array filtered out source array
	 */
	public function filterArray($input,$allowed)
	{
		foreach(array_keys($input) as $key )
		{
			if ( !in_array($key,$allowed) )
			{
				unset($input[$key]);
			}
		}
		return $input;
	}


	public function value_implode($data){

		$data = (array) $data;

		foreach($data as &$value){
			if( !is_int( $value) ){
				$value = "'{$value}'";
			}
		}

		return $data;
	}


	public function column_implode($data){

		$data = (array) $data;

		foreach($data as &$value){
			$value = "`{$value}`";
		}

		return $data;
	}

	public function escape_array( array $data){

		$arr = array(
			'column' => array_map( array( $this, 'escape_string' ), array_keys($data) ),
			'value' => array_map( array( $this, 'escape_string' ), array_values($data) )
		);

		return $arr;
	}

	public function insert_implode($data){

		$arr = $this->escape_array($data);

		$arr['column'] = implode ( ',' , $this->column_implode( $arr['column'] ) );
		$arr['value'] =  implode ( ',' , $this->value_implode( $arr['value'] ) );

		return $arr;
	}

	public function update_implode($data){

		$arr = $this->escape_array($data);

		$arr = array_combine(
			$this->column_implode( $arr['column'] ) ,
			$this->value_implode( $arr['value'] )
		);

		$update = array();

		foreach($arr as $column => $val) {
			$update[] = "{$column} = {$val}";
		}

		return implode(', ', $update );
	}

}