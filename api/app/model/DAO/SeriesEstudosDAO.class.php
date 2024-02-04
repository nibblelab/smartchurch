<?php

/**
 * Classe para as interações do SeriesEstudos com o banco de dados. 
 * 
 */
class SeriesEstudosDAO extends BaseDAO
{
    protected $dto_object = "SeriesEstudos";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new SeriesEstudosDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 

