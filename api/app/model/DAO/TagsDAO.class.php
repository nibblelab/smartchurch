<?php

/**
 * Classe para as interações do Tags com o banco de dados. 
 * 
 */
class TagsDAO extends BaseDAO
{
    protected $dto_object = "Tags";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new TagsDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 
