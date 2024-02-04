<?php

/**
 * Classe de dados (DTO) do PessoasDasComissoes
 *
 *
 */
class PessoasDasComissoesDTO extends BaseDTO
{
    /* dados propriamento ditos */
    public $pessoa;
    public $comissao;
    public $relator;
}

/**
 * Classe Façade que provê interface entre o DAO e o controller para o PessoasDasComissoes
 *
 *
 */
class PessoasDasComissoesADO extends BaseADO
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
        $this->dao = new PessoasDasComissoesDAO();
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
