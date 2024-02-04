<?php

/**
 * Classe para as interações do Frequencias com o banco de dados. 
 * 
 */
class FrequenciasDAO extends BaseDAO
{
    protected $dto_object = "Frequencias";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new FrequenciasDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    /* específicos */
    
    /**
     * Busque os 10 últimos dias em que houveram registros de frequência da sala
     * 
     * @param array $range array com os dados
     * @param string $sala id da sala
     * @return boolean
     */
    public function getFrequenciasRangeBySala(&$range, $sala): bool
    {
        $query = "select 
                        dia
                    from 
                        frequencias
                    where 
                        sala = '$sala'
                    group by dia 
                    order by dia desc
                    limit 0, 10"; 

        if(LOG_QUERY) {
            $this->logThisInfo($query);
        }
        
        $result = $this->con->Execute($query);
        if(!$result)
        {
            $this->err_msg = $this->con->ErrorMsg();
            if(LOG_DB_ERRS)
            {
                $this->logThisInfo($this->err_msg);
            }
            
            return false;
        }
        else
        {
            while(!$result->EOF)
            {
                $range[] = $result->fields['dia'];
                $result->MoveNext();
            }

            $result->close();
            return true;
        }   
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
        $query = "delete from 
                        frequencias
                    where 
                        dia = '$dia' and
                        sala = '$sala'
                    "; 

        if(LOG_QUERY) {
            $this->logThisInfo($query);
        }
        
        $this->con->beginTrans();
        $result = $this->con->Execute($query);
        if(!$result)
        {
            $this->err_msg = $this->con->ErrorMsg();
            if(LOG_DB_ERRS)
            {
                $this->logThisInfo($this->err_msg);
            }
            
            $this->con->rollbackTrans();
            return false;
        }
        else
        {
            $this->con->commitTrans();
            return true;
        }   
    }
    
} 

