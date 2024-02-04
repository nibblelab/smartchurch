<?php

/**
 * Classe para as interações do FreqSumarios com o banco de dados. 
 * 
 */
class FreqSumariosDAO extends BaseDAO
{
    protected $dto_object = "FreqSumarios";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new FreqSumariosDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 
