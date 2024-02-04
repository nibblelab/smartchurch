<?php

/**
 * Classe para as interações do Sociedades com o banco de dados. 
 * 
 */
class SociedadesDAO extends BaseDAO
{
    protected $dto_object = "Sociedades";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new SociedadesDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 

