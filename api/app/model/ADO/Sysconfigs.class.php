<?php

/**
 * Classe de dados (DTO) do Sysconfigs
 *
 *
 */
class SysconfigsDTO extends BaseDTO
{
    /* dados propriamento ditos */
    public $id;
    public $perfil_membro;
    public $perfil_cliente;
    public $perfil_diretoria_sociedade;
    public $perfil_federacao;
    public $perfil_sinodal;
    public $perfil_superintendente;
    public $perfil_secretario;
    public $perfil_ministerio;
    public $perfil_pastor;
    public $perfil_evangelista;
    public $perfil_presbitero;
    public $perfil_diacono;
    public $perfil_referencia;
    public $perfil_professor;
    public $tag_evento;
    public $tag_eleicao;
}

/**
 * Classe Façade que provê interface entre o DAO e o controller para o Sysconfigs
 *
 *
 */
class SysconfigsADO extends BaseADO
{ 
    /**
     * ponteiro para o elemento atual da lista de DTO
     */
    protected $current;
    /**
     * ponteiro para o primeiro elemento da lista de DTO
     */
    protected $dto;
    /**
     * referência para o objeto DAO
     */
    protected $dao;
    /**
     * tamanho da lista de DTO
     */
    protected $size;
    /**
     * array com os erros do DAO
     */
    protected $errs;

    public function __construct()
    {
        $this->dao = new SysconfigsDAO();
        $this->size = 0;
        $this->errs = array();
    }

    /**
     * Gera os resultados da lista em um array de strings
     * 
     * @return array
     */
    public function debug(): array
    {
        $dto = $this->dao->getDTO();
        return parent::getDebugAsString($dto);
    }

    /**
     * Gera os resultados da lista em um array de arrays
     * 
     * @return array
     */
    public function getDataAsArray(): array
    {
        $dto = $this->dao->getDTO();
        return parent::getDebugAsArray($dto);
    }
    
    /**
     * Gera o primeiro resultado da lista como um objeto
     * 
     * @return object
     */
    public function getDTODataObject(): object
    {
        $dto = $this->dao->getDTO();
        return parent::getDTOData($dto);
    }
    
    /**
     * Gera os resultados da lista em um array de objetos
     * 
     * @return array
     */
    public function getDTOAsArray(): array
    {
        $dto = $this->dao->getDTO();
        return parent::getDTODataAsArray($dto);
    }

    /* específicos */
    
} 

?>
