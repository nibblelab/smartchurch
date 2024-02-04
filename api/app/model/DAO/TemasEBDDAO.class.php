<?php

/**
 * Classe para as interações do TemasEBD com o banco de dados. 
 * 
 */
class TemasEBDDAO extends BaseDAO
{
    protected $dto_object = "TemasEBD";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new TemasEBDDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 

