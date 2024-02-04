<?php

/**
 * Classe para as interações do TagsDaSaida com o banco de dados. 
 * 
 */
class TagsDaSaidaDAO extends BaseDAO
{
    protected $dto_object = "TagsDaSaida";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new TagsDaSaidaDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 
