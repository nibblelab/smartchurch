<?php

/**
 * Classe de dados (DTO) do Igrejas
 *
 *
 */
class IgrejasDTO extends BaseDTO
{
    /* dados propriamento ditos */
    public $id;
    public $sinodo;
    public $presbiterio;
    public $igreja;
    public $nome;
    public $fundacao;
    public $organizada;
    public $telefone;
    public $email;
    public $stat;
    public $endereco;
    public $numero;
    public $complemento;
    public $bairro;
    public $cidade;
    public $uf;
    public $cep;
    public $site;
    public $facebook;
    public $instagram;
    public $youtube;
    public $vimeo;
    public $time_cad;
    public $last_mod;
    public $last_amod;
}

/**
 * Classe Façade que provê interface entre o DAO e o controller para o Igrejas
 *
 *
 */
class IgrejasADO extends BaseADO
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
        $this->dao = new IgrejasDAO();
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
