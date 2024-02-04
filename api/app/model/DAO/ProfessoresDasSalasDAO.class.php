<?php

/**
 * Classe para as interações do ProfessoresDasSalas com o banco de dados. 
 * 
 */
class ProfessoresDasSalasDAO extends BaseDAO
{
    protected $dto_object = "ProfessoresDasSalas";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new ProfessoresDasSalasDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 

