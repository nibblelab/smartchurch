<?php

/**
 * Classe para as interações do Diretorias com o banco de dados. 
 * 
 */
class DiretoriasDAO extends BaseDAO
{
    protected $dto_object = "Diretorias";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new DiretoriasDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 

