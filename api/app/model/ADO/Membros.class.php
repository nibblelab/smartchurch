<?php

/**
 * Classe de dados (DTO) do Membros
 *
 *
 */
class MembrosDTO extends BaseDTO
{
    /* dados propriamento ditos */
    public $id;
    public $pessoa;
    public $igreja;
    public $codigo;
    public $comungante;
    public $especial;
    public $arrolado;
    public $data_admissao;
    public $data_demissao;
    public $stat;
    public $time_cad;
    public $last_mod;
    public $last_amod;
}

/**
 * Classe Façade que provê interface entre o DAO e o controller para o Membros
 *
 *
 */
class MembrosADO extends BaseADO
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
        $this->dao = new MembrosDAO();
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
     * Conte a quantidade de membros de acordo com o ano de nascimento
     * 
     * @param string $igreja id da igreja
     * @return array
     */
    public function countByAno($igreja): array
    {
        $count = array();
        $this->dao->countByAno($count, $igreja);
        return $count;
    }
    
    /**
     * Conte a quantidade de membros das igrejas de acordo com o ano de nascimento
     * 
     * @param string $igrejas ids das igrejas
     * @return array
     */
    public function countByAnoForIgrejas($igrejas): array
    {
        $count = array();
        $this->dao->countByAnoForIgrejas($count, $igrejas);
        return $count;
    }
    
    /**
     * Conte a quantidade de membros de acordo com o ano e mês de nascimento
     * 
     * @param string $igreja id da igreja
     * @return array
     */
    public function countByMesAndAno($igreja): array
    {
        $count = array();
        $this->dao->countByMesAndAno($count, $igreja);
        return $count;
    }
    
    /**
     * Conte a quantidade de membros das igrejas de acordo com o ano e mês de nascimento
     * 
     * @param string $igrejas ids das igrejas
     * @return array
     */
    public function countByMesAndAnoForIgrejas($igrejas): array
    {
        $count = array();
        $this->dao->countByMesAndAnoForIgrejas($count, $igrejas);
        return $count;
    }
    
    /**
     * Conte a quantidade de membros de acordo com o sexo
     * 
     * @param string $igreja id da igreja
     * @return array
     */
    public function countBySexo($igreja): array
    {
        $count = array();
        $this->dao->countBySexo($count, $igreja);
        return $count;
    }
    
    /**
     * Conte a quantidade de membros das igrejas de acordo com o sexo
     * 
     * @param string $igrejas ids das igrejas
     * @return array
     */
    public function countBySexoForIgrejas($igrejas): array
    {
        $count = array();
        $this->dao->countBySexoForIgrejas($count, $igrejas);
        return $count;
    }
    
    /**
     * Conte a quantidade de membros de acordo com o estado civil
     * 
     * @param string $igreja id da igreja
     * @return array
     */
    public function countByEstadoCivil($igreja): array
    {
        $count = array();
        $this->dao->countByEstadoCivil($count, $igreja);
        return $count;
    }
    
    /**
     * Conte a quantidade de membros das igrejas de acordo com o estado civil
     * 
     * @param string $igrejas ids das igrejas
     * @return array
     */
    public function countByEstadoCivilForIgrejas($igrejas): array
    {
        $count = array();
        $this->dao->countByEstadoCivilForIgrejas($count, $igrejas);
        return $count;
    }
    
    /**
     * Conte a quantidade de membros de acordo com a escolaridade
     * 
     * @param string $igreja id da igreja
     * @return array
     */
    public function countByEscolaridade($igreja): array
    {
        $count = array();
        $this->dao->countByEscolaridade($count, $igreja);
        return $count;
    }
    
    /**
     * Conte a quantidade de membros das igrejas de acordo com a escolaridade
     * 
     * @param string $igrejas ids das igrejas
     * @return array
     */
    public function countByEscolaridadeForIgrejas($igrejas): array
    {
        $count = array();
        $this->dao->countByEscolaridadeForIgrejas($count, $igrejas);
        return $count;
    }
    
    /**
     * Conte a quantidade de membros de acordo com a profissão de fé
     * 
     * @param string $igreja id da igreja
     * @return array
     */
    public function countByProfissaoFe($igreja): array
    {
        $count = array();
        $this->dao->countByProfissaoFe($count, $igreja);
        return $count;
    }
    
    /**
     * Conte a quantidade de membros das igrejas de acordo com a profissão de fé
     * 
     * @param string $igrejas ids das igrejas
     * @return array
     */
    public function countByProfissaoFeForIgrejas($igrejas): array
    {
        $count = array();
        $this->dao->countByProfissaoFeForIgrejas($count, $igrejas);
        return $count;
    }
    
    /**
     * Conte a quantidade de membros de acordo com o arrolamento
     * 
     * @param string $igreja id da igreja
     * @return array
     */
    public function countByArrolamento($igreja): array
    {
        $count = array();
        $this->dao->countByArrolamento($count, $igreja);
        return $count;
    }
    
    /**
     * Conte a quantidade de membros por necessidades especiais
     * 
     * @param string $igreja id da igreja
     * @return array
     */
    public function countByNecessidade($igreja): array
    {
        $count = array();
        $this->dao->countByNecessidade($count, $igreja);
        return $count;
    }
    
    /**
     * Conte a quantidade de membros por doação
     * 
     * @param string $igreja id da igreja
     * @return array
     */
    public function countByDoacao($igreja): array
    {
        $count = array();
        $this->dao->countByDoacao($count, $igreja);
        return $count;
    }
    
    /**
     * Conte a quantidade de membros por status de especial
     * 
     * @param string $igreja id da igreja
     * @return array
     */
    public function countByEspecial($igreja): array
    {
        $count = array();
        $this->dao->countByEspecial($count, $igreja);
        return $count;
    }
    
    /**
     * Conte a quantidade de membros por ano de admissão
     * 
     * @param string $igreja id da igreja
     * @return array
     */
    public function countByAnoAdmissao($igreja): array
    {
        $count = array();
        $this->dao->countByAnoAdmissao($count, $igreja);
        return $count;
    }
    
    /**
     * Conte a quantidade de membros por ano de demissão
     * 
     * @param string $igreja id da igreja
     * @return array
     */
    public function countByAnoDemissao($igreja): array
    {
        $count = array();
        $this->dao->countByAnoDemissao($count, $igreja);
        return $count;
    }
    
    /**
     * Conte a quantidade de membros por terem ou não filhos
     * 
     * @param string $igreja id da igreja
     * @return array
     */
    public function countByTemFilhos($igreja): array
    {
        $count = array();
        $this->dao->countByTemFilhos($count, $igreja);
        return $count;
    }
    
    /**
     * Mapeie pessoa por associação
     * 
     * @param string $igreja id da igreja [Opcional]
     * @param string $presbiterio id do presbitério [Opcional]
     * @param string $sinodo id do sínodo [Opcional]
     * @return array
     */
    public function mapPessoaByAssociacao($igreja = '', $presbiterio = '', $sinodo = ''): array
    {
        $map = array();
        $this->dao->mapPessoaByAssociacao($map, $igreja, $presbiterio, $sinodo);
        return $map;
    }
    
} 


