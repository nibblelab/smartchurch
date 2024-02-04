<?php

/**
 * Classe para as interações do Comissoes com o banco de dados. 
 * 
 */
class ComissoesDAO extends BaseDAO
{
    protected $dto_object = "Comissoes";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new ComissoesDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
} 
?>
