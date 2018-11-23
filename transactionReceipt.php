<?php 
require_once __DIR__ . "/vendor/autoload.php";

use Web3\Web3;
use EthTool\Callback;

$Web3 = new Web3('http://127.0.0.1:7545');
$Eth = $Web3->getEth();
$cb = new Callback();

$timeout = 30;
$interval =10;
$t0 = time();
$txhash = '0x3397f3c80b7427e7f9795db83d7c9046267fcc3cc21b6893dd969c96d6437b6a';
while (true) {
	$Eth->getTransactionReceipt($txhash, $cb);
	if ($cb->result) {
		break;
	}
	echo $cb->errMsg . "\r\n";
	$t1 = time();
	if (($t1 - $t0) > $timeout) {
		# code...
		echo 'timeout' . "\r\n";
		break;
	}
	sleep($interval);
}
print_r($cb->result);