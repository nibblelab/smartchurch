<?php
require_once ADO_PATH . '/PedidosOracao.class.php'; 
require_once DAO_PATH . '/PedidosOracaoDAO.class.php'; 
require_once WS_PATH . '/pessoas.ws.php'; 

/**
 * API REST de PedidosOracao
 */
class PedidosOracaoWS extends WSUtil
{
    /**
     * 
     * @var \PedidosOracaoWS singleton instance
     */
    private static $_Instance = null;
    
    /**
     * Get singleton instance
     * 
     * @return \PedidosOracaoWS 
     */
    public static function getInstance(): \PedidosOracaoWS {
        if(self::$_Instance == null) {
            self::$_Instance = new self();
        }
        return self::$_Instance;
    }
    
    
    /**
     * Obtem a agenda pelo id
     * 
     * @param string $id id da agenda
     * @return object|null
     */
    public static function getById($id): ?object
    {
       $dto = new PedidosOracaoDTO();
        $obj = new PedidosOracaoADO();
        
        $dto->id = $id;
        if(!is_null($obj->get($dto)))
        {
            return $obj->getDTODataObject();
        }
        else 
        {
            return null;
        }
    }
    
    
    /**
     * 
     * Cria
     * 
     * @httpmethod POST
     * @auth yes
     * @return array
     */
    public function create(): array
    {
        $perms = array('PedidosOracaoIgrejaSave','PedidosOracaoSociedadeSave');
        if(!doIHavePermission(NblFram::$context->token, $this->stringifyArray($perms, ',', false))) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new PedidosOracaoDTO();
        $obj = new PedidosOracaoADO();
        
        $dto->add = true;
        $dto->id = NblPHPUtil::makeNumericId();
        $dto->autor = NblFram::$context->data->autor;
        $dto->ref = NblFram::$context->data->ref;
        $dto->ref_tp = NblFram::$context->data->ref_tp;
        $dto->pedido = NblFram::$context->data->pedido;
        $dto->stat = Status::ACTIVE;
        $dto->time_cad = date('Y-m-d H:i:s');
        $dto->last_mod = $dto->time_cad;
        $dto->last_amod = NblFram::$context->token['data']['nome'];
        
        $obj->add($dto);
        if($obj->sync())
        {
            return array('status' => 'ok', 'success' => true, 'id' => $dto->id);
        }
        else
        {
            return array('status' => 'no', 'success' => false, 'msg' => 'Erro na operação', 'errs' => $obj->getErrs());
        }
    }
    
    /**
     * 
     * Cria para igreja
     * 
     * @httpmethod POST
     * @auth yes
     * @require igreja
     * @return array
     */
    public function createForIgreja(): array
    {
        NblFram::$context->data->ref = NblFram::$context->data->igreja;
        NblFram::$context->data->ref_tp = References::IGREJA;
        return $this->create();
    }
    
    /**
     * 
     * Cria para sociedade
     * 
     * @httpmethod POST
     * @auth yes
     * @require sociedade
     * @return array
     */
    public function createForSociedade(): array
    {
        NblFram::$context->data->ref = NblFram::$context->data->sociedade;
        NblFram::$context->data->ref_tp = References::SOCIEDADE;
        return $this->create();
    }
    
    /**
     * 
     * Cria para ministério
     * 
     * @httpmethod POST
     * @auth yes
     * @require ministerio
     * @return array
     */
    public function createForMinisterio(): array
    {
        NblFram::$context->data->ref = NblFram::$context->data->ministerio;
        NblFram::$context->data->ref_tp = References::MINISTERIO;
        return $this->create();
    }

    /**
     * 
     * Edita
     * 
     * @httpmethod PUT
     * @auth yes
     * @require id
     * @return array
     */
    public function edit(): array
    {
        $perms = array('PedidosOracaoIgrejaSave','PedidosOracaoSociedadeSave');
        if(!doIHavePermission(NblFram::$context->token, $this->stringifyArray($perms, ',', false))) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new PedidosOracaoDTO();
        $obj = new PedidosOracaoADO();
        
        $dto->edit = true;
        $dto->id = NblFram::$context->data->id;
        $dto->pedido = NblFram::$context->data->pedido;
        $dto->last_mod = date('Y-m-d H:i:s');
        $dto->last_amod = NblFram::$context->token['data']['nome'];
        
        $obj->add($dto);
        if($obj->sync())
        {
            return array('status' => 'ok', 'success' => true, 'id' => $dto->id);
        }
        else
        {
            return array('status' => 'no', 'success' => false, 'msg' => 'Erro na operação', 'errs' => $obj->getErrs());
        }
    }
    
