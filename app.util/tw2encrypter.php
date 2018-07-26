<?php
/**
 * Created by Thiago Adril.
 * User: PTEC73
 * Date: 30/04/2015
 * Time: 09:43
 */
namespace util;

use app\TApp;

class TW2EncrypterHashType
{
    const Hash128 = MCRYPT_RIJNDAEL_128;
    const Hash192 = MCRYPT_RIJNDAEL_192;
    const Hash256 = MCRYPT_RIJNDAEL_256;
}

class TW2Encrypter
{
    private $_token;
    private $_key;
    private $_hash_type;
    private $_isbase64;
    private $crypt_mode = MCRYPT_MODE_ECB;

    const METHOD = 'aes-256-cbc';


    public function __construct($token, $key, $hashType = "256", $isbase64 = true)
    {
        $this->_token = $token;
        $this->_key = $key;
        $this->_hash_type = $hashType;
        $this->_isbase64 = $isbase64;
    }

    private function Encrypt($content)
    {
        
        $crypt_option = $this->_hash_type;
        $string_empty = "";
        $crypt_type = null;

        switch ($crypt_option) {
            case "128":
                $crypt_type = TW2EncrypterHashType::Hash128;
                break;
            case "192":
                $crypt_type = TW2EncrypterHashType::Hash192;
                break;
            case "256":
                $crypt_type = TW2EncrypterHashType::Hash256;
                break;
        }
        
        try {

            $crypt = mcrypt_module_open($crypt_type, $string_empty, $this->crypt_mode, $string_empty);
            mcrypt_generic_init($crypt, $this->_token, $this->_key);
            $encrypted = mcrypt_generic($crypt, $content);

            $encode = null;

            if ($this->_isbase64) {
                $encode = base64_encode($encrypted);
            }

            mcrypt_generic_deinit($crypt);
            mcrypt_module_close($crypt);

        } catch (Exception $ex) {
            $crypt = null;
        }
        

        return $encode;
    }

    private function  Decrypt($content_encoded)
    {
        $crypt_option = $this->_hash_type;
        $string_empty = "";
        $crypt_type = null;

        switch ($crypt_option) {
            case "128":
                $crypt_type = TW2EncrypterHashType::Hash128;
                break;
            case "192":
                $crypt_type = TW2EncrypterHashType::Hash192;
                break;
            case "256":
                $crypt_type = TW2EncrypterHashType::Hash256;
                break;
        }

        try {

            $content_decoded = base64_decode($content_encoded);

            $crypt = mcrypt_module_open($crypt_type, $string_empty, $this->crypt_mode, $string_empty);
            mcrypt_generic_init($crypt, $this->_token, $this->_key);
            if (isset($content_decoded) and $content_decoded !== "") {
                $decrypted = mdecrypt_generic($crypt, $content_decoded);
            } else {
                $decrypted = "";
            }

            mcrypt_generic_deinit($crypt);
            mcrypt_module_close($crypt);

        } catch (Exception $ex) {
            $crypt = null;
        }

        return trim($decrypted);
    }

    public function EncryptPWD($content)
    {
        $result_value = "";

        try {
            $enc = $this->Encrypt($content);

            $rnd_salt = TApp::generateGuid(false);
            $new_enc = $this->Encrypt($rnd_salt);

            $token = sprintf("%s:%s", $new_enc, $enc);

            $r = $this->Encrypt($token);

            $result_value = $r;

        } catch (Exception $ex) {
            $result_value = "";
        }

        return $result_value;
    }

    public function DecryptPWD($content_encoded)
    {
        $result_value = "";

        try {
            $dec = $this->Decrypt($content_encoded);
            $data_split = explode(':', $dec);

            if (isset($data_split[1])) {
                $r = $this->Decrypt($data_split[1]);
                $result_value = $r;
            }

        } catch (Exception $ex) {
            $result_value = "";
        }

        return $result_value;
    }
}
?>