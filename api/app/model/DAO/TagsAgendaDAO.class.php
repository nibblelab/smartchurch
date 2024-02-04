<?php

/**
 * Classe para as interações do TagsAgenda com o banco de dados. 
 * 
 */
class TagsAgendaDAO extends BaseDAO
{
    protected $dto_object = "TagsAgenda";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new TagsAgendaDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 

