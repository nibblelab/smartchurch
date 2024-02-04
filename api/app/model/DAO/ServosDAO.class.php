<?php

/**
 * Classe para as interações do Servos com o banco de dados. 
 * 
 */
class ServosDAO extends BaseDAO
{
    protected $dto_object = "Servos";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new ServosDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 
