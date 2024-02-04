<?php

/**
 * Classe para as interações do Perfis com o banco de dados. 
 * 
 */
class PerfisDAO extends BaseDAO
{
    protected $dto_object = "Perfis";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new PerfisDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    /* específicos */
    
    /**
     * Obtem as permissões de um perfil
     * 
     * @param array $permissoes array que receberá as permissões
     * @param string $perfil id do perfil
     * @return boolean
     */
    public function getPermissoes(&$permissoes, $perfil): bool
    {
        $query = "select prm.* from permissoes as prm, permissoes_do_perfil as prm_d_p where prm_d_p.perfil = '$perfil' and prm_d_p.permissao = prm.id ";
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
                $local_map = array();
                $local_map['id'] = $result->fields['id'];
                $local_map['nome'] = $result->fields['nome'];
                $local_map['modulo'] = $result->fields['modulo'];
                
                $permissoes[] = $local_map;
                $result->MoveNext();
            }

            $result->close();
            return true;
        }    
    }
    
    /**
     * 
     * Adiciona uma permissão a um perfil
     * 
     * @param string $permissao id da permissão
     * @param string $perfil id do perfil
     * @return boolean
     */
    public function addPermission($permissao, $perfil): bool
    {
        $query = "insert into permissoes_do_perfil values ('$permissao','$perfil')"; 
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
     * Remove uma permissão de um perfil
     * 
     * @param string $permissao id da permissão
     * @param string $perfil id do perfil
     * @return boolean
     */
    public function removePermission($permissao, $perfil): bool
    {
        $query = "delete from permissoes_do_perfil where permissao = '$permissao' and perfil = '$perfil'"; 
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
} 

