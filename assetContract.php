<?php 
require_once __DIR__ . "/vendor/autoload.php";

use Web3\Web3;
use Web3\Contract;
use EthTool\Callback;

$Web3 = new Web3('http://127.0.0.1:7545');
$Eth = $Web3->getEth();
$cb = new Callback();

$Eth->accounts($cb);
$accounts = $cb->result;

$abi = file_get_contents('./contract/EzToken.abi');
$contractAddr = file_get_contents('./contract/EzToken.addr');

$Contract = new Contract($Web3->provider, $abi);
$Contract->at($contractAddr);


$opts = [];
//调用abi中的接口
$Contract->call('balanceOf', $accounts[0], $opts, $cb);
if ($cb->result) {
	print_r($cb->result['balance']->toString());
}