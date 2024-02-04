<?php

/**
 * Classe para as interações do MotivosRecusa com o banco de dados. 
 * 
 */
class MotivosRecusaDAO extends BaseDAO
{
    protected $dto_object = "MotivosRecusa";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new MotivosRecusaDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 

