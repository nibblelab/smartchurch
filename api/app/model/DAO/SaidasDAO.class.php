<?php

/**
 * Classe para as interações do Saidas com o banco de dados. 
 * 
 */
class SaidasDAO extends BaseDAO
{
    protected $dto_object = "Saidas";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new SaidasDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 

