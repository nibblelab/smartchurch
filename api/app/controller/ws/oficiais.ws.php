<?php
require_once ADO_PATH . '/Oficiais.class.php'; 
require_once DAO_PATH . '/OficiaisDAO.class.php'; 
require_once WS_PATH . '/pessoas.ws.php'; 
require_once WS_PATH . '/instancias.ws.php'; 
require_once WS_PATH . '/contextos.ws.php'; 

/**
 * API REST de Oficiais
 */
class OficiaisWS extends WSUtil
{
    
    /**
     * Obtem o oficial pelo id
     * 
     * @param string $id id do oficial
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
     * Busque todos os oficiais por tipo e referência
     * 
     * @param \TipoOficiais $tipo
     * @param string $ref id da referência
     * @param \References $ref_tp tipo de referência
     * @return array
     */
    public static function getAllByTipoAndRef($tipo, $ref, $ref_tp): array 
    {
        $dto = new OficiaisDTO();
        $obj = new OficiaisADO();
        
        OficiaisADO::addComparison($dto, 'tipo', CMP_EQUAL, $tipo);
        OficiaisADO::addComparison($dto, 'ref', CMP_EQUAL, $ref);
        OficiaisADO::addComparison($dto, 'ref_tp', CMP_EQUAL, $ref_tp);
        
        if($obj->getAllbyParam($dto)) {
            return $obj->getDTOAsArray();
        }
        
        return [];
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
        $perms = array('OficialIgrejaSave','DiretoriaSociedadeOficiais','DiretoriaSinodalOficiais');
        if(!doIHavePermission(NblFram::$context->token, $this->stringifyArray($perms, ',', false))) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new OficiaisDTO();
        $obj = new OficiaisADO();
        
        $dto->add = true;
        $dto->id = NblPHPUtil::makeNumericId();
        $dto->pessoa = NblFram::$context->data->pessoa;
        $dto->cargo = NblFram::$context->data->cargo;
        $dto->diretoria = (empty(NblFram::$context->data->diretoria)) ? NULL : NblFram::$context->data->diretoria;
        $dto->email = NblFram::$context->data->email;
        $dto->telefone = NblFram::$context->data->telefone;
        $dto->celular = NblFram::$context->data->celular;
        $dto->inicio = (empty(NblFram::$context->data->inicio)) ? NULL : NblPHPUtil::HumanDate2DBDate(NblFram::$context->data->inicio);
        $dto->fim = (empty(NblFram::$context->data->fim)) ? NULL : NblPHPUtil::HumanDate2DBDate(NblFram::$context->data->fim);
        $dto->ref = NblFram::$context->data->ref;
        $dto->ref_tp = NblFram::$context->data->ref_tp;
        $dto->stat = Status::ACTIVE;
        $dto->tipo = TipoOficiais::OUTRO;
        $dto->disponibilidade = Voids::TipoOficial;
        $dto->time_cad = date('Y-m-d H:i:s');
        $dto->last_mod = $dto->time_cad;
        $dto->last_amod = NblFram::$context->token['data']['nome'];
        
        $obj->add($dto);
        if($obj->sync())
        {
            // crie o contexto de administração do oficial
            $errs = [];
            if(!ContextosWS::create($dto->pessoa, 
                    InstanciasWS::getIdbyRef($dto->ref, $dto->ref_tp), 
                    $dto->ref, 
                    $dto->ref_tp, 
                    $errs, 
                    NblFram::$context->data->socio->perfil))
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
     * Cria
     * 
     * @httpmethod POST
     * @auth yes
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
     * Cria
     * 
     * @httpmethod POST
     * @auth yes
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
     * Cria
     * 
     * @httpmethod POST
     * @auth yes
     * @return array
     */
    public function createForSinodal(): array
    {
        NblFram::$context->data->ref = NblFram::$context->data->sinodal;
        NblFram::$context->data->ref_tp = References::SINODAL;
        
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
        $perms = array('OficialIgrejaSave','DiretoriaSociedadeOficiais','DiretoriaSinodalOficiais');
        if(!doIHavePermission(NblFram::$context->token, $this->stringifyArray($perms, ',', false))) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $oficial = OficiaisWS::getById(NblFram::$context->data->id);
        
        $dto = new OficiaisDTO();
        $obj = new OficiaisADO();
        
        $dto->edit = true;
        $dto->id = NblFram::$context->data->id;
        if(!NblFram::$context->data->demoted) {
            $dto->pessoa = NblFram::$context->data->pessoa;
            $dto->cargo = NblFram::$context->data->cargo;
        }
        $dto->email = NblFram::$context->data->email;
        $dto->telefone = NblFram::$context->data->telefone;
        $dto->celular = NblFram::$context->data->celular;
        $dto->inicio = (empty(NblFram::$context->data->inicio)) ? NULL : NblPHPUtil::HumanDate2DBDate(NblFram::$context->data->inicio);
        $dto->fim = (empty(NblFram::$context->data->fim)) ? NULL : NblPHPUtil::HumanDate2DBDate(NblFram::$context->data->fim);
        $dto->last_mod = date('Y-m-d H:i:s');
        $dto->last_amod = NblFram::$context->token['data']['nome'];
        
        $obj->add($dto);
        if($obj->sync())
        {
            /*
             * em caso de mundança de cargo, edita o perfil do contexto de administração
             */
            if($oficial->cargo != $dto->cargo && !NblFram::$context->data->demoted) {
                ContextosWS::changePerfilByUserAndReference(NblFram::$context->data->perfil, 
                                    $oficial->pessoa, 
                                    $oficial->ref, 
                                    $oficial->ref_tp);
            }
            /**
             * em caso de expiração de cargo, expire o contexto de administração
             */            
            if(!is_null($dto->fim) && !NblFram::$context->data->demoted) {
                $now = new DateTime();
                $fim_dt = new DateTime($dto->fim);
                if($now > $fim_dt) {
                    // veja se ainda há contexto 
                    $contextos = ContextosWS::getByUserAndReference($oficial->pessoa, $oficial->ref, $oficial->ref_tp);
                    if(!empty($contextos)) {
                        foreach($contextos as $cnt) {
                            ContextosWS::removeByUserInstanciaAndRefs($oficial->pessoa, 
                                                        InstanciasWS::getIdbyRef($oficial->ref, $oficial->ref_tp), 
                                                        $oficial->ref, 
                                                        $oficial->ref_tp);
                        }
                    }
                }
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
     * Edita status 
     * 
     * @httpmethod PUT
     * @auth yes
     * @require id
     * @return array
     */
    public function changestat(): array
    {
        $perms = array('OficialIgrejaSave','DiretoriaSociedadeOficiais','DiretoriaSinodalOficiais');
        if(!doIHavePermission(NblFram::$context->token, $this->stringifyArray($perms, ',', false))) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new OficiaisDTO();
        $obj = new OficiaisADO();
        
        $dto->edit = true;
        $dto->id = NblFram::$context->data->id;
        $dto->stat = (NblFram::$context->data->stat == Status::ACTIVE) ? Status::BLOCKED : Status::ACTIVE;
        $dto->fim = ($dto->stat == Status::ACTIVE) ? NULL : date('Y-m-d');
        $dto->last_mod = date('Y-m-d H:i:s');
        $dto->last_amod = NblFram::$context->token['data']['nome'];
        
        $obj->add($dto);
        if($obj->sync())
        {
            $oficial = OficiaisWS::getById($dto->id);
            if(!is_null($oficial)) {
                ContextosWS::changeStatByUserAndReference($dto->stat, 
                                    $oficial->pessoa, 
                                    $oficial->ref, 
                                    $oficial->ref_tp);
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
        $perms = array('OficialIgrejaSave','DiretoriaSociedadeOficiais','DiretoriaSinodalOficiais');
        if(!doIHavePermission(NblFram::$context->token, $this->stringifyArray($perms, ',', false))) {
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
                
                $contexto = ContextosWS::getByUserAndReference($d->pessoa, $d->ref, $d->ref_tp);
                if(!empty($contexto)) 
                {
                    $d->perfil = $contexto[$d->pessoa]['perfil'];
                    $d->demoted = false;
                }
                else 
                {
                    $d->perfil = null;
                    $d->demoted = true;
                }
                
                return array('status' => 'ok', 'success' => true, 'datas' => $d);
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
        $perms = array('OficialIgrejaSave','DiretoriaSociedadeOficiais','DiretoriaSinodalOficiais','Dados');
        if(!doIHavePermission(NblFram::$context->token, $this->stringifyArray($perms, ',', false))) {
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
        
        $stat = ($this->testInputString('stat')) ? NblFram::$context->data->stat : '';
        $cargo = ($this->testInputString('cargo')) ? NblFram::$context->data->cargo : '';
        $diretoria = ($this->testInputString('diretoria')) ? NblFram::$context->data->diretoria : '';
        $pessoa = ($this->testInputString('pessoa')) ? NblFram::$context->data->pessoa : '';
        
        $igreja = ($this->testInputString('igreja')) ? NblFram::$context->data->igreja : '';
        $sociedade = ($this->testInputString('sociedade')) ? NblFram::$context->data->sociedade : '';
        
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
        OficiaisADO::addComparison($dto, 'tipo', CMP_EQUAL, TipoOficiais::OUTRO);

        if(!empty($searchBy))
        {   
            $has_at_least_one_filter = true;
            OficiaisADO::addComparison($dto, 'email', CMP_INCLUDE_INSIDE, $searchBy, OP_OR);
            OficiaisADO::addComparison($dto, 'telefone', CMP_INCLUDE_INSIDE, $searchBy, OP_OR);
            OficiaisADO::addComparison($dto, 'celular', CMP_INCLUDE_INSIDE, $searchBy, OP_OR);
        }

        if(!empty($stat))
        {   
            $has_at_least_one_filter = true;
            OficiaisADO::addComparison($dto, 'stat', CMP_EQUAL, $stat);
        }

        if(!empty($cargo))
        {   
            $has_at_least_one_filter = true;
            OficiaisADO::addComparison($dto, 'cargo', CMP_EQUAL, $cargo);
        }

        if(!empty($diretoria))
        {   
            $has_at_least_one_filter = true;
            OficiaisADO::addComparison($dto, 'diretoria', CMP_EQUAL, $diretoria);
        }
        
        if(!empty($igreja))
        {   
            $has_at_least_one_filter = true;
            OficiaisADO::addComparison($dto, 'ref_tp', CMP_EQUAL, References::IGREJA);
            OficiaisADO::addComparison($dto, 'ref', CMP_EQUAL, $igreja);
        }
        
        if(!empty($sociedade))
        {   
            $has_at_least_one_filter = true;
            OficiaisADO::addComparison($dto, 'ref_tp', CMP_EQUAL, References::SOCIEDADE);
            OficiaisADO::addComparison($dto, 'ref', CMP_EQUAL, $sociedade);
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
                    $pessoa_perfil = '';
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
                        $pessoa_perfil = $pessoas[$it->pessoa]['perfil'];
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
                        'perfil' => $pessoa_perfil,
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
                        'cargo' => $it->cargo,
                        'diretoria' => $it->diretoria,
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
        $perms = array('OficialIgrejaSave','DiretoriaSociedadeOficiais','DiretoriaSinodalOficiais');
        if(!doIHavePermission(NblFram::$context->token, $this->stringifyArray($perms, ',', false))) {
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
                ContextosWS::removeByUserAndInstancia($r->pessoa, InstanciasWS::getIdbyRef($r->ref, $r->ref_tp));
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
        $perms = array('OficialIgrejaSave','DiretoriaSociedadeOficiais','DiretoriaSinodalOficiais');
        if(!doIHavePermission(NblFram::$context->token, $this->stringifyArray($perms, ',', false))) {
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
                        'instancia' => InstanciasWS::getIdbyRef($it->ref, $it->ref_tp)
                    );
                }
            }
            
            if($obj->sync())
            {
                // remova os contextos 
                foreach($contextos as $ctx) {
                    ContextosWS::removeByUserAndInstancia($ctx['pessoa'], $ctx['instancia']);
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

