<?php

/**
 * Classe para as interações do Candidatos com o banco de dados. 
 * 
 */
class CandidatosDAO extends BaseDAO
{
    protected $dto_object = "Candidatos";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new CandidatosDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 
?>
