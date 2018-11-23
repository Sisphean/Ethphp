<?php 
require_once __DIR__ . "/vendor/autoload.php";

use Web3\Web3;
use Web3\Utils;
use EthTool\Callback;
use EthTool\Credential;


$Web3 = new Web3('http://127.0.0.1:7545');
$Eth = $Web3->getEth();
$cb = new Callback();

// $wallet = Credential::newWallet('123456', './keystore');
$wallet = './keystore/f3d600309b992e04ef560680e6ba484467bc9a69.json';
$credential = Credential::fromWallet('123456', $wallet);
$walletAddreses = $credential->getAddress();

$Eth->getBalance($walletAddreses, 'latest', $cb);
$walletBalance = $cb->result;

echo 'wallet address: ' . $walletAddreses . '  balance: ' . $walletBalance . ' wei' . "\r\n";

$Eth->accounts($cb);
$accounts = $cb->result;

if (is_array($accounts)) {
	if (!empty($accounts[1])) {
		// $wei = '0x' . Utils::toWei('1','ether')->toHex();
		// transaction($Eth, $accounts[0], $walletAddreses, $wei);

		$wei = '0x' . Utils::toWei('0.2', 'ether')->toHex();
		$gasPrice = '0x' . Utils::toWei('20', 'gwei')->toHex();
		rawTransaction($Eth, $credential, $accounts[1], $wei, $gasPrice);

	}
}


function transaction($Eth, $from, $to, $value){
	$txreq = array(
		'from' 	=> $from,
		'to'	=> $to,
		'value'	=> $value
	);

	$cb = new Callback();
	$Eth->sendTransaction($txreq, $cb);
	if ($cb->result) {
		# code...
		$txhash = $cb->result;
		$receipt = transactionReceipt($Eth, $txhash);
		print_r($receipt);

		$Eth->getBalance($from, 'latest', $cb);
		list($q, $r) = Utils::toEther($cb->result, 'wei');
		echo 'from address: ' . $from . ' balance: ' . $q . 'ether '. $r . 'wei' . "\r\n";

		$Eth->getBalance($to, 'latest', $cb);
		list($q, $r) = Utils::toEther($cb->result, 'wei');
		echo "to address: " . $to . ' balance: ' . $q . 'ether '. $r . 'wei' . "\r\n";
	}
}

function transactionReceipt($Eth,$txhash){
	$timeout = 30;
	$interval = 10;
	$t0 = time();

	$cb = new Callback();
	while (true) {
		$Eth->getTransactionReceipt($txhash, $cb);
		if ($cb->result) {
			return $cb->result;
			break;
		}
		echo 'no txhash' . "\r\n";

		$t1 = time();
		if (($t1 - $t0) > $timeout) {
			echo 'timeout' . "\r\n";
			break;
		}
		sleep($interval);
	}

}


function rawTransaction($Eth, $credential, $to, $value, $gasPrice){
	$from = $credential->getAddress();
	$raw = array(
		'from'	=> $from,
		'to'	=> $to,
		'value'	=> $value,
		'nonce'	=> getAccountNonce($Eth, $from),
		'gasPrice'	=> $gasPrice,
		'gasLimit'	=> '0x76c0',
		// 'data' => '0x' . bin2hex('hello'),
    	'chainId' => 10
	);

	$req = $credential->signTransaction($raw);
	echo 'signed: ' . $req . "\r\n";
	$cb = new Callback();
	$Eth->sendRawTransaction($req, $cb);
	if ($cb->result) {
		$txhash = $cb->result;
		$receipt = transactionReceipt($Eth, $txhash);
		print_r($receipt);

		$Eth->getBalance($from, 'latest', $cb);
		list($ether, $wei) = Utils::toEther($cb->result, 'wei');
		echo 'from address: ' . $from . ' balance: ' . $ether . ' ether ' . $wei . ' wei ' . "\r\n" ;

		$Eth->getBalance($to, 'latest', $cb);
		list($ether, $wei) = Utils::toEther($cb->result, 'wei');
		echo 'to address: ' . $to . ' balance: ' . $ether . ' ether ' . $wei . ' wei ' . "\r\n";
	} else {
		echo $cb->errMsg;
	}
}

function getAccountNonce($Eth, $from){
	$cb = new Callback();
	$Eth->getTransactionCount($from, $cb);
	$nonce = '0x' . Utils::toHex($cb->result);
	return $nonce;
}