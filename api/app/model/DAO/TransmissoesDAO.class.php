<?php

/**
 * Classe para as interações do Transmissoes com o banco de dados. 
 * 
 */
class TransmissoesDAO extends BaseDAO
{
    protected $dto_object = "Transmissoes";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new TransmissoesDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 

