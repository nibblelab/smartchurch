<?php
require_once ADO_PATH . '/Secretarios.class.php'; 
require_once DAO_PATH . '/SecretariosDAO.class.php'; 
require_once WS_PATH . '/instancias.ws.php'; 
require_once WS_PATH . '/contextos.ws.php'; 
require_once WS_PATH . '/pessoas.ws.php'; 
require_once WS_PATH . '/secretarias.ws.php'; 

/**
 * API REST de Secretarios
 */
class SecretariosWS extends WSUtil
{
    /**
     * 
     * @var \SecretariosWS singleton instance
     */
    private static $_Instance = null;
    
    /**
     * Get singleton instance
     * 
     * @return \SecretariosWS
     */
    public static function getInstance(): \SecretariosWS {
        if(self::$_Instance == null) {
            self::$_Instance = new self();
        }
        return self::$_Instance;
    }
    
    /**
     * Obtem o secretário pelo id
     * 
     * @param string $id id do secretário
     * @return object|null
     */
    public static function getById($id): ?object
    {
        $dto = new SecretariosDTO();
        $obj = new SecretariosADO();
        
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
     * Verifica se uma pessoa é secretário de uma secretaria
     * 
     * @param string $pessoa id da pessoa
     * @param string $secretaria id da secretaria
     * @return bool
     */
    public static function checkSecretario($pessoa, $secretaria): bool
    {
        $dto = new SecretariosDTO();
        $obj = new SecretariosADO();
        
        SecretariosADO::addComparison($dto, 'pessoa', CMP_EQUAL, $pessoa);
        SecretariosADO::addComparison($dto, 'secretaria', CMP_EQUAL, $secretaria);
        SecretariosADO::addComparison($dto, 'stat', CMP_EQUAL, Status::ACTIVE);
        SecretariosADO::addComparison($dto, 'data_demissao', CMP_IS_NULL);
        
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
        $perms = array('SecretariaIgrejaPessoa','SecretariaSociedadePessoa','SecretariaMinisterioPessoa','SecretarioSave','SecretariaSinodalPessoa');
        if(!doIHavePermission(NblFram::$context->token, $this->stringifyArray($perms, ',', false))) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        if(SecretariosWS::checkSecretario(NblFram::$context->data->pessoa, NblFram::$context->data->secretaria)) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Essa pessoa já faz parte da secretaria');
        }
        
        $dto = new SecretariosDTO();
        $obj = new SecretariosADO();
        
        $dto->add = true;
        $dto->id = NblPHPUtil::makeNumericId();
        $dto->secretaria = NblFram::$context->data->secretaria;
        $dto->pessoa = NblFram::$context->data->pessoa;
        $dto->perfil = NblFram::$context->data->perfil;
        $dto->tipo = (NblFram::$context->data->auxiliar) ? TiposSecretario::AUXILIAR : TiposSecretario::OFICIAL;
        $dto->data_admissao = (empty(NblFram::$context->data->data_admissao)) ? NULL : NblPHPUtil::HumanDate2DBDate(NblFram::$context->data->data_admissao);
        $dto->data_demissao = (empty(NblFram::$context->data->data_demissao)) ? NULL : NblPHPUtil::HumanDate2DBDate(NblFram::$context->data->data_demissao);
        $dto->stat = (empty(NblFram::$context->data->data_demissao)) ? Status::ACTIVE : Status::BLOCKED;
        $dto->time_cad = date('Y-m-d H:i:s');
        $dto->last_mod = $dto->time_cad;
        $dto->last_amod = NblFram::$context->token['data']['nome'];
        
        $obj->add($dto);
        if($obj->sync())
        {
            // crie os contextos de administração do secretário
            $instancia_id = InstanciasWS::getIdbyRef($dto->secretaria, References::SECRETARIA);
            $errs = [];
            if(!ContextosWS::create($dto->pessoa, 
                    $instancia_id, 
                    $dto->secretaria, 
                    References::SECRETARIA, 
                    $errs, 
                    NblFram::$context->data->perfil))
            {
                
                return array('status' => 'no', 'success' => false, 'msg' => 'Erro na operação', 'errs' => $errs);
            }
            
            // se a instância do contexto tem um pai, crie um contexto para o pai também
            $instancia = InstanciasWS::getbyId($instancia_id);
            if(!is_null($instancia) && !is_null($instancia->pai)) {
                $instancia_pai = InstanciasWS::getbyId($instancia->pai);
                if(!is_null($instancia_pai)) {
                    ContextosWS::create($dto->pessoa, 
                        $instancia_pai->id, 
                        $instancia_pai->ref, 
                        $instancia_pai->ref_tp, 
                        $errs, 
                        NblFram::$context->data->perfil);
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
     * Edita
     * 
     * @httpmethod PUT
     * @auth yes
     * @require id
     * @return array
     */
    public function edit(): array
    {
        $perms = array('SecretariaIgrejaPessoa','SecretariaSociedadePessoa','SecretariaMinisterioPessoa','SecretarioSave','SecretariaSinodalPessoa');
        if(!doIHavePermission(NblFram::$context->token, $this->stringifyArray($perms, ',', false))) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new SecretariosDTO();
        $obj = new SecretariosADO();
        
        $dto->edit = true;
        $dto->id = NblFram::$context->data->id;
        $dto->secretaria = NblFram::$context->data->secretaria;
        $dto->pessoa = NblFram::$context->data->pessoa;
        $dto->perfil = NblFram::$context->data->perfil;
        $dto->tipo = (NblFram::$context->data->auxiliar) ? TiposSecretario::AUXILIAR : TiposSecretario::OFICIAL;
        $dto->data_admissao = (empty(NblFram::$context->data->data_admissao)) ? NULL : NblPHPUtil::HumanDate2DBDate(NblFram::$context->data->data_admissao);
        $dto->data_demissao = (empty(NblFram::$context->data->data_demissao)) ? NULL : NblPHPUtil::HumanDate2DBDate(NblFram::$context->data->data_demissao);
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
        $perms = array('SecretariaIgrejaPessoa','SecretariaSociedadePessoa','SecretariaMinisterioPessoa','SecretarioBlock','SecretariaSinodalPessoa');
        if(!doIHavePermission(NblFram::$context->token, $this->stringifyArray($perms, ',', false))) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new SecretariosDTO();
        $obj = new SecretariosADO();
        
        $dto->edit = true;
        $dto->id = NblFram::$context->data->id;
        $dto->stat = (NblFram::$context->data->stat == Status::ACTIVE) ? Status::BLOCKED : Status::ACTIVE;
        $dto->data_demissao = ($dto->stat == Status::BLOCKED) ? date('Y-m-d') : NULL;
        $dto->last_mod = date('Y-m-d H:i:s');
        $dto->last_amod = NblFram::$context->token['data']['nome'];
        
        $obj->add($dto);
        if($obj->sync())
        {
            $secretario = SecretariosWS::getById($dto->id);
            if(!is_null($secretario)) {
                ContextosWS::changeStatByUserAndReference($dto->stat, 
                                    $secretario->pessoa, 
                                    $secretario->secretaria, 
                                    References::SECRETARIA);
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
        $perms = array('SecretariaIgrejaPessoa','SecretariaSociedadePessoa','SecretariaMinisterioPessoa','Secretario','SecretariaSinodalPessoa');
        if(!doIHavePermission(NblFram::$context->token, $this->stringifyArray($perms, ',', false))) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new SecretariosDTO();
        $obj = new SecretariosADO();
        
        $dto->id = NblFram::$context->data->id;
        if(!is_null($obj->get($dto)))
        {
            $d = $obj->getDTODataObject();
            $d->auxiliar = ($d->tipo == TiposSecretario::AUXILIAR);
            
            $pessoa = PessoasWS::getById($d->pessoa);
            if(!is_null($pessoa)) 
            {
                $d->nome = $pessoa->nome;
                
                return array('status' => 'ok', 'success' => true, 'datas' => $d);
            }
            else 
            {
                return array('status' => 'no', 'success' => false, 'msg' => 'Erro na operação', 'errs' => '');
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
     * @require secretaria
     * @require pessoa
     * @return array
     */
    public function check(): array
    {
        $perms = array('SecretariaIgrejaPessoa','SecretariaSociedadePessoa','SecretariaMinisterioPessoa','Secretario','SecretariaSinodalPessoa');
        if(!doIHavePermission(NblFram::$context->token, $this->stringifyArray($perms, ',', false))) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new SecretariosDTO();
        $obj = new SecretariosADO();
        
        SecretariosADO::addComparison($dto, 'secretaria', CMP_EQUAL, NblFram::$context->data->secretaria);
        SecretariosADO::addComparison($dto, 'pessoa', CMP_EQUAL, NblFram::$context->data->pessoa);
        
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
        $perms = array('SecretariaIgrejaPessoa','SecretariaSociedadePessoa','SecretariaMinisterioPessoa','Secretario','SecretariaSinodalPessoa');
        if(!doIHavePermission(NblFram::$context->token, $this->stringifyArray($perms, ',', false))) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }

        $dto = new SecretariosDTO();
        $obj = new SecretariosADO();

        $page = ($this->testInputString('page')) ? (int) NblFram::$context->data->page : -1;
        $pageSize = ($this->testInputString('pageSize')) ? (int) NblFram::$context->data->pageSize : -1;
        $pagination = $this->calcPagination($page, $pageSize);

        $searchBy = ($this->testInputString('searchBy')) ? NblFram::$context->data->searchBy : '';
        $orderBy = ($this->testInputString('orderBy')) ? NblFram::$context->data->orderBy : '';
        $groupBy = ($this->testInputString('groupBy')) ? NblFram::$context->data->groupBy : '';
        
        $tipo = ($this->testInputString('tipo')) ? NblFram::$context->data->tipo : '';
        $secretaria = ($this->testInputString('secretaria')) ? NblFram::$context->data->secretaria : '';
        $pessoa = ($this->testInputString('pessoa')) ? NblFram::$context->data->pessoa : '';
        $auxiliar = ($this->testInputBool('auxiliar')) ? true : false;
        $oficial = ($this->testInputBool('oficial')) ? true : false;
        
        // filtros aplicados em secretaria
        $igreja = ($this->testInputString('igreja')) ? NblFram::$context->data->igreja : '';
        $federacao = ($this->testInputString('federacao')) ? NblFram::$context->data->federacao : '';
        $sinodal = ($this->testInputString('sinodal')) ? NblFram::$context->data->sinodal : '';
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
            SecretariosADO::addComparison($dto, 'pessoa', CMP_EQUAL, $pessoa);
            
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
            
            SecretariosADO::addComparison($dto, 'pessoa', CMP_IN_LIST, $id_list);
        }
        
        if(!empty($searchBy))
        {
            if($this->isDateStr($searchBy))
            {
                SecretariosADO::addComparison($dto, 'data_admissao', CMP_GREATER_THEN, $searchBy, OP_OR);
                SecretariosADO::addComparison($dto, 'data_demissao', CMP_LESSER_THEN, $searchBy, OP_OR);
            }
        }
        
        if(!empty($secretaria))
        {   
            SecretariosADO::addComparison($dto, 'secretaria', CMP_EQUAL, $secretaria);
        }
        else {
            $secretarias_ids = [];
            $has_secretaria_filter = false;
            if(!empty($igreja))
            {
                $has_secretaria_filter = true;
                $secretarias_ids = SecretariasWS::getIdsByIgreja($igreja);
            }
            else if(!empty($federacao))
            {
                $has_secretaria_filter = true;
                $secretarias_ids = SecretariasWS::getIdsByFederacao($federacao);
            }
            else if(!empty($sinodal))
            {
                $has_secretaria_filter = true;
                $secretarias_ids = SecretariasWS::getIdsBySinodal($sinodal);
            }
            else if(!empty($sociedade))
            {
                $has_secretaria_filter = true;
                $secretarias_ids = SecretariasWS::getIdsBySinodal($sociedade);
            }
            
            if($has_secretaria_filter)
            {
                /* há filtro sobre secretaria. Veja se algum pre-resultado foi encontrado */
                if(empty($secretarias_ids)) {
                    /* não. Então não há nada a retornar */
                    return array('status' => 'ok', 'success' => true, 'datas' => array(), 'total' => 0);
                }
                else {
                    /* sim. Aplique o filtro */
                    SecretariosADO::addComparison($dto, 'secretaria', CMP_IN_LIST, $this->stringifyArray($secretarias_ids));
                }
            }
        }
        
        if(!empty($tipo)) 
        {
            SecretariosADO::addComparison($dto, 'tipo', CMP_EQUAL, $tipo);
        }
        else
        {
            if($auxiliar)
            {   
                SecretariosADO::addComparison($dto, 'tipo', CMP_EQUAL, TiposSecretario::AUXILIAR);
            }
            else if($oficial)
            {
                SecretariosADO::addComparison($dto, 'tipo', CMP_EQUAL, TiposSecretario::OFICIAL);
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
                        'secretaria' => $it->secretaria,
                        'perfil' => $it->perfil,
                        'pessoa' => $it->pessoa,
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
                        'tipo' => $it->tipo,
                        'auxiliar' => ($it->tipo == TiposSecretario::AUXILIAR),
                        'oficial' => ($it->tipo == TiposSecretario::OFICIAL),
                        'data_admissao' => $it->data_admissao,
                        'data_demissao' => $it->data_demissao,
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
    
    public function removeInstances($id_secretaria, $id_pessoa)
    {
        $instancia_id = InstanciasWS::getIdbyRef($id_secretaria, References::SECRETARIA);            
        // se a instância do contexto tem um pai, busque o contexto para o pai também
        $instancia = InstanciasWS::getbyId($instancia_id);
        $instancia_pai = null;
        if(!is_null($instancia) && !is_null($instancia->pai)) {
            $instancia_pai = InstanciasWS::getbyId($instancia->pai);
                               
        }
                
        // remova
        ContextosWS::removeByUserInstanciaAndRefs($id_pessoa, $instancia_id, $id_secretaria, References::SECRETARIA);
        if(!is_null($instancia_pai)) {
            ContextosWS::removeByUserInstanciaAndRefs($id_pessoa, $instancia_pai->id, $instancia_pai->ref, $instancia_pai->ref_tp);
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
        $perms = array('SecretariaIgrejaPessoa','SecretariaSociedadePessoa','SecretariaMinisterioPessoa','SecretarioRemove','SecretariaSinodalPessoa');
        if(!doIHavePermission(NblFram::$context->token, $this->stringifyArray($perms, ',', false))) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new SecretariosDTO();
        $obj = new SecretariosADO();
        
        $dto->id = NblFram::$context->data->id;
        $r = $obj->get($dto);
        if(!is_null($r))
        {
            $r->delete = true;
            $id_secretaria = $r->secretaria;
            $id_pessoa = $r->pessoa;
            if($obj->sync())
            {
                $this->removeInstances($id_secretaria, $id_pessoa);
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
        $perms = array('SecretariaIgrejaPessoa','SecretariaSociedadePessoa','SecretariaMinisterioPessoa','SecretarioRemove','SecretariaSinodalPessoa');
        if(!doIHavePermission(NblFram::$context->token, $this->stringifyArray($perms, ',', false))) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new SecretariosADO();
        
        foreach(NblFram::$context->data->ids as $id) {
            $dto = new SecretariosDTO();
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

