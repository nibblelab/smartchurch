<?php

/**
 * Classe para as interações do HistoricoMembresia com o banco de dados. 
 * 
 */
class HistoricoMembresiaDAO extends BaseDAO
{
    protected $dto_object = "HistoricoMembresia";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new HistoricoMembresiaDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    /* específicos */
    
} 

