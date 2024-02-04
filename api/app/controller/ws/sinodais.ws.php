<?php
require_once ADO_PATH . '/Sinodais.class.php'; 
require_once DAO_PATH . '/SinodaisDAO.class.php'; 

/**
 * API REST de Sinodais
 */
class SinodaisWS extends WSUtil
{
    /**
     * Obtem a sinodal pelo id
     * 
     * @param string $id id da sinodal
     * @return object|null
     */
    public static function getById($id): ?object
    {
        $dto = new SinodaisDTO();
        $obj = new SinodaisADO();
        
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
     * Obtenha o id do sínodo
     * 
     * @param string $id id da sinodal
     * @return string
     */
    public static function getSinodoId($id): string
    {
        $sinodal = SinodaisWS::getById($id);
        if(!is_null($sinodal))
        {
            return $sinodal->sinodo;
        }
        
        return '';
    }
    
    /**
     * Obtenha os ids das igrejas da sinodal
     * 
     * @param string $id id da sinodal
     * @return array
     */
    public static function getIgrejasIds($id): array
    {
        $sinodal = SinodaisWS::getById($id);
        if(!is_null($sinodal))
        {
            return IgrejasWS::getIdsBySinodo($sinodal->sinodo);
        }
        
        return [];
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
        if(!doIHavePermission(NblFram::$context->token, 'SinodalSave')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new SinodaisDTO();
        $obj = new SinodaisADO();
        
        $dto->add = true;
        $dto->id = NblPHPUtil::makeNumericId();
        $dto->sinodo = (empty(NblFram::$context->data->sinodo)) ? NULL : NblFram::$context->data->sinodo;
        $dto->nacional = (empty(NblFram::$context->data->nacional)) ? NULL : NblFram::$context->data->nacional;
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
        if(!doIHavePermission(NblFram::$context->token, 'SinodalSave,InstanciaSave')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new SinodaisDTO();
        $obj = new SinodaisADO();
        
        $dto->edit = true;
        $dto->id = NblFram::$context->data->id;
        $dto->sinodo = (empty(NblFram::$context->data->sinodo)) ? NULL : NblFram::$context->data->sinodo;
        $dto->nacional = (empty(NblFram::$context->data->nacional)) ? NULL : NblFram::$context->data->nacional;
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
        if(!doIHavePermission(NblFram::$context->token, 'SinodalBlock')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new SinodaisDTO();
        $obj = new SinodaisADO();
        
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
        if(!doIHavePermission(NblFram::$context->token, 'Sinodal,InstanciaSave,Dados')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new SinodaisDTO();
        $obj = new SinodaisADO();
        
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
        if(!doIHavePermission(NblFram::$context->token, 'Sinodal,Dados')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }

        $dto = new SinodaisDTO();
        $obj = new SinodaisADO();
        $obj_count = new SinodaisADO();

        $page = ($this->testInputString('page')) ? (int) NblFram::$context->data->page : -1;
        $pageSize = ($this->testInputString('pageSize')) ? (int) NblFram::$context->data->pageSize : -1;
        $pagination = $this->calcPagination($page, $pageSize);

        $searchBy = ($this->testInputString('searchBy')) ? NblFram::$context->data->searchBy : '';
        $orderBy = ($this->testInputString('orderBy')) ? NblFram::$context->data->orderBy : '';
        $groupBy = ($this->testInputString('groupBy')) ? NblFram::$context->data->groupBy : '';
        
        $stat = ($this->testInputString('stat')) ? NblFram::$context->data->stat : '';
        $sinodo = ($this->testInputString('sinodo')) ? NblFram::$context->data->sinodo : '';
        $nacional = ($this->testInputString('nacional')) ? NblFram::$context->data->nacional : '';
        $reference = ($this->testInputString('reference')) ? NblFram::$context->data->reference : '';

        $has_at_least_one_filter = false;

        if(!empty($searchBy))
        {   
            $has_at_least_one_filter = true;
            SinodaisADO::addComparison($dto, 'nome', CMP_INCLUDE_INSIDE, $searchBy, OP_OR);
            SinodaisADO::addComparison($dto, 'sigla', CMP_INCLUDE_INSIDE, $searchBy, OP_OR);
        }

        if(!empty($stat))
        {   
            $has_at_least_one_filter = true;
            SinodaisADO::addComparison($dto, 'stat', CMP_EQUAL, $stat);
        }

        if(!empty($sinodo))
        {   
            $has_at_least_one_filter = true;
            SinodaisADO::addComparison($dto, 'sinodo', CMP_EQUAL, $sinodo);
        }

        if(!empty($nacional))
        {   
            $has_at_least_one_filter = true;
            SinodaisADO::addComparison($dto, 'nacional', CMP_EQUAL, $nacional);
        }

        if(!empty($reference))
        {   
            $has_at_least_one_filter = true;
            SinodaisADO::addComparison($dto, 'reference', CMP_EQUAL, $reference);
        }
        
        if(!empty($orderBy))
        {
            $has_at_least_one_filter = true;
            $order_by_v = explode(',',$orderBy);
            SinodaisADO::addOrdering($dto, $order_by_v[0], ($order_by_v[1] == 'asc') ? ORDER_ASC : ORDER_DESC);
        }

        if(!empty($groupBy))
        {
            $has_at_least_one_filter = true;
            $groups = explode(',',$groupBy);
            foreach($groups as $g) {
                SinodaisADO::addGrouping($dto, $g);
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
                        'nacional' => $it->nacional,
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
        if(!doIHavePermission(NblFram::$context->token, 'SinodalRemove')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new SinodaisDTO();
        $obj = new SinodaisADO();
        
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
        if(!doIHavePermission(NblFram::$context->token, 'SinodalRemove')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new SinodaisADO();
        
        foreach(NblFram::$context->data->ids as $id) {
            $dto = new SinodaisDTO();
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

