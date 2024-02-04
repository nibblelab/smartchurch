<?php

/**
 * Classe para as interações do Participantes com o banco de dados. 
 * 
 */
class ParticipantesDAO extends BaseDAO
{
    protected $dto_object = "Participantes";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new ParticipantesDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 

