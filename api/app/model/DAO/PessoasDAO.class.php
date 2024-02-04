<?php

/**
 * Classe para as interações do Pessoas com o banco de dados. 
 * 
 */
class PessoasDAO extends BaseDAO
{
    protected $dto_object = "Pessoas";

    /**
     * Gera uma instância do objeto DTO
     */
    public function getDTO()
    {
        return new PessoasDTO();
    }

    public function __construct()
    {
        parent::connect();
    }
    
    
    /* específico */
    
    /**
     * Mapeie os nomes das pessoas pelo id
     * 
     * @param array $map array que receberá o mapeamento
     * @return bool
     */
    public function mapNomesById(&$map): bool
    {
        $query = "select 
                        p.id as id, 
                        u.nome as nome
                    from 
                        pessoas as p, 
                        usuarios as u 
                    where p.id = u.id "; 

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
                    'nome' => $result->fields['nome']
                );
                $result->MoveNext();
            }

            $result->close();
            return true;
        }    
    }
    
    /**
     * Mapeie nome, email, sexo, data de nascimento, estado_civil, escolaridade, 
     * se tem ou não filhos, telefone e celular (1 e 2) das pessoas pelo id
     * 
     * @param array $map array que receberá o mapeamento
     * @return bool
     */
    public function mapBasicDataById(&$map): bool
    {
        $query = "select 
                        p.id as id, 
                        u.nome as nome,
                        u.perfil as perfil, 
                        p.data_nascimento as data_nascimento,
                        u.email as email,
                        p.telefone as telefone,
                        p.celular_1 as celular_1,
                        p.celular_2 as celular_2,
                        p.sexo as sexo,
                        p.estado_civil as estado_civil,
                        p.escolaridade as escolaridade,
                        p.tem_filhos as tem_filhos
                    from 
                        pessoas as p inner join usuarios as u on u.id = p.id
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
                    'nome' => $result->fields['nome'],
                    'perfil' => $result->fields['perfil'],
                    'data_nascimento' => $result->fields['data_nascimento'],
                    'email' => $result->fields['email'],
                    'telefone' => $result->fields['telefone'],
                    'celular_1' => $result->fields['celular_1'],
                    'celular_2' => $result->fields['celular_2'],
                    'sexo' => $result->fields['sexo'],
                    'estado_civil' => $result->fields['estado_civil'],
                    'escolaridade' => $result->fields['escolaridade'],
                    'tem_filhos' => $result->fields['tem_filhos']
                );
                $result->MoveNext();
            }

            $result->close();
            return true;
        }    
    }
    
    /**
     * Mapeie os nomes das pessoas pelo id conforme o parâmetro passado
     * 
     * @param array $map array que receberá o mapeamento
     * @param object $dto filtro
     * @return bool
     */
    public function mapNomesByIdWithParam(&$map, &$dto): bool
    {
        $query = "select 
                        p.id as id, 
                        u.nome as nome
                    from 
                        pessoas as p, 
                        usuarios as u 
                    where p.id = u.id "; 
        
        $where = '';

        $wheres = array(
            'AND' => array(),
            'OR' => array()
        );
        
        $vars = get_object_vars($dto);
        foreach($vars as $v => $val)
        {
            if($v != 'ignore')
            {
                if(!in_array($v, $dto->ignore))
                {
                    if($dto->{$v} != VOID)
                    {
                        if(is_array($dto->{$v})) {
                            foreach($dto->{$v} as $dto_v) {
                                $wheres = $this->mountComparison($wheres, $dto_v, $v);
                            }
                        }
                        else {
                            $wheres = $this->mountComparison($wheres, $dto->{$v}, $v);
                        }
                    }
                }
            }
        }

        foreach($wheres['AND'] as $w) {
            if(!empty($where))
            {
                $where .= " AND ";
            }
            $where .= $w;
        }

        if(!empty($wheres['OR'])) {
            if(!empty($where))
            {
                $where .= " AND (";
            }
            
            $first = true;
            foreach($wheres['OR'] as $w) {
                if(!empty($where) && !$first)
                {
                    $where .= " OR ";
                }
                $where .= $w;
                $first = false;
            }
            
            if(!empty($wheres['AND'])) {
                $where .= ")";
            }
        }
        
        if(!empty($where))
        {
            $query .= ' and ' . $where;
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
                $map[$result->fields['id']] = array(
                    'nome' => $result->fields['nome']
                );
                $result->MoveNext();
            }

            $result->close();
            return true;
        }    
    }
    
    /**
     * Mapeie nome, email, sexo, data de nascimento, estado_civil, escolaridade, 
     * se tem ou não filhos, telefone e celular (1 e 2) das pessoas pelo id conforme o parâmetro passado
     * 
     * @param array $map array que receberá o mapeamento
     * @param object $dto filtro
     * @return bool
     */
    public function mapBasicDataByIdWithParam(&$map, &$dto): bool
    {
        $query = "select 
                        p.id as id, 
                        u.nome as nome, 
                        u.perfil as perfil, 
                        p.data_nascimento as data_nascimento,
                        u.email as email,
                        p.telefone as telefone,
                        p.celular_1 as celular_1,
                        p.celular_2 as celular_2,
                        p.sexo as sexo,
                        p.estado_civil as estado_civil,
                        p.escolaridade as escolaridade,
                        p.tem_filhos as tem_filhos
                    from 
                        pessoas as p inner join usuarios as u on u.id = p.id
                    where "; 
        
        $where = '';

        $wheres = array(
            'AND' => array(),
            'OR' => array()
        );
        
        $vars = get_object_vars($dto);
        foreach($vars as $v => $val)
        {
            if($v != 'ignore')
            {
                if(!in_array($v, $dto->ignore))
                {
                    if($dto->{$v} != VOID)
                    {
                        if(is_array($dto->{$v})) {
                            foreach($dto->{$v} as $dto_v) {
                                $wheres = $this->mountComparison($wheres, $dto_v, $v);
                            }
                        }
                        else {
                            $wheres = $this->mountComparison($wheres, $dto->{$v}, $v);
                        }
                    }
                }
            }
        }

        foreach($wheres['AND'] as $w) {
            if(!empty($where))
            {
                $where .= " AND ";
            }
            $where .= $w;
        }

        if(!empty($wheres['OR'])) {
            if(!empty($where))
            {
                $where .= " AND (";
            }
            
            $first = true;
            foreach($wheres['OR'] as $w) {
                if(!empty($where) && !$first)
                {
                    $where .= " OR ";
                }
                $where .= $w;
                $first = false;
            }
            
            if(!empty($wheres['AND'])) {
                $where .= ")";
            }
        }
        
        if(!empty($where))
        {
            $query .= $where;
        }
        
        /* filtro especial: múltiplas faixas de idade */
        if(property_exists($dto, 'faixas_idade')) 
        {
            if(!is_null($dto->faixas_idade) && !empty($dto->faixas_idade)) {
                $faixas_sql = '';
                foreach($dto->faixas_idade->value as $faixa) {
                    $faixa_block = '';
                    if(property_exists($faixa, 'ini')) {
                        $faixa_block .= "DATE(data_nascimento) >= '{$faixa->ini}' and ";
                    }
                    $faixa_block .= "DATE(data_nascimento) <= '{$faixa->end}'";
                    if(!empty($faixas_sql)) {
                        $faixas_sql .= ' or ';
                    }
                    $faixas_sql .= "($faixa_block)";
                }
                
                if(!empty($where))
                {
                    $query .= ' and ';
                }
                
                $query .= " ($faixas_sql)";
            }
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
                $map[$result->fields['id']] = array(
                    'nome' => $result->fields['nome'],
                    'perfil' => $result->fields['perfil'],
                    'data_nascimento' => $result->fields['data_nascimento'],
                    'email' => $result->fields['email'],
                    'telefone' => $result->fields['telefone'],
                    'celular_1' => $result->fields['celular_1'],
                    'celular_2' => $result->fields['celular_2'],
                    'sexo' => $result->fields['sexo'],
                    'estado_civil' => $result->fields['estado_civil'],
                    'escolaridade' => $result->fields['escolaridade'],
                    'tem_filhos' => $result->fields['tem_filhos']
                );
                $result->MoveNext();
            }

            $result->close();
            return true;
        }    
    }
    
} 

