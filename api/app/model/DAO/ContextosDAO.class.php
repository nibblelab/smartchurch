<?php

/**
 * Classe para as interações do Contextos com o banco de dados. 
 * 
 */
class ContextosDAO extends BaseDAO
{
    protected $dto_object = "Contextos";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new ContextosDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 

