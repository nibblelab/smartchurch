<?php

/**
 * Classe para as interações do AprovacoesAta com o banco de dados. 
 * 
 */
class AprovacoesAtaDAO extends BaseDAO
{
    protected $dto_object = "AprovacoesAta";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new AprovacoesAtaDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 
?>
