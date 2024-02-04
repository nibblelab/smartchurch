<?php

namespace SmartChurch\Config;

/**
 * Configurações da API
 *
 */
abstract class Config
{
    /**
     * URL de produção
     */
    const PRODUCTION_URL = 'https://www.smartchurch.software/api/';
    /**
     * URL de testes 
     */
    const SANDBOX_URL = 'http://smartchurch.local/api/';
    const RESOURCE_PATH = 'rc/';
    const SERMAO_PATH = 'rc/sermoes/';
}
