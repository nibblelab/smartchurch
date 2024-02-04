<?php

/**
 * Classe para as interações do Profissoes com o banco de dados. 
 * 
 */
class ProfissoesDAO extends BaseDAO
{
    protected $dto_object = "Profissoes";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new ProfissoesDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 

