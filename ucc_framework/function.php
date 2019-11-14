<?php
/**
 * Created by gradus.
 * email: gradus0@mail.ru
 * Date: 09.11.14
 * Time: 18:43
 */

namespace ucc;

function pathDirSeparator($path){
	return str_replace(array('\\','/'), DIRECTORY_SEPARATOR, $path);
}

function pathDirSeparator_r($path){
	return pathDirSeparator($path.'/');
}

function fatal_error($txt,$file=null,$line=null,$debug=null){
	if(!isset($debug)){
		$debug = print_r(debug_backtrace(),1);
	}
	$html='
		<!DOCTYPE html>
		<html>
			<head>
				<title>'._UCC_DOMEN.' fatal error</title>
				<style>
				#fatal_error_message{
					background: none repeat scroll 0 0 #ffccaa;
					border: 1px solid #ff3334;
					border-radius: 5px;
					box-shadow: 5px 5px rgba(0, 0, 0, 0.1);
					font-weight: bold;
					margin-bottom: 1em;
					padding: 5px;
					text-align: center;
				}
				.need_error_read{
					color:red;
				}
				.border{
					border: 2px solid #000;
    				padding: 5px;
				}
				</style>
			</head>
			<body>
				<div id="fatal_error_message">
					<div><span>ERROR</span>: '.$txt.'</div>';
					if(isset($file)){
						$html.='<div><span>FILE</span>: '.$file.'</div>';
					}
					if(isset($line)){
						$html.='<div><span>LINE</span>: '.$line.'</div>';
					}
					if(!empty($debug)){
						$html.='
						<div class="border">
							<span>back_trace</span>
							<div style="text-align:left;">
								<pre>'.$debug.'</pre>
							</div>
						</div>';
					}
		$html.='</div>
			</body>
		</html>
	';

	exit($html);
}


function backtrace_fatal_error($txt,$exception,$debug_backtrace){

	$debug_trace=print_r($debug_backtrace,1);

	$txt.=' '.$exception->getMessage();

	fatal_error($txt,$exception->getFile(),$exception->getLine(),$debug_trace);
}

function exception_handler($exception) {
	backtrace_fatal_error('<span class="need_error_read">Uncaught exception</span> ',$exception,debug_backtrace());
}
