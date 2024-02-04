<?php

/**
 * Classe para as interações do Credenciais com o banco de dados. 
 * 
 */
class CredenciaisDAO extends BaseDAO
{
    protected $dto_object = "Credenciais";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new CredenciaisDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    /**
     * Mapeie as credenciais para o responsavel em um evento
     * 
     * @param array $map array que receberá o mapeamento
     * @param string $responsavel id do responsável
     * @param string $evento id do evento
     * @return bool
     */
    public function mapAllCredentialsForResponsavelInEvento(&$map, $responsavel, $evento): bool
    {
        $query = "select 
                        c.id as id, 
                        c.assinatura_inscrito as assinatura_inscrito,
                        c.assinatura_responsavel as assinatura_responsavel,
                        u.nome as nome, 
                        i.cargo as cargo,
                        i.cargo_ref as cargo_ref,
                        i.time_cad as time_cad,
                        c.last_mod as last_mod
                    from 
                        inscricoes as i,
                        pessoas as p,
                        usuarios as u,
                        credenciais as c,
                        cargo as cg
                    where 
                        c.email_responsavel = '$responsavel' and 
                        c.id = i.credencial_digital and 
                        i.evento = '$evento' and 
                        i.pessoa = p.id and 
                        p.id = u.id
                    order by nome ASC
                    "; 
        
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
                $map[$result->fields['id']] = array(
                    'id' => $result->fields['id'],
                    'assinatura_inscrito' => $result->fields['assinatura_inscrito'],
                    'assinatura_responsavel' => $result->fields['assinatura_responsavel'],
                    'nome' => $result->fields['nome'],
                    'cargo' => $result->fields['cargo'],
                    'cargo_ref' => $result->fields['cargo_ref'],
                    'time_cad' => $result->fields['time_cad'],
                    'last_mod' => $result->fields['last_mod']
                );
                $result->MoveNext();
            }

            $result->close();
            return true;
        }    
    }
    
    
} 
