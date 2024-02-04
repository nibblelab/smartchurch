<?php

/**
 * Classe para as interações do Cargo com o banco de dados. 
 * 
 */
class CargoDAO extends BaseDAO
{
    protected $dto_object = "Cargo";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new CargoDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 
?>
