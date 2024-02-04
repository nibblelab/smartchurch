<?php

/**
 * Classe para as interações do Familias com o banco de dados. 
 * 
 */
class FamiliasDAO extends BaseDAO
{
    protected $dto_object = "Familias";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new FamiliasDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 

