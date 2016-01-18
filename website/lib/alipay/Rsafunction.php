<?php
/*
 * 签名字符串
 * @param $prestr 需要签名的字符串
 * return 签名结果
 */
function rsaSign($prestr)
{
    $public_key = file_get_contents(getcwd().'/rsa_private_key.pem');
    $pkeyid = openssl_get_privatekey($public_key);
    openssl_sign($prestr, $sign, $pkeyid);
    openssl_free_key($pkeyid);
    $sign = base64_encode($sign);
    return $sign;
}

/**
 * 验证签名
 * @param $prestr 需要签名的字符串
 * @param $sign 签名结果
 * return 签名结果
 */
function rsaVerify($prestr, $sign)
{
    $sign = base64_decode($sign);
    $public_key = file_get_contents(getcwd().'/rsa_public_key.pem');
    $pkeyid = openssl_get_publickey($public_key);
    if ($pkeyid) {
        $verify = openssl_verify($prestr, $sign, $pkeyid);
        openssl_free_key($pkeyid);
    }
    if ($verify == 1) {
        return true;
    } else {
        return false;
    }
}

?>
