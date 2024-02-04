<?php

/**
 * Classe para as interações do NecessidadesDasPessoas com o banco de dados. 
 * 
 */
class NecessidadesDasPessoasDAO extends BaseDAO
{
    protected $dto_object = "NecessidadesDasPessoas";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new NecessidadesDasPessoasDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 

