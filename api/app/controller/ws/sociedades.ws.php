<?php
require_once ADO_PATH . '/Sociedades.class.php'; 
require_once DAO_PATH . '/SociedadesDAO.class.php'; 
require_once WS_PATH . '/instancias.ws.php'; 

/**
 * API REST de Sociedades
 */
class SociedadesWS extends WSUtil
{
    /**
     * Obtêm a sociedade pelo seu id
     * 
     * @param string $id id da sociedade
     * @return object|null
     */
    public static function getById($id): ?object
    {
        $dto = new SociedadesDTO();
        $obj = new SociedadesADO();
        
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
     * Busque a sociedade pelo id da igreja e referência
     * 
     * @param string $igreja id da igreja
     * @param string $reference referência
     * @return object|null
     */
    public static function getByIgrejaAndReference($igreja, $reference): ?object
    {
        $dto = new SociedadesDTO();
        $obj = new SociedadesADO();
        
        SociedadesADO::addComparison($dto, 'igreja', CMP_EQUAL, $igreja);
        SociedadesADO::addComparison($dto, 'reference', CMP_EQUAL, $reference);
        SociedadesADO::addComparison($dto, 'stat', CMP_EQUAL, Status::ACTIVE);
        
        if(!is_null($obj->getBy($dto)))
        {
            return $obj->getDTODataObject();
        }
        
        return null;
    }

    /**
     * Obtenha os ids das sociedades da igreja
     * 
     * @param string $igreja id da igreja
     * @return array
     */
    public static function getIdsByIgreja($igreja): array
    {
        $dto = new SociedadesDTO();
        $obj = new SociedadesADO();
        
        SociedadesADO::addComparison($dto, 'igreja', CMP_EQUAL, $igreja);
        
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
     * Obtenha os ids das sociedades da federação
     * 
     * @param string $federacao id da federação
     * @return array
     */
    public static function getIdsByFederacao($federacao): array
    {
        $dto = new SociedadesDTO();
        $obj = new SociedadesADO();
        
        SociedadesADO::addComparison($dto, 'federacao', CMP_EQUAL, $federacao);
        
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
     * Obtenha os ids das igrejas das sociedades da federação
     * 
     * @param string $federacao id da federação
     * @return array
     */
    public static function getIgrejaDasSociedadesByFederacao($federacao): array
    {
        $dto = new SociedadesDTO();
        $obj = new SociedadesADO();
        
        SociedadesADO::addComparison($dto, 'federacao', CMP_EQUAL, $federacao);
        
        $result = [];
        if($obj->getAllbyParam($dto))
        {
            $obj->iterate();
            while($obj->hasNext())
            {
                $it = $obj->next();
                if(!is_null($it->id))
                {
                    $result[] = $it->igreja;
                }
            }
        }
        
        return $result;
    }
    
    /**
     * Obtenha os ids das igrejas das sociedades ativas da federação
     * 
     * @param string $federacao id da federação
     * @return array
     */
    public static function getIgrejaDasSociedadesAtivasByFederacao($federacao): array
    {
        $dto = new SociedadesDTO();
        $obj = new SociedadesADO();
        
        SociedadesADO::addComparison($dto, 'federacao', CMP_EQUAL, $federacao);
        SociedadesADO::addComparison($dto, 'stat', CMP_EQUAL, Status::ACTIVE);
        
        $result = [];
        if($obj->getAllbyParam($dto))
        {
            $obj->iterate();
            while($obj->hasNext())
            {
                $it = $obj->next();
                if(!is_null($it->id))
                {
                    $result[] = $it->igreja;
                }
            }
        }
        
        return $result;
    }
    
    /**
     * Obtenha os ids das sociedades da sinodal
     * 
     * @param string $sinodal id da sinodal
     * @return array
     */
    public static function getIdsBySinodal($sinodal): array
    {
        $dto = new SociedadesDTO();
        $obj = new SociedadesADO();
        
        SociedadesADO::addComparison($dto, 'sinodal', CMP_EQUAL, $sinodal);
        
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
     * Obtenha os ids das igrejas das sociedades da sinodal
     * 
     * @param string $sinodal id da sinodal
     * @return array
     */
    public static function getIgrejaDasSociedadesBySinodal($sinodal): array
    {
        $dto = new SociedadesDTO();
        $obj = new SociedadesADO();
        
        SociedadesADO::addComparison($dto, 'sinodal', CMP_EQUAL, $sinodal);
        
        $result = [];
        if($obj->getAllbyParam($dto))
        {
            $obj->iterate();
            while($obj->hasNext())
            {
                $it = $obj->next();
                if(!is_null($it->id))
                {
                    $result[] = $it->igreja;
                }
            }
        }
        
        return $result;
    }
    
    /**
     * Obtenha os ids das igrejas das sociedades ativas da sinodal
     * 
     * @param string $sinodal id da sinodal
     * @return array
     */
    public static function getIgrejaDasSociedadesAtivasBySinodal($sinodal): array
    {
        $dto = new SociedadesDTO();
        $obj = new SociedadesADO();
        
        SociedadesADO::addComparison($dto, 'sinodal', CMP_EQUAL, $sinodal);
        SociedadesADO::addComparison($dto, 'stat', CMP_EQUAL, Status::ACTIVE);
        
        $result = [];
        if($obj->getAllbyParam($dto))
        {
            $obj->iterate();
            while($obj->hasNext())
            {
                $it = $obj->next();
                if(!is_null($it->id))
                {
                    $result[] = $it->igreja;
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
        if(!doIHavePermission(NblFram::$context->token, 'SociedadeIgrejaSave')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new SociedadesDTO();
        $obj = new SociedadesADO();
        
        $dto->add = true;
        $dto->id = NblPHPUtil::makeNumericId();
        $dto->igreja = NblFram::$context->data->igreja;
        $dto->federacao = (empty(NblFram::$context->data->federacao)) ? NULL : NblFram::$context->data->federacao;
        $dto->sinodal = (empty(NblFram::$context->data->sinodal)) ? NULL : NblFram::$context->data->sinodal;
        $dto->nacional = (empty(NblFram::$context->data->nacional)) ? NULL : NblFram::$context->data->nacional;
        $dto->reference = NblFram::$context->data->reference;
        $dto->nome = NblFram::$context->data->nome;
        $dto->logo = NblFram::$context->data->logo;
        $dto->fundacao = (empty(NblFram::$context->data->fundacao)) ? NULL : NblPHPUtil::HumanDate2DBDate(NblFram::$context->data->fundacao);
        $dto->email = NblFram::$context->data->email;
        $dto->telefone = NblFram::$context->data->telefone;
        $dto->ramal = NblFram::$context->data->ramal;
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
            // crie a instância para a sociedade, ligada à igreja
            $errs = [];
            $id_instancia = '';
            if(InstanciasWS::create($dto->id, 
                                        References::SOCIEDADE, 
                                        '', 
                                        InstanciasWS::getIdbyRef($dto->igreja, References::IGREJA), 
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
     * Edita
     * 
     * @httpmethod PUT
     * @auth yes
     * @require id
     * @return array
     */
    public function edit(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'SociedadeIgrejaSave')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new SociedadesDTO();
        $obj = new SociedadesADO();
        
        $dto->edit = true;
        $dto->id = NblFram::$context->data->id;
        $dto->nome = NblFram::$context->data->nome;
        $dto->logo = NblFram::$context->data->logo;
        $dto->fundacao = (empty(NblFram::$context->data->fundacao)) ? NULL : NblPHPUtil::HumanDate2DBDate(NblFram::$context->data->fundacao);
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
        if(!doIHavePermission(NblFram::$context->token, 'SociedadeIgrejaBlock')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new SociedadesDTO();
        $obj = new SociedadesADO();
        
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
        if(!doIHavePermission(NblFram::$context->token, 'SociedadeIgreja,Dados')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new SociedadesDTO();
        $obj = new SociedadesADO();
        
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
        if(!doIHavePermission(NblFram::$context->token, 'Dados,SociedadeIgreja,SociedadeFederacao,SociedadeSinodal')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }

        $dto = new SociedadesDTO();
        $obj = new SociedadesADO();
        $obj_count = new SociedadesADO();

        $page = ($this->testInputString('page')) ? (int) NblFram::$context->data->page : -1;
        $pageSize = ($this->testInputString('pageSize')) ? (int) NblFram::$context->data->pageSize : -1;
        $pagination = $this->calcPagination($page, $pageSize);

        $searchBy = ($this->testInputString('searchBy')) ? NblFram::$context->data->searchBy : '';
        $orderBy = ($this->testInputString('orderBy')) ? NblFram::$context->data->orderBy : '';
        $groupBy = ($this->testInputString('groupBy')) ? NblFram::$context->data->groupBy : '';

        $stat = ($this->testInputString('stat')) ? NblFram::$context->data->stat : '';
        $reference = ($this->testInputString('reference')) ? NblFram::$context->data->reference : '';
        $igreja = ($this->testInputString('igreja')) ? NblFram::$context->data->igreja : '';
        $federacao = ($this->testInputString('federacao')) ? NblFram::$context->data->federacao : '';
        $sinodal = ($this->testInputString('sinodal')) ? NblFram::$context->data->sinodal : '';
        $nacional = ($this->testInputString('nacional')) ? NblFram::$context->data->nacional : '';

        $has_at_least_one_filter = false;

        if(!empty($searchBy))
        {   
            $has_at_least_one_filter = true;
            SociedadesADO::addComparison($dto, 'nome', CMP_INCLUDE_INSIDE, $searchBy, OP_OR);
            SociedadesADO::addComparison($dto, 'email', CMP_INCLUDE_INSIDE, $searchBy, OP_OR);
            SociedadesADO::addComparison($dto, 'telefone', CMP_INCLUDE_INSIDE, $searchBy, OP_OR);
            SociedadesADO::addComparison($dto, 'ramal', CMP_INCLUDE_INSIDE, $searchBy, OP_OR);
        }

        if(!empty($stat))
        {   
            $has_at_least_one_filter = true;
            SociedadesADO::addComparison($dto, 'stat', CMP_EQUAL, $stat);
        }

        if(!empty($reference))
        {   
            $has_at_least_one_filter = true;
            SociedadesADO::addComparison($dto, 'reference', CMP_EQUAL, $reference);
        }
        
        if(!empty($igreja))
        {   
            $has_at_least_one_filter = true;
            SociedadesADO::addComparison($dto, 'igreja', CMP_EQUAL, $igreja);
        }
        
        if(!empty($federacao))
        {   
            $has_at_least_one_filter = true;
            SociedadesADO::addComparison($dto, 'federacao', CMP_EQUAL, $federacao);
        }
        
        if(!empty($sinodal))
        {   
            $has_at_least_one_filter = true;
            SociedadesADO::addComparison($dto, 'sinodal', CMP_EQUAL, $sinodal);
        }
        
        if(!empty($nacional))
        {   
            $has_at_least_one_filter = true;
            SociedadesADO::addComparison($dto, 'nacional', CMP_EQUAL, $nacional);
        }
        
        if(!empty($orderBy))
        {
            $has_at_least_one_filter = true;
            $order_by_v = explode(',',$orderBy);
            SociedadesADO::addOrdering($dto, $order_by_v[0], ($order_by_v[1] == 'asc') ? ORDER_ASC : ORDER_DESC);
        }

        if(!empty($groupBy))
        {
            $has_at_least_one_filter = true;
            $groups = explode(',',$groupBy);
            foreach($groups as $g) {
                SociedadesADO::addGrouping($dto, $g);
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
                        'igreja' => $it->igreja,
                        'federacao' => $it->federacao,
                        'sinodal' => $it->sinodal,
                        'nacional' => $it->nacional,
                        'reference' => $it->reference,
                        'nome' => $it->nome,
                        'logo' => $it->logo,
                        'fundacao' => $it->fundacao,
                        'email' => $it->email,
                        'telefone' => $it->telefone,
                        'ramal' => $it->ramal,
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
        if(!doIHavePermission(NblFram::$context->token, 'SociedadeIgrejaRemove')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new SociedadesDTO();
        $obj = new SociedadesADO();
        
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
        if(!doIHavePermission(NblFram::$context->token, 'SociedadeIgrejaRemove')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new SociedadesADO();
        
        foreach(NblFram::$context->data->ids as $id) {
            $dto = new SociedadesDTO();
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

