<?php

/**
 * Classe para as interações do Votantes com o banco de dados. 
 * 
 */
class VotantesDAO extends BaseDAO
{
    protected $dto_object = "Votantes";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new VotantesDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 
