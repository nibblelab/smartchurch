<?php
require_once ADO_PATH . '/TagsAgenda.class.php'; 
require_once DAO_PATH . '/TagsAgendaDAO.class.php'; 

/**
 * API REST de TagsAgenda
 */
class TagsAgendaWS extends WSUtil
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
        if(!doIHavePermission(NblFram::$context->token, 'TagAgendaSave')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new TagsAgendaDTO();
        $obj = new TagsAgendaADO();
        
        $dto->add = true;
        $dto->id = NblPHPUtil::makeNumericId();
        $dto->tag = NblFram::$context->data->tag;
        $dto->cor = NblFram::$context->data->cor;
        $dto->contextos = NblFram::$context->data->contextos;
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
        if(!doIHavePermission(NblFram::$context->token, 'TagAgendaSave')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new TagsAgendaDTO();
        $obj = new TagsAgendaADO();
        
        $dto->edit = true;
        $dto->id = NblFram::$context->data->id;
        $dto->tag = NblFram::$context->data->tag;
        $dto->cor = NblFram::$context->data->cor;
        $dto->contextos = NblFram::$context->data->contextos;
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
        if(!doIHavePermission(NblFram::$context->token, 'TagAgendaBlock')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new TagsAgendaDTO();
        $obj = new TagsAgendaADO();
        
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
        if(!doIHavePermission(NblFram::$context->token, 'TagAgenda')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new TagsAgendaDTO();
        $obj = new TagsAgendaADO();
        
        $dto->id = NblFram::$context->data->id;
        if(!is_null($obj->get($dto)))
        {
            $d = $obj->getDTODataObject();
            $d->contextos = json_decode($d->contextos);
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
        if(!doIHavePermission(NblFram::$context->token, 'Dados,TagAgenda')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }

        $dto = new TagsAgendaDTO();
        $obj = new TagsAgendaADO();
        $obj_count = new TagsAgendaADO();

        $page = ($this->testInputString('page')) ? (int) NblFram::$context->data->page : -1;
        $pageSize = ($this->testInputString('pageSize')) ? (int) NblFram::$context->data->pageSize : -1;
        $pagination = $this->calcPagination($page, $pageSize);

        $searchBy = ($this->testInputString('searchBy')) ? NblFram::$context->data->searchBy : '';
        $orderBy = ($this->testInputString('orderBy')) ? NblFram::$context->data->orderBy : '';
        $groupBy = ($this->testInputString('groupBy')) ? NblFram::$context->data->groupBy : '';
        
        $stat = ($this->testInputString('stat')) ? NblFram::$context->data->stat : '';
        $contextos = ($this->testInputString('contextos')) ? NblFram::$context->data->contextos : '';

        $has_at_least_one_filter = false;

        if(!empty($searchBy))
        {   
            $has_at_least_one_filter = true;
            TagsAgendaADO::addComparison($dto, 'nome', CMP_INCLUDE_INSIDE, $searchBy);
        }

        if(!empty($stat))
        {   
            $has_at_least_one_filter = true;
            TagsAgendaADO::addComparison($dto, 'stat', CMP_EQUAL, $stat);
        }

        if(!empty($contextos))
        {   
            $has_at_least_one_filter = true;
            
            $contextos_json = $this->generateFilterJsonFromInputList($contextos, ',', function($item) {
                return '"value":"'.$item.'","checked":true';
            });
            foreach($contextos_json as $ctx) {
                TagsAgendaADO::addComparison($dto, 'contextos', CMP_INCLUDE_INSIDE, $ctx, OP_OR, true);
            }
            
        }
        
        if(!empty($orderBy))
        {
            $has_at_least_one_filter = true;
            $order_by_v = explode(',',$orderBy);
            TagsAgendaADO::addOrdering($dto, $order_by_v[0], ($order_by_v[1] == 'asc') ? ORDER_ASC : ORDER_DESC);
        }

        if(!empty($groupBy))
        {
            $has_at_least_one_filter = true;
            $groups = explode(',',$groupBy);
            foreach($groups as $g) {
                TagsAgendaADO::addGrouping($dto, $g);
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
                        'tag' => $it->tag,
                        'cor' => $it->cor,
                        'contextos' => $it->contextos,
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
        if(!doIHavePermission(NblFram::$context->token, 'TagAgendaRemove')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new TagsAgendaDTO();
        $obj = new TagsAgendaADO();
        
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
        if(!doIHavePermission(NblFram::$context->token, 'TagAgendaRemove')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new TagsAgendaADO();
        
        foreach(NblFram::$context->data->ids as $id) {
            $dto = new TagsAgendaDTO();
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

