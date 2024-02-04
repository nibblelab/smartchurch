<?php
require_once ADO_PATH . '/Diretorias.class.php'; 
require_once DAO_PATH . '/DiretoriasDAO.class.php'; 

/**
 * API REST de Diretorias
 */
class DiretoriasWS extends WSUtil
{
    
    /**
     * Desabilita diretorias cadastradas pela referência, com exceção da informada em parâmetro
     * 
     * @param string $ref referência
     * @param string $ref_tp tipo da referência
     * @param string $ignore_id id da diretoria que não deve ser desabilitada [opcional]
     * @return void
     */
    private function disableByRef($ref, $ref_tp, $ignore_id = ''): void
    {
        $dto = new DiretoriasDTO();
        $obj = new DiretoriasADO();
        
        DiretoriasADO::addComparison($dto, 'ref', CMP_EQUAL, $ref);
        DiretoriasADO::addComparison($dto, 'ref_tp', CMP_EQUAL, $ref_tp);
        
        if($obj->getAllbyParam($dto))
        {
            $obj->iterate();
            while($obj->hasNext())
            {
                $it = $obj->next();
                if(!is_null($it->id))
                {
                    if($ignore_id != $it->id) 
                    {
                        $it->edit = true;
                        $it->stat = Status::BLOCKED;
                    }
                }
            }
            
            $obj->sync();
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
        $perms = array('DiretoriaSociedadeSave','DiretoriaSinodalSave');
        if(!doIHavePermission(NblFram::$context->token, $this->stringifyArray($perms, ',', false))) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new DiretoriasDTO();
        $obj = new DiretoriasADO();
        
        $dto->add = true;
        $dto->id = NblPHPUtil::makeNumericId();
        $dto->nome = NblFram::$context->data->nome;
        $dto->gestao_inicio = (empty(NblFram::$context->data->gestao_inicio)) ? NULL : NblPHPUtil::HumanDate2DBDate(NblFram::$context->data->gestao_inicio);
        $dto->gestao_fim = (empty(NblFram::$context->data->gestao_fim)) ? NULL : NblPHPUtil::HumanDate2DBDate(NblFram::$context->data->gestao_fim);
        $dto->ref = NblFram::$context->data->ref;
        $dto->ref_tp = NblFram::$context->data->ref_tp;
        $dto->stat = Status::ACTIVE;
        $dto->time_cad = date('Y-m-d H:i:s');
        $dto->last_mod = $dto->time_cad;
        $dto->last_amod = NblFram::$context->token['data']['nome'];
                
        $obj->add($dto);
        if($obj->sync())
        {
            // desabilita as antigas 
            $this->disableByRef(NblFram::$context->data->ref, NblFram::$context->data->ref_tp, $dto->id);
            
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
    public function createForSociedade(): array
    {
        NblFram::$context->data->ref_tp = References::SOCIEDADE;
        NblFram::$context->data->ref = NblFram::$context->data->sociedade;
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
    public function createForFederacao(): array
    {
        NblFram::$context->data->ref_tp = References::FEDERACAO;
        NblFram::$context->data->ref = NblFram::$context->data->federacao;
        return $this->create();
    }
    
    /**
     * 
     * Cria
     * 
     * @httpmethod POST
     * @auth yes
     * @require sinodal
     * @return array
     */
    public function createForSinodal(): array
    {
        NblFram::$context->data->ref_tp = References::SINODAL;
        NblFram::$context->data->ref = NblFram::$context->data->sinodal;
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
        $perms = array('DiretoriaSociedadeSave','DiretoriaSinodalSave');
        if(!doIHavePermission(NblFram::$context->token, $this->stringifyArray($perms, ',', false))) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new DiretoriasDTO();
        $obj = new DiretoriasADO();
        
        $dto->edit = true;
        $dto->id = NblFram::$context->data->id;
        $dto->nome = NblFram::$context->data->nome;
        $dto->gestao_inicio = (empty(NblFram::$context->data->gestao_inicio)) ? NULL : NblPHPUtil::HumanDate2DBDate(NblFram::$context->data->gestao_inicio);
        $dto->gestao_fim = (empty(NblFram::$context->data->gestao_inicio)) ? NULL : NblPHPUtil::HumanDate2DBDate(NblFram::$context->data->gestao_fim);
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
        $perms = array('DiretoriaSociedadeBlock','DiretoriaSinodalBlock');
        if(!doIHavePermission(NblFram::$context->token, $this->stringifyArray($perms, ',', false))) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new DiretoriasDTO();
        $obj = new DiretoriasADO();
        
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
        $perms = array('DiretoriaSociedade','DiretoriaSinodal');
        if(!doIHavePermission(NblFram::$context->token, $this->stringifyArray($perms, ',', false))) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new DiretoriasDTO();
        $obj = new DiretoriasADO();
        
        $dto->id = NblFram::$context->data->id;
        if(!is_null($obj->get($dto)))
        {
            $d = $obj->getDTODataObject();
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
        $perms = array('DiretoriaSociedade','DiretoriaSinodal,Dados');
        if(!doIHavePermission(NblFram::$context->token, $this->stringifyArray($perms, ',', false))) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }

        $dto = new DiretoriasDTO();
        $obj = new DiretoriasADO();
        $obj_count = new DiretoriasADO();

        $page = ($this->testInputString('page')) ? (int) NblFram::$context->data->page : -1;
        $pageSize = ($this->testInputString('pageSize')) ? (int) NblFram::$context->data->pageSize : -1;
        $pagination = $this->calcPagination($page, $pageSize);

        $searchBy = ($this->testInputString('searchBy')) ? NblFram::$context->data->searchBy : '';
        $orderBy = ($this->testInputString('orderBy')) ? NblFram::$context->data->orderBy : '';
        $groupBy = ($this->testInputString('groupBy')) ? NblFram::$context->data->groupBy : '';
        
        $stat = ($this->testInputString('stat')) ? NblFram::$context->data->stat : '';
        $ref = ($this->testInputString('ref')) ? NblFram::$context->data->ref : '';
        $ref_tp = ($this->testInputString('ref_tp')) ? NblFram::$context->data->ref_tp : '';
        
        $sociedade = ($this->testInputString('sociedade')) ? NblFram::$context->data->sociedade : '';
        $federacao = ($this->testInputString('federacao')) ? NblFram::$context->data->federacao : '';
        $sinodal = ($this->testInputString('sinodal')) ? NblFram::$context->data->sinodal : '';

        $has_at_least_one_filter = false;

        if(!empty($searchBy))
        {   
            $has_at_least_one_filter = true;
            if($this->isDateStr($searchBy))
            {
                $searchByDate = NblPHPUtil::HumanDate2DBDate($searchBy);
                DiretoriasADO::addComparison($dto, 'gestao_inicio', CMP_GREATER_THEN_DATE, $searchByDate, OP_OR);
                DiretoriasADO::addComparison($dto, 'gestao_fim', CMP_LESSER_THEN_DATE, $searchByDate, OP_OR);
            }
            else 
            {
                DiretoriasADO::addComparison($dto, 'nome', CMP_INCLUDE_INSIDE, $searchBy);
            }
        }

        if(!empty($stat))
        {   
            $has_at_least_one_filter = true;
            DiretoriasADO::addComparison($dto, 'stat', CMP_EQUAL, $stat);
        }

        if(!empty($ref))
        {   
            $has_at_least_one_filter = true;
            DiretoriasADO::addComparison($dto, 'ref', CMP_EQUAL, $ref);
        }

        if(!empty($ref_tp))
        {   
            $has_at_least_one_filter = true;
            DiretoriasADO::addComparison($dto, 'ref_tp', CMP_EQUAL, $ref_tp);
        }

        if(!empty($sociedade))
        {   
            $has_at_least_one_filter = true;
            DiretoriasADO::addComparison($dto, 'ref', CMP_EQUAL, $sociedade);
            DiretoriasADO::addComparison($dto, 'ref_tp', CMP_EQUAL, References::SOCIEDADE);
        }

        if(!empty($federacao))
        {   
            $has_at_least_one_filter = true;
            DiretoriasADO::addComparison($dto, 'ref', CMP_EQUAL, $federacao);
            DiretoriasADO::addComparison($dto, 'ref_tp', CMP_EQUAL, References::FEDERACAO);
        }

        if(!empty($sinodal))
        {   
            $has_at_least_one_filter = true;
            DiretoriasADO::addComparison($dto, 'ref', CMP_EQUAL, $sinodal);
            DiretoriasADO::addComparison($dto, 'ref_tp', CMP_EQUAL, References::SINODAL);
        }
        
        if(!empty($orderBy))
        {
            $has_at_least_one_filter = true;
            $order_by_v = explode(',',$orderBy);
            DiretoriasADO::addOrdering($dto, $order_by_v[0], ($order_by_v[1] == 'asc') ? ORDER_ASC : ORDER_DESC);
        }

        if(!empty($groupBy))
        {
            $has_at_least_one_filter = true;
            $groups = explode(',',$groupBy);
            foreach($groups as $g) {
                DiretoriasADO::addGrouping($dto, $g);
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
                        'nome' => $it->nome,
                        'gestao_inicio' => $it->gestao_inicio,
                        'gestao_fim' => $it->gestao_fim,
                        'ref' => $it->ref,
                        'ref_tp' => $it->ref_tp,
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
        $perms = array('DiretoriaSociedadeRemove','DiretoriaSinodalRemove');
        if(!doIHavePermission(NblFram::$context->token, $this->stringifyArray($perms, ',', false))) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new DiretoriasDTO();
        $obj = new DiretoriasADO();
        
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
        $perms = array('DiretoriaSociedadeRemove','DiretoriaSinodalRemove');
        if(!doIHavePermission(NblFram::$context->token, $this->stringifyArray($perms, ',', false))) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new DiretoriasADO();
        
        foreach(NblFram::$context->data->ids as $id) {
            $dto = new DiretoriasDTO();
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

