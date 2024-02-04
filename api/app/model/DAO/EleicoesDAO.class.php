<?php

/**
 * Classe para as interações do Eleicoes com o banco de dados. 
 * 
 */
class EleicoesDAO extends BaseDAO
{
    protected $dto_object = "Eleicoes";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new EleicoesDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 

