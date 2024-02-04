<?php

/**
 * Classe para as interações do Checkins com o banco de dados. 
 * 
 */
class CheckinsDAO extends BaseDAO
{
    protected $dto_object = "Checkins";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new CheckinsDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 
?>