    /**
     * 
     * Busca 
     * 
     * @httpmethod GET
     * @auth yes
     * @require id
     * @return array
     */
    public function me(): array
    {
        $perms = array('PedidosOracaoIgreja','PedidosOracaoSociedade');
        if(!doIHavePermission(NblFram::$context->token, $this->stringifyArray($perms, ',', false))) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new PedidosOracaoDTO();
        $obj = new PedidosOracaoADO();
        
        $dto->id = NblFram::$context->data->id;
        if(!is_null($obj->get($dto)))
        {
            $d = $obj->getDTODataObject();
            return array('status' => 'ok', 'success' => true, 'datas' => $d);
        }
        else 
        {
            return array('status' => 'no', 'success' => false, 'msg' => 'Erro na operação', 'errs' => $obj->getErrs());
        }
    }

    /**
     * 
     * Busca 
     * 
     * @httpmethod GET
     * @auth yes
     * @return array
     */
    public function all(): array
    {
        $perms = array('Dados','PedidosOracaoIgreja','PedidosOracaoSociedade');
        if(!doIHavePermission(NblFram::$context->token, $this->stringifyArray($perms, ',', false))) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }

        $dto = new PedidosOracaoDTO();
        $obj = new PedidosOracaoADO();
        $obj_count = new PedidosOracaoADO();
        
        $page = ($this->testInputString('page')) ? (int) NblFram::$context->data->page : -1;
        $pageSize = ($this->testInputString('pageSize')) ? (int) NblFram::$context->data->pageSize : -1;
        $pagination = $this->calcPagination($page, $pageSize);

        $searchBy = ($this->testInputString('searchBy')) ? NblFram::$context->data->searchBy : '';
        $orderBy = ($this->testInputString('orderBy')) ? NblFram::$context->data->orderBy : '';
        $groupBy = ($this->testInputString('groupBy')) ? NblFram::$context->data->groupBy : '';
        
        $autor = ($this->testInputString('autor')) ? NblFram::$context->data->autor : '';
        $stat = ($this->testInputString('stat')) ? NblFram::$context->data->stat : '';
        $ref = ($this->testInputString('ref')) ? NblFram::$context->data->ref : '';
        $ref_tp = ($this->testInputString('ref_tp')) ? NblFram::$context->data->ref_tp : '';
        
        $igreja = ($this->testInputString('igreja')) ? NblFram::$context->data->igreja : '';
        $sociedade = ($this->testInputString('sociedade')) ? NblFram::$context->data->sociedade : '';
        $ministerio = ($this->testInputString('ministerio')) ? NblFram::$context->data->ministerio : '';

        $has_at_least_one_filter = false;
        
