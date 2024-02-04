<?php
require_once ADO_PATH . '/ProfessoresDasSalas.class.php'; 
require_once DAO_PATH . '/ProfessoresDasSalasDAO.class.php'; 
require_once WS_PATH . '/pessoas.ws.php'; 
require_once WS_PATH . '/instancias.ws.php'; 
require_once WS_PATH . '/contextos.ws.php'; 

/**
 * API REST de ProfessoresDasSalas
 */
class ProfessoresDasSalasWS extends WSUtil
{
    /**
     * Verifica se uma pessoa é professor de uma sala
     * 
     * @param string $pessoa id da pessoa
     * @param string $sala id da secretaria
     * @return bool
     */
    public static function checkProfessor($pessoa, $sala): bool
    {
        $dto = new ProfessoresDasSalasDTO();
        $obj = new ProfessoresDasSalasADO();
        
        ProfessoresDasSalasADO::addComparison($dto, 'pessoa', CMP_EQUAL, $pessoa);
        ProfessoresDasSalasADO::addComparison($dto, 'sala', CMP_EQUAL, $sala);
        
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
        if(!doIHavePermission(NblFram::$context->token, 'SalaEBDProfessores')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        if(ProfessoresDasSalasWS::checkProfessor(NblFram::$context->data->pessoa, NblFram::$context->data->sala)) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Esse professor já está cadastrado para essa sala!');
        }
        
        $dto = new ProfessoresDasSalasDTO();
        $obj = new ProfessoresDasSalasADO();
        
        $dto->add = true;
        $dto->id = NblPHPUtil::makeNumericId();
        $dto->pessoa = NblFram::$context->data->pessoa;
        $dto->sala = NblFram::$context->data->sala;
        $dto->inicio = (empty(NblFram::$context->data->inicio)) ? NULL : NblPHPUtil::HumanDate2DBDate(NblFram::$context->data->inicio);
        $dto->fim = (empty(NblFram::$context->data->fim)) ? NULL : NblPHPUtil::HumanDate2DBDate(NblFram::$context->data->fim);
        $dto->stat = Status::ACTIVE;
        $dto->time_cad = date('Y-m-d H:i:s');
        $dto->last_mod = $dto->time_cad;
        $dto->last_amod = NblFram::$context->token['data']['nome'];
        
        $obj->add($dto);
        if($obj->sync())
        {
            /* crie o contexto de ebd, caso o usuário já não o possua */
            $instancia = InstanciasWS::getIdbyRef(NblFram::$context->data->igreja, References::IGREJA);
            if(!ContextosWS::exists($dto->pessoa, $instancia, NblFram::$context->data->igreja, References::EBD)) 
            {
                $errs = [];
                if(!ContextosWS::create($dto->pessoa, 
                        $instancia, 
                        NblFram::$context->data->igreja, 
                        References::EBD, 
                        $errs, 
                        NblFram::$context->data->perfil))
                {
                    return array('status' => 'no', 'success' => false, 'msg' => 'Erro na operação', 'errs' => $errs);
                }
            }
            
            return array('status' => 'ok', 'success' => true);
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
        if(!doIHavePermission(NblFram::$context->token, 'SalaEBDProfessores')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new ProfessoresDasSalasDTO();
        $obj = new ProfessoresDasSalasADO();
        
        $dto->edit = true;
        $dto->id = NblFram::$context->data->id;
        $dto->inicio = (empty(NblFram::$context->data->inicio)) ? NULL : NblPHPUtil::HumanDate2DBDate(NblFram::$context->data->inicio);
        $dto->fim = (empty(NblFram::$context->data->fim)) ? NULL : NblPHPUtil::HumanDate2DBDate(NblFram::$context->data->fim);
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
     * Edita status 
     * 
     * @httpmethod PUT
     * @auth yes
     * @require id
     * @return array
     */
    public function changestat(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'SalaEBDProfessores')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new ProfessoresDasSalasDTO();
        $obj = new ProfessoresDasSalasADO();
        
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
        if(!doIHavePermission(NblFram::$context->token, 'SalaEBDProfessores')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new ProfessoresDasSalasDTO();
        $obj = new ProfessoresDasSalasADO();
        
        $dto->id = NblFram::$context->data->id;
        if(!is_null($obj->get($dto)))
        {
            $d = $obj->getDTODataObject();
            
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
     * @require pessoa
     * @require sala
     * @return array
     */
    public function check(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'SalaEBDProfessores')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new ProfessoresDasSalasDTO();
        $obj = new ProfessoresDasSalasADO();
        
        ProfessoresDasSalasADO::addComparison($dto, 'pessoa', CMP_EQUAL, NblFram::$context->data->pessoa);
        ProfessoresDasSalasADO::addComparison($dto, 'sala', CMP_EQUAL, NblFram::$context->data->sala);
        
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
        if(!doIHavePermission(NblFram::$context->token, 'SalaEBDProfessores')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }

        $dto = new ProfessoresDasSalasDTO();
        $obj = new ProfessoresDasSalasADO();
        $obj_count = new ProfessoresDasSalasADO();

        $page = ($this->testInputString('page')) ? (int) NblFram::$context->data->page : -1;
        $pageSize = ($this->testInputString('pageSize')) ? (int) NblFram::$context->data->pageSize : -1;
        $pagination = $this->calcPagination($page, $pageSize);

        $searchBy = ($this->testInputString('searchBy')) ? NblFram::$context->data->searchBy : '';
        $orderBy = ($this->testInputString('orderBy')) ? NblFram::$context->data->orderBy : '';
        $groupBy = ($this->testInputString('groupBy')) ? NblFram::$context->data->groupBy : '';
        
        $pessoa = ($this->testInputString('pessoa')) ? NblFram::$context->data->pessoa : '';
        $sala = ($this->testInputString('sala')) ? NblFram::$context->data->sala : '';
        
        // filtros que aplicam em pessoa
        $sexo = ($this->testInputString('sexo')) ? NblFram::$context->data->sexo : '';
        $estado_civil = ($this->testInputString('estado_civil')) ? NblFram::$context->data->estado_civil : '';
        $escolaridade = ($this->testInputString('escolaridade')) ? NblFram::$context->data->escolaridade : '';
        $com_filhos = ($this->testInputBool('com_filhos')) ? true : false;
        $sem_filhos = ($this->testInputBool('sem_filhos')) ? true : false;

        if(!empty($pessoa))
        {   
            // filtre por uma pessoa diretamente
            ProfessoresDasSalasADO::addComparison($dto, 'pessoa', CMP_EQUAL, $pessoa);
            
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
            
            ProfessoresDasSalasADO::addComparison($dto, 'pessoa', CMP_IN_LIST, $id_list);
        }
        
        if(!empty($sala)) 
        {
            ProfessoresDasSalasADO::addComparison($dto, 'sala', CMP_EQUAL, $sala);
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
                        'pessoa' => $it->pessoa,
                        'sala' => $it->sala,
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
        if(!doIHavePermission(NblFram::$context->token, 'SalaEBDProfessores')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new ProfessoresDasSalasDTO();
        $obj = new ProfessoresDasSalasADO();
        
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
        if(!doIHavePermission(NblFram::$context->token, 'SalaEBDProfessores')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new ProfessoresDasSalasADO();
        
        foreach(NblFram::$context->data->ids as $id) {
            $dto = new ProfessoresDasSalasDTO();
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

