<?php

/**
 * Classe para as interações do Estudos com o banco de dados. 
 * 
 */
class EstudosDAO extends BaseDAO
{
    protected $dto_object = "Estudos";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new EstudosDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 
