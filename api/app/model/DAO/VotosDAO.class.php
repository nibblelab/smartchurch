<?php

/**
 * Classe para as interações do Votos com o banco de dados. 
 * 
 */
class VotosDAO extends BaseDAO
{
    protected $dto_object = "Votos";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new VotosDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 
