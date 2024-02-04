<?php

/**
 * Classe de dados (DTO) do Inscricoes
 *
 *
 */
class InscricoesDTO extends BaseDTO
{
    /* dados propriamento ditos */
    public $id;
    public $pessoa;
    public $evento;
    public $credencial_digital;
    public $motivo_recusa;
    public $igreja;
    public $presbiterio;
    public $sinodo;
    public $sociedade;
    public $federacao;
    public $sinodal;
    public $qrcode;
    public $delegado;
    public $cargo_ref;
    public $cargo;
    public $credencial;
    public $stat;
    public $forma_pagto;
    public $stat_pagto;
    public $valor_pago;
    public $gateway_code;
    public $data_pagto;
    public $time_cad;
    public $last_mod;
    public $last_amod;
}

/**
 * Classe Façade que provê interface entre o DAO e o controller para o Inscricoes
 *
 *
 */
class InscricoesADO extends BaseADO
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
        $this->dao = new InscricoesDAO();
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
