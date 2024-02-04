<?php

/**
 * Classe para as interações do AtualizacoesSumarios com o banco de dados. 
 * 
 */
class AtualizacoesSumariosDAO extends BaseDAO
{
    protected $dto_object = "AtualizacoesSumarios";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new AtualizacoesSumariosDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 
