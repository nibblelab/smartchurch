<?php

/**
 * Classe para as interações do Cidades com o banco de dados. 
 * 
 */
class CidadesDAO extends BaseDAO
{
    protected $dto_object = "Cidades";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new CidadesDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 

