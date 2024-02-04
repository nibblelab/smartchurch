<?php

/**
 * Classe de dados (DTO) do Usuarios
 *
 *
 */
class UsuariosDTO extends BaseDTO
{
    /* dados propriamento ditos */
    public $id;
    public $perfil;
    public $nome;
    public $email;
    public $senha;
    public $stat;
    public $tp;
    public $avatar;
    public $is_master;
    public $is_android;
    public $fcm_token;
    public $reset_code;
    public $time_cad;
    public $last_mod;
    public $last_sync;
    public $last_amod;
}

/**
 * Classe Façade que provê interface entre o DAO e o controller para o Usuarios
 *
 *
 */
class UsuariosADO extends BaseADO
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
        $this->dao = new UsuariosDAO();
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
     * Atualize o registro de sincronização do usuário
     * 
     * @param string $id id do usuário
     * @return bool
     */
    public function updateLastSync($id)
    {
        return $this->dao->updateLastSync($id);
    }
    
    /**
     * Obtenha o registro de sincronização do usuário após a data de corte
     * 
     * @param string $last data de corte
     * @return bool
     */
    public function searchLastSync($last)
    {
        $this->clear();
        return $this->dao->searchLastSync($this, $last);
    }
} 

?>
