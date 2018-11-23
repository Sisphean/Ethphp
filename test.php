<?php 
require_once __DIR__ . "/vendor/autoload.php";

use EthTool\Credential;

$wallet = Credential::newWallet('123456', './keystore');

echo 'new wallet:' . $wallet . "\r\n";

$credential = Credential::fromWallet('123456', $wallet);
$prvkey = $credential->getPrivateKey();
echo 'private key :' . $prvkey  . "\r\n";

$pubkey = $credential->getPublicKey();
echo 'public key:' . $pubkey  . "\r\n";

$address = $credential->getAddress();
echo 'address:' . $address  . "\r\n";