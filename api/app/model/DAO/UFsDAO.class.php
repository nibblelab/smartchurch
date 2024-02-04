<?php

/**
 * Classe para as interações do UFs com o banco de dados. 
 * 
 */
class UFsDAO extends BaseDAO
{
    protected $dto_object = "UFs";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new UFsDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 
