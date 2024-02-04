<?php


class BaseDAO extends DBO
{
    protected $dto_object = "";

    protected function getDTO()
    {
        
    }

    /**
     * Gere um log da informação
     * 
     * @param type $info informação para o log
     * @return void 
     */
    protected function logThisInfo($info): void
    {
        if(LOG_LEVEL == 0) { return; }
        if(LOG_LEVEL == 1 || LOG_LEVEL == 3) {
            error_log($info);
        }
        if(LOG_LEVEL == 2 || LOG_LEVEL == 3) {
            NblLogUtil::log(LOG_PATH, $info);
        }
    }

    /**
     * Conecta no banco
     */
    public function connect()
    {
        parent::connect();
    }
    
    /**
     * Adiciona os dados do objeto DTO ao banco
     * 
     * @param object $dto dados a serem adicionados
     * @param object $ado objeto façade com a lista de erros
     * @return bool
     */
    public function add(&$dto, &$ado): bool
    {
        global $dao_config;

        $adds = array();
        $fields = array();
        $errs = array();

        // obtem as tabela pelo DTO
        $tables = $dao_config->getDBTables($this->dto_object);

        // gera o array associativo de adição pelas tabelas
        foreach($tables as $t) {
            $adds[$t] = '';
            $fields[$t] = '';
        }

        // itere sobre o objeto DTO para obter os dados
        $vars = get_object_vars($dto);
        foreach($vars as $f => $val)
        {
            if($f != 'ignore')
            {
                if(!in_array($f, $dto->ignore))
                {
                    // veja em qual tabela o campo está
                    $db_tables = $dao_config->getTableOfField($this->dto_object, $tables, $f);
                    if(!empty($db_tables)) {
                        foreach($db_tables as $db_table) {
                            // veja se o campo não é autogerado
                            if(!$dao_config->checkIfFieldIsNotAuto($this->dto_object, $db_table, $f)) {
                                // não é. Adicione na lista de campos da tabela
                                if(!empty($fields[$db_table])) {
                                    $fields[$db_table] .= ", ";
                                }
                                $fields[$db_table] .= $f;
                                // obtem o valor default caso haja algum setado 
                                $value = $dto->{$f};
                                $value = $dao_config->getFieldValueOrDefault($this->dto_object, $db_table, $f, $value);
                                // valida o valor 
                                if($dao_config->isFieldValid($this->dto_object, $db_table, $f, $value, $errs)) {
                                    // gera o add da tabela correta
                                    if(!empty($adds[$db_table]))
                                    {
                                        $adds[$db_table] .= ", ";
                                    }

                                    if(!is_null($value))
                                    {
                                        $adds[$db_table] .= " '".$value."'";
                                    }
                                    else
                                    {
                                        $adds[$db_table] .= " NULL";
                                    }
                                }
                                else {
                                    // campo inválido. Impeça a inserção
                                    $ado->setErrs($errs);
                                    return false;
                                }
                            }
                        }
                    }
                }
            }
        }

        // gere a query
        $this->con->beginTrans();
        foreach($adds as $t => $add) {
            $query = "insert into {$t}({$fields[$t]}) values ($add)";
            if(LOG_QUERY) {
                $this->logThisInfo($query);
            }
            if(!$this->con->Execute($query)) {
                $this->err_msg = '[tabela] '. $t . ' ::. ' . $this->con->ErrorMsg();
                if(LOG_DB_ERRS)
                {
                    $this->logThisInfo($this->err_msg);
                }
                $this->con->rollbackTrans();
                return false;
            }
        }
        $this->con->commitTrans();
        return true;   
    }
    
