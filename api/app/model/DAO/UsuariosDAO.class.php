<?php

/**
 * Classe para as interações do Usuarios com o banco de dados. 
 * 
 */
class UsuariosDAO extends BaseDAO
{
    protected $dto_object = "Usuarios";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new UsuariosDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    /* específicos */
    
    /**
     * Atualize o registro de sincronização do usuário
     * 
     * @param string $id id do usuário
     * @return bool
     */
    public function updateLastSync($id): bool
    {
        $last_sync = date('Y-m-d H:i:s');
        $query = "update usuarios set last_sync = '$last_sync' where id = '$id' "; 

        if(LOG_QUERY) {
            $this->logThisInfo($query);
        }

        if($this->con->Execute($query))
        {
            return true;
        }
        else
        {
            $this->err_msg = $this->con->ErrorMsg();

            if(LOG_DB_ERRS)
            {
                $this->logThisInfo($this->err_msg);
            }
            
            return false;
        }    
    }
    
    /**
     * Obtenha o registro de sincronização do usuário após a data de corte
     * 
     * @param object $ado objeto ADO que contém a lista de resultados
     * @param string $last data de corte
     * @return bool
     */
    public function searchLastSync(&$ado, $last): bool
    {
        $query = "select * from usuarios where last_sync is not null and last_sync >= '$last'"; 

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
                $dto = $this->getDTO();
                $vars = get_object_vars($dto);
                
                foreach($vars as $v => $val)
                {
                    if(array_key_exists($v,$result->fields))
                    {
                        $dto->{$v} = $result->fields[$v];
                    }
                }
                
                $ado->add($dto);
                $ado->incrementCount();
                $result->MoveNext();
            }

            $result->close();
            return true;
        }    
    }
} 
