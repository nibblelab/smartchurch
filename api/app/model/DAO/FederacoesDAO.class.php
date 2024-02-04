<?php

/**
 * Classe para as interações do Federacoes com o banco de dados. 
 * 
 */
class FederacoesDAO extends BaseDAO
{
    protected $dto_object = "Federacoes";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new FederacoesDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 

