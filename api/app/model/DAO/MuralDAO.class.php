<?php

/**
 * Classe para as interações do Mural com o banco de dados. 
 * 
 */
class MuralDAO extends BaseDAO
{
    protected $dto_object = "Mural";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new MuralDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 
