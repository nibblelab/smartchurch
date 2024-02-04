<?php

namespace SmartChurch\Config;

use SmartChurch\Config\Config;
use SmartChurch\Request\RequestConfig;

/**
 * Description of SmartChurchConfig
 *
 * @author johnatas
 */
class SmartChurchConfig {
    
    public static $config;
    private static $debug;
    
    /**
     * 
     * @param string $token token de integração
     * @param boolean $production se é pra usar o servidor de produção ou testes. Default = true
     * @param boolean $debug modo debug. Default = false
     */
    public static function load($token, $production = true, $debug = false) {
        self::$config = new RequestConfig();
        self::$config->setToken($token);
        if($production)
        {
            self::$config->setUrl(Config::PRODUCTION_URL);
        }
        else
        {
            self::$config->setUrl(Config::SANDBOX_URL);
        }
        self::$debug = $debug;
    }
    
    public static function isDebugActive(): bool {
        return self::$debug;
    }
    
}
