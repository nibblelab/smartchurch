<?php

/**
 * Classe para as interações do Bookmarks com o banco de dados. 
 * 
 */
class BookmarksDAO extends BaseDAO
{
    protected $dto_object = "Bookmarks";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new BookmarksDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 
