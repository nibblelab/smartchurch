<?php

/**
 * Classe para as interações do Enderecos com o banco de dados. 
 * 
 */
class EnderecosDAO extends BaseDAO
{
    protected $dto_object = "Enderecos";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new EnderecosDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 

