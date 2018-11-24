<?php  
require_once __DIR__ . "/vendor/autoload.php";

use Web3\Web3;
use Web3\Contract;
use EthTool\Callback;

$Web3 = new Web3('http://127.0.0.1:7545');

$abi = file_get_contents('./contract/EzToken.abi');
$contractAddr = file_get_contents('./contract/EzToken.addr');
$Contract = new Contract($Web3->provider, $abi);
$Contract->at($contractAddr);

$ethabi = $Contract->ethabi;
$topic_transfer = $ethabi->encodeEventSignature($Contract->events['Transfer']);
$topic_approval = $ethabi->encodeEventSignature($Contract->events['Approval']);
// print_r($topic);

$opts=[
	'topics' => [[$topic_transfer, $topic_approval]]
];
$cb = new Callback();
$Web3->eth->newFilter($opts, $cb);
$fid = $cb->result;
print_r($fid);

while (true) {
	$Web3->eth->getFilterChanges($fid, $cb);
	$logs = $cb->result;
	echo 'listen...' . "\r\n";

	if (!empty($logs) && is_array($logs)) {
		foreach ($logs as $log) {
			echo 'txhash: ' . $log->transactionHash . PHP_EOL;
			$from = $ethabi->decodeParameter('address', $log->topics[1]);
			echo 'from: ' . $from . PHP_EOL;
			$to = $ethabi->decodeParameter('address', $log->topics[2]);
			echo 'to: ' . $to . PHP_EOL;
			$value = $ethabi->decodeParameter('uint256' ,$log->data);
			echo 'value: ' . $value . PHP_EOL;
		}
	}

	sleep(3);
}