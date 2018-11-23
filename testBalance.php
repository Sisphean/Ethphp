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

if (is_array($accounts)) {
	# code...
	foreach ($accounts as $key => $account) {
		# code...
		$Eth->getBalance($account, 'latest', $cb);
		list($bnq, $bnr) = Utils::toEther($cb->result, 'wei');
		echo 'account:' . $account . '		balance:' . $bnq->toString() . ' ether , bnr:' . $bnr . "\r\n";
	}
}

$Eth->getBalance($accounts[5], 'earliest', $cb);
echo '6th account:' . $accounts[5] . 'earliest  balance:' .$cb->result . "\r\n";

$wei = Utils::toWei('20', 'shannon');
echo '20GWei = ' . $wei->toString() . ' wei' . "\r\n";

list($bnq, $bnr) = Utils::fromWei('5200', 'shannon');
echo '5200 wei = ' . $bnq->toString() . 'GWei' . $bnr->toString() . 'wei' . "\r\n";

