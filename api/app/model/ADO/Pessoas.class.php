<?php

/**
 * Classe de dados (DTO) do Pessoas
 *
 *
 */
class PessoasDTO extends UsuariosDTO
{
    /* dados propriamento ditos */
    public $id;
    public $profissao;
    public $sexo;
    public $data_nascimento;
    public $crianca;
    public $responsavel;
    public $estado_civil;
    public $tem_filhos;
    public $escolaridade;
    public $telefone;
    public $celular_1;
    public $celular_2;
    public $pai;
    public $mae;
    public $naturalidade;
    public $nacionalidade;
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
    
    public function __construct()
    {
        parent::__construct();
        $this->ignore[] = 'faixas_idade';
    }
}

/**
 * Classe Façade que provê interface entre o DAO e o controller para o Pessoas
 *
 *
 */
class PessoasADO extends BaseADO
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
        $this->dao = new PessoasDAO();
        $this->size = 0;
        $this->errs = array();
    }
    
    /**
     *  Pega o próximo da lista 
     * @return \PessoasDTO
     */
    public function next(): ?\PessoasDTO
    {
        return parent::next();
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
     * Mapeie os nomes das pessoas pelo id
     * 
     * @return array
     */
    public function mapNomesById(): array
    {
        $map = array();
        $this->dao->mapNomesById($map);
        return $map;
    }
    
    /**
     * Mapeie nome, email, sexo, data de nascimento, estado_civil, escolaridade, 
     * se tem ou não filhos, telefone e celular (1 e 2) das pessoas pelo id
     * 
     * @return array
     */
    public function mapBasicDataById(): array
    {
        $map = array();
        $this->dao->mapBasicDataById($map);
        return $map;
    }
    
    /**
     * Mapeie os nomes das pessoas pelo id conforme o parâmetro passado
     * 
     * @param object $dto filtro
     * @return array
     */
    public function mapNomesByIdWithParam($dto): array
    {
        $map = array();
        $this->dao->mapNomesByIdWithParam($map, $dto);
        return $map;
    }
    
    /**
     * Mapeie nome, email, sexo, data de nascimento, estado_civil, escolaridade, 
     * se tem ou não filhos, telefone e celular (1 e 2) das pessoas pelo id conforme o parâmetro passado
     * 
     * @param object $dto filtro
     * @return array
     */
    public function mapBasicDataByIdWithParam($dto): array
    {
        $map = array();
        $this->dao->mapBasicDataByIdWithParam($map, $dto);
        return $map;
    }
    

} 

?>
