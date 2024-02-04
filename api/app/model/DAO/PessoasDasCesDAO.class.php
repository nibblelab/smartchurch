<?php

/**
 * Classe para as interações do PessoasDasCes com o banco de dados. 
 * 
 */
class PessoasDasCesDAO extends BaseDAO
{
    protected $dto_object = "PessoasDasCes";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new PessoasDasCesDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 

