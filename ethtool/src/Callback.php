<?php 
namespace EthTool;

class Callback{
	function __invoke($err, $result){
		if ($err) {
			$this->errMsg = $err->getMessage();
		}
		$this->result = $result;
	}
}