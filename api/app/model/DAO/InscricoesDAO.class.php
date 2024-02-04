<?php

/**
 * Classe para as interações do Inscricoes com o banco de dados. 
 * 
 */
class InscricoesDAO extends BaseDAO
{
    protected $dto_object = "Inscricoes";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new InscricoesDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 

