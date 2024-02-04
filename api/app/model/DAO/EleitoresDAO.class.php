<?php

/**
 * Classe para as interações do Eleitores com o banco de dados. 
 * 
 */
class EleitoresDAO extends BaseDAO
{
    protected $dto_object = "Eleitores";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new EleitoresDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 

