<?php
require_once ADO_PATH . '/Participantes.class.php'; 
require_once DAO_PATH . '/ParticipantesDAO.class.php'; 
require_once WS_PATH . '/usuarios.ws.php'; 
require_once WS_PATH . '/pessoas.ws.php'; 
require_once WS_PATH . '/pequenosgrupos.ws.php'; 

/**
 * API REST de Participantes
 */
class ParticipantesWS extends WSUtil
{
    /**
     * 
     * @var \ParticipantesWS singleton instance
     */
    private static $_Instance = null;
    
    /**
     * Get singleton instance
     * 
     * @return \ParticipantesWS
     */
    public static function getInstance(): \ParticipantesWS {
        if(self::$_Instance == null) {
            self::$_Instance = new self();
        }
        return self::$_Instance;
    }
    
    /**
     * Verifica se uma pessoa é participante de um pequeno grupo
     * 
     * @param string $pessoa id da pessoa
     * @param string $grupo id do pequeno grupo
     * @return bool
     */
    public static function checkParticipante($pessoa, $grupo): bool
    {
        $dto = new ParticipantesDTO();
        $obj = new ParticipantesADO();
        
        ParticipantesADO::addComparison($dto, 'pessoa', CMP_EQUAL, $pessoa);
        ParticipantesADO::addComparison($dto, 'grupo', CMP_EQUAL, $grupo);
        ParticipantesADO::addComparison($dto, 'stat', CMP_EQUAL, Status::ACTIVE);
        
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
        if(!doIHavePermission(NblFram::$context->token, 'PequenoGrupoIgrejaParticipantes')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        if(ParticipantesWS::checkParticipante(NblFram::$context->data->pessoa, NblFram::$context->data->grupo)) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Essa pessoa já faz parte do pequeno grupo');
        }
        
        $dto = new ParticipantesDTO();
        $obj = new ParticipantesADO();
        
        $dto->add = true;
        $dto->id = NblPHPUtil::makeNumericId();
        $dto->grupo = NblFram::$context->data->grupo;
        $dto->pessoa = NblFram::$context->data->pessoa;
        $dto->lider = (NblFram::$context->data->lider) ? GenericHave::YES : GenericHave::NO;
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
     * Edita
     * 
     * @httpmethod PUT
     * @auth yes
     * @require id
     * @return array
     */
    public function edit(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'PequenoGrupoIgrejaParticipantes')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new ParticipantesDTO();
        $obj = new ParticipantesADO();
        
        $dto->edit = true;
        $dto->id = NblFram::$context->data->id;
        $dto->lider = (NblFram::$context->data->lider) ? GenericHave::YES : GenericHave::NO;
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
        if(!doIHavePermission(NblFram::$context->token, 'PequenoGrupoIgrejaParticipantes')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new ParticipantesDTO();
        $obj = new ParticipantesADO();
        
        $dto->edit = true;
        $dto->id = NblFram::$context->data->id;
        $dto->stat = (NblFram::$context->data->stat == Status::ACTIVE) ? Status::BLOCKED : Status::ACTIVE;
        $dto->last_mod = date('Y-m-d H:i:s');
        $dto->last_amod = NblFram::$context->token['data']['nome'];
        
        $obj->add($dto);
        if($obj->sync())
        {
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
        if(!doIHavePermission(NblFram::$context->token, 'PequenoGrupoIgrejaParticipantes')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new ParticipantesDTO();
        $obj = new ParticipantesADO();
        
        $dto->id = NblFram::$context->data->id;
        if(!is_null($obj->get($dto)))
        {
            $d = $obj->getDTODataObject();
            $d->lider = ($d->lider == GenericHave::YES);
            
            $pessoa = PessoasWS::getById($d->pessoa);
            if(!is_null($pessoa))
            {
                $d->nome = $pessoa->nome;
                
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
     * Verifica se a pessoa já está cadastrada no pequeno grupo
     * 
     * @httpmethod GET
     * @auth yes
     * @require grupo
     * @require pessoa
     * @return array
     */
    public function check(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'PequenoGrupoIgrejaParticipantes')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new ParticipantesDTO();
        $obj = new ParticipantesADO();
        
        ParticipantesADO::addComparison($dto, 'grupo', CMP_EQUAL, NblFram::$context->data->grupo);
        ParticipantesADO::addComparison($dto, 'pessoa', CMP_EQUAL, NblFram::$context->data->pessoa);
        
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
        if(!doIHavePermission(NblFram::$context->token, 'PequenoGrupoIgrejaParticipantes')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }

        $dto = new ParticipantesDTO();
        $obj = new ParticipantesADO();

        $page = ($this->testInputString('page')) ? (int) NblFram::$context->data->page : -1;
        $pageSize = ($this->testInputString('pageSize')) ? (int) NblFram::$context->data->pageSize : -1;
        $pagination = $this->calcPagination($page, $pageSize);

        $searchBy = ($this->testInputString('searchBy')) ? NblFram::$context->data->searchBy : '';
        $orderBy = ($this->testInputString('orderBy')) ? NblFram::$context->data->orderBy : '';
        $groupBy = ($this->testInputString('groupBy')) ? NblFram::$context->data->groupBy : '';
        
        $grupo = ($this->testInputString('grupo')) ? NblFram::$context->data->grupo : '';
        $pessoa = ($this->testInputString('pessoa')) ? NblFram::$context->data->pessoa : '';
        $stat = ($this->testInputString('stat')) ? NblFram::$context->data->stat : '';
        
        $lider = ($this->testInputBool('lider')) ? true : false;
        
        // filtros aplicados em grupo
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
            ParticipantesADO::addComparison($dto, 'pessoa', CMP_EQUAL, $pessoa);
            
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
            
            ParticipantesADO::addComparison($dto, 'pessoa', CMP_IN_LIST, $id_list);
        }
        
        $grupos_ids = [];
        $has_grupo_filter = false;
        if(!empty($igreja))
        {
            $has_grupo_filter = true;
            $grupos_ids = PequenosGruposWS::getIdsByIgreja($igreja);
        }
                
        if($has_grupo_filter)
        {
            /* há filtro de igreja. Veja se algum pre-resultado foi encontrado */
            if(empty($grupos_ids)) {
                /* não. Então não há nada a retornar */
                return array('status' => 'ok', 'success' => true, 'datas' => array(), 'total' => 0);
            }
            else {
                /* sim. Aplique o filtro */
                ParticipantesADO::addComparison($dto, 'grupo', CMP_IN_LIST, $this->stringifyArray($grupos_ids));
            }
        }
        
        if(!empty($grupo))
        {   
            ParticipantesADO::addComparison($dto, 'grupo', CMP_EQUAL, $grupo);
        }

        if(!empty($stat))
        {   
            ParticipantesADO::addComparison($dto, 'stat', CMP_EQUAL, $stat);
        }
        
        if($lider)
        {   
            ParticipantesADO::addComparison($dto, 'lider', CMP_EQUAL, GenericHave::YES);
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
                        'grupo' => $it->grupo,
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
                        'stat' => $it->stat,
                        'lider' => ($it->lider == GenericHave::YES),
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
        if(!doIHavePermission(NblFram::$context->token, 'PequenoGrupoIgrejaParticipantes')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new ParticipantesDTO();
        $obj = new ParticipantesADO();
        
        $dto->id = NblFram::$context->data->id;
        $r = $obj->get($dto);
        if(!is_null($r))
        {
            $r->delete = true;
            if($obj->sync())
            {
                // remova o contexto 
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
        if(!doIHavePermission(NblFram::$context->token, 'PequenoGrupoIgrejaParticipantes')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new ParticipantesDTO();
        $obj = new ParticipantesADO();
        
        $id_list = '';
        foreach(NblFram::$context->data->ids as $id) {
            if(!empty($id_list)) {
                $id_list .= ', ';
            }
            
            $id_list .= "'$id'";
        }
        
        ParticipantesADO::addComparison($dto, 'id', CMP_IN_LIST, $id_list);
        if($obj->getAllbyParam($dto))
        {
            $obj->iterate();
            while($obj->hasNext())
            {
                $it = $obj->next();
                if(!is_null($it->id))
                {
                    $it->delete = true;
                }
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
        else 
        {
            return array('status' => 'no', 'success' => false, 'msg' => 'Recursos não encontrados');
        }
    }
}

