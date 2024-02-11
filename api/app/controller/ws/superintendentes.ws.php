<?php
require_once ADO_PATH . '/Oficiais.class.php'; 
require_once DAO_PATH . '/OficiaisDAO.class.php'; 
require_once WS_PATH . '/pessoas.ws.php'; 
require_once WS_PATH . '/instancias.ws.php'; 
require_once WS_PATH . '/contextos.ws.php';

/**
 * API REST de Superintendente
 */
class SuperintendentesWS extends WSUtil
{
    /**
     * 
     * @var \SuperintendentesWS singleton instance
     */
    private static $_Instance = null;
    
    /**
     * Get singleton instance
     * 
     * @return \SuperintendentesWS
     */
    public static function getInstance(): \SuperintendentesWS {
        if(self::$_Instance == null) {
            self::$_Instance = new self();
        }
        return self::$_Instance;
    }
    
    /**
     * Obtem o superintendente pelo id
     * 
     * @param string $id id do superintendente
     * @return object|null
     */
    public static function getById($id): ?object
    {
        $dto = new OficiaisDTO();
        $obj = new OficiaisADO();
        
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
        if(!doIHavePermission(NblFram::$context->token, 'SuperintendenciaIgrejaSave')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new OficiaisDTO();
        $obj = new OficiaisADO();
        
        $dto->add = true;
        $dto->id = NblPHPUtil::makeNumericId();
        $dto->pessoa = NblFram::$context->data->pessoa;
        $dto->cargo = NULL;
        $dto->diretoria = NULL;
        $dto->email = NblFram::$context->data->email;
        $dto->telefone = NblFram::$context->data->telefone;
        $dto->celular = NblFram::$context->data->celular;
        $dto->inicio = (empty(NblFram::$context->data->inicio)) ? NULL : NblPHPUtil::HumanDate2DBDate(NblFram::$context->data->inicio);
        $dto->fim = (empty(NblFram::$context->data->fim)) ? NULL : NblPHPUtil::HumanDate2DBDate(NblFram::$context->data->fim);
        $dto->ref = NblFram::$context->data->igreja;
        $dto->ref_tp = References::IGREJA;
        $dto->stat = Status::ACTIVE;
        $dto->tipo = TipoOficiais::SUPERINTENDENTE;
        $dto->disponibilidade = DisponibilidadeOficiais::ATIVO;
        $dto->time_cad = date('Y-m-d H:i:s');
        $dto->last_mod = $dto->time_cad;
        $dto->last_amod = NblFram::$context->token['data']['nome'];
        
        $obj->add($dto);
        if($obj->sync())
        {
            /* crie o contexto de ebd */
            $errs = [];
            if(!ContextosWS::create($dto->pessoa, 
                    InstanciasWS::getIdbyRef(NblFram::$context->data->igreja, References::IGREJA), 
                    NblFram::$context->data->igreja, 
                    References::EBD, 
                    $errs, 
                    NblFram::$context->data->perfil))
            {
                return array('status' => 'no', 'success' => false, 'msg' => 'Erro na operação', 'errs' => $errs);
            }
                
            return array('status' => 'ok', 'success' => true, 'id' => $dto->id);
        }
        else
        {
            return array('status' => 'no', 'success' => false, 'msg' => 'Erro na operação', 'errs' => $obj->getErrs());
        }
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
        if(!doIHavePermission(NblFram::$context->token, 'SuperintendenciaIgrejaSave')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new OficiaisDTO();
        $obj = new OficiaisADO();
        
        $dto->edit = true;
        $dto->id = NblFram::$context->data->id;
        $dto->pessoa = NblFram::$context->data->pessoa;
        $dto->email = NblFram::$context->data->email;
        $dto->telefone = NblFram::$context->data->telefone;
        $dto->celular = NblFram::$context->data->celular;
        $dto->inicio = (empty(NblFram::$context->data->inicio)) ? NULL : NblPHPUtil::HumanDate2DBDate(NblFram::$context->data->inicio);
        $dto->fim = (empty(NblFram::$context->data->fim)) ? NULL : NblPHPUtil::HumanDate2DBDate(NblFram::$context->data->fim);
        $dto->disponibilidade = NblFram::$context->data->disponibilidade;
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
     * Edita status 
     * 
     * @httpmethod PUT
     * @auth yes
     * @require id
     * @return array
     */
    public function changestat(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'SuperintendenciaIgrejaBlock')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new OficiaisDTO();
        $obj = new OficiaisADO();
        
        $dto->edit = true;
        $dto->id = NblFram::$context->data->id;
        $dto->stat = (NblFram::$context->data->stat == Status::ACTIVE) ? Status::BLOCKED : Status::ACTIVE;
        $dto->last_mod = date('Y-m-d H:i:s');
        $dto->last_amod = NblFram::$context->token['data']['nome'];
        
        $obj->add($dto);
        if($obj->sync())
        {
            $superintendente = SuperintendentesWS::getById($dto->id);
            if(!is_null($superintendente)) {
                ContextosWS::changeStatByUserAndReference($dto->stat, 
                                    $superintendente->pessoa, 
                                    $superintendente->ref, 
                                    References::EBD);
            }
            
            return array('status' => 'ok', 'success' => true, 'stat' => $dto->stat);
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
        if(!doIHavePermission(NblFram::$context->token, 'SuperintendenciaIgreja')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new OficiaisDTO();
        $obj = new OficiaisADO();
        
        $dto->id = NblFram::$context->data->id;
        if(!is_null($obj->get($dto)))
        {
            $d = $obj->getDTODataObject();
            
            $pessoa = PessoasWS::getById($d->pessoa);
            if(!is_null($pessoa))
            {
                $d->nome = $pessoa->nome;
                
                $contexto = ContextosWS::getByUserAndReference($d->pessoa, $d->ref, References::EBD);
                if(!empty($contexto)) 
                {
                    $d->perfil = $contexto[$d->pessoa]['perfil'];

                    return array('status' => 'ok', 'success' => true, 'datas' => $d);
                }
                else 
                {
                    return array('status' => 'no', 'success' => false, 'msg' => 'Erro na operação', 'errs' => []);
                } 
            }
            else 
            {
                return array('status' => 'no', 'success' => false, 'msg' => 'Erro na operação', 'errs' => []);
            }
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
        if(!doIHavePermission(NblFram::$context->token, 'SuperintendenciaIgreja')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }

        $dto = new OficiaisDTO();
        $obj = new OficiaisADO();

        $page = ($this->testInputString('page')) ? (int) NblFram::$context->data->page : -1;
        $pageSize = ($this->testInputString('pageSize')) ? (int) NblFram::$context->data->pageSize : -1;
        $pagination = $this->calcPagination($page, $pageSize);

        $searchBy = ($this->testInputString('searchBy')) ? NblFram::$context->data->searchBy : '';
        $orderBy = ($this->testInputString('orderBy')) ? NblFram::$context->data->orderBy : '';
        $groupBy = ($this->testInputString('groupBy')) ? NblFram::$context->data->groupBy : '';
        
        $pessoa = ($this->testInputString('pessoa')) ? NblFram::$context->data->pessoa : '';
        $igreja = ($this->testInputString('igreja')) ? NblFram::$context->data->igreja : '';
        $stat = ($this->testInputString('stat')) ? NblFram::$context->data->stat : '';
        
        // filtros que aplicam em pessoa
        $sexo = ($this->testInputString('sexo')) ? NblFram::$context->data->sexo : '';
        $estado_civil = ($this->testInputString('estado_civil')) ? NblFram::$context->data->estado_civil : '';
        $escolaridade = ($this->testInputString('escolaridade')) ? NblFram::$context->data->escolaridade : '';
        $com_filhos = ($this->testInputBool('com_filhos')) ? true : false;
        $sem_filhos = ($this->testInputBool('sem_filhos')) ? true : false;

        if(!empty($pessoa))
        {   
            // filtre por uma pessoa diretamente
            OficiaisADO::addComparison($dto, 'pessoa', CMP_EQUAL, $pessoa);
            
            $pessoas = PessoasWS::mapBasicDataById();
        }
        else
        {
            // filtros nos dados de pessoa
            if(!empty($searchBy) || !empty($sexo) || !empty($estado_civil) || !empty($escolaridade) || $com_filhos || $sem_filhos)
            {
                $pessoas = PessoasWS::mapBasicDataByIdWithFilters($searchBy, $sexo, $estado_civil, $escolaridade, $com_filhos, $sem_filhos);
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
            
            OficiaisADO::addComparison($dto, 'pessoa', CMP_IN_LIST, $id_list);
        }
        
        $has_at_least_one_filter = true;
        OficiaisADO::addComparison($dto, 'ref_tp', CMP_EQUAL, References::IGREJA);
        OficiaisADO::addComparison($dto, 'tipo', CMP_EQUAL, TipoOficiais::SUPERINTENDENTE);
        
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

        if(!empty($stat))
        {   
            $has_at_least_one_filter = true;
            OficiaisADO::addComparison($dto, 'stat', CMP_EQUAL, $stat);
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
                    $pessoa_data_nascimento = '';
                    $pessoa_email = '';
                    $pessoa_telefone = '';
                    $pessoa_celular_1 = '';
                    $pessoa_masculino = false;
                    $pessoa_feminino = false;
                    $pessoa_estado_civil = '';
                    $pessoa_escolaridade = '';
                    $pessoa_tem_filhos = false;
                    if(isset($pessoas[$it->pessoa])) {
                        $pessoa_nome = $pessoas[$it->pessoa]['nome'];
                        $pessoa_data_nascimento = $pessoas[$it->pessoa]['data_nascimento'];
                        $pessoa_email = $pessoas[$it->pessoa]['email'];
                        $pessoa_telefone = $pessoas[$it->pessoa]['telefone'];
                        $pessoa_celular_1 = $pessoas[$it->pessoa]['celular_1'];
                        $pessoa_masculino = ($pessoas[$it->pessoa]['sexo'] == Sexo::MASCULINO);
                        $pessoa_feminino = ($pessoas[$it->pessoa]['sexo'] == Sexo::FEMININO);
                        $pessoa_estado_civil = $pessoas[$it->pessoa]['estado_civil'];
                        $pessoa_escolaridade = $pessoas[$it->pessoa]['escolaridade'];
                        $pessoa_tem_filhos = ($pessoas[$it->pessoa]['tem_filhos'] == GenericHave::YES);
                    }
                    
                    $pre_result[] = array(
                        'id' => $it->id,
                        'nome' => $pessoa_nome,
                        'data_nascimento' => $pessoa_data_nascimento,
                        'email' => $pessoa_email,
                        'telefone' => $pessoa_telefone,
                        'celular_1' => $pessoa_celular_1,
                        'masculino' => $pessoa_masculino,
                        'feminino' => $pessoa_feminino,
                        'estado_civil' => $pessoa_estado_civil,
                        'escolaridade' => $pessoa_escolaridade,
                        'tem_filhos' => $pessoa_tem_filhos,
                        'pessoa' => $it->pessoa,
                        'email' => $it->email,
                        'telefone' => $it->telefone,
                        'celular' => $it->celular,
                        'inicio' => $it->inicio,
                        'fim' => $it->fim,
                        'stat' => $it->stat,
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
        if(!doIHavePermission(NblFram::$context->token, 'SuperintendenciaIgrejaRemove')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new OficiaisDTO();
        $obj = new OficiaisADO();
        
        $dto->id = NblFram::$context->data->id;
        $r = $obj->get($dto);
        if(!is_null($r))
        {
            $r->delete = true;
            if($obj->sync())
            {
                // remova o contexto 
                ContextosWS::removeByUserInstanciaAndRefs($r->pessoa, 
                                                            InstanciasWS::getIdbyRef($r->ref, References::IGREJA),
                                                            $r->ref,
                                                            References::EBD);
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
        if(!doIHavePermission(NblFram::$context->token, 'SuperintendenciaIgrejaRemove')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new OficiaisDTO();
        $obj = new OficiaisADO();
        
        $id_list = '';
        foreach(NblFram::$context->data->ids as $id) {
            if(!empty($id_list)) {
                $id_list .= ', ';
            }
            
            $id_list .= "'$id'";
        }
        
        OficiaisADO::addComparison($dto, 'id', CMP_IN_LIST, $id_list);
        if($obj->getAllbyParam($dto))
        {
            $contextos = [];
            $obj->iterate();
            while($obj->hasNext())
            {
                $it = $obj->next();
                if(!is_null($it->id))
                {
                    $it->delete = true;
                    
                    $contextos[] = array(
                        'pessoa' => $it->pessoa,
                        'instancia' => InstanciasWS::getIdbyRef($it->ref, References::IGREJA),
                        'ref' => $it->ref,
                        'ref_tp' => $it->ref_tp
                    );
                }
            }
            
            if($obj->sync())
            {
                // remova os contextos 
                foreach($contextos as $ctx) {
                    ContextosWS::removeByUserInstanciaAndRefs($ctx['pessoa'], $ctx['instancia'], $ctx['ref'], $ctx['ref_tp']);
                }
                
                return array('status' => 'ok', 'success' => true);
            }
            else 
            {
                return array('status' => 'no', 'success' => false, 'msg' => 'Recursos não encontrados');
            }
        }
        else 
        {
            return array('status' => 'no', 'success' => false, 'msg' => 'Recursos não encontrados');
        }
    }
}

