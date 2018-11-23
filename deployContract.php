<?php  
require_once __DIR__ . "/vendor/autoload.php";

use Web3\Web3;
use Web3\Utils;
use Web3\Contract;
use EthTool\Callback;

$Web3 = new Web3('http://127.0.0.1:7545');
$cb = new Callback();
$Eth = $Web3->getEth();

$Eth->accounts($cb);
$accounts = $cb->result;

$abi = file_get_contents('contract/EzToken.abi');
// print_r($abi);

$bytecode = '0x' . file_get_contents('contract/EzToken.bin');
// print_r($bytecode);

$contract = new Contract($Web3->provider, $abi);
$contract->bytecode($bytecode);

$opts = array(
	'from' => $accounts[0],
	'gas' => Utils::toHex(2000000, true)
);
$contract->new(10000000, 'EZ TOKEN', 0, 'EZT', $opts, $cb);
$txhash = $cb->result;

$timeout = 60;
$interval= 1;
$t0 = time();
while (true) {
	$Eth->getTransactionReceipt($txhash, $cb);
	if ($cb->result) {
		$receipt = $cb->result;
		break;
	}
	echo $cb->errMsg;
	$t1 = time();
	if (($t1 - $t0) > $timeout) {
		break;
	}
	sleep($interval);
}

if (!empty($receipt)) {
	$contract->at($receipt->contractAddress);
	file_put_contents('./contract/EzToken.addr', $receipt->contractAddress);
}