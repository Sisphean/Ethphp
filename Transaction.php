<?php 
require_once __DIR__ . "/vendor/autoload.php";

use Web3\Web3;
use Web3\Utils;
use EthTool\Callback;

$Web3 = new Web3('http://127.0.0.1:7545');
$Eth = $Web3->getEth();

$cb = new Callback();
$Eth->accounts($cb);
$accounts = $cb->result;
$txhash = null;

if (!empty($accounts) && is_array($accounts)) {
	$txreq = [
		"from" 	=> $accounts[2],
		"to"	=> $accounts[4],
		"value"	=> '0x' . Utils::toWei('1','ether')->toHex()
	];
	$Eth->sendTransaction($txreq, $cb);
	$txhash = $cb->result;
	echo 'tx hash: ' . $cb->result . "\r\n" ;

	$Eth->getBalance($accounts[2], 'latest', $cb);
	list($bnq, $bnr) = Utils::toEther($cb->result, 'wei');
	echo 'account: ' . $accounts[2] . ' balance: ' . $bnq->toString() . 'ether' . "\r\n";

	$Eth->getBalance($accounts[4], 'latest', $cb);
	list($bnq, $bnr) = Utils::toEther($cb->result, 'wei');
	echo 'account: ' . $accounts[4] . ' balance: ' . $bnq->toString() . 'ether' . "\r\n";

	getTransactionReceipt($Web3, $txhash);

}


function getTransactionReceipt($Web3, $txhash){
	$timeout = 30;
	$interval = 10;
	$t0 = time();
	$cb = new Callback();
	while (true) {
		# code...
		$Web3->eth->getTransactionReceipt($txhash,$cb);
		if ($cb->result) {
			# code...
			break;
		}
		echo 'no txhash' . "\r\n";
		$t1 = time();
		if (($t1 - $t0) > $timeout) {
			# code...
			break;
		}
		sleep($interval);
	}
	print_r($cb->result);
}

