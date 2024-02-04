<?php

/**
 * Classe de dados (DTO) do Credenciais
 *
 *
 */
class CredenciaisDTO extends BaseDTO
{
    /* dados propriamento ditos */
    public $id;
    public $id_responsavel;
    public $nome_responsavel;
    public $email_responsavel;
    public $telefone_responsavel;
    public $assinatura_inscrito;
    public $assinatura_responsavel;
    public $assinatura_instancia;
    public $assinatura_plataforma;
    public $stat;
    public $time_cad;
    public $last_mod;
    public $last_amod;
}

/**
 * Classe Façade que provê interface entre o DAO e o controller para o Credenciais
 *
 *
 */
class CredenciaisADO extends BaseADO
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
        $this->dao = new CredenciaisDAO();
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

    /**
     * Mapeie as credenciais para o responsavel em um evento
     * 
     * @param string $responsavel id do responsável
     * @param string $evento id do evento
     * @return array
     */
    public function mapAllCredentialsForResponsavelInEvento($responsavel, $evento): array
    {
        $map = array();
        $this->dao->mapAllCredentialsForResponsavelInEvento($map, $responsavel, $evento);
        return $map;
    }
} 
