<?php

/**
 * Classe para as interações do TagsDaEntrada com o banco de dados. 
 * 
 */
class TagsDaEntradaDAO extends BaseDAO
{
    protected $dto_object = "TagsDaEntrada";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new TagsDaEntradaDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 
