<?php

/**
 * Classe para as interações do PedidosOracao com o banco de dados. 
 * 
 */
class PedidosOracaoDAO extends BaseDAO
{
    protected $dto_object = "PedidosOracao";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new PedidosOracaoDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 

