<?php

/**
 * Classe para as interações do ConteudosEBD com o banco de dados. 
 * 
 */
class ConteudosEBDDAO extends BaseDAO
{
    protected $dto_object = "ConteudosEBD";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new ConteudosEBDDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 

