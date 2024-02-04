<?php

/**
 * Classe para as interações do Instancias com o banco de dados. 
 * 
 */
class InstanciasDAO extends BaseDAO
{
    protected $dto_object = "Instancias";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new InstanciasDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 

