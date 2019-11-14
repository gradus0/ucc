<?php
/**
 * Created by PhpStorm.
 * User: gradus
 * Date: 26.02.15
 * Time: 0:15
 */

namespace ucc\library\navigate;

class treeMake{

	private $arr;
	private $keys_arr;
	private $pm;


	function __construct(array $array = array() , array $param = array()){
		/*
			$this->keys_arr - array [id] => parent_id
		*/
		$this->keys_arr = array();

		/*
			$this->pm - settings name keys in array
		*/
		$this->pm = array(
			'key_parent'=>'parent_id',
			'key_id'=>'id'
		);


		$this->arr = $array;
		$this->pm = array_merge($this->pm, $param);
	}

	/*
		properly_view - for type array view:
			array(
				'id' => 1,
				'parent_id' => 0
			),
			array(
				'id' => 2,
				'parent_id' => 0
			),
	*/
	function properly_view(){

		$parent_name_key = $this->pm['key_parent'];
		$id_name_key = $this->pm['key_id'];

		$arr_new = array();
		foreach($this->arr as $elem){
			$parent_id = $elem[$parent_name_key];
			if(empty($arr_new[$parent_id])) {
				$arr_new[$parent_id] = array();
			}
			$arr_new[$parent_id][$elem[$id_name_key]] = $elem;
			$this->keys_arr[$elem[$id_name_key]] = $elem[$parent_name_key];
		}

		$this->arr = $arr_new;
	}

	/*
		properly_view_children - for type array view:
				array(
					'id' => 1,
					'parent_id' => 0,
					'children' => array(
						array(
							'id' => 3,
							'parent_id' => 1,
							'children' => array...
							)
						),

					),
	*/
	function properly_view_children($children_key_name = 'children', $del_children = true ){
		$del_children = (bool)$del_children;

		$parent_name_key = $this->pm['key_parent'];
		$id_name_key = $this->pm['key_id'];

		$arr_new = array();

		$func_rec = function($elem) use(&$func_rec, &$arr_new , &$children_key_name, &$del_children, &$parent_name_key, &$id_name_key){
			foreach($elem as &$val){
				$parent_id = $val[$parent_name_key];
				if(empty($arr_new[$parent_id])) {
					$arr_new[$parent_id] = array();
				}

				$this->keys_arr[$val[$id_name_key]] = $val[$parent_name_key];
				if(isset($val[$children_key_name]) && count($val[$children_key_name])){
					$func_rec($val[$children_key_name]);
					if($del_children)
						unset($val[$children_key_name]);
				}

				$arr_new[$parent_id][$val[$id_name_key]] = $val;
			}
		};

		$func_rec($this->arr);

		$this->arr = $arr_new;

	}

	/*
		get_level - not use listing call
	*/
	function get_level($id){
		$level = 0 ;


		$level_f = function($id) use(&$level_f,&$level){
			$parent_id = $this->keys_arr[$id];
			if(isset($this->keys_arr[$parent_id])){
				++$level;
				$level_f($parent_id);
			}
		};

		if(isset($this->keys_arr[$id]))
			$level_f($id);

		return $level;
	}

	private	function check_user_func($user_func){
		if(!is_callable($user_func,false)){
			throw new \Exception($user_func.' is not correct function/method');
		}
	}

	private function listing_down($call_back, $parent_id, $level = 0){
		$r = '';

		if(isset($this->arr[$parent_id])){
			$id_name_ley = $this->pm['key_id'];

			foreach($this->arr[$parent_id] as $elem){
				$id_key = $elem[$id_name_ley];
				$arr_in_arr = (isset($this->arr[$id_key]) && count($this->arr[$id_key]));
				$r .= call_user_func_array ($call_back, array($elem, $level, $arr_in_arr) );
				if($arr_in_arr){
					$r.=$this->listing_down($call_back,$id_key,$level+1);
				}
			}
		}

		return $r;
	}

	private function listing_up($call_back,$id,$level){
		$r = array();

		if(isset($this->keys_arr[$id])){

			$parent_name_key = $this->pm['key_parent'];

			$p_id = $this->keys_arr[$id];

			$elem = $this->arr[$p_id][$id];

			$parent_id = $elem[$parent_name_key];

			$arr_in_arr = (isset($this->arr[$id]) && count($this->arr[$id]));

			$r[] = call_user_func_array ($call_back, array($elem, $level, $arr_in_arr) );

			if(isset($this->keys_arr[$parent_id])){
				$r[] = $this->listing_up($call_back, $parent_id, --$level);
			}
		}

		return implode('',array_reverse($r));
	}


	function down($user_func, $id = null, $children_id_type = true  ){
		$this->check_user_func($user_func);

		$children_id_type = (bool)$children_id_type;

		if(!isset($id))
			$id = key($this->arr);

		reset($this->arr);

		if($children_id_type){
			return $this->listing_down($user_func,$id);
		}else{
			$r = '';
			if(isset($this->keys_arr[$id])){
				$parent_id = $this->keys_arr[$id];
				$level = $this->get_level($id);
				$elem = $this->arr[$parent_id][$id];
				$arr_in_arr = (isset($this->arr[$id]) && count($this->arr[$id]));

				$r .= call_user_func_array ($user_func, array($elem, $level, $arr_in_arr) );

				if($arr_in_arr){
					$r .= $this->listing_down($user_func,$id,++$level);
				}
			}
			return $r;
		}
	}

	function up($user_func, $id){
		$this->check_user_func($user_func);

		$level = $this->get_level($id);

		return $this->listing_up($user_func,$id,$level);
	}

}