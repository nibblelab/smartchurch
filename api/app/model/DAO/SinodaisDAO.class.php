<?php

/**
 * Classe para as interações do Sinodais com o banco de dados. 
 * 
 */
class SinodaisDAO extends BaseDAO
{
    protected $dto_object = "Sinodais";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new SinodaisDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 
