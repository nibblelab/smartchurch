<?php

/**
 * Classe de dados (DTO) do Frequencias
 *
 *
 */
class FrequenciasDTO extends BaseDTO
{
    /* dados propriamento ditos */
    public $id;
    public $pessoa;
    public $sala;
    public $presente;
    public $dia;
}

/**
 * Classe Façade que provê interface entre o DAO e o controller para o Frequencias
 *
 *
 */
class FrequenciasADO extends BaseADO
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
        $this->dao = new FrequenciasDAO();
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
     * Busque os 10 últimos dias em que houveram registros de frequência da sala
     * 
     * @param string $sala id da sala
     * @return array
     */
    public function getFrequenciasRangeBySala($sala): array
    {
        $range = array();
        $this->dao->getFrequenciasRangeBySala($range, $sala);
        return $range;
    }
    
    /**
     * Remova por dia e sala
     * 
     * @param string $dia data para a remoção
     * @param string $sala id da sala
     * @return bool
     */
    public function deleteByDiaAndSala($dia, $sala): bool
    {
        return $this->dao->deleteByDiaAndSala($dia, $sala);
    }
    

} 

?>
