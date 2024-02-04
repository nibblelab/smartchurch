<?php

/**
 * Classe para as interações do Sysconfigs com o banco de dados. 
 * 
 */
class SysconfigsDAO extends BaseDAO
{
    protected $dto_object = "Sysconfigs";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new SysconfigsDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    /* específicos */
    
} 

