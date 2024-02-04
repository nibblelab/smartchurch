<?php

/**
 * Classe para as interações do Igrejas com o banco de dados. 
 * 
 */
class IgrejasDAO extends BaseDAO
{
    protected $dto_object = "Igrejas";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new IgrejasDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 

