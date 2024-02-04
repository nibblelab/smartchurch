<?php

/**
 * Classe para as interações do Oficiais com o banco de dados. 
 * 
 */
class OficiaisDAO extends BaseDAO
{
    protected $dto_object = "Oficiais";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new OficiaisDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 

