<?php

namespace Stone\SDK\Util;

class Crypt
{
    /**
     * partnerEncrypt
     * 公寓方加密
     *
     * @param mixed $data
     * @param string $key
     * @static
     * @return string
     */
    public static function partnerEncrypt($data, $key)
    {
        $aes_key = md5(microtime(true) . mt_rand(0, 1000));
        $data = json_encode($data);
        $encrypted_data = self::encryptUseAES($data, $aes_key);

        if (openssl_public_encrypt($aes_key, $encrypted_key, $key)) {
            return base64_encode($encrypted_key) . ':' . $encrypted_data;
        }

        return null;
    }

    /**
     * partnerDecrypt
     * 公寓方解密
     *
     * @param string $data
     * @param string $key
     * @static
     * @return mixed
     */
    public static function partnerDecrypt($data, $key)
    {
        if (strpos($data, ':') === false) {
            return null;
        }

        list($encrypted_key, $encrypted_data) = explode(':', $data);

        $encrypted_key = base64_decode($encrypted_key);
        if (openssl_public_decrypt($encrypted_key, $aes_key, $key)) {
            $data = self::decryptUseAES($encrypted_data, $aes_key);
            return json_decode($data, true);
        }

        return null;
    }

    /**
     * platformEncrypt
     * 平台方加密
     *
     * @param mixed $data
     * @param string $key
     * @static
     * @return string
     */
    public static function platformEncrypt($data, $key)
    {
        $aes_key = md5(microtime(true) . mt_rand(0, 1000));
        $data = json_encode($data);
        $encrypted_data = self::encryptUseAES($data, $aes_key);

        if (openssl_private_encrypt($aes_key, $encrypted_key, $key)) {
            return base64_encode($encrypted_key) . ':' . $encrypted_data;
        }

        return null;
    }

    /**
     * platformDecrypt
     * 平台方解密
     *
     * @param string $data
     * @param string $key
     * @static
     * @return mixed
     */
    public static function platformDecrypt($data, $key)
    {
        if (strpos($data, ':') === false) {
            return null;
        }

        list($encrypted_key, $encrypted_data) = explode(':', $data);

        $encrypted_key = base64_decode($encrypted_key);
        if (openssl_private_decrypt($encrypted_key, $aes_key, $key)) {
            $data = self::decryptUseAES($encrypted_data, $aes_key);
            return json_decode($data, true);
        }

        return null;
    }

    /**
     * 使用AES基础算法加密数据
     *
     * @param  string  $value
     * @param  string  $key
     * @return string
     */
    public static function encryptUseAES($value, $key, $iv = '2014032100000000')
    {
        $cipher = MCRYPT_RIJNDAEL_128;
        $mode = MCRYPT_MODE_CBC;
        $value = self::addPkcs7Padding($value, $cipher, $mode);

        $encrypt_str = mcrypt_encrypt($cipher, $key, $value, $mode, $iv);
        return base64_encode($encrypt_str);
    }

    /**
     * 使用AES基础算法解密数据
     *
     * @param  string  $value
     * @param  string  $key
     * @return string
     */
    public static function decryptUseAES($value, $key, $iv = '2014032100000000')
    {
        $cipher = MCRYPT_RIJNDAEL_128;
        $mode = MCRYPT_MODE_CBC;
        $value = base64_decode($value);

        $decrypt_str = mcrypt_decrypt($cipher, $key, $value, $mode, $iv);

        return self::stripPkcs7Padding($decrypt_str);
    }

    private static function addPkcs7Padding($source, $cipher, $mode)
    {
        // 去除字符两边空格
        $source = trim($source);

        // 根据算法和模式算出长度，byte为单位
        $block = mcrypt_get_block_size($cipher, $mode);

        // 取得补码的长度
        $pad = $block - (strlen($source) % $block);

        // 用ASCII码为补码长度的字符， 补足最后一段
        $source .= str_repeat(chr($pad), $pad);

        return $source;
    }

    /**
     * 除去pkcs7 padding
     *
     * @param String $string 解密后的结果
     * @return String
     */
    private static function stripPkcs7Padding($string)
    {
        $slast  = ord(substr($string, -1));
        $slastc = chr($slast);
        $pcheck = substr($string, -$slast);

        if (preg_match("/$slastc{".$slast."}/", $string)) {
            $string = substr($string, 0, strlen($string) - $slast);
            return $string;
        } else {
            return false;
        }
    }

}
