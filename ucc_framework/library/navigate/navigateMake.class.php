<?php
/**
 * Created by PhpStorm.
 * User: gradus
 * Date: 25.02.15
 * Time: 23:22
 */

namespace ucc\library\navigate;

class navigateMake{

		static function pagination($max_count = 0 , $count = 0 , $count_void = 3, $current_num = 1){
		$p = array();

		if($count>=$max_count) return $p;

		//осуществл¤ем проверку, чтобы выводимые перва¤ и последн¤¤ страницы
		// не вышли за границы нумерации
		$first=$current_num-$count_void;
		if($first<1) $first=1;
		$last=$current_num+$count_void;
		$tmpnum=ceil($max_count/$count);
		if($last>$tmpnum) $last=$tmpnum;

		if($first>1)  $p[]=1;
		//если текуща¤ страница далеко от 1-й, то часть предыдущих страниц
		//скрываем троеточием

		if($first>=$count_void) {
			$p[]=false;
		}
		//если текуща¤ страница имеет номер до 10, то выводим все номера
		//перед заданным диапазоном без скрыти¤
		else {
			for($i=2;$i<$first;$i++){
				$p[]=$i;
			}
		}
		//отображаем заданный диапазон: текуща¤ страница +-$prev
		for($i=$first;$i<$last+1;$i++){
			/*
			if($i==$current_num)
			*/
			$p[]=$i;
		}

		//часть страниц скрываем
		if($last<$tmpnum and $tmpnum-$last>1) $p[]=false;
		//выводим последнюю страницу
		if($last<$tmpnum)  $p[]=$tmpnum;

		return $p;
	}
}