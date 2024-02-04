<?php
require_once ADO_PATH . '/Eleicoes.class.php'; 
require_once DAO_PATH . '/EleicoesDAO.class.php'; 

/**
 * API REST de Eleicoes
 */
class EleicoesWS extends WSUtil
{
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
        if(!doIHavePermission(NblFram::$context->token, 'EleicaoIgrejaSave,EleicaoSinodalSave')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new EleicoesDTO();
        $obj = new EleicoesADO();
        
        $dto->add = true;
        $dto->id = NblPHPUtil::makeNumericId();
        $dto->agenda = (empty(NblFram::$context->data->agenda)) ? NULL : NblFram::$context->data->agenda;
        $dto->evento = (empty(NblFram::$context->data->evento)) ? NULL : NblFram::$context->data->evento;
        $dto->nome = NblFram::$context->data->nome;
        $dto->ref = NblFram::$context->data->ref;
        $dto->ref_tp = NblFram::$context->data->ref_tp;
        $dto->time_ini = (empty(NblFram::$context->data->time_ini)) ? NULL : NblFram::$context->data->time_ini;
        $dto->time_end = (empty(NblFram::$context->data->time_end)) ? NULL : NblFram::$context->data->time_end;
        $dto->apenas_presentes = (NblFram::$context->data->apenas_presentes) ? GenericHave::YES : GenericHave::NO;
        $dto->apenas_delegados = (NblFram::$context->data->apenas_delegados) ? GenericHave::YES : GenericHave::NO;
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
     * Cria para igreja
     * 
     * @httpmethod POST
     * @auth yes
     * @require sinodal
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
        if(!doIHavePermission(NblFram::$context->token, 'EleicaoIgrejaSave,EleicaoSinodalSave')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new EleicoesDTO();
        $obj = new EleicoesADO();
        
        $dto->edit = true;
        $dto->id = NblFram::$context->data->id;
        $dto->agenda = (empty(NblFram::$context->data->agenda)) ? NULL : NblFram::$context->data->agenda;
        $dto->evento = (empty(NblFram::$context->data->evento)) ? NULL : NblFram::$context->data->evento;
        $dto->nome = NblFram::$context->data->nome;
        $dto->time_ini = (empty(NblFram::$context->data->time_ini)) ? NULL : NblFram::$context->data->time_ini;
        $dto->time_end = (empty(NblFram::$context->data->time_end)) ? NULL : NblFram::$context->data->time_end;
        $dto->apenas_presentes = (NblFram::$context->data->apenas_presentes) ? GenericHave::YES : GenericHave::NO;
        $dto->apenas_delegados = (NblFram::$context->data->apenas_delegados) ? GenericHave::YES : GenericHave::NO;
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
        if(!doIHavePermission(NblFram::$context->token, 'EleicaoIgrejaBlock,EleicaoSinodalBlock')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new EleicoesDTO();
        $obj = new EleicoesADO();
        
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
        if(!doIHavePermission(NblFram::$context->token, 'EleicaoIgreja,EleicaoSinodal')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new EleicoesDTO();
        $obj = new EleicoesADO();
        
        $dto->id = NblFram::$context->data->id;
        if(!is_null($obj->get($dto)))
        {
            $d = $obj->getDTODataObject();
            $d->apenas_presentes = ($d->apenas_presentes == GenericHave::YES);
            $d->apenas_delegados = ($d->apenas_delegados == GenericHave::YES);
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
        if(!doIHavePermission(NblFram::$context->token, 'EleicaoIgreja,EleicaoSinodal')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }

        $dto = new EleicoesDTO();
        $obj = new EleicoesADO();
        $obj_count = new EleicoesADO();

        $page = ($this->testInputString('page')) ? (int) NblFram::$context->data->page : -1;
        $pageSize = ($this->testInputString('pageSize')) ? (int) NblFram::$context->data->pageSize : -1;
        $pagination = $this->calcPagination($page, $pageSize);

        $searchBy = ($this->testInputString('searchBy')) ? NblFram::$context->data->searchBy : '';
        $orderBy = ($this->testInputString('orderBy')) ? NblFram::$context->data->orderBy : '';
        $groupBy = ($this->testInputString('groupBy')) ? NblFram::$context->data->groupBy : '';
        
        $stat = ($this->testInputString('stat')) ? NblFram::$context->data->stat : '';
        $agenda = ($this->testInputString('agenda')) ? NblFram::$context->data->agenda : '';
        $evento = ($this->testInputString('evento')) ? NblFram::$context->data->evento : '';
        $ref = ($this->testInputString('ref')) ? NblFram::$context->data->ref : '';
        $ref_tp = ($this->testInputString('ref_tp')) ? NblFram::$context->data->ref_tp : '';
        $inicio = ($this->testInputString('inicio')) ? NblFram::$context->data->inicio : '';
        $termino = ($this->testInputString('termino')) ? NblFram::$context->data->termino : '';
        
        $igreja = ($this->testInputString('igreja')) ? NblFram::$context->data->igreja : '';
        
        $apenas_presentes = ($this->testInputBool('apenas_presentes')) ? true : false;
        $apenas_delegados = ($this->testInputBool('apenas_delegados')) ? true : false;

        $has_at_least_one_filter = false;

        if(!empty($searchBy))
        {   
            $has_at_least_one_filter = true;
            EleicoesADO::addComparison($dto, 'nome', CMP_INCLUDE_INSIDE, $searchBy);
        }

        if(!empty($stat))
        {   
            $has_at_least_one_filter = true;
            EleicoesADO::addComparison($dto, 'stat', CMP_EQUAL, $stat);
        }

        if(!empty($agenda))
        {   
            $has_at_least_one_filter = true;
            EleicoesADO::addComparison($dto, 'agenda', CMP_EQUAL, $agenda);
        }

        if(!empty($evento))
        {   
            $has_at_least_one_filter = true;
            EleicoesADO::addComparison($dto, 'evento', CMP_EQUAL, $evento);
        }

        if(!empty($ref))
        {   
            $has_at_least_one_filter = true;
            EleicoesADO::addComparison($dto, 'ref', CMP_EQUAL, $ref);
        }

        if(!empty($ref_tp))
        {   
            $has_at_least_one_filter = true;
            EleicoesADO::addComparison($dto, 'ref_tp', CMP_EQUAL, $ref_tp);
        }

        if(!empty($igreja))
        {   
            $has_at_least_one_filter = true;
            EleicoesADO::addComparison($dto, 'ref', CMP_EQUAL, $igreja);
            EleicoesADO::addComparison($dto, 'ref_tp', CMP_EQUAL, References::IGREJA);
        }
        
        if(!empty($inicio))
        {   
            $has_at_least_one_filter = true;
            EleicoesADO::addComparison($dto, 'time_ini', CMP_GREATER_THEN_DATE, NblPHPUtil::HumanDate2DBDate($inicio), OP_OR, true);
            EleicoesADO::addComparison($dto, 'time_ini', CMP_IS_NULL, '', OP_OR, true);
        }

        if(!empty($termino))
        {   
            $has_at_least_one_filter = true;
            EleicoesADO::addComparison($dto, 'time_end', CMP_LESSER_THEN_DATE, NblPHPUtil::HumanDate2DBDate($termino), OP_OR, true);
            EleicoesADO::addComparison($dto, 'time_end', CMP_IS_NULL, '', OP_OR, true);
        }

        if($apenas_presentes)
        {   
            $has_at_least_one_filter = true;
            EleicoesADO::addComparison($dto, 'apenas_presentes', CMP_EQUAL, GenericHave::YES);
        }

        if($apenas_delegados)
        {   
            $has_at_least_one_filter = true;
            EleicoesADO::addComparison($dto, 'apenas_delegados', CMP_EQUAL, GenericHave::YES);
        }
        
        if(!empty($orderBy))
        {
            $has_at_least_one_filter = true;
            $order_by_v = explode(',',$orderBy);
            EleicoesADO::addOrdering($dto, $order_by_v[0], ($order_by_v[1] == 'asc') ? ORDER_ASC : ORDER_DESC);
        }

        if(!empty($groupBy))
        {
            $has_at_least_one_filter = true;
            $groups = explode(',',$groupBy);
            foreach($groups as $g) {
                EleicoesADO::addGrouping($dto, $g);
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
                    $result[] = array(
                        'id' => $it->id,
                        'agenda' => $it->agenda,
                        'evento' => $it->evento,
                        'nome' => $it->nome,
                        'ref' => $it->ref,
                        'ref_tp' => $it->ref_tp,
                        'time_ini' => $it->time_ini,
                        'time_end' => $it->time_end,
                        'apenas_presentes' => ($it->apenas_presentes == GenericHave::YES),
                        'apenas_delegados' => ($it->apenas_delegados == GenericHave::YES),
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
     * Remove
     * 
     * @httpmethod DELETE
     * @auth yes
     * @require id
     * @return array
     */
    public function remove(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'EleicaoIgrejaRemove,EleicaoSinodalRemove')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new EleicoesDTO();
        $obj = new EleicoesADO();
        
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
        if(!doIHavePermission(NblFram::$context->token, 'EleicaoIgrejaRemove,EleicaoSinodalRemove')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new EleicoesADO();
        
        foreach(NblFram::$context->data->ids as $id) {
            $dto = new EleicoesDTO();
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

