<?php

/**
 * Classe para as interações do VotosCtrl com o banco de dados. 
 * 
 */
class VotosCtrlDAO extends BaseDAO
{
    protected $dto_object = "VotosCtrl";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new VotosCtrlDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 
