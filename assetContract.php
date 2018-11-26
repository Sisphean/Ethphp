<?php 
require_once __DIR__ . "/vendor/autoload.php";

use Web3\Web3;
use Web3\Contract;
use Web3\Utils;
use EthTool\Callback;

$Web3 = new Web3('http://127.0.0.1:7545');
$Eth = $Web3->getEth();
$cb = new Callback();

$Eth->accounts($cb);
$accounts = $cb->result;

$abi = file_get_contents('./contract/EzToken.abi');
$contractAddr = file_get_contents('./contract/EzToken.addr');

$Contract = loadContract($Web3, $abi, $contractAddr);
$balance = balanceOf($Contract, $accounts[0]);
echo 'address: ' . $accounts[0] . ' balance: ' . $balance . "\r\n";

$balance = balanceOf($Contract, $accounts[1]);
echo 'address: ' . $accounts[1] . ' balance: ' . $balance . "\r\n";

$balance = balanceOf($Contract, $accounts[2]);
echo 'address: ' . $accounts[2] . ' balance: ' . $balance . "\r\n";

$balance = balanceOf($Contract, $accounts[3]);
echo 'address: ' . $accounts[3] . ' balance: ' . $balance . "\r\n";


transfer($Contract, $accounts[0], $accounts[1], 100);


$balance = balanceOf($Contract, $accounts[0]);
echo 'address: ' . $accounts[0] . ' balance: ' . $balance ."\r\n";

$balance = balanceOf($Contract, $accounts[1]);
echo 'address: ' . $accounts[1] . ' balance: ' . $balance . "\r\n";


approval($Contract, $accounts[1], $accounts[2], 80);


$balance = balanceOf($Contract, $accounts[1]);
echo 'address: ' . $accounts[1] . ' balance: ' . $balance . "\r\n";

$balance = balanceOf($Contract, $accounts[2]);
echo 'address: ' . $accounts[2] . ' balance: ' . $balance . "\r\n";

$allowance = allowance($Contract, $accounts[1], $accounts[2]);
echo 'owner: ' . $accounts[1] . ' spender: ' . $accounts[2] . ' allowance: ' . $allowance ."\r\n";


transferFrom($Contract, $accounts[2], $accounts[1], $accounts[3], 50);

$balance = balanceOf($Contract, $accounts[1]);
echo 'address: ' . $accounts[1] . ' balance: ' . $balance . "\r\n";

$balance = balanceOf($Contract, $accounts[2]);
echo 'address: ' . $accounts[2] . ' balance: ' . $balance . "\r\n";

$balance = balanceOf($Contract, $accounts[3]);
echo 'address: ' . $accounts[3] . ' balance: ' . $balance . "\r\n";

$allowance = allowance($Contract, $accounts[1], $accounts[2]);
echo 'owner: ' . $accounts[1] . ' spender: ' . $accounts[2] . ' allowance: ' . $allowance ."\r\n";






function loadContract($Web3, $abi, $contractAddr){
	$Contract = new Contract($Web3->provider, $abi);
	$Contract->at($contractAddr);
	return $Contract;
}

function balanceOf($Contract, $owner){
	$cb = new Callback();
	$opts = [];
	$Contract->call('balanceOf', $owner, $cb);
	if ($cb->result) {
		$balance = $cb->result['balance']->toString();
		return $balance;
	} else {
		return $cb->errMsg;
	}
}

function allowance($Contract, $owner, $spender){
	$cb = new Callback();
	$Contract->call('allowance', $owner, $spender, $cb);
	if ($cb->result) {
		$allowance = $cb->result['allowce']->toString();
		return $allowance;
	} else {
		return $cb->errMsg;
	}
}

function approval($Contract, $owner, $spender, $value){
	$cb = new Callback();
	$opts = array(
		'from'	=> $owner,
		'gas'	=> Utils::toHex(2000000, true)
	);
	$Contract->send('approval', $spender, $value, $opts, $cb);
	if ($cb->result) {
		$txhash = $cb->result;
		$receipt = waitForReceipt($Contract->eth, $txhash);
		print_r($receipt);
	} else {
		print_r($cb->errMsg);
	}
}

function transfer($Contract, $init, $_to, $_value){
	$cb = new Callback();
	$opts = array(
		'from'	=> $init,
		'gas'	=> Utils::toHex(2000000, true)
	);
	$Contract->send('transfer', $_to, $_value, $opts, $cb);
	if ($cb->result) {
		$txhash = $cb->result;
		$receipt = waitForReceipt($Contract->eth, $txhash);
		print_r($receipt);
	} else{
		print_r($cb->errMsg);
	}
}

function transferFrom($Contract, $init, $from, $to, $value){
	$cb = new Callback();
	$opts = array(
		'from'	=> $init,
		'gas'	=> Utils::toHex(2000000, true)
	);
	$Contract->send('transferFrom', $from, $to, $value, $opts, $cb);
	if ($cb->result) {
		$txhash = $cb->result;
		$receipt = waitForReceipt($Contract->eth, $txhash);
		print_r($receipt);
	} else {
		print_r($cb->errMsg);
	}
}

function waitForReceipt($Eth, $txhash){
	$timeout = 30;
	$interval = 10;
	$t0 = time();
	$cb = new Callback();
	while (true) {
		$Eth->getTransactionReceipt($txhash, $cb);
		if ($cb->result) {
			$receipt = $cb->result;
			return $receipt;
		} 
		echo $cb->errMsg;
		if (($t1 - $t0) > $timeout) {
			echo 'timeout';
			break;
		}
		sleep($interval);
	}
	return 'no txhash';
	
}