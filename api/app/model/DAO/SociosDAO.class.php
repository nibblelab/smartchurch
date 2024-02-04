<?php

/**
 * Classe para as interações do Socios com o banco de dados. 
 * 
 */
class SociosDAO extends BaseDAO
{
    protected $dto_object = "Socios";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new SociosDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    /* específicos */
    
    /**
     * Conte a quantidade de sócios de acordo com o ano de nascimento
     * 
     * @param array $count array com os dados
     * @param string $sociedade id da sociedade
     * @return bool
     */
    public function countByAno(&$count, $sociedade): bool
    {
        $query = "select 
                        count(s.id) as total, 
                        YEAR(p.data_nascimento) as ano 
                    from 
                        socios as s, 
                        pessoas as p 
                    where 
                        s.sociedade = '$sociedade' and
                        s.stat = 'ATV' and
                        s.admin <> 'S' and
                        s.pessoa = p.id and
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
    
    public function countByAnoForSociedades(&$count, $sociedades): bool
    {
        $query = "select 
                        s.sociedade as sociedade,
                        count(s.id) as total, 
                        YEAR(p.data_nascimento) as ano 
                    from 
                        socios as s, 
                        pessoas as p 
                    where 
                        s.sociedade in ($sociedades) and
                        s.stat = 'ATV' and
                        s.admin <> 'S' and
                        s.pessoa = p.id and
                        p.data_nascimento IS NOT NULL
                    group by sociedade, ano 
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
                    'sociedade' => $result->fields['sociedade'],
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
     * Conte a quantidade de sócios de acordo com o ano e mês de nascimento
     * 
     * @param array $count array com os dados
     * @param string $sociedade id da sociedade
     * @return bool
     */
    public function countByMesAndAno(&$count, $sociedade): bool
    {
        $query = "select 
                        count(s.id) as total, 
                        YEAR(p.data_nascimento) as ano, 
                        MONTH(p.data_nascimento) as mes 
                    from 
                        socios as s, 
                        pessoas as p 
                    where 
                        s.sociedade = '$sociedade' and
                        s.stat = 'ATV' and
                        s.admin <> 'S' and
                        s.pessoa = p.id and
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
     * Conte a quantidade de sócios das sociedades de acordo com o ano e mês de nascimento
     * 
     * @param array $count array com os dados
     * @param string $sociedades ids das sociedades
     * @return bool
     */
    public function countByMesAndAnoForSociedades(&$count, $sociedades): bool
    {
        $query = "select 
                        s.sociedade as sociedade,
                        count(s.id) as total, 
                        YEAR(p.data_nascimento) as ano, 
                        MONTH(p.data_nascimento) as mes 
                    from 
                        socios as s, 
                        pessoas as p 
                    where 
                        s.sociedade in ($sociedades) and
                        s.stat = 'ATV' and
                        s.admin <> 'S' and
                        s.pessoa = p.id and
                        p.data_nascimento IS NOT NULL
                    group by sociedade, ano, mes 
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
                    'sociedade' => $result->fields['sociedade'],
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
     * Conte a quantidade de sócios de acordo com o sexo
     * 
     * @param array $count array com os dados
     * @param string $sociedade id da sociedade
     * @return bool
     */
    public function countBySexo(&$count, $sociedade): bool
    {
        $query = "select 
                        count(s.id) as total, 
                        p.sexo as sexo
                    from 
                        socios as s, 
                        pessoas as p 
                    where 
                        s.sociedade = '$sociedade' and
                        s.stat = 'ATV' and
                        s.admin <> 'S' and
                        s.pessoa = p.id 
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
     * Conte a quantidade de sócios das sociedades de acordo com o sexo
     * 
     * @param array $count array com os dados
     * @param string $sociedades ids das sociedades
     * @return bool
     */
    public function countBySexoForSociedades(&$count, $sociedades): bool
    {
        $query = "select 
                        s.sociedade as sociedade,
                        count(s.id) as total, 
                        p.sexo as sexo
                    from 
                        socios as s, 
                        pessoas as p 
                    where 
                        s.sociedade in ($sociedades) and
                        s.stat = 'ATV' and
                        s.admin <> 'S' and
                        s.pessoa = p.id 
                    group by sociedade, sexo"; 

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
                if(!isset($count[$result->fields['sexo']])) {
                    $count[$result->fields['sexo']] = array();
                }
                
                if(!isset($count[$result->fields['sexo']][$result->fields['sociedade']])) {
                    $count[$result->fields['sexo']][$result->fields['sociedade']] = array();
                }
                
                $count[$result->fields['sexo']][$result->fields['sociedade']] = array(
                    'sociedade' => $result->fields['sociedade'],
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
     * Conte a quantidade de sócios de acordo com o estado civil
     * 
     * @param array $count array com os dados
     * @param string $sociedade id da sociedade
     * @return bool
     */
    public function countByEstadoCivil(&$count, $sociedade): bool
    {
        $query = "select 
                        count(s.id) as total, 
                        p.estado_civil as estado_civil
                    from 
                        socios as s, 
                        pessoas as p 
                    where 
                        s.sociedade = '$sociedade' and
                        s.stat = 'ATV' and
                        s.admin <> 'S' and
                        s.pessoa = p.id
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
     * Conte a quantidade de sócios das sociedades de acordo com o estado civil
     * 
     * @param array $count array com os dados
     * @param string $sociedades ids das sociedades
     * @return bool
     */
    public function countByEstadoCivilForSociedades(&$count, $sociedades): bool
    {
        $query = "select 
                        s.sociedade as sociedade,
                        count(s.id) as total, 
                        p.estado_civil as estado_civil
                    from 
                        socios as s, 
                        pessoas as p 
                    where 
                        s.sociedade in ($sociedades) and
                        s.stat = 'ATV' and
                        s.admin <> 'S' and
                        s.pessoa = p.id
                    group by sociedade, estado_civil"; 

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
                if(!isset($count[$result->fields['estado_civil']])) {
                    $count[$result->fields['estado_civil']] = array();
                }
                
                if(!isset($count[$result->fields['estado_civil']][$result->fields['sociedade']])) {
                    $count[$result->fields['estado_civil']][$result->fields['sociedade']] = array();
                }
                
                $count[$result->fields['estado_civil']][$result->fields['sociedade']] = array(
                    'sociedade' => $result->fields['sociedade'],
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
     * Conte a quantidade de sócios de acordo com a escolaridade
     * 
     * @param array $count array com os dados
     * @param string $sociedade id da sociedade
     * @return bool
     */
    public function countByEscolaridade(&$count, $sociedade): bool
    {
        $query = "select 
                        count(s.id) as total, 
                        p.escolaridade as escolaridade
                    from 
                        socios as s, 
                        pessoas as p 
                    where 
                        s.sociedade = '$sociedade' and
                        s.stat = 'ATV' and
                        s.admin <> 'S' and
                        s.pessoa = p.id 
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
     * Conte a quantidade de sócios das sociedades de acordo com a escolaridade
     * 
     * @param array $count array com os dados
     * @param string $sociedades ids das sociedades
     * @return bool
     */
    public function countByEscolaridadeForSociedades(&$count, $sociedades): bool
    {
        $query = "select 
                        s.sociedade as sociedade,
                        count(s.id) as total, 
                        p.escolaridade as escolaridade
                    from 
                        socios as s, 
                        pessoas as p 
                    where 
                        s.sociedade in ($sociedades) and
                        s.stat = 'ATV' and
                        s.admin <> 'S' and
                        s.pessoa = p.id 
                    group by sociedade, escolaridade"; 

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
                if(!isset($count[$result->fields['escolaridade']])) {
                    $count[$result->fields['escolaridade']] = array();
                }
                
                if(!isset($count[$result->fields['escolaridade']][$result->fields['sociedade']])) {
                    $count[$result->fields['escolaridade']][$result->fields['sociedade']] = array();
                }
                
                $count[$result->fields['escolaridade']][$result->fields['sociedade']] = array(
                    'sociedade' => $result->fields['sociedade'],
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
     * Conte a quantidade de sócios de acordo com a profissão de fé
     * 
     * @param array $count array com os dados
     * @param string $sociedade id da sociedade
     * @return bool
     */
    public function countByProfissaoFe(&$count, $sociedade): bool
    {
        $query = "select 
                        count(s.id) as total, 
                        m.comungante as comungante
                    from 
                        socios as s,
                        membros as m
                    where
                        s.stat = 'ATV' and
                        s.admin <> 'S' and
                        s.sociedade = '$sociedade' and
                        s.pessoa = m.pessoa
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
     * Conte a quantidade de sócios das sociedades de acordo com a profissão de fé
     * 
     * @param array $count array com os dados
     * @param string $sociedades ids das sociedades
     * @return bool
     */
    public function countByProfissaoFeForSociedades(&$count, $sociedades): bool
    {
        $query = "select 
                        s.sociedade as sociedade,
                        count(s.id) as total, 
                        m.comungante as comungante
                    from 
                        socios as s,
                        membros as m
                    where
                        s.stat = 'ATV' and
                        s.admin <> 'S' and
                        s.sociedade in ($sociedades) and
                        s.pessoa = m.pessoa
                    group by sociedade, comungante"; 

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
                if(!isset($count[$result->fields['comungante']])) {
                    $count[$result->fields['comungante']] = array();
                }
                
                if(!isset($count[$result->fields['comungante']][$result->fields['sociedade']])) {
                    $count[$result->fields['comungante']][$result->fields['sociedade']] = array();
                }
                
                $count[$result->fields['comungante']][$result->fields['sociedade']] = array(
                    'sociedade' => $result->fields['sociedade'],
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
     * Conte a quantidade de sócios por terem ou não filhos
     * 
     * @param array $count array com os dados
     * @param string $sociedade id da sociedade
     * @return bool
     */
    public function countByTemFilhos(&$count, $sociedade): bool
    {
        $query = "select 
                        count(s.id) as total, 
                        p.tem_filhos as tem_filhos
                    from 
                        socios as s, 
                        pessoas as p 
                    where 
                        s.sociedade = '$sociedade' and
                        s.stat = 'ATV' and
                        s.admin <> 'S' and
                        s.pessoa = p.id 
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
     * Conte a quantidade de sócios das sociedades por terem ou não filhos
     * 
     * @param array $count array com os dados
     * @param string $sociedades ids das sociedades
     * @return bool
     */
    public function countByTemFilhosForSociedades(&$count, $sociedades): bool
    {
        $query = "select 
                        s.sociedade as sociedade,
                        count(s.id) as total, 
                        p.tem_filhos as tem_filhos
                    from 
                        socios as s, 
                        pessoas as p 
                    where 
                        s.sociedade in ($sociedades) and
                        s.stat = 'ATV' and
                        s.admin <> 'S' and
                        s.pessoa = p.id 
                    group by sociedade, tem_filhos";
        
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
                if(!isset($count[$result->fields['tem_filhos']])) {
                    $count[$result->fields['tem_filhos']] = array();
                }
                
                if(!isset($count[$result->fields['tem_filhos']][$result->fields['sociedade']])) {
                    $count[$result->fields['tem_filhos']][$result->fields['sociedade']] = array();
                }
                
                $count[$result->fields['tem_filhos']][$result->fields['sociedade']] = array(
                    'sociedade' => $result->fields['sociedade'],
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
     * Conte a quantidade de sócios por terem ou não filhos, conforme o sexo dos sócios
     * 
     * @param array $count array com os dados
     * @param string $sociedade id da sociedade
     * @return bool
     */
    public function countByTemFilhosAndSexo(&$count, $sociedade): bool
    {
        $query = "select 
                        count(s.id) as total, 
                        p.tem_filhos as tem_filhos,
                        p.sexo as sexo
                    from 
                        socios as s, 
                        pessoas as p 
                    where 
                        s.sociedade = '$sociedade' and
                        s.stat = 'ATV' and
                        s.admin <> 'S' and
                        s.pessoa = p.id 
                    group by tem_filhos, sexo";
        
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
                $count[$result->fields['tem_filhos'].'_'.$result->fields['sexo']] = array(
                    'total' => (int) $result->fields['total'],
                    'tem_filhos' => $result->fields['tem_filhos'],
                    'sexo' => $result->fields['sexo']
                );
                $result->MoveNext();
            }

            $result->close();
            return true;
        } 
    }
    
    /**
     * Conte a quantidade de sócios das sociedades por terem ou não filhos, conforme o sexo dos sócios
     * 
     * @param array $count array com os dados
     * @param string $sociedades ids das sociedades
     * @return bool
     */
    public function countByTemFilhosAndSexoForSociedades(&$count, $sociedades): bool
    {
        $query = "select 
                        s.sociedade as sociedade,
                        count(s.id) as total, 
                        p.tem_filhos as tem_filhos,
                        p.sexo as sexo
                    from 
                        socios as s, 
                        pessoas as p 
                    where 
                        s.sociedade in ($sociedades) and
                        s.stat = 'ATV' and
                        s.admin <> 'S' and
                        s.pessoa = p.id 
                    group by sociedade, tem_filhos, sexo";
        
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
                if(!isset($count[$result->fields['tem_filhos'].'_'.$result->fields['sexo']])) {
                    $count[$result->fields['tem_filhos'].'_'.$result->fields['sexo']] = array();
                }
                
                if(!isset($count[$result->fields['tem_filhos'].'_'.$result->fields['sexo']][$result->fields['sociedade']])) {
                    $count[$result->fields['tem_filhos'].'_'.$result->fields['sexo']][$result->fields['sociedade']] = array();
                }
                
                $count[$result->fields['tem_filhos'].'_'.$result->fields['sexo']][$result->fields['sociedade']] = array(
                    'sociedade' => $result->fields['sociedade'],
                    'total' => (int) $result->fields['total'],
                    'tem_filhos' => $result->fields['tem_filhos'],
                    'sexo' => $result->fields['sexo']
                );
                $result->MoveNext();
            }

            $result->close();
            return true;
        } 
    }
    
    /**
     * Conte a quantidade de sócios por necessidades especiais
     * 
     * @param array $count array com os dados
     * @param string $sociedade id da sociedade
     * @return bool
     */
    public function countByNecessidade(&$count, $sociedade): bool
    {
        $query = "select 
                        count(s.id) as total, 
                        np.necessidade as necessidade
                    from 
                        socios as s, 
                        necessidades_das_pessoas as np 
                    where 
                        s.sociedade = '$sociedade' and
                        s.stat = 'ATV' and
                        s.admin <> 'S' and
                        s.pessoa = np.pessoa 
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
     * Conte a quantidade de sócios das sociedades por necessidades especiais
     * 
     * @param array $count array com os dados
     * @param string $sociedades ids das sociedades
     * @return bool
     */
    public function countByNecessidadeForSociedades(&$count, $sociedades): bool
    {
        $query = "select 
                        s.sociedade as sociedade,
                        count(s.id) as total, 
                        np.necessidade as necessidade
                    from 
                        socios as s, 
                        necessidades_das_pessoas as np 
                    where 
                        s.sociedade in ($sociedades) and
                        s.stat = 'ATV' and
                        s.admin <> 'S' and
                        s.pessoa = np.pessoa 
                    group by sociedade, necessidade";
        
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
                if(!isset($count[$result->fields['necessidade']])) {
                    $count[$result->fields['necessidade']] = array();
                }
                
                if(!isset($count[$result->fields['necessidade']][$result->fields['sociedade']])) {
                    $count[$result->fields['necessidade']][$result->fields['sociedade']] = array();
                }
                
                $count[$result->fields['necessidade']][$result->fields['sociedade']] = array(
                    'sociedade' => $result->fields['sociedade'],
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
     * Conte a quantidade de sócios por doação
     * 
     * @param array $count array com os dados
     * @param string $sociedade id da sociedade
     * @return bool
     */
    public function countByDoacao(&$count, $sociedade): bool
    {
        $query = "select 
                        count(s.id) as total, 
                        d.doacao as doacao
                    from 
                        socios as s, 
                        doadores as d 
                    where 
                        s.sociedade = '$sociedade' and
                        s.stat = 'ATV' and
                        s.admin <> 'S' and
                        s.pessoa = d.pessoa 
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
     * Conte a quantidade de sócios das sociedades por doação
     * 
     * @param array $count array com os dados
     * @param string $sociedades ids das sociedades
     * @return bool
     */
    public function countByDoacaoForSociedades(&$count, $sociedades): bool
    {
        $query = "select 
                        s.sociedade as sociedade,
                        count(s.id) as total, 
                        d.doacao as doacao
                    from 
                        socios as s, 
                        doadores as d 
                    where 
                        s.sociedade in ($sociedades) and
                        s.stat = 'ATV' and
                        s.admin <> 'S' and
                        s.pessoa = d.pessoa 
                    group by sociedade, doacao";
        
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
                if(!isset($count[$result->fields['doacao']])) {
                    $count[$result->fields['doacao']] = array();
                }
                
                if(!isset($count[$result->fields['doacao']][$result->fields['sociedade']])) {
                    $count[$result->fields['doacao']][$result->fields['sociedade']] = array();
                }
                
                $count[$result->fields['doacao']][$result->fields['sociedade']] = array(
                    'sociedade' => $result->fields['sociedade'],
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
     * Conte a quantidade de sócios por arrolamento
     * 
     * @param array $count array com os dados
     * @param string $sociedade id da sociedade
     * @return bool
     */
    public function countByArrolamento(&$count, $sociedade): bool
    {
        $query = "select 
                        count(s.id) as total, 
                        s.cooperador as cooperador
                    from 
                        socios as s
                    where 
                        s.sociedade = '$sociedade' and
                        s.stat = 'ATV' and
                        s.admin <> 'S' 
                    group by cooperador";
        
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
                $count[$result->fields['cooperador']] = array(
                    'total' => (int) $result->fields['total'],
                    'cooperador' => $result->fields['cooperador']
                );
                $result->MoveNext();
            }

            $result->close();
            return true;
        } 
    }
    
    /**
     * Conte a quantidade de sócios das sociedades por arrolamento
     * 
     * @param array $count array com os dados
     * @param string $sociedades ids das sociedades
     * @return bool
     */
    public function countByArrolamentoForSociedades(&$count, $sociedades): bool
    {
        $query = "select 
                        s.sociedade as sociedade,
                        count(s.id) as total, 
                        s.cooperador as cooperador
                    from 
                        socios as s
                    where 
                        s.sociedade in ($sociedades) and
                        s.stat = 'ATV' and
                        s.admin <> 'S' 
                    group by sociedade, cooperador";
        
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
                if(!isset($count[$result->fields['cooperador']])) {
                    $count[$result->fields['cooperador']] = array();
                }
                
                if(!isset($count[$result->fields['cooperador']][$result->fields['sociedade']])) {
                    $count[$result->fields['cooperador']][$result->fields['sociedade']] = array();
                }
                
                $count[$result->fields['cooperador']][$result->fields['sociedade']] = array(
                    'sociedade' => $result->fields['sociedade'],
                    'total' => (int) $result->fields['total'],
                    'cooperador' => $result->fields['cooperador']
                );
                $result->MoveNext();
            }

            $result->close();
            return true;
        } 
    }
    
} 
