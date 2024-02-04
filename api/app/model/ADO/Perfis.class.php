<?php

/**
 * Classe de dados (DTO) do Perfis
 *
 *
 */
class PerfisDTO extends BaseDTO
{
    /* dados propriamento ditos */
    public $id;
    public $nome;
    public $descricao;
    public $staff_only;
    public $time_cad;
    public $last_mod;
    public $last_amod;
}

/**
 * Classe Façade que provê interface entre o DAO e o controller para o Perfis
 *
 *
 */
class PerfisADO extends BaseADO
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
        $this->dao = new PerfisDAO();
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
     * Obtenha as permissões de um perfil
     * 
     * @param string $perfil id do perfil
     * @return array
     */
    public function getPermissoes($perfil): array
    {
        $permissoes = array();
        $this->dao->getPermissoes($permissoes, $perfil);
        return $permissoes;
    }
    
    /**
     * Adicione uma permissão a um perfil
     * 
     * @param string $permissao id da permissão
     * @param string $perfil id do perfil
     * @return bool
     */
    public function addPermission($permissao, $perfil): bool
    {
        return $this->dao->addPermission($permissao, $perfil);
    }
    
    /**
     * Remova uma permissão de um perfil
     * 
     * @param string $permissao id da permissão
     * @param string $perfil id do perfil
     * @return bool
     */
    public function removePermission($permissao, $perfil): bool
    {
        return $this->dao->removePermission($permissao, $perfil);
    }

} 

?>