    /**
     * Edita
     * 
     * @param object $dto dados a serem alterados
     * @param object $ado objeto façade com a lista de erros
     * @return bool
     */
    public function edit(&$dto, &$ado): bool
    {
        global $dao_config;

        $edits = array();
        $errs = array();
        
        // obtem as tabela pelo DTO
        $tables = $dao_config->getDBTables($this->dto_object);

        // gera o array associativo de adição pelas tabelas
        foreach($tables as $t) {
            $edits[$t] = '';
        }

        // itere sobre o objeto DTO para obter os dados
        $vars = get_object_vars($dto);
        foreach($vars as $v => $val)
        {
            if($v != 'ignore')
            {
                if(!in_array($v, $dto->ignore))
                {
                    if($dto->{$v} != VOID)
                    {
                        // veja em qual tabela o campo está
                        $db_tables = $dao_config->getTableOfField($this->dto_object, $tables, $v);
                        if(!empty($db_tables)) {
                            foreach($db_tables as $db_table) {
                                // veja se o campo não é autogerado
                                if(!$dao_config->checkIfFieldIsNotAuto($this->dto_object, $db_table, $v)) {
                                    // não é. Continue o processamento
                                    // obtem o valor default caso haja algum setado 
                                    $value = $dto->{$v};
                                    $value = $dao_config->getFieldValueOrDefault($this->dto_object, $db_table, $v, $value);
                                    // valida o valor 
                                    if($dao_config->isFieldValid($this->dto_object, $db_table, $v, $value, $errs)) {
                                        // gera o edit da tabela correta
                                        if(!empty($edits[$db_table]))
                                        {
                                            $edits[$db_table] .= ", ";
                                        }

                                        if(!is_null($value))
                                        {
                                            $edits[$db_table] .= " {$v} = '".$value."'";
                                        }
                                        else
                                        {
                                            $edits[$db_table] .= " {$v} = NULL";
                                        }
                                    }
                                    else {
                                        // campo inválido. Impeça a inserção
                                        $ado->setErrs($errs);
                                        return false;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        // gere a query
        $this->con->beginTrans();
        foreach($edits as $t => $edit) {
            $query = "update {$t} set $edit ";
            $where = '';
            // o where com as chaves
            $pks = $dao_config->getDBTablePKs($this->dto_object, $t);
            if(!empty($pks)) {
                foreach($pks as $pk) {
                    if(!empty($where)) {
                        $where .= ' and ';
                    }
                    $where .= " {$pk} = '".$dto->{$pk}."' ";
                }
            }

            if(!empty($where)) {
                $query .= ' where ' . $where;
            }
            else {
                // tentativa de edição sem where. Impeça!
                $errs[] = 'Não é seguro executar uma edição sem WHERE. Verifique a configuração de chaves primárias dos objetos';
                $ado->setErrs($errs);
                return false;
            }
            
            if(LOG_QUERY) {
                $this->logThisInfo($query);
            }
            if(!$this->con->Execute($query)) {
                $this->err_msg = '[tabela] '. $t . ' ::. ' . $this->con->ErrorMsg();
                if(LOG_DB_ERRS)
                {
                    $this->logThisInfo($this->err_msg);
                }
                $this->con->rollbackTrans();
                return false;
            }
        }
        $this->con->commitTrans();
        return true;     
    }

    /**
     * Edita apenas os campos que foram setados no DTO
     * 
     * @param object $dto dados a serem alterados
     * @param object $ado objeto façade com a lista de erros
     * @return bool
     */
    public function change(&$dto, &$ado): bool
    {
        global $dao_config;

        $edits = array();
        $errs = array();
        
        // obtem as tabela pelo DTO
        $tables = $dao_config->getDBTables($this->dto_object);

        // gera o array associativo de adição pelas tabelas
        foreach($tables as $t) {
            $edits[$t] = '';
        }

        // itere sobre o objeto DTO para obter os dados
        $vars = get_object_vars($dto);
        foreach($vars as $v => $val)
        {
            if($v != 'ignore')
            {
                if(!in_array($v, $dto->ignore))
                {
                    if($dto->{$v} != VOID)
                    {
                        // veja em qual tabela o campo está
                        $db_tables = $dao_config->getTableOfField($this->dto_object, $tables, $v);
                        if(!empty($db_tables)) {
                            foreach($db_tables as $db_table) {
                                // obtem o valor default caso haja algum setado 
                                $value = $dto->{$v};
                                $value = $dao_config->getFieldValueOrDefault($this->dto_object, $db_table, $v, $value);
                                // valida o valor 
                                if($dao_config->isFieldValid($this->dto_object, $db_table, $v, $value, $errs)) {
                                    // gera o edit da tabela correta
                                    if(!empty($edits[$db_table]))
                                    {
                                        $edits[$db_table] .= ", ";
                                    }
                                        
                                    if(!is_null($value))
                                    {
                                        $edits[$db_table] .= " {$v} = '".$value."'";
                                    }
                                    else
                                    {
                                        $edits[$db_table] .= " {$v} = NULL";
                                    }
                                }
                                else {
                                    // campo inválido. Impeça a inserção
                                    $ado->setErrs($errs);
                                    return false;
                                }
                            }
                        }
                    }
                }
            }
        }

        // gere a query
        $this->con->beginTrans();
        foreach($edits as $t => $edit) {
            $query = "update {$t} set $edit ";
            $where = '';
            // o where com as chaves
            $pks = $dao_config->getDBTablePKs($this->dto_object, $t);
            if(!empty($pks)) {
                foreach($pks as $pk) {
                    if(!empty($where)) {
                        $where .= ' and ';
                    }
                    $where .= " {$pk} = '".$dto->{$pk}."' ";
                }
            }

            if(!empty($where)) {
                $query .= ' where ' . $where;
            }
            else {
                // tentativa de edição sem where. Impeça!
                $errs[] = 'Não é seguro executar uma edição sem WHERE. Verifique a configuração de chaves primárias dos objetos';
                $ado->setErrs($errs);
                return false;
            }
            
            if(LOG_QUERY) {
                $this->logThisInfo($query);
            }
            if(!$this->con->Execute($query)) {
                $this->err_msg = '[tabela] '. $t . ' ::. ' . $this->con->ErrorMsg();
                if(LOG_DB_ERRS)
                {
                    $this->logThisInfo($this->err_msg);
                }
                $this->con->rollbackTrans();
                return false;
            }
        }
        $this->con->commitTrans();
        return true; 
    }
    
    /**
     * Remove
     * 
     * @param object $dto objeto DTO que contém os parâmetros para remoção
     * @param object $ado objeto façade com a lista de erros
     * @return bool
     */
    public function delete(&$dto, &$ado): bool
    {
        global $dao_config;

        $deletes = array();
        $errs = array();
        
        // obtem as tabela pelo DTO
        $tables = $dao_config->getDBTables($this->dto_object);

        // gera o array associativo de adição pelas tabelas
        foreach($tables as $t) {
            $deletes[$t] = '';
        }

        // gere a query
        $this->con->beginTrans();
        foreach($deletes as $t => $d) {
            $query = "delete from {$t}  ";
            $where = '';
            // o where com as chaves
            $pks = $dao_config->getDBTablePKs($this->dto_object, $t);
            if(!empty($pks)) {
                foreach($pks as $pk) {
                    if(!empty($where)) {
                        $where .= ' and ';
                    }
                    $where .= " {$pk} = '".$dto->{$pk}."' ";
                }
            }

            if(!empty($where)) {
                $query .= ' where ' . $where;
            }
            else {
                // tentativa de edição sem where. Impeça!
                $errs[] = 'Não é seguro executar uma remoção sem WHERE. Verifique a configuração de chaves primárias dos objetos';
                $ado->setErrs($errs);
                return false;
            }
            
            if(LOG_QUERY) {
                $this->logThisInfo($query);
            }
            if(!$this->con->Execute($query)) {
                $this->err_msg = '[tabela] '. $t . ' ::. ' . $this->con->ErrorMsg();
                if(LOG_DB_ERRS)
                {
                    $this->logThisInfo($this->err_msg);
                }
                $this->con->rollbackTrans();
                return false;
            }
        }
        $this->con->commitTrans();
        return true;    
    }
    
    /**
     * Busca pela chave primária
     * 
     * @param object $dto filtro e objeto DTO que receberá o resultado
     * @return bool
     */
    public function search(&$dto): bool
    {
        global $dao_config;

        $from = $dao_config->getFromSQL($this->dto_object);
        $fields = $dao_config->getFieldsOfDBTables($this->dto_object);
        $fields_select = NblPHPUtil::Array2CSV($fields, false);

        $query = "select {$fields_select} from {$from} "; 
        $where = '';
        $pks = $dao_config->getDBTablePKs4WhereSQL($this->dto_object);
        if(!empty($pks)) {
            foreach($pks as $pk) {
                if(!empty($where)) {
                    $where .= ' and ';
                }
                $where .= " {$pk['key']} = '".$dto->{$pk['name']}."' ";
            }
        }

        if(!empty($where)) {
            $query .= ' where ' . $where;
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
            // teste se há resultado
            if($result->EOF)
            {
                return false;
            }

            // teste as chaves primárias.
            foreach($pks as $pk) {
                if(is_null($result->fields[$pk['name']]))
                {
                    return false;
                }
            }

            $vars = get_object_vars($dto);
            foreach($vars as $v => $val)
            {
                if(array_key_exists($v,$result->fields))
                {
                    // em caso de join, alguns campos podem repetir nos resultados
                    if($dto->{$v} == VOID) {
                        $dto->{$v} = $result->fields[$v];
                    }
                }
            }
            
            $result->close();
            return true;
        }    
    }

    /**
     * Monta a comparação de campos 
     * 
     * @param array $wheres array com os condicionais SQL
     * @param object $dto_v objeto com as configurações do condicional (tipo e valor)
     * @param string $v campo que será comparado
     * @return array
     */
    protected function mountComparison($wheres, $dto_v, $v): array
    {
        if($dto_v->cmp == CMP_EQUAL)
        {
            $wheres[$dto_v->cmp_operation][] = " {$v} = '" . $dto_v->value . "' ";
        }
        else if($dto_v->cmp == CMP_INCLUDE_LEFT)
        {
            $wheres[$dto_v->cmp_operation][] = " {$v} like '" . $dto_v->value . "%' ";
        }
        else if($dto_v->cmp == CMP_INCLUDE_RIGHT)
        {
            $wheres[$dto_v->cmp_operation][] = " {$v} like '%" . $dto_v->value . "' ";
        }
        else if($dto_v->cmp == CMP_INCLUDE_INSIDE)
        {
            $wheres[$dto_v->cmp_operation][] = " {$v} like '%" . $dto_v->value . "%' ";
        }
        else if($dto_v->cmp == CMP_LESSER)
        {
            $wheres[$dto_v->cmp_operation][] = " {$v} < '" . $dto_v->value . "' ";
        }
        else if($dto_v->cmp == CMP_LESSER_THEN)
        {
            $wheres[$dto_v->cmp_operation][] = " {$v} <= '" . $dto_v->value . "' ";
        }
        else if($dto_v->cmp == CMP_GREATER)
        {
            $wheres[$dto_v->cmp_operation][] = " {$v} > '" . $dto_v->value . "' ";
        }
        else if($dto_v->cmp == CMP_GREATER_THEN)
        {
            $wheres[$dto_v->cmp_operation][] = " {$v} >= '" . $dto_v->value . "' ";
        }
        else if($dto_v->cmp == CMP_EQUAL_DATE)
        {
            $wheres[$dto_v->cmp_operation][] = " DATE({$v}) = '" . $dto_v->value . "' ";
        }
        else if($dto_v->cmp == CMP_GREATER_DATE)
        {
            $wheres[$dto_v->cmp_operation][] = " DATE({$v}) > '" . $dto_v->value . "' ";
        }
        else if($dto_v->cmp == CMP_GREATER_THEN_DATE)
        {
            $wheres[$dto_v->cmp_operation][] = " DATE({$v}) >= '" . $dto_v->value . "' ";
        }
        else if($dto_v->cmp == CMP_LESSER_DATE)
        {
            $wheres[$dto_v->cmp_operation][] = " DATE({$v}) < '" . $dto_v->value . "' ";
        }
        else if($dto_v->cmp == CMP_LESSER_THEN_DATE)
        {
            $wheres[$dto_v->cmp_operation][] = " DATE({$v}) <= '" . $dto_v->value . "' ";
        }
        else if($dto_v->cmp == CMP_EQUAL_TIME)
        {
            $wheres[$dto_v->cmp_operation][] = " TIME({$v}) = '" . $dto_v->value . "' ";
        }
        else if($dto_v->cmp == CMP_GREATER_TIME)
        {
            $wheres[$dto_v->cmp_operation][] = " TIME({$v}) > '" . $dto_v->value . "' ";
        }
        else if($dto_v->cmp == CMP_GREATER_THEN_TIME)
        {
            $wheres[$dto_v->cmp_operation][] = " TIME({$v}) >= '" . $dto_v->value . "' ";
        }
        else if($dto_v->cmp == CMP_LESSER_TIME)
        {
            $wheres[$dto_v->cmp_operation][] = " TIME({$v}) < '" . $dto_v->value . "' ";
        }
        else if($dto_v->cmp == CMP_LESSER_THEN_TIME)
        {
            $wheres[$dto_v->cmp_operation][] = " TIME({$v}) <= '" . $dto_v->value . "' ";
        }
        else if($dto_v->cmp == CMP_EQUAL_MONTH)
        {
            $wheres[$dto_v->cmp_operation][] = " MONTH({$v}) = '" . $dto_v->value . "' ";
        }
        else if($dto_v->cmp == CMP_GREATER_MONTH)
        {
            $wheres[$dto_v->cmp_operation][] = " MONTH({$v}) > '" . $dto_v->value . "' ";
        }
        else if($dto_v->cmp == CMP_GREATER_THEN_MONTH)
        {
            $wheres[$dto_v->cmp_operation][] = " MONTH({$v}) >= '" . $dto_v->value . "' ";
        }
        else if($dto_v->cmp == CMP_LESSER_MONTH)
        {
            $wheres[$dto_v->cmp_operation][] = " MONTH({$v}) < '" . $dto_v->value . "' ";
        }
        else if($dto_v->cmp == CMP_LESSER_THEN_MONTH)
        {
            $wheres[$dto_v->cmp_operation][] = " MONTH({$v}) <= '" . $dto_v->value . "' ";
        }
        else if($dto_v->cmp == CMP_EQUAL_YEAR)
        {
            $wheres[$dto_v->cmp_operation][] = " YEAR({$v}) = '" . $dto_v->value . "' ";
        }
        else if($dto_v->cmp == CMP_GREATER_YEAR)
        {
            $wheres[$dto_v->cmp_operation][] = " YEAR({$v}) > '" . $dto_v->value . "' ";
        }
        else if($dto_v->cmp == CMP_GREATER_THEN_YEAR)
        {
            $wheres[$dto_v->cmp_operation][] = " YEAR({$v}) >= '" . $dto_v->value . "' ";
        }
        else if($dto_v->cmp == CMP_LESSER_YEAR)
        {
            $wheres[$dto_v->cmp_operation][] = " YEAR({$v}) < '" . $dto_v->value . "' ";
        }
        else if($dto_v->cmp == CMP_LESSER_THEN_YEAR)
        {
            $wheres[$dto_v->cmp_operation][] = " YEAR({$v}) <= '" . $dto_v->value . "' ";
        }
        else if($dto_v->cmp == CMP_EQUAL_DAY)
        {
            $wheres[$dto_v->cmp_operation][] = " DAY({$v}) = '" . $dto_v->value . "' ";
        }
        else if($dto_v->cmp == CMP_GREATER_DAY)
        {
            $wheres[$dto_v->cmp_operation][] = " DAY({$v}) > '" . $dto_v->value . "' ";
        }
        else if($dto_v->cmp == CMP_GREATER_THEN_DAY)
        {
            $wheres[$dto_v->cmp_operation][] = " DAY({$v}) >= '" . $dto_v->value . "' ";
        }
        else if($dto_v->cmp == CMP_LESSER_DAY)
        {
            $wheres[$dto_v->cmp_operation][] = " DAY({$v}) < '" . $dto_v->value . "' ";
        }
        else if($dto_v->cmp == CMP_LESSER_THEN_DAY)
        {
            $wheres[$dto_v->cmp_operation][] = " DAY({$v}) <= '" . $dto_v->value . "' ";
        }
        else if($dto_v->cmp == CMP_IN_LIST)
        {
            $wheres[$dto_v->cmp_operation][] = " {$v} IN (" . $dto_v->value . ") ";
        }
        else if($dto_v->cmp == CMP_NOT_IN_LIST)
        {
            $wheres[$dto_v->cmp_operation][] = " {$v} NOT IN (" . $dto_v->value . ") ";
        }
        else if($dto_v->cmp == CMP_DIFF)
        {
            $wheres[$dto_v->cmp_operation][] = " {$v} <> '" . $dto_v->value . "' ";
        }
        else if($dto_v->cmp == CMP_IS_NULL)
        {
            $wheres[$dto_v->cmp_operation][] = " {$v} IS NULL  ";
        }
        else if($dto_v->cmp == CMP_NOT_NULL)
        {
            $wheres[$dto_v->cmp_operation][] = " {$v} IS NOT NULL  ";
        }
        else if($dto_v->cmp == CMP_IS_EMPTY)
        {
            $wheres[$dto_v->cmp_operation][] = " {$v} = '' ";
        }
        else if($dto_v->cmp == CMP_NOT_EMPTY)
        {
            $wheres[$dto_v->cmp_operation][] = " {$v} <> '' ";
        }

        return $wheres;
    }

    /**
     * Monta os AND e OR dentro do where SQL
     * 
     * @param array $wheres array com as subpartes do where
     * @param string $where string com o where
     * @return string
     */
    private function mountWhereAndOr($wheres, $where): string
    {
        foreach($wheres['AND'] as $w) {
            if(!empty($where))
            {
                $where .= " AND ";
            }
            $where .= $w;
        }

        if(!empty($wheres['OR'])) {
            if(count($wheres['OR']) == 1)
            {
                if(!empty($where))
                {
                    $where .= " OR ";
                }
                
                $where .= $wheres['OR'][0];
            }
            else
            {
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
        }
        
        return $where;
    }

    /**
     * Busca por outro campo (apenas um resultado)
     * 
     * @param object $dto filtro e objeto DTO que receberá o resultado
     * @return bool
     */
    public function searchBy(&$dto): bool
    {
        global $dao_config;

        $from = $dao_config->getFromSQL($this->dto_object);
        $pks = $dao_config->getDBTablePKs4WhereSQL($this->dto_object);
        $fields = $dao_config->getFieldsOfDBTables($this->dto_object);
        $fields_select = NblPHPUtil::Array2CSV($fields, false);
        
        $query = "select {$fields_select} from {$from} "; 
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

        $where .= $this->mountWhereAndOr($wheres, $where);
        
        if(!empty($where))
        {
            $query .= ' where ' . $where;
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
            // teste se há resultado
            if($result->EOF)
            {
                return false;
            }
            
            // teste as chaves primárias.
            foreach($pks as $pk) {
                if(is_null($result->fields[$pk['name']]))
                {
                    return false;
                }
            }

            // limpe o DTO
            $dto->reset();

            $vars = get_object_vars($dto);
            foreach($vars as $v => $val)
            {
                if(array_key_exists($v, $result->fields))
                {
                    // em caso de join, alguns campos podem repetir nos resultados
                    if($dto->{$v} == VOID) {
                        $dto->{$v} = $result->fields[$v];
                    }
                }
            }
            
            $result->close();
            return true;
        }    
    }
    
    /**
     * Conte os resultados (pelas chaves primárias)
     * 
     * @param object $ado objeto ADO que contém a lista de resultados
     * @return bool
     */
    public function count(&$ado): bool
    {
        global $dao_config;

        $from = $dao_config->getFromSQL($this->dto_object);
        
        $query = "select count(*) as total from {$from} "; 

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
            if(is_null($result->fields['total']))
            {
                return false;
            }
            else
            {
                $ado->setCount($result->fields['total']);
                $result->close();
                return true;
            }
        }    
    }

    /**
     * Conte os resultados (pelo filtro fornecido)
     * 
     * @param object $ado objeto ADO que contém a lista de resultados
     * @param object $dto filtro
     * @return bool
     */
    public function countBy(&$ado, &$dto): bool
    {
        global $dao_config;

        $from = $dao_config->getFromSQL($this->dto_object);
        
        $query = "select count(*) as total from {$from} "; 
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

        $where .= $this->mountWhereAndOr($wheres, $where);
        
        if(!empty($where))
        {
            $query .= ' where ' . $where;
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
            if(is_null($result->fields['total']))
            {
                return false;
            }
            else
            {
                $ado->setCount($result->fields['total']);
                $result->close();
                return true;
            }
        }    
    }
    
    /**
     * Busque todos os resultados (sem filtro)
     * 
     * @param object $ado objeto ADO que contém a lista de resultados
     * @param string $page parâmetro opcional com o número da página
     * @param string $len parâmetro opcional com o tamanho da página
     * @return bool
     */
    public function searchAll(&$ado, $page = null, $len = null): bool
    {
        global $dao_config;

        $from = $dao_config->getFromSQL($this->dto_object);
        $fields = $dao_config->getFieldsOfDBTables($this->dto_object);
        $fields_select = NblPHPUtil::Array2CSV($fields, false);
        
        $query = "select {$fields_select} from {$from} "; 

        if(LOG_QUERY) {
            $this->logThisInfo($query);
        }

        if(!is_null($page)) 
        { 
            $query .= "limit {$page},{$len} "; 
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
                        // em caso de join, alguns campos podem repetir nos resultados
                        if($dto->{$v} == VOID) {
                            $dto->{$v} = $result->fields[$v];
                        }
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
    
    /**
     * Busque todos os resultados conforme filtro
     * 
     * @param object $ado objeto ADO que contém a lista de resultados
     * @param object $dto filtro
     * @param string $page parâmetro opcional com o número da página
     * @param string $len parâmetro opcional com o tamanho da página
     * @return bool
     */
    public function searchAllbyParam(&$ado, &$dto, $page = null, $len = null): bool
    {
        global $dao_config;

        $from = $dao_config->getFromSQL($this->dto_object);
        $fields = $dao_config->getFieldsOfDBTables($this->dto_object);
        $fields_select = NblPHPUtil::Array2CSV($fields, false);
        
        $query = "select {$fields_select} from {$from} "; 
        
        $where = '';
        $order = '';
        $group = '';

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
        
        $where .= $this->mountWhereAndOr($wheres, $where);
        
        if(!is_null($dto->order_by))
        {
            foreach($dto->order_by->fields as $v) 
            {
                if(!empty($order))
                {
                    $order .= ", " ;
                }

                $order .= $v;
            }
            
            $order .= ' '. $dto->order_by->mode;
        }

        if(!is_null($dto->group_by))
        {
            foreach($dto->group_by->fields as $v) 
            {
                if(!empty($group))
                {
                    $group .= ", " ;
                }

                $group .= $v;
            }
        }
        
        if(!empty($where))
        {
            $query .= ' where ' . $where;
        }
        
        if(!empty($order))
        {
            $query .= ' order by ' . $order;
        }
        
        if(!empty($group))
        {
            $query .= ' group by ' . $group;
        }
        
        if(!is_null($page)) 
        { 
            $query .= " limit {$page},{$len} "; 
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
                $dto = $this->getDTO();
                $vars = get_object_vars($dto);
                
                foreach($vars as $v => $val)
                {
                    if(array_key_exists($v,$result->fields))
                    {
                        // em caso de join, alguns campos podem repetir nos resultados
                        if($dto->{$v} == VOID) {
                            $dto->{$v} = $result->fields[$v];
                        }
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

    /* mapeamento */

    /**
     * Mapeie todos os dados conforme o parâmetro passado
     * 
     * @param array $map array que receberá o mapeamento
     * @param string $map_by parâmetro de mapeamento
     * @return bool
     */
    public function mapAllBy(&$map, $map_by): bool
    {
        global $dao_config;

        $from = $dao_config->getFromSQL($this->dto_object);
        $fields = $dao_config->getFieldsOfDBTables($this->dto_object);
        $fields_select = NblPHPUtil::Array2CSV($fields, false);
        
        $query = "select {$fields_select} from {$from} "; 

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
            $dto = $this->getDTO();
            $vars = get_object_vars($dto);

            while(!$result->EOF)
            {
                $local_map = array();
                foreach($vars as $v => $val)
                {
                    if(array_key_exists($v,$result->fields))
                    {
                        // em caso de join, alguns campos podem repetir nos resultados
                        if(!isset($local_map[$v])) {
                            $local_map[$v] = $result->fields[$v];
                        }
                    }
                }
                
                $map[$result->fields[$map_by]] = $local_map;
                $result->MoveNext();
            }

            $result->close();
            return true;
        }    
    }

    /**
     * Mapeie todos os dados conforme um hash dos parâmetros passados
     * 
     * @param array $map array que receberá o mapeamento
     * @param array $hash_params parâmetros para o hash de mapeamento de mapeamento
     * @return bool
     */
    public function mapAllbyHash(&$map, $hash_params): bool
    {
        global $dao_config;

        $from = $dao_config->getFromSQL($this->dto_object);
        $fields = $dao_config->getFieldsOfDBTables($this->dto_object);
        $fields_select = NblPHPUtil::Array2CSV($fields, false);
        
        $query = "select {$fields_select} from {$from} "; 

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
            $dto = $this->getDTO();
            $vars = get_object_vars($dto);

            while(!$result->EOF)
            {
                $local_map = array();
                $hash_source = '';
                foreach($vars as $v => $val)
                {
                    if(array_key_exists($v,$result->fields))
                    {
                        // em caso de join, alguns campos podem repetir nos resultados
                        if(!isset($local_map[$v])) {
                            $local_map[$v] = $result->fields[$v];
                            if(in_array($v, $hash_params)) {
                                // se o campo está como fonte do hash, armazene
                                $hash_source .= $result->fields[$v];
                            }
                        }
                    }
                }
                
                $map[sha1($hash_source)] = $local_map;
                $result->MoveNext();
            }

            $result->close();
            return true;
        } 
    }

    /**
     * Mapeie os dados (filtrados) conforme o parâmetro passado
     * 
     * @param array $map array que receberá o mapeamento
     * @param string $map_by parâmetro de mapeamento
     * @param object $dto filtro
     * @return bool
     */
    public function mapAllByWithParam(&$map, $map_by, &$dto): bool
    {
        global $dao_config;

        $from = $dao_config->getFromSQL($this->dto_object);
        $fields = $dao_config->getFieldsOfDBTables($this->dto_object);
        $fields_select = NblPHPUtil::Array2CSV($fields, false);
        
        $query = "select {$fields_select} from {$from} "; 
        
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

        $where .= $this->mountWhereAndOr($wheres, $where);
        
        if(!empty($where))
        {
            $query .= ' where ' . $where;
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
            $dto = $this->getDTO();
            $vars = get_object_vars($dto);
            
            while(!$result->EOF)
            {
                $local_map = array();
                foreach($vars as $v => $val)
                {
                    if(array_key_exists($v,$result->fields))
                    {
                        // em caso de join, alguns campos podem repetir nos resultados
                        if(!isset($local_map[$v])) {
                            $local_map[$v] = $result->fields[$v];
                        }
                    }
                }
                
                $map[$result->fields[$map_by]] = $local_map;
                $result->MoveNext();
            }

            $result->close();
            return true;
        }    
    }

    /**
     * Gere uma matriz de dados conforme o parâmetro passado
     * 
     * @param array $map array que receberá o mapeamento
     * @param string $map_by parâmetro de mapeamento
     * @return bool
     */
    public function multiMapAllBy(&$map, $map_by): bool
    {
        global $dao_config;

        $from = $dao_config->getFromSQL($this->dto_object);
        $fields = $dao_config->getFieldsOfDBTables($this->dto_object);
        $fields_select = NblPHPUtil::Array2CSV($fields, false);
        
        $query = "select {$fields_select} from {$from} "; 

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
            $dto = $this->getDTO();
            $vars = get_object_vars($dto);
            
            while(!$result->EOF)
            {
                $local_map = array();
                foreach($vars as $v => $val)
                {
                    if(array_key_exists($v,$result->fields))
                    {
                        // em caso de join, alguns campos podem repetir nos resultados
                        if(!isset($local_map[$v])) {
                            $local_map[$v] = $result->fields[$v];
                        }
                    }
                }
                
                if(!isset($map[$result->fields[$map_by]]))
                {
                    $map[$result->fields[$map_by]] = array();
                }

                $map[$result->fields[$map_by]][] = $local_map;
                $result->MoveNext();
            }

            $result->close();
            return true;
        }    
    }

    /**
     * Gere uma matriz de dados (filtrados) conforme o parâmetro passado
     * 
     * @param array $map array que receberá o mapeamento
     * @param string $map_by parâmetro de mapeamento
     * @param object $dto filtro
     * @return bool
     */
    public function multiMapAllByWithParam(&$map, $map_by, &$dto): bool
    {
        global $dao_config;

        $from = $dao_config->getFromSQL($this->dto_object);
        $fields = $dao_config->getFieldsOfDBTables($this->dto_object);
        $fields_select = NblPHPUtil::Array2CSV($fields, false);
        
        $query = "select {$fields_select} from {$from} "; 
        
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

        $where .= $this->mountWhereAndOr($wheres, $where);
        
        if(!empty($where))
        {
            $query .= ' where ' . $where;
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
            $dto = $this->getDTO();
            $vars = get_object_vars($dto);
            
            while(!$result->EOF)
            {
                $local_map = array();
                foreach($vars as $v => $val)
                {
                    if(array_key_exists($v,$result->fields))
                    {
                        // em caso de join, alguns campos podem repetir nos resultados
                        if(!isset($local_map[$v])) {
                            $local_map[$v] = $result->fields[$v];
                        }
                    }
                }
                
                if(!isset($map[$result->fields[$map_by]]))
                {
                    $map[$result->fields[$map_by]] = array();
                }

                $map[$result->fields[$map_by]][] = $local_map;
                $result->MoveNext();
            }

            $result->close();
            return true;
        }    
    }

    /* avaliações */

    /**
     * Compara dois objetos DTO pela(s) chave(s) primária(s)
     * 
     * @param object $a objeto DTO para comparação
     * @param object $b objeto DTO para comparação
     * @return bool
     */
    public function compareByKeys($a, $b): bool
    {
        global $dao_config;

        $pks = $dao_config->getDBTablePKs4WhereSQL($this->dto_object);
        if(!empty($pks)) {
            foreach($pks as $pk) {
                if(is_array($pk) && isset($pk['name'])) {
                    if($a->{$pk['name']} != $b->{$pk['name']}) {
                        return false;
                    }
                }
                else {
                    if($a->{$pk} != $b->{$pk}) {
                        return false;
                    }
                }
            }

            return true;
        }

        return false;
    }
    
} 
?>
