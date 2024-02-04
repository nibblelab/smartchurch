<?php

/**
 * Classe para as interações do NecessidadesEspeciais com o banco de dados. 
 * 
 */
class NecessidadesEspeciaisDAO extends BaseDAO
{
    protected $dto_object = "NecessidadesEspeciais";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new NecessidadesEspeciaisDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 

