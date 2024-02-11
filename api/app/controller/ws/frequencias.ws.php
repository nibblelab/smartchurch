<?php
require_once ADO_PATH . '/Frequencias.class.php'; 
require_once DAO_PATH . '/FrequenciasDAO.class.php'; 
require_once WS_PATH . '/pessoas.ws.php'; 
require_once WS_PATH . '/salasebd.ws.php'; 
require_once WS_PATH . '/atualizacoessumarios.ws.php'; 


/**
 * API REST de Frequencias
 */
class FrequenciasWS extends WSUtil
{
    /**
     * 
     * @var \FrequenciasWS singleton instance
     */
    private static $_Instance = null;
    
    /**
     * Get singleton instance
     * 
     * @return \FrequenciasWS
     */
    public static function getInstance(): \FrequenciasWS {
        if(self::$_Instance == null) {
            self::$_Instance = new self();
        }
        return self::$_Instance;
    }
    
    /**
     * Busque as frequências por sala e dia
     * 
     * @param string $sala id da sala
     * @param string $dia dia do sumário. Formato: YYYY-MM-DD
     * @return array
     */
    public static function getBySalaAndDia($sala, $dia): array 
    {
        $dto = new FrequenciasDTO();
        $obj = new FrequenciasADO();
        
        FrequenciasADO::addComparison($dto, 'sala', CMP_EQUAL, $sala);
        FrequenciasADO::addComparison($dto, 'dia', CMP_EQUAL, $dia);
        $result = [];
        if($obj->getAllbyParam($dto)) {
            $obj->iterate();
            while($obj->hasNext())
            {
                $it = $obj->next();
                if(!is_null($it->id))
                {
                    $result[] = array(
                        'id' => $it->id,
                        'pessoa' => $it->pessoa,
                        'presente' => ($it->presente == Frequencia::PRESENTE)
                    );
                }
            }
        }
        
        return $result;
    }
    
