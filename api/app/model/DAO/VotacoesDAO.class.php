<?php

/**
 * Classe para as interações do Votacoes com o banco de dados. 
 * 
 */
class VotacoesDAO extends BaseDAO
{
    protected $dto_object = "Votacoes";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new VotacoesDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 
