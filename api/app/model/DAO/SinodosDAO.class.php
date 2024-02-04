<?php

/**
 * Classe para as interações do Sinodos com o banco de dados. 
 * 
 */
class SinodosDAO extends BaseDAO
{
    protected $dto_object = "Sinodos";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new SinodosDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 

