<?php

/**
 * Classe para as interações do Doadores com o banco de dados. 
 * 
 */
class DoadoresDAO extends BaseDAO
{
    protected $dto_object = "Doadores";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new DoadoresDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 

