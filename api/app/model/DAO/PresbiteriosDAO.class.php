<?php

/**
 * Classe para as interações do Presbiterios com o banco de dados. 
 * 
 */
class PresbiteriosDAO extends BaseDAO
{
    protected $dto_object = "Presbiterios";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new PresbiteriosDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 

