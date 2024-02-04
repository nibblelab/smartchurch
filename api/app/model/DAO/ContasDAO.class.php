<?php

/**
 * Classe para as interações do Contas com o banco de dados. 
 * 
 */
class ContasDAO extends BaseDAO
{
    protected $dto_object = "Contas";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new ContasDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 
