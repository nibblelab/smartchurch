<?php
require_once ADO_PATH . '/Federacoes.class.php'; 
require_once DAO_PATH . '/FederacoesDAO.class.php'; 
require_once WS_PATH . '/igrejas.ws.php'; 

/**
 * API REST de Federacoes
 */
class FederacoesWS extends WSUtil
{
    /**
     * 
     * @var \FederacoesWS singleton instance
     */
    private static $_Instance = null;
    
    /**
     * Get singleton instance
     * 
     * @return \FederacoesWS
     */
    public static function getInstance(): \FederacoesWS {
        if(self::$_Instance == null) {
            self::$_Instance = new self();
        }
        return self::$_Instance;
    }
    
    /**
     * Obtem a federação pelo id
     * 
     * @param string $id id da federação
     * @return object|null
     */
    public static function getById($id): ?object
    {
        $dto = new FederacoesDTO();
        $obj = new FederacoesADO();
        
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
     * Obtenha os ids das igrejas da federação
     * 
     * @param string $id id da federação
     * @return array
     */
    public static function getIgrejasIds($id): array
    {
        $federacao = FederacoesWS::getById($id);
        if(!is_null($federacao))
        {
            return IgrejasWS::getIdsByPresbiterio($federacao->presbiterio);
        }
        
        return [];
    }
    
    /**
     * Obtenha os ids das federações pela sinodal
     * 
     * @param string $sinodal id da sinodal
     * @return array
     */
    public static function getIdsBySinodal($sinodal): array
    {
        $dto = new FederacoesDTO();
        $obj = new FederacoesADO();
        
        $result = [];
        
        FederacoesADO::addComparison($dto, 'sinodal', CMP_EQUAL, $sinodal);
        if($obj->getAllbyParam($dto))
        {
            $obj->iterate();
            while($obj->hasNext())
            {
                $it = $obj->next();
                if(!is_null($it->id))
                {
                    $result[] = $it->id;
                }
            }
        }
        
        return $result;
    }
    
    /**
     * Obtenha os ids das federações ativas pela sinodal
     * 
     * @param string $sinodal id da sinodal
     * @return array
     */
    public static function getIdsAtivosBySinodal($sinodal): array
    {
        $dto = new FederacoesDTO();
        $obj = new FederacoesADO();
        
        $result = [];
        
        FederacoesADO::addComparison($dto, 'sinodal', CMP_EQUAL, $sinodal);
        FederacoesADO::addComparison($dto, 'stat', CMP_EQUAL, Status::ACTIVE);
        
        if($obj->getAllbyParam($dto))
        {
            $obj->iterate();
            while($obj->hasNext())
            {
                $it = $obj->next();
                if(!is_null($it->id))
                {
                    $result[] = $it->id;
                }
            }
        }
        
        return $result;
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
        if(!doIHavePermission(NblFram::$context->token, 'FederacaoSave')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new FederacoesDTO();
        $obj = new FederacoesADO();
        
        $dto->add = true;
        $dto->id = NblPHPUtil::makeNumericId();
        $dto->sinodo = (empty(NblFram::$context->data->sinodo)) ? NULL : NblFram::$context->data->sinodo;
        $dto->presbiterio = (empty(NblFram::$context->data->presbiterio)) ? NULL : NblFram::$context->data->presbiterio;
        $dto->sinodal = (empty(NblFram::$context->data->sinodal)) ? NULL : NblFram::$context->data->sinodal;
        $dto->reference = NblFram::$context->data->reference;
        $dto->sigla = NblFram::$context->data->sigla;
        $dto->nome = NblFram::$context->data->nome;
        $dto->email = NblFram::$context->data->email;
        $dto->fundacao = (empty(NblFram::$context->data->fundacao)) ? NULL : NblPHPUtil::HumanDate2DBDate(NblFram::$context->data->fundacao);
        $dto->site = NblFram::$context->data->site;
        $dto->facebook = NblFram::$context->data->facebook;
        $dto->instagram = NblFram::$context->data->instagram;
        $dto->youtube = NblFram::$context->data->youtube;
        $dto->vimeo = NblFram::$context->data->vimeo;
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
        if(!doIHavePermission(NblFram::$context->token, 'FederacaoSave')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new FederacoesDTO();
        $obj = new FederacoesADO();
        
        $dto->edit = true;
        $dto->id = NblFram::$context->data->id;
        $dto->sinodo = (empty(NblFram::$context->data->sinodo)) ? NULL : NblFram::$context->data->sinodo;
        $dto->presbiterio = (empty(NblFram::$context->data->presbiterio)) ? NULL : NblFram::$context->data->presbiterio;
        $dto->sinodal = (empty(NblFram::$context->data->sinodal)) ? NULL : NblFram::$context->data->sinodal;
        $dto->reference = NblFram::$context->data->reference;
        $dto->sigla = NblFram::$context->data->sigla;
        $dto->nome = NblFram::$context->data->nome;
        $dto->email = NblFram::$context->data->email;
        $dto->fundacao = (empty(NblFram::$context->data->fundacao)) ? NULL : NblPHPUtil::HumanDate2DBDate(NblFram::$context->data->fundacao);
        $dto->site = NblFram::$context->data->site;
        $dto->facebook = NblFram::$context->data->facebook;
        $dto->instagram = NblFram::$context->data->instagram;
        $dto->youtube = NblFram::$context->data->youtube;
        $dto->vimeo = NblFram::$context->data->vimeo;
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
        if(!doIHavePermission(NblFram::$context->token, 'FederacaoBlock')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new FederacoesDTO();
        $obj = new FederacoesADO();
        
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
        if(!doIHavePermission(NblFram::$context->token, 'Federacao,Dados')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new FederacoesDTO();
        $obj = new FederacoesADO();
        
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
        if(!doIHavePermission(NblFram::$context->token, 'Federacao,Dados')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }

        $dto = new FederacoesDTO();
        $obj = new FederacoesADO();
        $obj_count = new FederacoesADO();

        $page = ($this->testInputString('page')) ? (int) NblFram::$context->data->page : -1;
        $pageSize = ($this->testInputString('pageSize')) ? (int) NblFram::$context->data->pageSize : -1;
        $pagination = $this->calcPagination($page, $pageSize);

        $searchBy = ($this->testInputString('searchBy')) ? NblFram::$context->data->searchBy : '';
        $orderBy = ($this->testInputString('orderBy')) ? NblFram::$context->data->orderBy : '';
        $groupBy = ($this->testInputString('groupBy')) ? NblFram::$context->data->groupBy : '';
        
        $stat = ($this->testInputString('stat')) ? NblFram::$context->data->stat : '';
        $sinodo = ($this->testInputString('sinodo')) ? NblFram::$context->data->sinodo : '';
        $presbiterio = ($this->testInputString('presbiterio')) ? NblFram::$context->data->presbiterio : '';
        $sinodal = ($this->testInputString('sinodal')) ? NblFram::$context->data->sinodal : '';
        $reference = ($this->testInputString('reference')) ? NblFram::$context->data->reference : '';

        $has_at_least_one_filter = false;

        if(!empty($searchBy))
        {   
            $has_at_least_one_filter = true;
            FederacoesADO::addComparison($dto, 'nome', CMP_INCLUDE_INSIDE, $searchBy, OP_OR);
            FederacoesADO::addComparison($dto, 'sigla', CMP_INCLUDE_INSIDE, $searchBy, OP_OR);
        }

        if(!empty($stat))
        {   
            $has_at_least_one_filter = true;
            FederacoesADO::addComparison($dto, 'stat', CMP_EQUAL, $stat);
        }

        if(!empty($sinodo))
        {   
            $has_at_least_one_filter = true;
            FederacoesADO::addComparison($dto, 'sinodo', CMP_EQUAL, $sinodo);
        }

        if(!empty($presbiterio))
        {   
            $has_at_least_one_filter = true;
            FederacoesADO::addComparison($dto, 'presbiterio', CMP_EQUAL, $presbiterio);
        }

        if(!empty($sinodal))
        {   
            $has_at_least_one_filter = true;
            FederacoesADO::addComparison($dto, 'sinodal', CMP_EQUAL, $sinodal);
        }

        if(!empty($reference))
        {   
            $has_at_least_one_filter = true;
            FederacoesADO::addComparison($dto, 'reference', CMP_EQUAL, $reference);
        }
        
        if(!empty($orderBy))
        {
            $has_at_least_one_filter = true;
            $order_by_v = explode(',',$orderBy);
            FederacoesADO::addOrdering($dto, $order_by_v[0], ($order_by_v[1] == 'asc') ? ORDER_ASC : ORDER_DESC);
        }

        if(!empty($groupBy))
        {
            $has_at_least_one_filter = true;
            $groups = explode(',',$groupBy);
            foreach($groups as $g) {
                FederacoesADO::addGrouping($dto, $g);
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
                        'sinodo' => $it->sinodo,
                        'presbiterio' => $it->presbiterio,
                        'sinodal' => $it->sinodal,
                        'reference' => $it->reference,
                        'sigla' => $it->sigla,
                        'nome' => $it->nome,
                        'email' => $it->email,
                        'fundacao' => $it->fundacao,
                        'site' => $it->site,
                        'facebook' => $it->facebook,
                        'instagram' => $it->instagram,
                        'youtube' => $it->youtube,
                        'vimeo' => $it->vimeo,
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
        if(!doIHavePermission(NblFram::$context->token, 'FederacaoRemove')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new FederacoesDTO();
        $obj = new FederacoesADO();
        
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
        if(!doIHavePermission(NblFram::$context->token, 'FederacaoRemove')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new FederacoesADO();
        
        foreach(NblFram::$context->data->ids as $id) {
            $dto = new FederacoesDTO();
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

