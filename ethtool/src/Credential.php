<?php 
namespace EthTool;

use Elliptic\EC;
use kornrunner\Keccak;
use Web3p\EthereumTx\Transaction;


class Credential{
	private $keyPair;

	public function __construct($keyPair){
		$this->keyPair = $keyPair;
	}

	/*生成新的密钥对*/
	public static function new() {
		$ec = new EC('secp256k1');
		$keyPair = $ec->genKeypair();
		return new self($keyPair);
	}

	/*从生成的新密钥对中获取公钥*/
	public function getPublicKey(){
		return $this->keyPair->getPublic()->encode('hex');
	}

	/*从密钥对中获取私钥*/
	public function getPrivateKey(){
		return $this->keyPair->getPrivate()->toString(16,2);
	}

	/*通过公钥生成地址*/
	public function getAddress(){
		$pubkey = $this->getPublicKey();
		$address = '0x' . substr(Keccak::hash(substr(hex2bin($pubkey), 1), 256), 24);
		return $address;
	}

	/*通过密钥恢复密钥对*/
	public static function keyPairFromPrvkey($prvkey){
		$ec = new EC('secp256k1');
		$keyPair = $ec->keyFromPrivate($prvkey);
		return new self($keyPair);
	}

	/*生成keystore文件*/
	public static function newWallet($pass, $dir){
		$credential = self::new();
		$prvkey = $credential->getPrivateKey();
		$wallet = KeyStore::save($prvkey, $pass, $dir);
		return $wallet;
	}

	/**
	* 通过keystore文件获取密钥对
	* $wallet keystore文件路径
	**/
	public static function fromWallet($pass, $wallet){
		$prvkey = KeyStore::load($pass, $wallet);
		return self::keyPairFromPrvkey($prvkey);
	}

	/*对裸交易进行签名*/
	public function signTransaction($raw){
		$txreq = new Transaction($raw);
		$prvkey = $this->getPrivateKey();
		$signed = '0x' . $txreq->sign($prvkey);
		return $signed;
	}
}