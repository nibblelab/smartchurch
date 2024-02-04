<?php
require_once ADO_PATH . '/Secretarias.class.php'; 
require_once DAO_PATH . '/SecretariasDAO.class.php'; 
require_once WS_PATH . '/instancias.ws.php'; 

/**
 * API REST de Secretarias
 */
class SecretariasWS extends WSUtil
{
    /**
     * Obtêm os ids das secretarias por suas referências
     * 
     * @param string $ref id da referência
     * @param \References $ref_tp tipo de referência
     * @return array
     */
    public static function getIdsByRef($ref, $ref_tp): array
    {
        $dto = new SecretariasDTO();
        $obj = new SecretariasADO();
        
        SecretariasADO::addComparison($dto, 'ref', CMP_EQUAL, $ref);
        SecretariasADO::addComparison($dto, 'ref_tp', CMP_EQUAL, $ref_tp);
        
        $result = [];
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
     * Obtenha os ids das secretarias da igreja
     * 
     * @param string $igreja id da igreja
     * @return array
     */
    public static function getIdsByIgreja($igreja): array
    {
        return SecretariasWS::getIdsByRef($igreja, References::IGREJA);
    }
    
    /**
     * Obtenha os ids das secretarias da federação
     * 
     * @param string $federacao id da federação
     * @return array
     */
    public static function getIdsByFederacao($federacao): array
    {
        return SecretariasWS::getIdsByRef($federacao, References::FEDERACAO);
    }
    
    /**
     * Obtenha os ids das secretarias da sinodal
     * 
     * @param string $sinodal id da sinodal
     * @return array
     */
    public static function getIdsBySinodal($sinodal): array
    {
        return SecretariasWS::getIdsByRef($sinodal, References::SINODAL);
    }
    
    /**
     * Obtenha os ids das secretarias da sociedade
     * 
     * @param string $sociedade id da sociedade
     * @return array
     */
    public static function getIdsBySociedade($sociedade): array
    {
        return SecretariasWS::getIdsByRef($sociedade, References::SOCIEDADE);
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
        $perms = array('SecretariaIgrejaSave','SecretariaSociedadeSave','SecretariaMinisterioSave','SecretariaSinodalSave');
        if(!doIHavePermission(NblFram::$context->token, $this->stringifyArray($perms, ',', false))) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new SecretariasDTO();
        $obj = new SecretariasADO();
        
        $dto->add = true;
        $dto->id = NblPHPUtil::makeNumericId();
        $dto->nome = NblFram::$context->data->nome;
        $dto->logo = NblFram::$context->data->logo;
        $dto->email = NblFram::$context->data->email;
        $dto->telefone = NblFram::$context->data->telefone;
        $dto->ramal = NblFram::$context->data->ramal;
        $dto->site = NblFram::$context->data->site;
        $dto->facebook = NblFram::$context->data->facebook;
        $dto->instagram = NblFram::$context->data->instagram;
        $dto->youtube = NblFram::$context->data->youtube;
        $dto->vimeo = NblFram::$context->data->vimeo;
        $dto->stat = Status::ACTIVE;
        $dto->ref = NblFram::$context->data->ref;
        $dto->ref_tp = NblFram::$context->data->ref_tp;
        $dto->time_cad = date('Y-m-d H:i:s');
        $dto->last_mod = $dto->time_cad;
        $dto->last_amod = NblFram::$context->token['data']['nome'];
        
        $obj->add($dto);
        if($obj->sync())
        {
            // crie a instância para a secretaria, ligada a referência dela
            $errs = [];
            $id_instancia = '';
            if(InstanciasWS::create($dto->id, 
                                        References::SECRETARIA, 
                                        '', 
                                        InstanciasWS::getIdbyRef(NblFram::$context->data->ref, NblFram::$context->data->ref_tp), 
                                        $id_instancia, 
                                        $errs)) 
            {
                return array('status' => 'ok', 'success' => true, 'id' => $dto->id);
            }
            else
            {
                return array('status' => 'no', 'success' => false, 'msg' => 'Erro na operação', 'errs' => $errs);
            }
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
     * @return array
     */
    public function createForSociedade(): array
    {
        NblFram::$context->data->ref = NblFram::$context->data->sociedade;
        NblFram::$context->data->ref_tp = References::SOCIEDADE;
        return $this->create();
    }
    
    /**
     * 
     * Cria para ministério
     * 
     * @httpmethod POST
     * @auth yes
     * @return array
     */
    public function createForMinisterio(): array
    {
        NblFram::$context->data->ref = NblFram::$context->data->ministerio;
        NblFram::$context->data->ref_tp = References::MINISTERIO;
        return $this->create();
    }
    
    /**
     * 
     * Cria para ministério
     * 
     * @httpmethod POST
     * @auth yes
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
        $perms = array('SecretariaIgrejaSave','SecretariaSociedadeSave','SecretariaMinisterioSave','SecretariaSinodalSave');
        if(!doIHavePermission(NblFram::$context->token, $this->stringifyArray($perms, ',', false))) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new SecretariasDTO();
        $obj = new SecretariasADO();
        
        $dto->edit = true;
        $dto->id = NblFram::$context->data->id;
        $dto->nome = NblFram::$context->data->nome;
        $dto->logo = NblFram::$context->data->logo;
        $dto->email = NblFram::$context->data->email;
        $dto->telefone = NblFram::$context->data->telefone;
        $dto->ramal = NblFram::$context->data->ramal;
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
        $perms = array('SecretariaIgrejaBlock','SecretariaSociedadeBlock','SecretariaMinisterioBlock','SecretariaSinodalBlock');
        if(!doIHavePermission(NblFram::$context->token, $this->stringifyArray($perms, ',', false))) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new SecretariasDTO();
        $obj = new SecretariasADO();
        
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
        $perms = array('SecretariaIgreja','SecretariaSociedade','SecretariaMinisterio','SecretariaSinodal');
        if(!doIHavePermission(NblFram::$context->token, $this->stringifyArray($perms, ',', false))) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new SecretariasDTO();
        $obj = new SecretariasADO();
        
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
        $perms = array('Dados','SecretariaIgreja','SecretariaSociedade','SecretariaMinisterio','SecretariaSinodal');
        if(!doIHavePermission(NblFram::$context->token, $this->stringifyArray($perms, ',', false))) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }

        $dto = new SecretariasDTO();
        $obj = new SecretariasADO();
        $obj_count = new SecretariasADO();

        $page = ($this->testInputString('page')) ? (int) NblFram::$context->data->page : -1;
        $pageSize = ($this->testInputString('pageSize')) ? (int) NblFram::$context->data->pageSize : -1;
        $pagination = $this->calcPagination($page, $pageSize);

        $searchBy = ($this->testInputString('searchBy')) ? NblFram::$context->data->searchBy : '';
        $orderBy = ($this->testInputString('orderBy')) ? NblFram::$context->data->orderBy : '';
        $groupBy = ($this->testInputString('groupBy')) ? NblFram::$context->data->groupBy : '';
        
        $stat = ($this->testInputString('stat')) ? NblFram::$context->data->stat : '';
        $ref_tp = ($this->testInputString('ref_tp')) ? NblFram::$context->data->ref_tp : '';
        $ref = ($this->testInputString('ref')) ? NblFram::$context->data->ref : '';
        
        $igreja = ($this->testInputString('igreja')) ? NblFram::$context->data->igreja : '';
        $sociedade = ($this->testInputString('sociedade')) ? NblFram::$context->data->sociedade : '';
        $ministerio = ($this->testInputString('ministerio')) ? NblFram::$context->data->ministerio : '';
        $sinodal = ($this->testInputString('sinodal')) ? NblFram::$context->data->sinodal : '';

        $has_at_least_one_filter = false;

        if(!empty($searchBy))
        {   
            $has_at_least_one_filter = true;
            SecretariasADO::addComparison($dto, 'nome', CMP_INCLUDE_INSIDE, $searchBy, OP_OR);
            SecretariasADO::addComparison($dto, 'email', CMP_INCLUDE_INSIDE, $searchBy, OP_OR);
            SecretariasADO::addComparison($dto, 'telefone', CMP_INCLUDE_INSIDE, $searchBy, OP_OR);
            SecretariasADO::addComparison($dto, 'ramal', CMP_INCLUDE_INSIDE, $searchBy, OP_OR);
        }

        if(!empty($stat))
        {   
            $has_at_least_one_filter = true;
            SecretariasADO::addComparison($dto, 'stat', CMP_EQUAL, $stat);
        }

        if(!empty($ref_tp))
        {   
            $has_at_least_one_filter = true;
            SecretariasADO::addComparison($dto, 'ref_tp', CMP_EQUAL, $ref_tp);
        }

        if(!empty($ref))
        {   
            $has_at_least_one_filter = true;
            SecretariasADO::addComparison($dto, 'ref', CMP_EQUAL, $ref);
        }

        if(!empty($igreja))
        {   
            $has_at_least_one_filter = true;
            SecretariasADO::addComparison($dto, 'ref_tp', CMP_EQUAL, References::IGREJA);
            SecretariasADO::addComparison($dto, 'ref', CMP_EQUAL, $igreja);
        }

        if(!empty($sociedade))
        {   
            $has_at_least_one_filter = true;
            SecretariasADO::addComparison($dto, 'ref_tp', CMP_EQUAL, References::SOCIEDADE);
            SecretariasADO::addComparison($dto, 'ref', CMP_EQUAL, $sociedade);
        }
        
        if(!empty($ministerio))
        {   
            $has_at_least_one_filter = true;
            SecretariasADO::addComparison($dto, 'ref_tp', CMP_EQUAL, References::MINISTERIO);
            SecretariasADO::addComparison($dto, 'ref', CMP_EQUAL, $ministerio);
        }
        
        if(!empty($sinodal))
        {   
            $has_at_least_one_filter = true;
            SecretariasADO::addComparison($dto, 'ref_tp', CMP_EQUAL, References::SINODAL);
            SecretariasADO::addComparison($dto, 'ref', CMP_EQUAL, $sinodal);
        }
        
        if(!empty($orderBy))
        {
            $has_at_least_one_filter = true;
            $order_by_v = explode(',',$orderBy);
            SecretariasADO::addOrdering($dto, $order_by_v[0], ($order_by_v[1] == 'asc') ? ORDER_ASC : ORDER_DESC);
        }

        if(!empty($groupBy))
        {
            $has_at_least_one_filter = true;
            $groups = explode(',',$groupBy);
            foreach($groups as $g) {
                SecretariasADO::addGrouping($dto, $g);
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
                        'logo' => $it->logo,
                        'email' => $it->email,
                        'telefone' => $it->telefone,
                        'ramal' => $it->ramal,
                        'site' => $it->site,
                        'facebook' => $it->facebook,
                        'instagram' => $it->instagram,
                        'youtube' => $it->youtube,
                        'vimeo' => $it->vimeo,
                        'stat' => $it->stat,
                        'ref' => $it->ref,
                        'ref_tp' => $it->ref_tp,
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
        $perms = array('SecretariaIgrejaRemove','SecretariaSociedadeRemove','SecretariaMinisterioRemove','SecretariaSinodalRemove');
        if(!doIHavePermission(NblFram::$context->token, $this->stringifyArray($perms, ',', false))) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new SecretariasDTO();
        $obj = new SecretariasADO();
        
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
        $perms = array('SecretariaIgrejaRemove','SecretariaSociedadeRemove','SecretariaMinisterioRemove','SecretariaSinodalRemove');
        if(!doIHavePermission(NblFram::$context->token, $this->stringifyArray($perms, ',', false))) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new SecretariasADO();
        
        foreach(NblFram::$context->data->ids as $id) {
            $dto = new SecretariasDTO();
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

