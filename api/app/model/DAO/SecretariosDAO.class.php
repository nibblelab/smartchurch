<?php

/**
 * Classe para as interações do Secretarios com o banco de dados. 
 * 
 */
class SecretariosDAO extends BaseDAO
{
    protected $dto_object = "Secretarios";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new SecretariosDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 

