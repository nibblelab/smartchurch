<?php

/**
 * Classe para as interações do Doacoes com o banco de dados. 
 * 
 */
class DoacoesDAO extends BaseDAO
{
    protected $dto_object = "Doacoes";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new DoacoesDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 

