<?php

/**
 * Classe para as interações do PequenosGrupos com o banco de dados. 
 * 
 */
class PequenosGruposDAO extends BaseDAO
{
    protected $dto_object = "PequenosGrupos";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new PequenosGruposDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 

