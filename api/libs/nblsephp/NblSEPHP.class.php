<?php

/**
 * A set of useful security helper functions
 *
 * @author johnatas
 */
class NblSEPHP
{
    /**
     * Checks if a string is a JSON code
     * 
     * @param string $str string to be tested
     * @return bool
     */
    public static function isJSONCode($str): bool
    {
        json_decode($str);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    /**
     * Filters a string for SQL injection code
     * 
     * @param string $str string to be filtered
     * @return string
     */
    public static function noSQLInjection($str): string
    {
        $str = trim($str);
        $str = addslashes($str);
        
        return $str;
    }
    
    /**
     * Filters a string for XSS code
     * 
     * @param string $str string to be filtered
     * @return string
     */
    public static function noXSS($str): string
    {
        return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Filters a string 
     * 
     * @param string $str string to be filtered
     * @param bool $filter_html flag to enable HTML filtering. Optional param. Default = true
     * @return string
     */
    public static function filter($str, $filter_html = true): string
    {
        if(self::isJSONCode($str)) {
            return $str;
        }
        if($filter_html) {
            $str = htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
        }
        $str = trim($str);
        $str = addslashes($str);
        
        return $str;
    }
    
    /**
     * Filters an array
     * 
     * @param array $arr to be filtered
     * @param bool $filter_html flag to enable HTML filtering. Optional param. Default = true
     * @return void
     */
    public static function filterArray(&$arr, $filter_html = true): void
    {
        foreach($arr as $k => $v)
        {
            if(is_string($v)) {
                $arr[$k] = NblSEPHP::filter($v, $filter_html); 
            }
        }
    }
    
    /**
     * Filters a object
     * 
     * @param object $obj object to be filtered
     * @param bool $filter_html flag to enable HTML filtering. Optional param. Default = true
     * @return void
     */
    public static function filterObject(&$obj, $filter_html = true): void 
    {
        $vars = get_object_vars($obj);
        foreach($vars as $k => $v) 
        {
            if(is_string($v)) {
                $obj->{$k} = NblSEPHP::filter($v, $filter_html);
            }
        }
    }
    
    /**
     * Safely base64 encode a string 
     * 
     * @param string $string string to be encoded
     * @return string
     */
    public static function safe_b64encode($string): string
    {
        $data = base64_encode($string);
        $data = str_replace(array('+','/','='),array('-','_',''),$data);
        return $data;
    }

    /**
     * Safely decode a base64 encoded string 
     * 
     * @param string $string string to be decoded
     * @return string
     */
    public static function safe_b64decode($string): string
    {
        $data = str_replace(array('-','_'),array('+','/'),$string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);
    }
    
    /**
     * Encrypt data whith openssl and returns a base64 encoded string
     * 
     * @param string $data data to be encrypted
     * @param string $c_k encryption key
     * @param string $c_iv encryption iv
     * @return string
     */
    public static function cryptData($data, $c_k, $c_iv): string
    {
        $encrypt_method = "AES-256-CBC";
        $key = hash('sha256', $c_k);
        $iv = substr(hash('sha256', $c_iv), 0, 16);
        
        $encrypted = openssl_encrypt($data,$encrypt_method, $key, 0, $iv);
        
        return trim(NblSEPHP::safe_b64encode($encrypted));
    }
    
    /**
     * Decrypt a base64 data whith openssl and return the decrypted data
     * 
     * @param string $data base64 encrypted data to be decrypted
     * @param string $c_k encryption key
     * @param string $c_iv encryption iv
     * @return string
     */
    public static function decryptData($data, $c_k, $c_iv): string
    {
        $encrypt_method = "AES-256-CBC";
        $key = hash('sha256', $c_k);
        $iv = substr(hash('sha256', $c_iv), 0, 16);

        $data = NblSEPHP::safe_b64decode($data);
        $decrypted = openssl_decrypt($data,$encrypt_method, $key, 0, $iv);
        
        return trim($decrypted);
    }
    
    /**
     * Verify if encryption is correct by comparing encrypted and non-encrypted data
     * 
     * @param string $data non-encrypted data
     * @param string $encData encrypted data
     * @param string $c_k encryption key
     * @param string $c_iv encryption iv
     * @return bool
     */
    public static function validEncryptedData($data, $encData, $c_k, $c_iv): bool
    {
        $encrypt_method = "AES-256-CBC";
        $key = hash('sha256', $c_k);
        $iv = substr(hash('sha256', $c_iv), 0, 16);
        
        $encrypted = openssl_encrypt($data,$encrypt_method, $key, 0, $iv);
        
        if(trim(NblSEPHP::safe_b64encode($encrypted)) == trim($encData))
        {
            return true;
        }
        
        return false;
    }
}

?>
