<?php

/**
 * Classe para as interações do Eventos com o banco de dados. 
 * 
 */
class EventosDAO extends BaseDAO
{
    protected $dto_object = "Eventos";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new EventosDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 

