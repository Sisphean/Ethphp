<?php 
require_once __DIR__ . "/vendor/autoload.php";

use Web3\Web3;
use EthTool\Callback;

$Web3 = new Web3('http://127.0.0.1:7545');
$Eth = $Web3->getEth();

echo 'block-monitor started' . "\r\n";
block_monitor($Web3, 3);



function block_monitor($Web3, $interval){
	$cb = new Callback();
	//newBlockFilter应该是无参数的，不解第一个参数的作用
	$Web3->eth->newBlockFilter(4, $cb);
	$fid = $cb->result;
	print_r('fid: ' . $fid);

	while (true) {
		$Web3->eth->getFilterChanges($fid, $cb);
		$blocks = $cb->result;
		if (!empty($blocks) && is_array($blocks)) {
			print_r($blocks);
			foreach ($blocks as $hash) {
				//第二个参数是表示是否返回完整的交易对象， false 只返回交易hash
				$Web3->eth->getBlockByHash($hash, false, $cb);
				print_r($cb->result);
			}
		}
		
		sleep($interval);
	}
}
