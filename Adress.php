<?php

require_once "vendor/autoload.php";

use Sop\CryptoTypes\Asymmetric\EC\ECPublicKey;
use Sop\CryptoTypes\Asymmetric\EC\ECPrivateKey;
use Sop\CryptoEncoding\PEM;
use kornrunner\Keccak;

$UUID = "UUID";
$SECRET = "SECRET";

$config = [
    'private_key_type' => OPENSSL_KEYTYPE_EC,
    'curve_name' => 'secp256k1'
];

$res = openssl_pkey_new($config);

if (!$res) {
    echo 'ERROR: Fail to generate private key. -> ' . openssl_error_string();
    exit;
}

openssl_pkey_export($res, $priv_key);

$key_detail = openssl_pkey_get_details($res);
$pub_key = $key_detail["key"];





$privateKey =  str_pad($UUID, 66, $SECRET, STR_PAD_RIGHT);



$priv_pem = PEM::fromString("-----BEGIN EC PRIVATE KEY-----
MHQCAQEEIHYyx9jXXBFef2Q/xnR7V9wR7g3qXs3HkwHXpCY7BtUGoAcGBSuBBAAK
oUQDQgAE0myr7QzMPfY6ERXCU1w/$privateKey==
-----END EC PRIVATE KEY-----");

$ec_priv_key = ECPrivateKey::fromPEM($priv_pem);
$ec_priv_seq = $ec_priv_key->toASN1();

$priv_key_hex = bin2hex($ec_priv_seq->at(1)->asOctetString()->string());
$priv_key_len = strlen($priv_key_hex) / 2;
$pub_key_hex = bin2hex($ec_priv_seq->at(3)->asTagged()->asExplicit()->asBitString()->string());
$pub_key_len = strlen($pub_key_hex) / 2;

$pub_key_hex_2 = substr($pub_key_hex, 2);
$pub_key_len_2 = strlen($pub_key_hex_2) / 2;

$hash = Keccak::hash(hex2bin($pub_key_hex_2), 256);

$wallet_address = '0x' . substr($hash, -40);
$wallet_private_key = '0x' . $priv_key_hex;

$wallet_private_key;
echo "\r\n   Address: " . $wallet_address . " \n";
?>