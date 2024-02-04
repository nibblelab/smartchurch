<?php
require_once ADO_PATH . '/Socios.class.php'; 
require_once DAO_PATH . '/SociosDAO.class.php'; 
require_once WS_PATH . '/instancias.ws.php'; 
require_once WS_PATH . '/contextos.ws.php'; 
require_once WS_PATH . '/usuarios.ws.php'; 
require_once WS_PATH . '/pessoas.ws.php'; 
require_once WS_PATH . '/sociedades.ws.php'; 

/**
 * API REST de Socios
 */
class SociosWS extends WSUtil
{
    
    /**
     * Obtem o sócio pelo id
     * 
     * @param string $id id do sócio
     * @return object|null
     */
    public static function getById($id): ?object
    {
        $dto = new SociosDTO();
        $obj = new SociosADO();
        
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
     * Verifica se uma pessoa é sócia de uma sociedade
     * 
     * @param string $pessoa id da pessoa
     * @param string $sociedade id da sociedade
     * @return bool
     */
    public static function checkSocio($pessoa, $sociedade): bool
    {
        $dto = new SociosDTO();
        $obj = new SociosADO();
        
        SociosADO::addComparison($dto, 'pessoa', CMP_EQUAL, $pessoa);
        SociosADO::addComparison($dto, 'sociedade', CMP_EQUAL, $sociedade);
        SociosADO::addComparison($dto, 'stat', CMP_EQUAL, Status::ACTIVE);
        SociosADO::addComparison($dto, 'data_demissao', CMP_IS_NULL);
        
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
        if(!doIHavePermission(NblFram::$context->token, 'SociedadeIgrejaSocio,SocioSave')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        if(SociosWS::checkSocio(NblFram::$context->data->pessoa, NblFram::$context->data->sociedade)) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Essa pessoa já faz parte da sociedade');
        }
        
        $dto = new SociosDTO();
        $obj = new SociosADO();
        
        $dto->add = true;
        $dto->id = NblPHPUtil::makeNumericId();
        $dto->sociedade = NblFram::$context->data->sociedade;
        $dto->pessoa = NblFram::$context->data->pessoa;
        $dto->data_admissao = (empty(NblFram::$context->data->data_admissao)) ? NULL : NblPHPUtil::HumanDate2DBDate(NblFram::$context->data->data_admissao);
        $dto->data_demissao = (empty(NblFram::$context->data->data_demissao)) ? NULL : NblPHPUtil::HumanDate2DBDate(NblFram::$context->data->data_demissao);
        $dto->stat = (empty(NblFram::$context->data->data_demissao)) ? Status::ACTIVE : Status::BLOCKED;
        $dto->admin = (NblFram::$context->data->admin) ? GenericHave::YES : GenericHave::NO;
        $dto->diretoria = (NblFram::$context->data->diretoria) ? GenericHave::YES : GenericHave::NO;
        $dto->cooperador = (NblFram::$context->data->cooperador) ? GenericHave::YES : GenericHave::NO;
        $dto->time_cad = date('Y-m-d H:i:s');
        $dto->last_mod = $dto->time_cad;
        $dto->last_amod = NblFram::$context->token['data']['nome'];
        
        $obj->add($dto);
        if($obj->sync())
        {
            if(NblFram::$context->data->diretoria || NblFram::$context->data->admin) {
                // é parte da diretoria ou é admin. Crie o contexto de administração dele 
                $errs = [];
                if(!ContextosWS::create($dto->pessoa, 
                        InstanciasWS::getIdbyRef($dto->sociedade, References::SOCIEDADE), 
                        $dto->sociedade, 
                        References::SOCIEDADE, 
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
        if(!doIHavePermission(NblFram::$context->token, 'SociedadeIgrejaSocio,SocioSave')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new SociosDTO();
        $obj = new SociosADO();
        
        $dto->edit = true;
        $dto->id = NblFram::$context->data->id;
        $dto->data_admissao = (empty(NblFram::$context->data->data_admissao)) ? NULL : NblPHPUtil::HumanDate2DBDate(NblFram::$context->data->data_admissao);
        $dto->data_demissao = (empty(NblFram::$context->data->data_demissao)) ? NULL : NblPHPUtil::HumanDate2DBDate(NblFram::$context->data->data_demissao);
        $dto->cooperador = (NblFram::$context->data->cooperador) ? GenericHave::YES : GenericHave::NO;
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
        if(!doIHavePermission(NblFram::$context->token, 'SociedadeIgrejaSocio,SocioBlock')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new SociosDTO();
        $obj = new SociosADO();
        
        $dto->edit = true;
        $dto->id = NblFram::$context->data->id;
        $dto->stat = (NblFram::$context->data->stat == Status::ACTIVE) ? Status::BLOCKED : Status::ACTIVE;
        $dto->data_demissao = ($dto->stat == Status::BLOCKED) ? date('Y-m-d') : NULL;
        $dto->last_mod = date('Y-m-d H:i:s');
        $dto->last_amod = NblFram::$context->token['data']['nome'];
        
        $obj->add($dto);
        if($obj->sync())
        {
            $socio = SociosWS::getById($dto->id);
            if(!is_null($socio)) {
                if(($socio->diretoria == GenericHave::YES) || ($socio->admin == GenericHave::YES)) {
                    ContextosWS::changeStatByUserAndReference($dto->stat, 
                                    $socio->pessoa, 
                                    $socio->sociedade, 
                                    References::SOCIEDADE);
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
     * Edita diretoria
     * 
     * @httpmethod PUT
     * @auth yes
     * @require id
     * @require diretoria
     * @require sociedade
     * @require pessoa
     * @require perfil
     * @return array
     */
    public function changeDiretoria(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'SociedadeIgrejaSocio,SocioSave')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new SociosDTO();
        $obj = new SociosADO();
        
        $dto->edit = true;
        $dto->id = NblFram::$context->data->id;
        $dto->diretoria = (NblFram::$context->data->diretoria) ? GenericHave::YES : GenericHave::NO;
        $dto->last_mod = date('Y-m-d H:i:s');
        $dto->last_amod = NblFram::$context->token['data']['nome'];
        
        $obj->add($dto);
        if($obj->sync())
        {
            if(NblFram::$context->data->diretoria) {
                // é parte da diretoria. Crie o contexto de administração dele 
                $errs = [];
                $instancia = InstanciasWS::getIdbyRef(NblFram::$context->data->sociedade, References::SOCIEDADE);
                if(!ContextosWS::exists(NblFram::$context->data->pessoa, $instancia))
                {
                    if(!ContextosWS::create(NblFram::$context->data->pessoa, 
                            $instancia, 
                            NblFram::$context->data->sociedade, 
                            References::SOCIEDADE, 
                            $errs, 
                            NblFram::$context->data->perfil))
                    {
                        return array('status' => 'no', 'success' => false, 'msg' => 'Erro na operação', 'errs' => $errs);
                    }
                }
                                
                return array('status' => 'ok', 'success' => true, 'id' => $dto->id);
            }
            else 
            {
                // não é mais parte da diretoria. Remova o contexto 
                $instancia = InstanciasWS::getIdbyRef(NblFram::$context->data->sociedade, References::SOCIEDADE);
                ContextosWS::removeByUserAndInstancia(NblFram::$context->data->pessoa, $instancia);
                
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
     * Cancela diretorias (em massa)
     * 
     * @httpmethod POST
     * @auth yes
     * @require socios
     * @return array
     */
    public function cancelDiretorias(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'SociedadeIgrejaSocio,SocioSave')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new SociosADO();
        
        $contextos_to_remove = [];
        foreach(NblFram::$context->data->socios as $socio) {
            $dto = new SociosDTO();
            $dto->edit = true;
            $dto->id = $socio->id;
            $dto->diretoria = GenericHave::NO;
            $dto->last_mod = date('Y-m-d H:i:s');
            $dto->last_amod = NblFram::$context->token['data']['nome'];
            
            $contextos_to_remove[] = array(
                'instancia' => InstanciasWS::getIdbyRef($socio->sociedade, References::SOCIEDADE),
                'pessoa' => $socio->pessoa);
            
            $obj->add($dto);
        }
        
        if($obj->sync())
        {
            foreach($contextos_to_remove as $ctx) {
                ContextosWS::removeByUserAndInstancia($ctx['pessoa'], $ctx['instancia']);
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
     * Busca 
     * 
     * @httpmethod GET
     * @auth yes
     * @require id
     * @return array
     */
    public function me(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'SociedadeIgrejaSocio,Socio')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new SociosDTO();
        $obj = new SociosADO();
        
        $dto->id = NblFram::$context->data->id;
        if(!is_null($obj->get($dto)))
        {
            $d = $obj->getDTODataObject();
            $d->admin = ($d->admin == GenericHave::YES);
            $d->diretoria = ($d->diretoria == GenericHave::YES);
            $d->cooperador = ($d->cooperador == GenericHave::YES);
            
            $pessoa = PessoasWS::getById($d->pessoa);
            if(!is_null($pessoa))
            {
                $d->nome = $pessoa->nome;
                
                if(!$d->diretoria) {
                    return array('status' => 'ok', 'success' => true, 'datas' => $d);
                }
                
                $contexto = ContextosWS::getByUserAndReference($d->pessoa, $d->sociedade, References::SOCIEDADE);
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
     * Verifica se o sócio já está cadastrado na sociedade
     * 
     * @httpmethod GET
     * @auth yes
     * @require sociedade
     * @require pessoa
     * @return array
     */
    public function check(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'SociedadeIgrejaSocio,Socio')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new SociosDTO();
        $obj = new SociosADO();
        
        SociosADO::addComparison($dto, 'sociedade', CMP_EQUAL, NblFram::$context->data->sociedade);
        SociosADO::addComparison($dto, 'pessoa', CMP_EQUAL, NblFram::$context->data->pessoa);
        
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
        if(!doIHavePermission(NblFram::$context->token, 'SociedadeIgrejaSocio,Socio,PessoaFederacao,PessoaSinodal')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }

        $dto = new SociosDTO();
        $obj = new SociosADO();

        $page = ($this->testInputString('page')) ? (int) NblFram::$context->data->page : -1;
        $pageSize = ($this->testInputString('pageSize')) ? (int) NblFram::$context->data->pageSize : -1;
        $pagination = $this->calcPagination($page, $pageSize);

        $searchBy = ($this->testInputString('searchBy')) ? NblFram::$context->data->searchBy : '';
        $orderBy = ($this->testInputString('orderBy')) ? NblFram::$context->data->orderBy : '';
        $groupBy = ($this->testInputString('groupBy')) ? NblFram::$context->data->groupBy : '';
        
        $sociedade = ($this->testInputString('sociedade')) ? NblFram::$context->data->sociedade : '';
        $pessoa = ($this->testInputString('pessoa')) ? NblFram::$context->data->pessoa : '';
        $stat = ($this->testInputString('stat')) ? NblFram::$context->data->stat : '';
        
        $admin = ($this->testInputBool('admin')) ? true : false;
        $diretoria = ($this->testInputBool('diretoria')) ? true : false;
        $cooperador = ($this->testInputBool('cooperador')) ? true : false;
        $apenas_socios = ($this->testInputBool('apenas_socios')) ? true : false;
        
        // filtros aplicados em sociedade
        $igreja = ($this->testInputString('igreja')) ? NblFram::$context->data->igreja : '';
        $federacao = ($this->testInputString('federacao')) ? NblFram::$context->data->federacao : '';
        $sinodal = ($this->testInputString('sinodal')) ? NblFram::$context->data->sinodal : '';
        
        // filtros que aplicam em pessoa
        $sexo = ($this->testInputString('sexo')) ? NblFram::$context->data->sexo : '';
        $estado_civil = ($this->testInputString('estado_civil')) ? NblFram::$context->data->estado_civil : '';
        $escolaridade = ($this->testInputString('escolaridade')) ? NblFram::$context->data->escolaridade : '';
        $com_filhos = ($this->testInputBool('com_filhos')) ? true : false;
        $sem_filhos = ($this->testInputBool('sem_filhos')) ? true : false;

        if(!empty($pessoa))
        {   
            // filtre por uma pessoa diretamente
            SociosADO::addComparison($dto, 'pessoa', CMP_EQUAL, $pessoa);
            
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
            
            SociosADO::addComparison($dto, 'pessoa', CMP_IN_LIST, $id_list);
        }
        
        $sociedades_ids = [];
        $has_sociedade_filter = false;
        if(!empty($igreja))
        {
            $has_sociedade_filter = true;
            $sociedades_ids = SociedadesWS::getIdsByIgreja($igreja);
        }
        else if(!empty($federacao))
        {
            $has_sociedade_filter = true;
            $sociedades_ids = SociedadesWS::getIdsByFederacao($federacao);
        }
        else if(!empty($sinodal))
        {
            $has_sociedade_filter = true;
            $sociedades_ids = SociedadesWS::getIdsBySinodal($sinodal);
        }
                
        if($has_sociedade_filter)
        {
            /* há filtro de sinodal ou federação ou igreja. Veja se algum pre-resultado foi encontrado */
            if(empty($sociedades_ids)) {
                /* não. Então não há nada a retornar */
                return array('status' => 'ok', 'success' => true, 'datas' => array(), 'total' => 0);
            }
            else {
                /* sim. Aplique o filtro */
                SociosADO::addComparison($dto, 'sociedade', CMP_IN_LIST, $this->stringifyArray($sociedades_ids));
            }
        }
        
        if(!empty($searchBy))
        {
            if($this->isDateStr($searchBy))
            {
                SociosADO::addComparison($dto, 'data_admissao', CMP_GREATER_THEN, $searchBy, OP_OR);
                SociosADO::addComparison($dto, 'data_demissao', CMP_LESSER_THEN, $searchBy, OP_OR);
            }
        }

        if(!empty($sociedade))
        {   
            SociosADO::addComparison($dto, 'sociedade', CMP_EQUAL, $sociedade);
        }

        if(!empty($stat))
        {   
            SociosADO::addComparison($dto, 'stat', CMP_EQUAL, $stat);
        }
        
        if($admin)
        {   
            SociosADO::addComparison($dto, 'admin', CMP_EQUAL, GenericHave::YES);
        }
        
        if($diretoria)
        {   
            SociosADO::addComparison($dto, 'diretoria', CMP_EQUAL, GenericHave::YES);
        }
        
        if($cooperador)
        {   
            SociosADO::addComparison($dto, 'cooperador', CMP_EQUAL, GenericHave::YES);
        }
        
        if($apenas_socios)
        {   
            SociosADO::addComparison($dto, 'cooperador', CMP_EQUAL, GenericHave::NO);
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
                        'sociedade' => $it->sociedade,
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
                        'diretoria' => ($it->diretoria == GenericHave::YES),
                        'cooperador' => ($it->cooperador == GenericHave::YES),
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
        if(!doIHavePermission(NblFram::$context->token, 'SociedadeIgrejaSocio,SocioRemove')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new SociosDTO();
        $obj = new SociosADO();
        
        $dto->id = NblFram::$context->data->id;
        $r = $obj->get($dto);
        if(!is_null($r))
        {
            $r->delete = true;
            if($obj->sync())
            {
                // remova o contexto 
                ContextosWS::removeByUserAndInstancia($r->pessoa, InstanciasWS::getIdbyRef($r->sociedade, References::SOCIEDADE));
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
        if(!doIHavePermission(NblFram::$context->token, 'SociedadeIgrejaSocio,SocioRemove')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new SociosDTO();
        $obj = new SociosADO();
        
        $id_list = '';
        foreach(NblFram::$context->data->ids as $id) {
            if(!empty($id_list)) {
                $id_list .= ', ';
            }
            
            $id_list .= "'$id'";
        }
        
        SociosADO::addComparison($dto, 'id', CMP_IN_LIST, $id_list);
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
                        'instancia' => InstanciasWS::getIdbyRef($it->sociedade, References::SOCIEDADE)
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

