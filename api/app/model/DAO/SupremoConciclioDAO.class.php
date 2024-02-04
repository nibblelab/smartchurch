<?php

/**
 * Classe para as interações do SupremoConciclio com o banco de dados. 
 * 
 */
class SupremoConciclioDAO extends BaseDAO
{
    protected $dto_object = "SupremoConciclio";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new SupremoConciclioDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 