        /* filtros no autor */
        if(!empty($autor))
        {   
            // filtre por uma pessoa diretamente
            PedidosOracaoADO::addComparison($dto, 'autor', CMP_EQUAL, $autor);
            
            $pessoas = PessoasWS::mapBasicDataById();
        }
        else 
        {
            // filtros nos dados de pessoa
            if(!empty($searchBy))
            {
                $pessoas = PessoasWS::mapBasicDataByIdWithFilters($searchBy);
            }
            else {
                $pessoas = PessoasWS::mapBasicDataById();
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
            
            PedidosOracaoADO::addComparison($dto, 'autor', CMP_IN_LIST, $id_list);
        }
        
        /* filtros diretos no pedido de oração */
        
        if(!empty($searchBy))
        {   
            $has_at_least_one_filter = true;
            PedidosOracaoADO::addComparison($dto, 'pedido', CMP_INCLUDE_INSIDE, $searchBy);
        }

        if(!empty($stat))
        {   
            $has_at_least_one_filter = true;
            PedidosOracaoADO::addComparison($dto, 'stat', CMP_EQUAL, $stat);
        }

        if(!empty($ref))
        {   
            $has_at_least_one_filter = true;
            PedidosOracaoADO::addComparison($dto, 'ref', CMP_EQUAL, $ref);
        }

        if(!empty($ref_tp))
        {   
            $has_at_least_one_filter = true;
            PedidosOracaoADO::addComparison($dto, 'ref_tp', CMP_EQUAL, $ref_tp);
        }

        if(!empty($igreja))
        {   
            $has_at_least_one_filter = true;
            PedidosOracaoADO::addComparison($dto, 'ref_tp', CMP_EQUAL, References::IGREJA);
            PedidosOracaoADO::addComparison($dto, 'ref', CMP_EQUAL, $igreja);
        }

        if(!empty($sociedade))
        {   
            $has_at_least_one_filter = true;
            PedidosOracaoADO::addComparison($dto, 'ref_tp', CMP_EQUAL, References::SOCIEDADE);
            PedidosOracaoADO::addComparison($dto, 'ref', CMP_EQUAL, $sociedade);
        }

        if(!empty($ministerio))
        {   
            $has_at_least_one_filter = true;
            PedidosOracaoADO::addComparison($dto, 'ref_tp', CMP_EQUAL, References::MINISTERIO);
            PedidosOracaoADO::addComparison($dto, 'ref', CMP_EQUAL, $ministerio);
        }
        
        if(!empty($orderBy))
        {
            $has_at_least_one_filter = true;
            $order_by_v = explode(',',$orderBy);
            PedidosOracaoADO::addOrdering($dto, $order_by_v[0], ($order_by_v[1] == 'asc') ? ORDER_ASC : ORDER_DESC);
        }

        if(!empty($groupBy))
        {
            $has_at_least_one_filter = true;
            $groups = explode(',',$groupBy);
            foreach($groups as $g) {
                PedidosOracaoADO::addGrouping($dto, $g);
            }
        }

        if (!$has_at_least_one_filter) {
            $obj_count->count(true);
        }
        else {
            $obj_count->countBy($dto);
        }
        
        if($page == -1)
        {
            $ok = (!$has_at_least_one_filter) ? $obj->getAll() : $obj->getAllbyParam($dto);
        }
        else
        {
            $ok = (!$has_at_least_one_filter) ? $obj->getAll($pagination->page, $pagination->pagesize) : $obj->getAllbyParam($dto, $pagination->page, $pagination->pagesize);
        }
        
        $result = array();
        if($ok)
        {
            $obj->iterate();
            while($obj->hasNext())
            {
                $it = $obj->next();
                if(!is_null($it->id))
                {
                    $autor_nome = '';
                    if(isset($pessoas[$it->autor])) {
                        $autor_nome = $pessoas[$it->autor]['nome'];
                    }
                    
                    $result[] = array(
                        'id' => $it->id,
                        'autor' => $it->autor,
                        'autor_nome' => $autor_nome,
                        'ref' => $it->ref,
                        'ref_tp' => $it->ref_tp,
                        'pedido' => $it->pedido,
                        'stat' => $it->stat,
                        'time_cad' => $it->time_cad,
                        'last_mod' => $it->last_mod,
                        'last_amod' => $it->last_amod
                    );
                }
            }

            return array('status' => 'ok', 'success' => true, 'datas' => $result, 'total' => $obj_count->count());
        }
        else
        {
            return array('status' => 'no', 'success' => false, 'msg' => 'Erro na operação', 'errs' => $obj->getErrs());
        }
        
    }
    
    /**
     * 
     * Busca 
     * 
     * @httpmethod GET
     * @auth yes
     * @return array
     */
    public function mine(): array
    {
        NblFram::$context->data->autor = NblFram::$context->token['data']['id'];
        return $this->all();
    }

    /**
     * 
     * Remove
     * 
     * @httpmethod DELETE
     * @auth yes
     * @require id
     * @return array
     */
    public function remove(): array
    {
        $perms = array('PedidosOracaoIgrejaRemove','PedidosOracaoSociedadeRemove');
        if(!doIHavePermission(NblFram::$context->token, $this->stringifyArray($perms, ',', false))) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new PedidosOracaoDTO();
        $obj = new PedidosOracaoADO();
        
        $dto->id = NblFram::$context->data->id;
        $r = $obj->get($dto);
        if(!is_null($r))
        {
            $r->delete = true;
            if($obj->sync())
            {
                return array('status' => 'ok', 'success' => true);
            }
            else
            {
                return array('status' => 'no', 'success' => false, 'msg' => 'Erro na operação', 'errs' => $obj->getErrs());
            }
        }
        else 
        {
            return array('status' => 'no', 'success' => false, 'msg' => 'Recurso não encontrado');
        }
    }
    
    /**
     * 
     * Remove todos
     * 
     * @httpmethod POST
     * @auth yes
     * @require ids
     * @return array
     */
    public function removeAll(): array
    {
        $perms = array('PedidosOracaoIgrejaRemove','PedidosOracaoSociedadeRemove');
        if(!doIHavePermission(NblFram::$context->token, $this->stringifyArray($perms, ',', false))) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new PedidosOracaoADO();
        
        foreach(NblFram::$context->data->ids as $id) {
            $dto = new PedidosOracaoDTO();
            $dto->delete = true;
            $dto->id = $id;
            
            $obj->add($dto);
        }
        
        if($obj->sync())
        {
            return array('status' => 'ok', 'success' => true);
        }
        else 
        {
            return array('status' => 'no', 'success' => false, 'msg' => 'Recursos não encontrados');
        }
    }
}

