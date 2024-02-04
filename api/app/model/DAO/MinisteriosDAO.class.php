<?php

/**
 * Classe para as interações do Ministerios com o banco de dados. 
 * 
 */
class MinisteriosDAO extends BaseDAO
{
    protected $dto_object = "Ministerios";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new MinisteriosDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 