    /**
     * Gere um registro de atualização de sumário
     * 
     * @param string $sala id da sala
     * @param string $dia dia do sumário
     * @return void
     */
    public function registerAtualizacaoSumario($sala, $dia): void
    {
        if(empty(AtualizacoesSumariosWS::getBySalaAndDia($sala, $dia))) {
            AtualizacoesSumariosWS::create($sala, $dia);
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
        if(!doIHavePermission(NblFram::$context->token, 'FrequenciaEBDSave')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new FrequenciasADO();
        
        $dia = NblPHPUtil::HumanDate2DBDate(NblFram::$context->data->dia);
        foreach(NblFram::$context->data->frequencias as $frequencia) 
        {
            $dto = new FrequenciasDTO();
            $dto->add = true;
            $dto->id = NblPHPUtil::makeNumericId();
            $dto->pessoa = $frequencia->pessoa;
            $dto->sala = NblFram::$context->data->sala;
            $dto->presente = ($frequencia->presente) ? Frequencia::PRESENTE : Frequencia::AUSENTE;
            $dto->dia = $dia;

            $obj->add($dto);
        }
        
        if($obj->sync())
        {
            $this->registerAtualizacaoSumario(NblFram::$context->data->sala, $dia);
            SalasEbdWS::updateAtualizarSumario(NblFram::$context->data->sala, GenericHave::YES);
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
     * @httpmethod POST
     * @auth yes
     * @require frequencias
     * @return array
     */
    public function edit(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'FrequenciaEBDSave')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new FrequenciasADO();
        
        $dia = NblPHPUtil::HumanDate2DBDate(NblFram::$context->data->dia);
        foreach(NblFram::$context->data->frequencias as $frequencia) 
        {
            $dto = new FrequenciasDTO();
            $dto->edit = true;
            $dto->id = $frequencia->id;
            $dto->presente = ($frequencia->presente) ? Frequencia::PRESENTE : Frequencia::AUSENTE;
            $dto->dia = $dia;

            $obj->add($dto);
        }
        
        if($obj->sync())
        {
            $this->registerAtualizacaoSumario(NblFram::$context->data->sala, $dia);
            SalasEbdWS::updateAtualizarSumario(NblFram::$context->data->sala, GenericHave::YES);
            return array('status' => 'ok', 'success' => true, 'id' => $dto->id);
        }
        else
        {
            return array('status' => 'no', 'success' => false, 'msg' => 'Erro na operação', 'errs' => $obj->getErrs());
        }
    }

    /**
     * 
     * Edita presenca
     * 
     * @httpmethod PUT
     * @auth yes
     * @require id
     * @return array
     */
    public function changepresenca(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'FrequenciaEBDSave')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new AlunosDasSalasDTO();
        $obj = new AlunosDasSalasADO();
        
        $dto->edit = true;
        $dto->id = NblFram::$context->data->id;
        $dto->presente = (NblFram::$context->data->presente) ? Frequencia::PRESENTE : Frequencia::AUSENTE;
        
        $obj->add($dto);
        if($obj->sync())
        {
            return array('status' => 'ok', 'success' => true);
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
     * @require dia
     * @require sala
     * @return array
     */
    public function byDiaAndSala(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'FrequenciaEBD')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }

        $dto = new FrequenciasDTO();
        $obj = new FrequenciasADO();
        
        FrequenciasADO::addComparison($dto, 'sala', CMP_EQUAL, NblFram::$context->data->sala);
        FrequenciasADO::addComparison($dto, 'dia', CMP_EQUAL, NblFram::$context->data->dia);
        
        $pessoas = PessoasWS::mapBasicDataById();
        
        $ok = $obj->getAllbyParam($dto);
        $result = [];
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
                                  
                    $result[] = array(
                        'id' => $it->id,
                        'pessoa' => $it->pessoa,
                        'sala' => $it->sala,
                        'nome' => $pessoa_nome,
                        'presente' => ($it->presente == Frequencia::PRESENTE),
                        'dia' => $it->dia
                    );
                }
            }
            
            return array('status' => 'ok', 'success' => true, 'datas' => $result);
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
        if(!doIHavePermission(NblFram::$context->token, 'FrequenciaEBD')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }

        $dto = new FrequenciasDTO();
        $obj = new FrequenciasADO();

        $page = ($this->testInputString('page')) ? (int) NblFram::$context->data->page : -1;
        $pageSize = ($this->testInputString('pageSize')) ? (int) NblFram::$context->data->pageSize : -1;
        $pagination = $this->calcPagination($page, $pageSize);

        $searchBy = ($this->testInputString('searchBy')) ? NblFram::$context->data->searchBy : '';
        $orderBy = ($this->testInputString('orderBy')) ? NblFram::$context->data->orderBy : '';
        $groupBy = ($this->testInputString('groupBy')) ? NblFram::$context->data->groupBy : '';

        $pessoa = ($this->testInputString('pessoa')) ? NblFram::$context->data->pessoa : '';
        $sala = ($this->testInputString('sala')) ? NblFram::$context->data->sala : '';
        
        $presente = ($this->testInputString('presente')) ? NblFram::$context->data->presente : '';
        $dia = ($this->testInputString('dia')) ? NblFram::$context->data->dia : '';
        
        // filtros que aplicam em pessoa
        $sexo = ($this->testInputString('sexo')) ? NblFram::$context->data->sexo : '';
        $estado_civil = ($this->testInputString('estado_civil')) ? NblFram::$context->data->estado_civil : '';
        $escolaridade = ($this->testInputString('escolaridade')) ? NblFram::$context->data->escolaridade : '';
        $com_filhos = ($this->testInputBool('com_filhos')) ? true : false;
        $sem_filhos = ($this->testInputBool('sem_filhos')) ? true : false;

        if(!empty($pessoa))
        {   
            // filtre por uma pessoa diretamente
            FrequenciasADO::addComparison($dto, 'pessoa', CMP_EQUAL, $pessoa);
            
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
            
            FrequenciasADO::addComparison($dto, 'pessoa', CMP_IN_LIST, $id_list);
        }
        
        if(!empty($sala)) 
        {
            FrequenciasADO::addComparison($dto, 'sala', CMP_EQUAL, $sala);
        }
        
        if(!empty($presente)) 
        {
            FrequenciasADO::addComparison($dto, 'presente', CMP_EQUAL, $presente);
        }
        
        // filtro por dia no searchBy ou no campo dia
        $has_range_filter = false;
        if(!empty($dia)) {
            if($this->isDateRangeStr($dia)) {
                // pesquisa por faixa de datas
                $range = $this->getDateRangeFromStr($dia);
                if(!empty($range)) {
                    FrequenciasADO::addComparison($dto, 'dia', CMP_GREATER_THEN, NblPHPUtil::HumanDate2DBDate($range['start']), OP_AND, true);
                    FrequenciasADO::addComparison($dto, 'dia', CMP_LESSER_THEN, NblPHPUtil::HumanDate2DBDate($range['end']), OP_AND, true);
                    $has_range_filter = true;
                }
            }
            else if($this->isDateStr($dia)) {
                // pesquisa por data exata
                FrequenciasADO::addComparison($dto, 'dia', CMP_EQUAL, NblPHPUtil::HumanDate2DBDate($dia));
                $has_range_filter = true;
            }
        }
        else if(!empty($searchBy))
        {
            $dia = $searchBy;
            if($this->isDateRangeStr($dia)) {
                // pesquisa por faixa de datas
                $range = $this->getDateRangeFromStr($dia);
                if(!empty($range)) {
                    FrequenciasADO::addComparison($dto, 'dia', CMP_GREATER_THEN, NblPHPUtil::HumanDate2DBDate($range['start']), OP_AND, true);
                    FrequenciasADO::addComparison($dto, 'dia', CMP_LESSER_THEN, NblPHPUtil::HumanDate2DBDate($range['end']), OP_AND, true);
                    $has_range_filter = true;
                }
            }
            else if($this->isDateStr($dia)) {
                // pesquisa por data exata
                FrequenciasADO::addComparison($dto, 'dia', CMP_EQUAL, NblPHPUtil::HumanDate2DBDate($dia));
                $has_range_filter = true;
            }
        }
        
        // não houve filtro por data, então busque a partir das 10 últimas datas em que há registros
        if(!$has_range_filter && !empty($sala)) {
            $range = $obj->getFrequenciasRangeBySala($sala);
            if(!empty($range)) {
                $range = array_reverse($range);
                FrequenciasADO::addComparison($dto, 'dia', CMP_GREATER_THEN, NblPHPUtil::HumanDate2DBDate($range[0]), OP_AND, true);
                FrequenciasADO::addComparison($dto, 'dia', CMP_LESSER_THEN, NblPHPUtil::HumanDate2DBDate($range[count($range)-1]), OP_AND, true);
            }
        }
        
        $ok = $obj->getAllbyParam($dto);
        
        $periodo = array();
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
                    
                    if(!in_array($it->dia, $periodo)) {
                        $periodo[] = $it->dia;
                    }
                                        
                    $pre_result[] = array(
                        'id' => $it->id,
                        'pessoa' => $it->pessoa,
                        'sala' => $it->sala,
                        'nome' => $pessoa_nome,
                        'presente' => ($it->presente == Frequencia::PRESENTE),
                        'dia' => $it->dia
                    );
                }
            }
            
            /* ordene */
            if(!empty($orderBy))
            {
                $pre_result = $this->orderBy($pre_result);
            }
            
            usort($periodo, function($a, $b) {
                return ($a == $b) ? 0 : (($a < $b) ? -1 : 1);
            });
            
            /* gere o resultado final */
            $result = array();
            foreach($pre_result as $pre) {
                if(!isset($result[$pre['pessoa']])) {
                    $result[$pre['pessoa']] = array(
                        'pessoa' => $pre['pessoa'],
                        'nome' => $pre['nome'],
                        'frequencias' => []
                    );
                    
                    foreach($periodo as $p) {
                        $result[$pre['pessoa']]['frequencias'][$p] = array();
                    }
                }
                
                $result[$pre['pessoa']]['frequencias'][$pre['dia']] = array(
                    'id' => $pre['id'],
                    'presente' => $pre['presente'],
                    'dia' => $pre['dia']
                );
            }
            
            
            $total = count($result);
            
            return array('status' => 'ok', 'success' => true, 'datas' => array_values($result), 'total' => $total, 'range' => $periodo);
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
        if(!doIHavePermission(NblFram::$context->token, 'FrequenciaEBDRemove')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new FrequenciasDTO();
        $obj = new FrequenciasADO();
        
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
     * Remove
     * 
     * @httpmethod POST
     * @auth yes
     * @require dia
     * @require sala
     * @return array
     */
    public function removeByDiaAndSala(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'FrequenciaEBDRemove')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new FrequenciasADO();
        
        if($obj->deleteByDiaAndSala(NblPHPUtil::HumanDate2DBDate(NblFram::$context->data->dia), 
                                        NblFram::$context->data->sala))
        {
            return array('status' => 'ok', 'success' => true);
        }
        else 
        {
            return array('status' => 'no', 'success' => false, 'msg' => 'Erro na operação', 'errs' => $obj->getErrs());
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
        if(!doIHavePermission(NblFram::$context->token, 'FrequenciaEBDRemove')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new FrequenciasADO();
        
        foreach(NblFram::$context->data->ids as $id) {
            $dto = new FrequenciasDTO();
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

