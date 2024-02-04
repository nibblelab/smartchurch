<?php
require_once ADO_PATH . '/Perfis.class.php'; 
require_once DAO_PATH . '/PerfisDAO.class.php'; 

/**
 * API REST de Perfis
 */
class PerfisWS extends WSUtil
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
        if(!doIHavePermission(NblFram::$context->token, 'PerfilSave')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new PerfisDTO();
        $obj = new PerfisADO();
        
        $dto->add = true;
        $dto->id = NblPHPUtil::makeNumericId();
        $dto->nome = NblFram::$context->data->nome;
        $dto->descricao = NblFram::$context->data->descricao;
        $dto->staff_only = (NblFram::$context->data->staff_only) ? GenericHave::YES : GenericHave::NO;
        $dto->time_cad = date('Y-m-d H:i:s');
        $dto->last_mod = $dto->time_cad;
        $dto->last_amod = NblFram::$context->token['data']['nome'];
        
        $obj->add($dto);
        if($obj->sync())
        {
            /* grave as permissões */
            foreach(NblFram::$context->data->permissoes as $prm) {
                if($prm->checked) {
                    $obj->addPermission($prm->id, $dto->id);
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
        if(!doIHavePermission(NblFram::$context->token, 'PerfilSave')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new PerfisDTO();
        $obj = new PerfisADO();
        
        $dto->edit = true;
        $dto->id = NblFram::$context->data->id;
        $dto->nome = NblFram::$context->data->nome;
        $dto->descricao = NblFram::$context->data->descricao;
        $dto->staff_only = (NblFram::$context->data->staff_only) ? GenericHave::YES : GenericHave::NO;
        $dto->time_cad = VOID;
        $dto->last_mod = date('Y-m-d H:i:s');
        $dto->last_amod = NblFram::$context->token['data']['nome'];
        
        $obj->add($dto);
        if($obj->sync())
        {
            // atualize as permissões
            $permissoes_old = $obj->getPermissoes($dto->id);
            foreach(NblFram::$context->data->permissoes as $prm) {
                if($prm->checked) {
                    if(!checkIfExistsInArray($permissoes_old, 'id', $prm->id)) {
                        // novo. Adicione
                        $obj->addPermission($prm->id, $dto->id);
                    }
                }
                else {
                    if(checkIfExistsInArray($permissoes_old, 'id', $prm->id)) {
                        // existia antes e não está na lista nova. Remova
                        $obj->removePermission($prm->id, $dto->id);
                    }
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
     * Busca 
     * 
     * @httpmethod GET
     * @auth yes
     * @require id
     * @return array
     */
    public function me(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'Perfil')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new PerfisDTO();
        $obj = new PerfisADO();
        
        $dto->id = NblFram::$context->data->id;
        if(!is_null($obj->get($dto)))
        {
            $d = $obj->getDTODataObject();
            $d->staff_only = ($d->staff_only == GenericHave::YES);
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
        if(!doIHavePermission(NblFram::$context->token, 'Perfil,Me')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }

        $dto = new PerfisDTO();
        $obj = new PerfisADO();
        $obj_count = new PerfisADO();

        $page = ($this->testInputString('page')) ? (int) NblFram::$context->data->page : -1;
        $pageSize = ($this->testInputString('pageSize')) ? (int) NblFram::$context->data->pageSize : -1;
        $pagination = $this->calcPagination($page, $pageSize);

        $searchBy = ($this->testInputString('searchBy')) ? NblFram::$context->data->searchBy : '';
        $orderBy = ($this->testInputString('orderBy')) ? NblFram::$context->data->orderBy : '';
        $groupBy = ($this->testInputString('groupBy')) ? NblFram::$context->data->groupBy : '';
        
        $staff_only = ($this->testInputBool('staff_only')) ? true : false;
        $no_staff = ($this->testInputBool('no_staff')) ? true : false;

        $has_at_least_one_filter = false;

        if(!empty($searchBy))
        {   
            $has_at_least_one_filter = true;
            PerfisADO::addComparison($dto, 'nome', CMP_INCLUDE_INSIDE, $searchBy, OP_OR);
            PerfisADO::addComparison($dto, 'descricao', CMP_INCLUDE_INSIDE, $searchBy, OP_OR);
        }
        
        if($staff_only)
        {   
            $has_at_least_one_filter = true;
            PerfisADO::addComparison($dto, 'staff_only', CMP_EQUAL, GenericHave::YES);
        }
        
        if($no_staff)
        {   
            $has_at_least_one_filter = true;
            PerfisADO::addComparison($dto, 'staff_only', CMP_EQUAL, GenericHave::NO);
        }
        
        if(!empty($orderBy))
        {
            $has_at_least_one_filter = true;
            $order_by_v = explode(',',$orderBy);
            PerfisADO::addOrdering($dto, $order_by_v[0], ($order_by_v[1] == 'asc') ? ORDER_ASC : ORDER_DESC);
        }

        if(!empty($groupBy))
        {
            $has_at_least_one_filter = true;
            $groups = explode(',',$groupBy);
            foreach($groups as $g) {
                PerfisADO::addGrouping($dto, $g);
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
                        'descricao' => $it->descricao,
                        'staff_only' => ($it->staff_only == GenericHave::YES),
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
     * Busca as permissões do perfil
     * 
     * @httpmethod GET
     * @auth yes
     * @require id
     * @return array
     */
    public function permissoes(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'Perfil')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }

        $obj = new PerfisADO();
        $permissoes = $obj->getPermissoes(NblFram::$context->data->id);

        if(!empty($permissoes))
        {
            return array('status' => 'ok', 'success' => true, 'datas' => $permissoes, 'total' => count($permissoes));
        }
        else
        {
            return array('status' => 'no', 'success' => false, 'msg' => 'Permissões não encontradas');
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
        if(!doIHavePermission(NblFram::$context->token, 'PerfilRemove')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new PerfisDTO();
        $obj = new PerfisADO();
        
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
        if(!doIHavePermission(NblFram::$context->token, 'PerfilRemove')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new PerfisADO();
        
        foreach(NblFram::$context->data->ids as $id) {
            $dto = new PerfisDTO();
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
