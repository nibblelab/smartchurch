<?php

/**
 * Classe para as interações do PermissoesDoPerfil com o banco de dados. 
 * 
 */
class PermissoesDoPerfilDAO extends BaseDAO
{
    protected $dto_object = "PermissoesDoPerfil";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new PermissoesDoPerfilDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 

