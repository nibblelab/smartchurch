<?php

/**
 * Classe para as interações do ComissoesExecutivas com o banco de dados. 
 * 
 */
class ComissoesExecutivasDAO extends BaseDAO
{
    protected $dto_object = "ComissoesExecutivas";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new ComissoesExecutivasDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 
?>
