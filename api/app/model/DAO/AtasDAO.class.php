<?php

/**
 * Classe para as interações do Atas com o banco de dados. 
 * 
 */
class AtasDAO extends BaseDAO
{
    protected $dto_object = "Atas";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new AtasDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 
?>
