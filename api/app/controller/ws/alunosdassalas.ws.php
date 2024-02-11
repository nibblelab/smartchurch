<?php
require_once ADO_PATH . '/AlunosDasSalas.class.php'; 
require_once DAO_PATH . '/AlunosDasSalasDAO.class.php'; 
require_once WS_PATH . '/pessoas.ws.php'; 

/**
 * API REST de AlunosDasSalas
 */
class AlunosDasSalasWS extends WSUtil
{
    /**
     * 
     * @var \AlunosDasSalasWS singleton instance
     */
    private static $_Instance = null;
    
    /**
     * Get singleton instance
     * 
     * @return \AlunosDasSalasWS
     */
    public static function getInstance(): \AlunosDasSalasWS {
        if(self::$_Instance == null) {
            self::$_Instance = new self();
        }
        return self::$_Instance;
    }
    
    /**
     * Verifica se uma pessoa é aluno de uma sala
     * 
     * @param string $pessoa id da pessoa
     * @param string $sala id da sala
     * @return bool
     */
    public static function checkAluno($pessoa, $sala): bool
    {
        $dto = new AlunosDasSalasDTO();
        $obj = new AlunosDasSalasADO();
        
        AlunosDasSalasADO::addComparison($dto, 'pessoa', CMP_EQUAL, $pessoa);
        AlunosDasSalasADO::addComparison($dto, 'sala', CMP_EQUAL, $sala);
        
        return ($obj->countBy($dto) > 0);
    }
    
    /**
     * Busca os alunos de uma sala
     * 
     * @param string $sala id da sala
     * @return array
     */
    public static function getAlunosFromSala($sala): array
    {
        $dto = new AlunosDasSalasDTO();
        $obj = new AlunosDasSalasADO();
        
        AlunosDasSalasADO::addComparison($dto, 'sala', CMP_EQUAL, $sala);
        
        $alunos = [];
        if($obj->getAllbyParam($dto)) 
        {
            $obj->iterate();
            while($obj->hasNext())
            {
                $it = $obj->next();
                if(!is_null($it->id)) 
                {
                    $alunos[] = $it->pessoa;
                }
            }
        }
        
        return $alunos;
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
        if(!doIHavePermission(NblFram::$context->token, 'SalaEBDAlunos')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new AlunosDasSalasADO();
        
        if(NblFram::$context->data->multiple) {
            $alunos = AlunosDasSalasWS::getAlunosFromSala(NblFram::$context->data->sala);
            $data_inicio = (empty(NblFram::$context->data->inicio)) ? NULL : NblPHPUtil::HumanDate2DBDate(NblFram::$context->data->inicio);
            $data_fim = (empty(NblFram::$context->data->fim)) ? NULL : NblPHPUtil::HumanDate2DBDate(NblFram::$context->data->fim);
            foreach (NblFram::$context->data->pessoas as $pessoa) {
                if($pessoa->checked) {
                    if(!in_array($pessoa->id, $alunos)) {
                        $dto = new AlunosDasSalasDTO();
            
                        $dto->add = true;
                        $dto->id = NblPHPUtil::makeNumericId();
                        $dto->pessoa = $pessoa->id;
                        $dto->sala = NblFram::$context->data->sala;
                        $dto->inicio = $data_inicio;
                        $dto->fim = $data_fim;
                        $dto->stat = Status::ACTIVE;
                        $dto->time_cad = date('Y-m-d H:i:s');
                        $dto->last_mod = $dto->time_cad;
                        $dto->last_amod = NblFram::$context->token['data']['nome'];

                        $obj->add($dto);
                    }
                }
            }
        }
        else {
            if(AlunosDasSalasWS::checkAluno(NblFram::$context->data->pessoa, NblFram::$context->data->sala)) {
                return array('status' => 'no', 'success' => false, 'msg' => 'Esse aluno já está cadastrado para essa sala!');
            }

            $dto = new AlunosDasSalasDTO();
            
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
        }
        
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
        if(!doIHavePermission(NblFram::$context->token, 'SalaEBDAlunos')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new AlunosDasSalasDTO();
        $obj = new AlunosDasSalasADO();
        
        $dto->edit = true;
        $dto->id = NblFram::$context->data->id;
        $dto->inicio = (empty(NblFram::$context->data->inicio)) ? NULL : NblPHPUtil::HumanDate2DBDate(NblFram::$context->data->inicio);
        $dto->fim = (empty(NblFram::$context->data->fim)) ? NULL : NblPHPUtil::HumanDate2DBDate(NblFram::$context->data->fim);
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
        if(!doIHavePermission(NblFram::$context->token, 'SalaEBDAlunos')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new AlunosDasSalasDTO();
        $obj = new AlunosDasSalasADO();
        
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
        if(!doIHavePermission(NblFram::$context->token, 'SalaEBDAlunos')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new AlunosDasSalasDTO();
        $obj = new AlunosDasSalasADO();
        
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
        if(!doIHavePermission(NblFram::$context->token, 'SalaEBDAlunos')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new AlunosDasSalasDTO();
        $obj = new AlunosDasSalasADO();
        
        AlunosDasSalasADO::addComparison($dto, 'pessoa', CMP_EQUAL, NblFram::$context->data->pessoa);
        AlunosDasSalasADO::addComparison($dto, 'sala', CMP_EQUAL, NblFram::$context->data->sala);
        
        return array('status' => 'ok', 'success' => true, 'exists' => ($obj->countBy($dto) > 0));
    }
    
    /**
     * 
     * Busca 
     * 
     * @httpmethod GET
     * @auth yes
     * @require sala
     * @return array
     */
    public function alunos(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'SalaEBDAlunos')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new AlunosDasSalasDTO();
        $obj = new AlunosDasSalasADO();
        
        AlunosDasSalasADO::addComparison($dto, 'sala', CMP_EQUAL, NblFram::$context->data->sala);
        
        if($obj->getAllbyParam($dto)) {
            return array('status' => 'ok', 'success' => true, 'alunos' => $obj->getDataAsArray());
        }
        else {
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
        if(!doIHavePermission(NblFram::$context->token, 'SalaEBDAlunos')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }

        $dto = new AlunosDasSalasDTO();
        $obj = new AlunosDasSalasADO();
        $obj_count = new AlunosDasSalasADO();

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
            AlunosDasSalasADO::addComparison($dto, 'pessoa', CMP_EQUAL, $pessoa);
            
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
            
            AlunosDasSalasADO::addComparison($dto, 'pessoa', CMP_IN_LIST, $id_list);
        }
        
        if(!empty($sala)) 
        {
            AlunosDasSalasADO::addComparison($dto, 'sala', CMP_EQUAL, $sala);
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
        if(!doIHavePermission(NblFram::$context->token, 'SalaEBDAlunos')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new AlunosDasSalasDTO();
        $obj = new AlunosDasSalasADO();
        
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
        if(!doIHavePermission(NblFram::$context->token, 'SalaEBDAlunos')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new AlunosDasSalasADO();
        
        foreach(NblFram::$context->data->ids as $id) {
            $dto = new AlunosDasSalasDTO();
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

