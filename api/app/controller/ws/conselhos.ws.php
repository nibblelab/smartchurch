<?php
require_once ADO_PATH . '/Oficiais.class.php'; 
require_once DAO_PATH . '/OficiaisDAO.class.php'; 
require_once ADO_PATH . '/Usuarios.class.php'; 
require_once ADO_PATH . '/Pessoas.class.php'; 
require_once DAO_PATH . '/PessoasDAO.class.php'; 

/**
 * API REST de Conselhos
 */
class ConselhosWS extends WSUtil
{
    /**
     * 
     * @var \ConselhosWS singleton instance
     */
    private static $_Instance = null;
    
    /**
     * Get singleton instance
     * 
     * @return \ConselhosWS
     */
    public static function getInstance(): \ConselhosWS {
        if(self::$_Instance == null) {
            self::$_Instance = new self();
        }
        return self::$_Instance;
    }
    
    /**
     * 
     * Busca 
     * 
     * @httpmethod GET
     * @auth yes
     * @optional page página a ser buscada
     * @optional pageSize tamanho da página
     * @optional searchBy busca textual
     * @optional orderBy campo de ordenação
     * @optional groupBy campo de agrupamento
     * @optional igreja id da igreja
     * @return array
     */
    public function all(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'ConselhoIgreja')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }

        $dto = new OficiaisDTO();
        $obj = new OficiaisADO();
        
        $dto_p = new PessoasDTO();
        $obj_p = new PessoasADO();

        $page = ($this->testInputString('page')) ? (int) NblFram::$context->data->page : -1;
        $pageSize = ($this->testInputString('pageSize')) ? (int) NblFram::$context->data->pageSize : -1;
        $pagination = $this->calcPagination($page, $pageSize);

        $searchBy = ($this->testInputString('searchBy')) ? NblFram::$context->data->searchBy : '';
        $orderBy = ($this->testInputString('orderBy')) ? NblFram::$context->data->orderBy : '';
        $groupBy = ($this->testInputString('groupBy')) ? NblFram::$context->data->groupBy : '';
        
        $igreja = (isset(NblFram::$context->data->igreja) && !empty(NblFram::$context->data->igreja)) ? NblFram::$context->data->igreja : '';
        
        // filtros nos dados de pessoa
        $has_at_least_one_filter_pessoa = false;
        if(!empty($searchBy))
        {   
            $has_at_least_one_filter_pessoa = true;
            PessoasADO::addComparison($dto_p, 'nome', CMP_INCLUDE_INSIDE, $searchBy, OP_OR);
            PessoasADO::addComparison($dto_p, 'email', CMP_INCLUDE_INSIDE, $searchBy, OP_OR);
            PessoasADO::addComparison($dto_p, 'telefone', CMP_INCLUDE_INSIDE, $searchBy, OP_OR);
            PessoasADO::addComparison($dto_p, 'celular_1', CMP_INCLUDE_INSIDE, $searchBy, OP_OR);
            PessoasADO::addComparison($dto_p, 'celular_2', CMP_INCLUDE_INSIDE, $searchBy, OP_OR);
        }
        
        // mapeie os nomes das pessoas
        if($has_at_least_one_filter_pessoa) {
            $pessoas = $obj_p->mapNomesByIdWithParam($dto_p);
        }
        else {
            $pessoas = $obj_p->mapNomesById();
        }
        
        // gere a lista dos ids das pessoas e inclua nos filtros
        $id_list = '';
        foreach($pessoas as $id => $pessoa)
        {
            if(!empty($id_list)) {
                $id_list .= ',';
            }
            
            $id_list .= "'$id'";
        }
        
        if(empty($id_list)) {
            return array('status' => 'ok', 'success' => true, 'datas' => array(), 'total' => 0);
        }
        
        $tipo = "'".TipoOficiais::PRESBITERO."','".TipoOficiais::PASTOR."'";
        
        $has_at_least_one_filter = true;
        OficiaisADO::addComparison($dto, 'ref_tp', CMP_EQUAL, References::IGREJA);
        OficiaisADO::addComparison($dto, 'tipo', CMP_IN_LIST, $tipo);
        OficiaisADO::addComparison($dto, 'pessoa', CMP_IN_LIST, $id_list);
        OficiaisADO::addComparison($dto, 'stat', CMP_EQUAL, Status::ACTIVE);
        OficiaisADO::addComparison($dto, 'disponibilidade', CMP_EQUAL, DisponibilidadeOficiais::ATIVO);

        if(!empty($searchBy))
        {   
            $has_at_least_one_filter = true;
            OficiaisADO::addComparison($dto, 'email', CMP_INCLUDE_INSIDE, $searchBy, OP_OR);
            OficiaisADO::addComparison($dto, 'telefone', CMP_INCLUDE_INSIDE, $searchBy, OP_OR);
            OficiaisADO::addComparison($dto, 'celular', CMP_INCLUDE_INSIDE, $searchBy, OP_OR);
        }

        if(!empty($igreja))
        {   
            $has_at_least_one_filter = true;
            OficiaisADO::addComparison($dto, 'ref', CMP_EQUAL, $igreja);
        }
        
        if(!empty($orderBy))
        {
            $has_at_least_one_filter = true;
            $order_by_v = explode(',',$orderBy);
            OficiaisADO::addOrdering($dto, $order_by_v[0], ($order_by_v[1] == 'asc') ? ORDER_ASC : ORDER_DESC);
        }

        if(!empty($groupBy))
        {
            $has_at_least_one_filter = true;
            $groups = explode(',',$groupBy);
            foreach($groups as $g) {
                OficiaisADO::addGrouping($dto, $g);
            }
        }
        
        $ok = $obj->getAllbyParam($dto);
        $pre_result = array();
        if($ok)
        {
            $obj->iterate();
            while($obj->hasNext())
            {
                $it = $obj->next();
                if(!is_null($it->id))
                {
                    $pessoa_nome = '';
                    if(isset($pessoas[$it->pessoa])) {
                        $pessoa_nome = $pessoas[$it->pessoa]['nome'];
                    }
                    
                    $pre_result[] = array(
                        'id' => $it->id,
                        'nome' => $pessoa_nome,
                        'pessoa' => $it->pessoa,
                        'email' => $it->email,
                        'telefone' => $it->telefone,
                        'celular' => $it->celular,
                        'inicio' => $it->inicio,
                        'fim' => $it->fim,
                        'pastor' => ($it->tipo == TipoOficiais::PASTOR),
                        'presbitero' => ($it->tipo == TipoOficiais::PRESBITERO),
                        'time_cad' => $it->time_cad,
                        'last_mod' => $it->last_mod,
                        'last_amod' => $it->last_amod
                    );
                }
            }
            
            /* ordene */
            if(!empty($orderBy))
            {
                $pre_result = $this->orderBy($pre_result);
            }
            
            /* agora pagine, se aplicável */
            $total = count($pre_result);
            
            $result = array();
            if($page == -1) {
                $result = $pre_result;
            }
            else {
                $result = array_slice($pre_result, $pagination->page, $pagination->pagesize);
            }

            return array('status' => 'ok', 'success' => true, 'datas' => $result, 'total' => $total);
        }
        else
        {
            return array('status' => 'no', 'success' => false, 'msg' => 'Erro na operação', 'errs' => $obj->getErrs());
        }
        
    }

}

