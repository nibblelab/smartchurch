<?php

/**
 * Classe para as interações do Agendas com o banco de dados. 
 * 
 */
class AgendasDAO extends BaseDAO
{
    protected $dto_object = "Agendas";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new AgendasDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 
?>
