<?php

/**
 * Classe para as interações do AlunosDasSalas com o banco de dados. 
 * 
 */
class AlunosDasSalasDAO extends BaseDAO
{
    protected $dto_object = "AlunosDasSalas";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new AlunosDasSalasDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 
?>
