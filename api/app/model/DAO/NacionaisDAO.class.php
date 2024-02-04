<?php

/**
 * Classe para as interações do Nacionais com o banco de dados. 
 * 
 */
class NacionaisDAO extends BaseDAO
{
    protected $dto_object = "Nacionais";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new NacionaisDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 

