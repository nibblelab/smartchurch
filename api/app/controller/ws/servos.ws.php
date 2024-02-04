<?php
require_once ADO_PATH . '/Servos.class.php'; 
require_once DAO_PATH . '/ServosDAO.class.php'; 
require_once WS_PATH . '/instancias.ws.php'; 
require_once WS_PATH . '/contextos.ws.php'; 
require_once WS_PATH . '/usuarios.ws.php'; 
require_once WS_PATH . '/pessoas.ws.php'; 
require_once WS_PATH . '/ministerios.ws.php'; 

/**
 * API REST de Servos
 */
class ServosWS extends WSUtil
{
    
    /**
     * Obtem o servo pelo id
     * 
     * @param string $id id do servo
     * @return object|null
     */
    public static function getById($id): ?object
    {
        $dto = new ServosDTO();
        $obj = new ServosADO();
        
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
     * Verifica se uma pessoa é servo em um ministério
     * 
     * @param string $pessoa id da pessoa
     * @param string $ministerio id do ministério
     * @return bool
     */
    public static function checkServo($pessoa, $ministerio): bool
    {
        $dto = new ServosDTO();
        $obj = new ServosADO();
        
        ServosADO::addComparison($dto, 'pessoa', CMP_EQUAL, $pessoa);
        ServosADO::addComparison($dto, 'ministerio', CMP_EQUAL, $ministerio);
        ServosADO::addComparison($dto, 'stat', CMP_EQUAL, Status::ACTIVE);
        ServosADO::addComparison($dto, 'data_demissao', CMP_IS_NULL);
        
        return ($obj->countBy($dto) > 0);
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
        if(!doIHavePermission(NblFram::$context->token, 'MinisterioIgrejaPessoa,ServoSave')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        if(ServosWS::checkServo(NblFram::$context->data->pessoa, NblFram::$context->data->ministerio)) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Essa pessoa já faz parte do ministerio');
        }
        
        $dto = new ServosDTO();
        $obj = new ServosADO();
        
        $dto->add = true;
        $dto->id = NblPHPUtil::makeNumericId();
        $dto->ministerio = NblFram::$context->data->ministerio;
        $dto->pessoa = NblFram::$context->data->pessoa;
        $dto->data_admissao = (empty(NblFram::$context->data->data_admissao)) ? NULL : NblPHPUtil::HumanDate2DBDate(NblFram::$context->data->data_admissao);
        $dto->data_demissao = (empty(NblFram::$context->data->data_demissao)) ? NULL : NblPHPUtil::HumanDate2DBDate(NblFram::$context->data->data_demissao);
        $dto->admin = (NblFram::$context->data->admin) ? GenericHave::YES : GenericHave::NO;
        $dto->stat = (empty(NblFram::$context->data->data_demissao)) ? Status::ACTIVE : Status::BLOCKED;
        $dto->time_cad = date('Y-m-d H:i:s');
        $dto->last_mod = $dto->time_cad;
        $dto->last_amod = NblFram::$context->token['data']['nome'];
        
        $obj->add($dto);
        if($obj->sync())
        {
            if(NblFram::$context->data->admin) {
                // é parte da diretoria ou é admin. Crie o contexto de administração dele 
                $errs = [];
                if(!ContextosWS::create($dto->pessoa, 
                        InstanciasWS::getIdbyRef($dto->ministerio, References::MINISTERIO), 
                        $dto->ministerio, 
                        References::MINISTERIO, 
                        $errs, 
                        NblFram::$context->data->perfil))
                {
                    return array('status' => 'no', 'success' => false, 'msg' => 'Erro na operação', 'errs' => $errs);
                }
                
                return array('status' => 'ok', 'success' => true, 'id' => $dto->id);
            }
            else 
            {
                return array('status' => 'ok', 'success' => true, 'id' => $dto->id);
            }
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
        if(!doIHavePermission(NblFram::$context->token, 'MinisterioIgrejaPessoa,ServoSave')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new ServosDTO();
        $obj = new ServosADO();
        
        $dto->edit = true;
        $dto->id = NblFram::$context->data->id;
        $dto->data_admissao = (empty(NblFram::$context->data->data_admissao)) ? NULL : NblPHPUtil::HumanDate2DBDate(NblFram::$context->data->data_admissao);
        $dto->data_demissao = (empty(NblFram::$context->data->data_demissao)) ? NULL : NblPHPUtil::HumanDate2DBDate(NblFram::$context->data->data_demissao);
        $dto->admin = (NblFram::$context->data->admin) ? GenericHave::YES : GenericHave::NO;
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
        if(!doIHavePermission(NblFram::$context->token, 'MinisterioIgrejaPessoa,ServoBlock')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new ServosDTO();
        $obj = new ServosADO();
        
        $dto->edit = true;
        $dto->id = NblFram::$context->data->id;
        $dto->stat = (NblFram::$context->data->stat == Status::ACTIVE) ? Status::BLOCKED : Status::ACTIVE;
        $dto->data_demissao = ($dto->stat == Status::BLOCKED) ? date('Y-m-d') : NULL;
        $dto->last_mod = date('Y-m-d H:i:s');
        $dto->last_amod = NblFram::$context->token['data']['nome'];
        
        $obj->add($dto);
        if($obj->sync())
        {
            $servo = ServosWS::getById($dto->id);
            if(!is_null($servo)) {
                if($servo->admin == GenericHave::YES) {
                    ContextosWS::changeStatByUserAndReference($dto->stat, 
                                    $servo->pessoa, 
                                    $servo->ministerio, 
                                    References::MINISTERIO);
                }
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
        if(!doIHavePermission(NblFram::$context->token, 'MinisterioIgrejaPessoa,Servo')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new ServosDTO();
        $obj = new ServosADO();
        
        $dto->id = NblFram::$context->data->id;
        if(!is_null($obj->get($dto)))
        {
            $d = $obj->getDTODataObject();
            $d->admin = ($d->admin == GenericHave::YES);
            
            $pessoa = PessoasWS::getById($d->pessoa);
            if(!is_null($pessoa))
            {
                $d->nome = $pessoa->nome;
                
                if(!$d->admin) 
                {
                    return array('status' => 'ok', 'success' => true, 'datas' => $d);
                }
                
                $contexto = ContextosWS::getByUserAndReference($d->pessoa, $d->ministerio, References::MINISTERIO);
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
     * Verifica se a pessoa já está cadastrada no ministério
     * 
     * @httpmethod GET
     * @auth yes
     * @require ministerio
     * @require pessoa
     * @return array
     */
    public function check(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'MinisterioIgrejaPessoa,Servo')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new ServosDTO();
        $obj = new ServosADO();
        
        ServosADO::addComparison($dto, 'ministerio', CMP_EQUAL, NblFram::$context->data->ministerio);
        ServosADO::addComparison($dto, 'pessoa', CMP_EQUAL, NblFram::$context->data->pessoa);
        
        return array('status' => 'ok', 'success' => true, 'exists' => ($obj->countBy($dto) > 0));
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
        if(!doIHavePermission(NblFram::$context->token, 'MinisterioIgrejaPessoa,Servo')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }

        $dto = new ServosDTO();
        $obj = new ServosADO();

        $page = ($this->testInputString('page')) ? (int) NblFram::$context->data->page : -1;
        $pageSize = ($this->testInputString('pageSize')) ? (int) NblFram::$context->data->pageSize : -1;
        $pagination = $this->calcPagination($page, $pageSize);

        $searchBy = ($this->testInputString('searchBy')) ? NblFram::$context->data->searchBy : '';
        $orderBy = ($this->testInputString('orderBy')) ? NblFram::$context->data->orderBy : '';
        $groupBy = ($this->testInputString('groupBy')) ? NblFram::$context->data->groupBy : '';
        
        $ministerio = ($this->testInputString('ministerio')) ? NblFram::$context->data->ministerio : '';
        $pessoa = ($this->testInputString('pessoa')) ? NblFram::$context->data->pessoa : '';
        $stat = ($this->testInputString('stat')) ? NblFram::$context->data->stat : '';
        
        $admin = ($this->testInputBool('admin')) ? true : false;
        
        // filtros aplicados em ministerio
        $igreja = ($this->testInputString('igreja')) ? NblFram::$context->data->igreja : '';
        
        // filtros que aplicam em pessoa
        $sexo = ($this->testInputString('sexo')) ? NblFram::$context->data->sexo : '';
        $estado_civil = ($this->testInputString('estado_civil')) ? NblFram::$context->data->estado_civil : '';
        $escolaridade = ($this->testInputString('escolaridade')) ? NblFram::$context->data->escolaridade : '';
        $com_filhos = ($this->testInputBool('com_filhos')) ? true : false;
        $sem_filhos = ($this->testInputBool('sem_filhos')) ? true : false;

        if(!empty($pessoa))
        {   
            // filtre por uma pessoa diretamente
            ServosADO::addComparison($dto, 'pessoa', CMP_EQUAL, $pessoa);
            
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
            
            ServosADO::addComparison($dto, 'pessoa', CMP_IN_LIST, $id_list);
        }
        
        $ministerios_ids = [];
        $has_ministerio_filter = false;
        if(!empty($igreja))
        {
            $has_ministerio_filter = true;
            $ministerios_ids = MinisteriosWS::getIdsByIgreja($igreja);
        }
                
        if($has_ministerio_filter)
        {
            /* há filtro de igreja. Veja se algum pre-resultado foi encontrado */
            if(empty($ministerios_ids)) {
                /* não. Então não há nada a retornar */
                return array('status' => 'ok', 'success' => true, 'datas' => array(), 'total' => 0);
            }
            else {
                /* sim. Aplique o filtro */
                ServosADO::addComparison($dto, 'ministerio', CMP_IN_LIST, $this->stringifyArray($ministerios_ids));
            }
        }
        
        if(!empty($searchBy))
        {
            if($this->isDateStr($searchBy))
            {
                ServosADO::addComparison($dto, 'data_admissao', CMP_GREATER_THEN, $searchBy, OP_OR);
                ServosADO::addComparison($dto, 'data_demissao', CMP_LESSER_THEN, $searchBy, OP_OR);
            }
        }

        if(!empty($ministerio))
        {   
            ServosADO::addComparison($dto, 'ministerio', CMP_EQUAL, $ministerio);
        }

        if(!empty($stat))
        {   
            ServosADO::addComparison($dto, 'stat', CMP_EQUAL, $stat);
        }
        
        if($admin)
        {   
            ServosADO::addComparison($dto, 'admin', CMP_EQUAL, GenericHave::YES);
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
                        'ministerio' => $it->ministerio,
                        'pessoa' => $it->pessoa,
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
                        'data_admissao' => $it->data_admissao,
                        'data_demissao' => $it->data_demissao,
                        'stat' => $it->stat,
                        'admin' => ($it->admin == GenericHave::YES),
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
        if(!doIHavePermission(NblFram::$context->token, 'SociedadeIgrejaSocio,ServoRemove')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new ServosDTO();
        $obj = new ServosADO();
        
        $dto->id = NblFram::$context->data->id;
        $r = $obj->get($dto);
        if(!is_null($r))
        {
            $r->delete = true;
            if($obj->sync())
            {
                // remova o contexto 
                ContextosWS::removeByUserAndInstancia($r->pessoa, InstanciasWS::getIdbyRef($r->ministerio, References::MINISTERIO));
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
        if(!doIHavePermission(NblFram::$context->token, 'SociedadeIgrejaSocio,ServoRemove')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new ServosDTO();
        $obj = new ServosADO();
        
        $id_list = '';
        foreach(NblFram::$context->data->ids as $id) {
            if(!empty($id_list)) {
                $id_list .= ', ';
            }
            
            $id_list .= "'$id'";
        }
        
        ServosADO::addComparison($dto, 'id', CMP_IN_LIST, $id_list);
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
                        'instancia' => InstanciasWS::getIdbyRef($it->ministerio, References::MINISTERIO)
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

