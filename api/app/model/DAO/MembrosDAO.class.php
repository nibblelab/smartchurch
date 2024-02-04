<?php

/**
 * Classe para as interações do Membros com o banco de dados. 
 * 
 */
class MembrosDAO extends BaseDAO
{
    protected $dto_object = "Membros";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new MembrosDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    /* específicos */
    
    /**
     * Conte a quantidade de membros de acordo com o ano de nascimento
     * 
     * @param array $count array com os dados
     * @param string $igreja id da igreja
     * @return bool
     */
    public function countByAno(&$count, $igreja): bool
    {
        $query = "select 
                        count(m.id) as total, 
                        YEAR(p.data_nascimento) as ano 
                    from 
                        membros as m, 
                        pessoas as p 
                    where 
                        m.igreja = '$igreja' and
                        m.stat = 'ATV' and
                        m.pessoa = p.id and
                        p.data_nascimento IS NOT NULL
                    group by ano 
                    order by ano asc"; 

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
                $count[] = array(
                    'total' => (int) $result->fields['total'],
                    'ano' => (int) $result->fields['ano']
                );
                $result->MoveNext();
            }

            $result->close();
            return true;
        }    
    }
    
    /**
     * Conte a quantidade de membros das igrejas de acordo com o ano de nascimento
     * 
     * @param array $count array com os dados
     * @param string $igrejas ids das igrejas
     * @return bool
     */
    public function countByAnoForIgrejas(&$count, $igrejas): bool
    {
        $query = "select 
                        m.igreja as igreja,
                        count(m.id) as total, 
                        YEAR(p.data_nascimento) as ano 
                    from 
                        membros as m, 
                        pessoas as p 
                    where 
                        m.igreja in ($igrejas) and
                        m.stat = 'ATV' and
                        m.pessoa = p.id and
                        p.data_nascimento IS NOT NULL
                    group by igreja, ano 
                    order by ano asc"; 

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
                $count[] = array(
                    'igreja' => $result->fields['igreja'],
                    'total' => (int) $result->fields['total'],
                    'ano' => (int) $result->fields['ano']
                );
                $result->MoveNext();
            }

            $result->close();
            return true;
        }    
    }
    
    /**
     * Conte a quantidade de membros de acordo com o ano e mês de nascimento
     * 
     * @param array $count array com os dados
     * @param string $igreja id da igreja
     * @return bool
     */
    public function countByMesAndAno(&$count, $igreja): bool
    {
        $query = "select 
                        count(m.id) as total, 
                        YEAR(p.data_nascimento) as ano, 
                        MONTH(p.data_nascimento) as mes 
                    from 
                        membros as m, 
                        pessoas as p 
                    where 
                        m.igreja = '$igreja' and
                        m.stat = 'ATV' and
                        m.pessoa = p.id and
                        p.data_nascimento IS NOT NULL
                    group by ano, mes 
                    order by ano, mes asc"; 

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
                $count[] = array(
                    'total' => (int) $result->fields['total'],
                    'ano' => (int) $result->fields['ano'],
                    'mes' => (int) $result->fields['mes']
                );
                $result->MoveNext();
            }

            $result->close();
            return true;
        }    
    }
    
    /**
     * Conte a quantidade de membros das igrejas de acordo com o ano e mês de nascimento
     * 
     * @param array $count array com os dados
     * @param string $igrejas ids das igrejas
     * @return bool
     */
    public function countByMesAndAnoForIgrejas(&$count, $igrejas): bool
    {
        $query = "select 
                        m.igreja as igreja,
                        count(m.id) as total, 
                        YEAR(p.data_nascimento) as ano, 
                        MONTH(p.data_nascimento) as mes 
                    from 
                        membros as m, 
                        pessoas as p 
                    where 
                        m.igreja in ($igrejas) and
                        m.stat = 'ATV' and
                        m.pessoa = p.id and
                        p.data_nascimento IS NOT NULL
                    group by igreja, ano, mes 
                    order by ano, mes asc"; 

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
                $count[] = array(
                    'igreja' => $result->fields['igreja'],
                    'total' => (int) $result->fields['total'],
                    'ano' => (int) $result->fields['ano'],
                    'mes' => (int) $result->fields['mes']
                );
                $result->MoveNext();
            }

            $result->close();
            return true;
        }    
    }
    
    /**
     * Conte a quantidade de membros de acordo com o sexo
     * 
     * @param array $count array com os dados
     * @param string $igreja id da igreja
     * @return bool
     */
    public function countBySexo(&$count, $igreja): bool
    {
        $query = "select 
                        count(m.id) as total, 
                        p.sexo as sexo
                    from 
                        membros as m, 
                        pessoas as p 
                    where 
                        m.igreja = '$igreja' and
                        m.stat = 'ATV' and
                        m.pessoa = p.id 
                    group by sexo"; 

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
                $count[$result->fields['sexo']] = array(
                    'total' => (int) $result->fields['total'],
                    'sexo' => $result->fields['sexo']
                );
                $result->MoveNext();
            }

            $result->close();
            return true;
        }    
    }
    
    /**
     * Conte a quantidade de membros das igrejas de acordo com o sexo
     * 
     * @param array $count array com os dados
     * @param string $igrejas ids das igrejas
     * @return bool
     */
    public function countBySexoForIgrejas(&$count, $igrejas): bool
    {
        $query = "select 
                        m.igreja as igreja,
                        count(m.id) as total, 
                        p.sexo as sexo
                    from 
                        membros as m, 
                        pessoas as p 
                    where 
                        m.igreja in ($igrejas) and
                        m.stat = 'ATV' and
                        m.pessoa = p.id 
                    group by igreja, sexo"; 

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
                if(!isset($count[$result->fields['sexo']][$result->fields['igreja']])) {
                    $count[$result->fields['sexo']] = array();
                }
                
                $count[$result->fields['sexo']][$result->fields['igreja']] = array(
                    'igreja' => $result->fields['igreja'],
                    'total' => (int) $result->fields['total'],
                    'sexo' => $result->fields['sexo']
                );
                $result->MoveNext();
            }

            $result->close();
            return true;
        }    
    }
    
    /**
     * Conte a quantidade de membros de acordo com o estado civil
     * 
     * @param array $count array com os dados
     * @param string $igreja id da igreja
     * @return bool
     */
    public function countByEstadoCivil(&$count, $igreja): bool
    {
        $query = "select 
                        count(m.id) as total, 
                        p.estado_civil as estado_civil
                    from 
                        membros as m, 
                        pessoas as p 
                    where 
                        m.igreja = '$igreja' and
                        m.stat = 'ATV' and
                        m.pessoa = p.id 
                    group by estado_civil"; 

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
                $count[$result->fields['estado_civil']] = array(
                    'total' => (int) $result->fields['total'],
                    'estado_civil' => $result->fields['estado_civil']
                );
                $result->MoveNext();
            }

            $result->close();
            return true;
        }    
    }
    
    /**
     * Conte a quantidade de membros das igrejas de acordo com o estado civil
     * 
     * @param array $count array com os dados
     * @param string $igrejas ids das igrejas
     * @return bool
     */
    public function countByEstadoCivilForIgrejas(&$count, $igrejas): bool
    {
        $query = "select 
                        m.igreja as igreja,
                        count(m.id) as total, 
                        p.estado_civil as estado_civil
                    from 
                        membros as m, 
                        pessoas as p 
                    where 
                        m.igreja in ($igrejas) and
                        m.stat = 'ATV' and
                        m.pessoa = p.id 
                    group by igreja, estado_civil"; 

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
                if(!isset($count[$result->fields['estado_civil']][$result->fields['igreja']])) {
                    $count[$result->fields['estado_civil']] = array();
                }
                
                $count[$result->fields['estado_civil']][$result->fields['igreja']] = array(
                    'igreja' => $result->fields['igreja'],
                    'total' => (int) $result->fields['total'],
                    'estado_civil' => $result->fields['estado_civil']
                );
                $result->MoveNext();
            }

            $result->close();
            return true;
        }    
    }
    
    /**
     * Conte a quantidade de membros de acordo com a escolaridade
     * 
     * @param array $count array com os dados
     * @param string $igreja id da igreja
     * @return bool
     */
    public function countByEscolaridade(&$count, $igreja): bool
    {
        $query = "select 
                        count(m.id) as total, 
                        p.escolaridade as escolaridade
                    from 
                        membros as m, 
                        pessoas as p 
                    where 
                        m.igreja = '$igreja' and
                        m.stat = 'ATV' and
                        m.pessoa = p.id 
                    group by escolaridade"; 

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
                $count[$result->fields['escolaridade']] = array(
                    'total' => (int) $result->fields['total'],
                    'escolaridade' => $result->fields['escolaridade']
                );
                $result->MoveNext();
            }

            $result->close();
            return true;
        }    
    }
    
    /**
     * Conte a quantidade de membros das igrejas de acordo com a escolaridade
     * 
     * @param array $count array com os dados
     * @param string $igrejas ids das igrejas
     * @return bool
     */
    public function countByEscolaridadeForIgrejas(&$count, $igrejas): bool
    {
        $query = "select 
                        m.igreja as igreja,
                        count(m.id) as total, 
                        p.escolaridade as escolaridade
                    from 
                        membros as m, 
                        pessoas as p 
                    where 
                        m.igreja in ($igrejas) and
                        m.stat = 'ATV' and
                        m.pessoa = p.id 
                    group by igreja, escolaridade"; 

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
                if(!isset($count[$result->fields['escolaridade']][$result->fields['igreja']])) {
                    $count[$result->fields['escolaridade']] = array();
                }
                
                $count[$result->fields['escolaridade']][$result->fields['igreja']] = array(
                    'igreja' => $result->fields['igreja'],
                    'total' => (int) $result->fields['total'],
                    'escolaridade' => $result->fields['escolaridade']
                );
                $result->MoveNext();
            }

            $result->close();
            return true;
        }    
    }
    
    /**
     * Conte a quantidade de membros de acordo com a profissão de fé
     * 
     * @param array $count array com os dados
     * @param string $igreja id da igreja
     * @return bool
     */
    public function countByProfissaoFe(&$count, $igreja): bool
    {
        $query = "select 
                        count(m.id) as total, 
                        m.comungante as comungante
                    from 
                        membros as m
                    where
                        m.igreja = '$igreja' and
                        m.stat = 'ATV'
                    group by comungante"; 

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
                $count[$result->fields['comungante']] = array(
                    'total' => (int) $result->fields['total'],
                    'comungante' => $result->fields['comungante']
                );
                $result->MoveNext();
            }

            $result->close();
            return true;
        }    
    }
    
    /**
     * Conte a quantidade de membros das igrejas de acordo com a profissão de fé
     * 
     * @param array $count array com os dados
     * @param string $igrejas ids das igrejas
     * @return bool
     */
    public function countByProfissaoFeForIgrejas(&$count, $igrejas): bool
    {
        $query = "select 
                        m.igreja as igreja,
                        count(m.id) as total, 
                        m.comungante as comungante
                    from 
                        membros as m
                    where
                        m.igreja in ($igrejas) and
                        m.stat = 'ATV'
                    group by igreja, comungante"; 

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
                if(!isset($count[$result->fields['comungante']][$result->fields['igreja']])) {
                    $count[$result->fields['comungante']] = array();
                }
                
                $count[$result->fields['comungante']][$result->fields['igreja']] = array(
                    'igreja' => $result->fields['igreja'],
                    'total' => (int) $result->fields['total'],
                    'comungante' => $result->fields['comungante']
                );
                $result->MoveNext();
            }

            $result->close();
            return true;
        }    
    }
    
    /**
     * Conte a quantidade de membros de acordo com o arrolamento
     * 
     * @param array $count array com os dados
     * @param string $igreja id da igreja
     * @return bool
     */
    public function countByArrolamento(&$count, $igreja): bool
    {
        $query = "select 
                        count(m.id) as total, 
                        m.arrolado as arrolado
                    from 
                        membros as m
                    where
                        m.igreja = '$igreja' and
                        m.stat = 'ATV'
                    group by arrolado"; 

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
                $count[$result->fields['arrolado']] = array(
                    'total' => (int) $result->fields['total'],
                    'arrolado' => $result->fields['arrolado']
                );
                $result->MoveNext();
            }

            $result->close();
            return true;
        }    
    }
    
    /**
     * Conte a quantidade de membros por necessidades especiais
     * 
     * @param array $count array com os dados
     * @param string $igreja id da igreja
     * @return bool
     */
    public function countByNecessidade(&$count, $igreja): bool
    {
        $query = "select 
                        count(m.id) as total, 
                        np.necessidade as necessidade
                    from 
                        membros as m, 
                        necessidades_das_pessoas as np 
                    where
                        m.igreja = '$igreja' and
                        m.stat = 'ATV' and
                        m.pessoa = np.pessoa 
                    group by necessidade"; 

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
                $count[$result->fields['necessidade']] = array(
                    'total' => (int) $result->fields['total'],
                    'necessidade' => $result->fields['necessidade']
                );
                $result->MoveNext();
            }

            $result->close();
            return true;
        }    
    }
    
    /**
     * Conte a quantidade de membros por doação
     * 
     * @param array $count array com os dados
     * @param string $igreja id da igreja
     * @return bool
     */
    public function countByDoacao(&$count, $igreja): bool
    {
        $query = "select 
                        count(m.id) as total, 
                        d.doacao as doacao
                    from 
                        membros as m, 
                        doadores as d 
                    where 
                        m.igreja = '$igreja' and
                        m.stat = 'ATV' and
                        m.pessoa = d.pessoa 
                    group by doacao";
        
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
                $count[$result->fields['doacao']] = array(
                    'total' => (int) $result->fields['total'],
                    'doacao' => $result->fields['doacao']
                );
                $result->MoveNext();
            }

            $result->close();
            return true;
        } 
    }
    
    /**
     * Conte a quantidade de membros por status de especial
     * 
     * @param array $count array com os dados
     * @param string $igreja id da igreja
     * @return bool
     */
    public function countByEspecial(&$count, $igreja): bool
    {
        $query = "select 
                        count(m.id) as total, 
                        m.especial as especial
                    from 
                        membros as m
                    where 
                        m.igreja = '$igreja' and
                        m.stat = 'ATV'
                    group by especial";
        
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
                $count[$result->fields['especial']] = array(
                    'total' => (int) $result->fields['total'],
                    'especial' => $result->fields['especial']
                );
                $result->MoveNext();
            }

            $result->close();
            return true;
        } 
    }
    
    /**
     * Conte a quantidade de membros por ano de admissão
     * 
     * @param array $count array com os dados
     * @param string $igreja id da igreja
     * @return bool
     */
    public function countByAnoAdmissao(&$count, $igreja): bool
    {
        $query = "select 
                        count(m.id) as total, 
                        YEAR(m.data_admissao) as ano
                    from 
                        membros as m 
                    where 
                        m.igreja = '$igreja' and
                        m.stat = 'ATV' and
                        m.data_admissao IS NOT NULL
                    group by ano 
                    order by ano asc"; 

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
                $count[] = array(
                    'total' => (int) $result->fields['total'],
                    'ano' => (int) $result->fields['ano']
                );
                $result->MoveNext();
            }

            $result->close();
            return true;
        }    
    }
    
    /**
     * Conte a quantidade de membros por ano de demissão
     * 
     * @param array $count array com os dados
     * @param string $igreja id da igreja
     * @return bool
     */
    public function countByAnoDemissao(&$count, $igreja): bool
    {
        $query = "select 
                        count(m.id) as total, 
                        YEAR(m.data_demissao) as ano
                    from 
                        membros as m 
                    where 
                        m.igreja = '$igreja' and
                        m.data_demissao IS NOT NULL
                    group by ano 
                    order by ano asc"; 

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
                $count[] = array(
                    'total' => (int) $result->fields['total'],
                    'ano' => (int) $result->fields['ano']
                );
                $result->MoveNext();
            }

            $result->close();
            return true;
        }    
    }
    
    /**
     * Conte a quantidade de membros por terem ou não filhos
     * 
     * @param array $count array com os dados
     * @param string $igreja id da igreja
     * @return bool
     */
    public function countByTemFilhos(&$count, $igreja): bool
    {
        $query = "select 
                        count(m.id) as total, 
                        p.tem_filhos as tem_filhos
                    from 
                        membros as m, 
                        pessoas as p 
                    where 
                        m.igreja = '$igreja' and
                        m.stat = 'ATV' and
                        m.pessoa = p.id 
                    group by tem_filhos";
        
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
                $count[$result->fields['tem_filhos']] = array(
                    'total' => (int) $result->fields['total'],
                    'tem_filhos' => $result->fields['tem_filhos']
                );
                $result->MoveNext();
            }

            $result->close();
            return true;
        } 
    }
    
    /**
     * Mapeie pessoa por associação
     * 
     * @param array $map array que receberá o mapeamento
     * @param string $igreja id da igreja [Opcional]
     * @param string $presbiterio id do presbitério [Opcional]
     * @param string $sinodo id do sínodo [Opcional]
     * @return bool
     */
    public function mapPessoaByAssociacao(&$map, $igreja = '', $presbiterio = '', $sinodo = ''): bool
    {
        $query = "select 
                        m.pessoa as pessoa,
                        i.id as igreja,
                        i.presbiterio as presbiterio,
                        i.sinodo as sinodo
                    from 
                        membros as m,
                        igrejas as i
                    where
                        m.igreja = i.id
                        "; 
        
        if(!empty($igreja)) {
            $query .= " and i.id = '{$igreja}' ";
        }
        
        if(!empty($presbiterio)) {
            $query .= " and i.presbiterio = '{$presbiterio}' ";
        }
        
        if(!empty($sinodo)) {
            $query .= " and i.sinodo = '{$sinodo}' ";
        }
        
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
                $map[$result->fields['pessoa']] = array(
                    'pessoa' => $result->fields['pessoa'],
                    'igreja' => $result->fields['igreja'],
                    'presbiterio' => $result->fields['presbiterio'],
                    'sinodo' => $result->fields['sinodo']
                );
                $result->MoveNext();
            }

            $result->close();
            return true;
        }  
        
    }
    
} 

