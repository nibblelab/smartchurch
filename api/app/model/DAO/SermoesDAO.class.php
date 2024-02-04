<?php

/**
 * Classe para as interações do Sermoes com o banco de dados. 
 * 
 */
class SermoesDAO extends BaseDAO
{
    protected $dto_object = "Sermoes";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new SermoesDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 

