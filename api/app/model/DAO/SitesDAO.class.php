<?php

/**
 * Classe para as interações do Sites com o banco de dados. 
 * 
 */
class SitesDAO extends BaseDAO
{
    protected $dto_object = "Sites";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new SitesDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 

