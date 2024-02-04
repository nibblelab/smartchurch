<?php

/**
 * Classe para as interações do Permissoes com o banco de dados. 
 * 
 */
class PermissoesDAO extends BaseDAO
{
    protected $dto_object = "Permissoes";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new PermissoesDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 

