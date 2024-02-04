<?php

/**
 * Extract the token from HTPP headers
 *  * 
 * @return string token if finded. NULL if not finded
 */
function getRawToken(): string
{
    $srv = $_SERVER;
    if(isset($srv['HTTP_AUTHORIZATION']))
    {
        $token_str = $srv['HTTP_AUTHORIZATION'];
        $token_raw = str_replace('token=', '', $token_str);
        
        return $token_raw;
    }
    
    return null;
}

/**
 * Get authentication token from HTTP headers
 * 
 * @param Configs $configs config object
 * @return array
 */
function getToken($configs): array
{
    $raw_token = getRawToken();
    if(!is_null($raw_token))
    {
        $token_dec = NblSEPHP::decryptData($raw_token, $configs->crypt_key,
                            $configs->crypt_iv);
        
        $token = json_decode($token_dec, TRUE);
        
        if(empty($token_dec))
        {
            return array();
        }
        
        if(is_null($token))
        {
            return array();
        }
        
        if(empty($token))
        {
            return array();
        }
        
        if(!isset($token['data']))
        {
            return array();
        }
        
        if(!isset($token['expire']))
        {
            return array();
        }

        if(isset($token['isAPI']) && $token['isAPI']) 
        {
            // API token doesn't expire
            return $token;
        }
        
        $dt = new DateTime();
        
        if($dt->getTimestamp() > $token['expire'])
        {
            $token['isValid'] = false;
        }
        
        return $token;
    }
    
    return array();
}

/**
 * Checks if token is valid
 * 
 * @param array $token
 * @return bool
 */
function isValidToken($token): bool
{
    if(empty($token))
    {
        return false;
    }
    
    return $token['isValid'];
}

/**
 * Generates token
 * 
 * @param object $data token data
 * @param object $configs config object
 * @param bool $is_api flag to indicate if token is meant for direct user or API use
 * @return string serialized token
 */
function generateToken($data, $configs, $is_api = false): string
{
    if(!$is_api)
    {
        // token gerado por autenticação direta de usuário. Não de API
        $dt = new DateTime();
        $now = $dt->getTimestamp();

        $dt->add(new DateInterval('P1D'));
        $expire = $dt->getTimestamp();

        $token = array(
            'data' => $data,
            'created' => $now,
            'expire' => $expire,
            'isValid' => true,
            'isAPI' => false,
            'random' => NblPHPUtil::makeRandomAlphaNumericCode(20)
        );
    }
    else
    {
        // token gerado para api
        $dt = new DateTime();
        $now = $dt->getTimestamp();
        
        $token = array(
            'data' => $data,
            'created' => $now,
            'expire' => '',
            'isValid' => true,
            'isAPI' => true,
            'random' => NblPHPUtil::makeRandomAlphaNumericCode(20)
        );
    }
    
    $token_enc = NblSEPHP::cryptData(json_encode($token), $configs->crypt_key,
                                $configs->crypt_iv);
    
    return $token_enc;
}

/**
 * Generate token data
 * 
 * @param object $data data to be tokenized
 * @return array token data
 */
function generateTokenData($data): array
{
    $dt = new DateTime();
    $now = $dt->getTimestamp();

    $token = array(
        'data' => $data,
        'created' => $now,
        'expire' => '',
        'isValid' => true,
        'isAPI' => true,
        'random' => NblPHPUtil::makeRandomAlphaNumericCode(20)
    );
    
    return $token;
}

