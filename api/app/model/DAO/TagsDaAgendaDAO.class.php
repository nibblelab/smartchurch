<?php

/**
 * Classe para as interações do TagsDaAgenda com o banco de dados. 
 * 
 */
class TagsDaAgendaDAO extends BaseDAO
{
    protected $dto_object = "TagsDaAgenda";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new TagsDaAgendaDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 

