<?php

/**
 * Classe de dados (DTO) do Socios
 *
 *
 */
class SociosDTO extends BaseDTO
{
    /* dados propriamento ditos */
    public $id;
    public $sociedade;
    public $pessoa;
    public $data_admissao;
    public $data_demissao;
    public $stat;
    public $admin;
    public $diretoria;
    public $cooperador;
    public $time_cad;
    public $last_mod;
    public $last_amod;
}

/**
 * Classe Façade que provê interface entre o DAO e o controller para o Socios
 *
 *
 */
class SociosADO extends BaseADO
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
        $this->dao = new SociosDAO();
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
     * Conte a quantidade de sócios de acordo com o ano de nascimento
     * 
     * @param string $sociedade id da sociedade
     * @return array
     */
    public function countByAno($sociedade): array
    {
        $count = array();
        $this->dao->countByAno($count, $sociedade);
        return $count;
    }
    
    /**
     * Conte a quantidade de sócios das sociedades de acordo com o ano de nascimento
     * 
     * @param string $sociedades ids das sociedades
     * @return array
     */
    public function countByAnoForSociedades($sociedades): array
    {
        $count = array();
        $this->dao->countByAnoForSociedades($count, $sociedades);
        return $count;
    }
    
    /**
     * Conte a quantidade de sócios de acordo com o ano e mês de nascimento
     * 
     * @param string $sociedade id da sociedade
     * @return array
     */
    public function countByMesAndAno($sociedade): array
    {
        $count = array();
        $this->dao->countByMesAndAno($count, $sociedade);
        return $count;
    }
    
    /**
     * Conte a quantidade de sócios das sociedades de acordo com o ano e mês de nascimento
     * 
     * @param string $sociedades ids das sociedades
     * @return array
     */
    public function countByMesAndAnoForSociedades($sociedades): array
    {
        $count = array();
        $this->dao->countByMesAndAnoForSociedades($count, $sociedades);
        return $count;
    }
    
    /**
     * Conte a quantidade de sócios de acordo com o sexo
     * 
     * @param string $sociedade id da sociedade
     * @return array
     */
    public function countBySexo($sociedade): array
    {
        $count = array();
        $this->dao->countBySexo($count, $sociedade);
        return $count;
    }
    
    /**
     * Conte a quantidade de sócios das sociedades de acordo com o sexo
     * 
     * @param string $sociedades ids das sociedades
     * @return array
     */
    public function countBySexoForSociedades($sociedades): array
    {
        $count = array();
        $this->dao->countBySexoForSociedades($count, $sociedades);
        return $count;
    }
    
    /**
     * Conte a quantidade de sócios de acordo com o estado civil
     * 
     * @param string $sociedade id da sociedade
     * @return array
     */
    public function countByEstadoCivil($sociedade): array
    {
        $count = array();
        $this->dao->countByEstadoCivil($count, $sociedade);
        return $count;
    }
    
    /**
     * Conte a quantidade de sócios das sociedades de acordo com o estado civil
     * 
     * @param string $sociedades ids das sociedades
     * @return array
     */
    public function countByEstadoCivilForSociedades($sociedades): array
    {
        $count = array();
        $this->dao->countByEstadoCivilForSociedades($count, $sociedades);
        return $count;
    }
    
    /**
     * Conte a quantidade de sócios de acordo com a escolaridade
     * 
     * @param string $sociedade id da sociedade
     * @return array
     */
    public function countByEscolaridade($sociedade): array
    {
        $count = array();
        $this->dao->countByEscolaridade($count, $sociedade);
        return $count;
    }
    
    /**
     * Conte a quantidade de sócios das sociedades de acordo com a escolaridade
     * 
     * @param string $sociedades ids das sociedades
     * @return array
     */
    public function countByEscolaridadeForSociedades($sociedades): array
    {
        $count = array();
        $this->dao->countByEscolaridadeForSociedades($count, $sociedades);
        return $count;
    }
    
    /**
     * Conte a quantidade de sócios de acordo com a profissão de fé
     * 
     * @param string $sociedade id da sociedade
     * @return array
     */
    public function countByProfissaoFe($sociedade): array
    {
        $count = array();
        $this->dao->countByProfissaoFe($count, $sociedade);
        return $count;
    }
    
    /**
     * Conte a quantidade de sócios das sociedades de acordo com a profissão de fé
     * 
     * @param string $sociedades ids das sociedades
     * @return array
     */
    public function countByProfissaoFeForSociedades($sociedades): array
    {
        $count = array();
        $this->dao->countByProfissaoFeForSociedades($count, $sociedades);
        return $count;
    }
    
    /**
     * Conte a quantidade de sócios por terem ou não filhos
     * 
     * @param string $sociedade id da sociedade
     * @return array
     */
    public function countByTemFilhos($sociedade): array
    {
        $count = array();
        $this->dao->countByTemFilhos($count, $sociedade);
        return $count;
    }
    
    /**
     * Conte a quantidade de sócios das sociedades por terem ou não filhos
     * 
     * @param string $sociedades ids das sociedades
     * @return array
     */
    public function countByTemFilhosForSociedades($sociedades): array
    {
        $count = array();
        $this->dao->countByTemFilhosForSociedades($count, $sociedades);
        return $count;
    }
    
    /**
     * Conte a quantidade de sócios por terem ou não filhos, conforme o sexo dos sócios
     * 
     * @param string $sociedade id da sociedade
     * @return array
     */
    public function countByTemFilhosAndSexo($sociedade): array
    {
        $count = array();
        $this->dao->countByTemFilhosAndSexo($count, $sociedade);
        return $count;
    }
    
    /**
     * Conte a quantidade de sócios das sociedades por terem ou não filhos, conforme o sexo dos sócios
     * 
     * @param string $sociedades ids das sociedades
     * @return array
     */
    public function countByTemFilhosAndSexoForSociedades($sociedades): array
    {
        $count = array();
        $this->dao->countByTemFilhosAndSexoForSociedades($count, $sociedades);
        return $count;
    }
    
    /**
     * Conte a quantidade de sócios por necessidades especiais
     * 
     * @param string $sociedade id da sociedade
     * @return array
     */
    public function countByNecessidade($sociedade): array
    {
        $count = array();
        $this->dao->countByNecessidade($count, $sociedade);
        return $count;
    }
    
    /**
     * Conte a quantidade de sócios das sociedades por necessidades especiais
     * 
     * @param string $sociedades ids das sociedades
     * @return array
     */
    public function countByNecessidadeForSociedades($sociedades): array
    {
        $count = array();
        $this->dao->countByNecessidadeForSociedades($count, $sociedades);
        return $count;
    }
    
    /**
     * Conte a quantidade de sócios por doação
     * 
     * @param string $sociedade id da sociedade
     * @return array
     */
    public function countByDoacao($sociedade): array
    {
        $count = array();
        $this->dao->countByDoacao($count, $sociedade);
        return $count;
    }
    
    /**
     * Conte a quantidade de sócios das sociedades por doação
     * 
     * @param string $sociedades ids das sociedades
     * @return array
     */
    public function countByDoacaoForSociedades($sociedades): array
    {
        $count = array();
        $this->dao->countByDoacaoForSociedades($count, $sociedades);
        return $count;
    }
    
    /**
     * Conte a quantidade de sócios por arrolamento
     * 
     * @param string $sociedade id da sociedade
     * @return array
     */
    public function countByArrolamento($sociedade): array
    {
        $count = array();
        $this->dao->countByArrolamento($count, $sociedade);
        return $count;
    }
    
    /**
     * Conte a quantidade de sócios das sociedades por arrolamento
     * 
     * @param string $sociedades ids das sociedades
     * @return array
     */
    public function countByArrolamentoForSociedades($sociedades): array
    {
        $count = array();
        $this->dao->countByArrolamentoForSociedades($count, $sociedades);
        return $count;
    }
    
} 

