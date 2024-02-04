<?php

/**
 * Classe para as interações do SeriesSermoes com o banco de dados. 
 * 
 */
class SeriesSermoesDAO extends BaseDAO
{
    protected $dto_object = "SeriesSermoes";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new SeriesSermoesDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 

