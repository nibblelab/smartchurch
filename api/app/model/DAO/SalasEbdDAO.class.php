<?php

/**
 * Classe para as interações do SalasEbd com o banco de dados. 
 * 
 */
class SalasEbdDAO extends BaseDAO
{
    protected $dto_object = "SalasEbd";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new SalasEbdDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 

