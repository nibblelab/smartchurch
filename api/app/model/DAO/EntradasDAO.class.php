<?php

/**
 * Classe para as interações do Entradas com o banco de dados. 
 * 
 */
class EntradasDAO extends BaseDAO
{
    protected $dto_object = "Entradas";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new EntradasDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 

