<?php

/**
 * Classe para as interações do ConfigsPagto com o banco de dados. 
 * 
 */
class ConfigsPagtoDAO extends BaseDAO
{
    protected $dto_object = "ConfigsPagto";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new ConfigsPagtoDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 

