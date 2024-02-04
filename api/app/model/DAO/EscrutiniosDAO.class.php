<?php

/**
 * Classe para as interações do Escrutinios com o banco de dados. 
 * 
 */
class EscrutiniosDAO extends BaseDAO
{
    protected $dto_object = "Escrutinios";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new EscrutiniosDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 

