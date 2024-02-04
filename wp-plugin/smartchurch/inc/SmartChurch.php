<?php
namespace SmartChurch;

include_once dirname(__FILE__) . '/loader.php';

use SmartChurch\Config\SmartChurchConfig;
use SmartChurch\Context\SmartChurchContext;
use SmartChurch\Log\SmartChurchLog;

/**
 * Integração com a API SmartChurch
 */
class SmartChurch
{
    
    /**
     * 
     * @param string $token token de integração
     * @param \SmartChurch\Context\Context $context contexto da API
     * @param boolean $production se é pra usar o servidor de produção ou testes. Default = true
     * @param boolean $debug modo debug. Default = false
     */
    public function __construct($token, $context, $production = true, $debug = false)
    {
        SmartChurchConfig::load($token, $production, $debug);
        if(SmartChurchConfig::isDebugActive()) {
            SmartChurchLog::load(wp_upload_dir() . '/smartchurch.log');
        }
        SmartChurchContext::create($context);
    }
    
    /**
     * Obtem o ponteiro para o contexto ativo
     * 
     * @return \SmartChurch\Context\Context
     */
    public function getContext(): \SmartChurch\Context\Context
    {
        return SmartChurchContext::$context;
    }
}

