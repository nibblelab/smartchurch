<?php

/**
 * Classe para as interações do Categorias com o banco de dados. 
 * 
 */
class CategoriasDAO extends BaseDAO
{
    protected $dto_object = "Categorias";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new CategoriasDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 
