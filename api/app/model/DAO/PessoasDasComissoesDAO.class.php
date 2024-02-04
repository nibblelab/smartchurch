<?php

/**
 * Classe para as interações do PessoasDasComissoes com o banco de dados. 
 * 
 */
class PessoasDasComissoesDAO extends BaseDAO
{
    protected $dto_object = "PessoasDasComissoes";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new PessoasDasComissoesDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 

