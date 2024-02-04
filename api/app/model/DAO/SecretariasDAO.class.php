<?php

/**
 * Classe para as interações do Secretarias com o banco de dados. 
 * 
 */
class SecretariasDAO extends BaseDAO
{
    protected $dto_object = "Secretarias";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new SecretariasDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 

