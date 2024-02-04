<?php

/**
 * Classe para as interações do Subcategorias com o banco de dados. 
 * 
 */
class SubcategoriasDAO extends BaseDAO
{
    protected $dto_object = "Subcategorias";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new SubcategoriasDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 
