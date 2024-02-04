<?php
require_once ADO_PATH . '/SeriesEstudos.class.php'; 
require_once DAO_PATH . '/SeriesEstudosDAO.class.php'; 
require_once HLP_PATH . '/ImageHelper.class.php'; 
require_once HLP_PATH . '/DocHelper.class.php'; 

/**
 * API REST de SeriesEstudos
 */
class SeriesEstudosWS extends WSUtil
{
    
    /**
     * Obtenha a logo da série
     * 
     * @return string
     */
    private function getLogo(): string
    {
        $dto = new SeriesEstudosDTO();
        $obj = new SeriesEstudosADO();
        
        $dto->id = NblFram::$context->data->id;
        if(!is_null($obj->get($dto)))
        {
            $d = $obj->getDTODataObject();
            return $d->logo;
        }
        else 
        {
            return '';
        }
    }
    
    /**
     * Obtenha as logos das séries pelos seus ids
     * 
     * @param array $ids array com os ids
     * @return array
     */
    private function getLogos($ids): array
    {
        $dto = new SeriesEstudosDTO();
        $obj = new SeriesEstudosADO();
        
        SeriesEstudosADO::addComparison($dto, id, CMP_IN_LIST, $this->stringifyArray($ids));
        
        $result = array();
        if($obj->getAllbyParam($dto))
        {
            $obj->iterate();
            while($obj->hasNext())
            {
                $it = $obj->next();
                if(!is_null($it->id))
                {
                    $result[] = $it->logo;
                }
            }
        }
        
        return $result;
    }
    
    /**
     * Remova as logos das séries
     * 
     * @param array $logos array com os nomes dos anexos
     * @return void
     */
    private function removeLogos($logos): void
    {
        if(empty($logos)) {
            return;
        }
        
        $helper = new DocHelper();
        
        $logos_to_remove = [];
        foreach($logos as $logo)
        {
            $logos_to_remove[] = RSC_PATH . '/' . $logo;
        }
        
        $helper->delete($logos_to_remove);
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
        if(!doIHavePermission(NblFram::$context->token, 'SerieEstudoIgrejaSave')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new SeriesEstudosDTO();
        $obj = new SeriesEstudosADO();
        
        // trate uma possível imagem de logo
        $logo = '';
        if($this->isBase64Img(NblFram::$context->data->arquivo->content)) {
            $imagehlp = new ImageHelper();
            $image = $imagehlp->generateFromBase64(NblFram::$context->data->arquivo->content, 
                                                    NblFram::$context->data->arquivo->name, 
                                                    RSC_PATH, 
                                                    false);
            $logo = $image->path;
        }
        
        $dto->add = true;
        $dto->id = NblPHPUtil::makeNumericId();
        $dto->ref = NblFram::$context->data->ref;
        $dto->ref_tp = NblFram::$context->data->ref_tp;
        $dto->nome = NblFram::$context->data->nome;
        $dto->chave = NblFram::$context->data->chave;
        $dto->logo = $logo;
        $dto->destinatarios = NblFram::$context->data->destinatarios;
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
     * Cria
     * 
     * @httpmethod POST
     * @auth yes
     * @return array
     */
    public function createFoIgreja(): array
    {
        NblFram::$context->data->ref = NblFram::$context->data->igreja;
        NblFram::$context->data->ref_tp = References::IGREJA;
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
        if(!doIHavePermission(NblFram::$context->token, 'SerieEstudoIgrejaSave')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new SeriesEstudosDTO();
        $obj = new SeriesEstudosADO();
        
        // veja se tem uma logo anterior
        $old_logo = $this->getLogo();

        // trate uma possível imagem de logo
        $logo = $old_logo;
        if($this->isBase64Img(NblFram::$context->data->arquivo->content)) {
            $imagehlp = new ImageHelper();
            $image = $imagehlp->generateFromBase64(NblFram::$context->data->arquivo->content, 
                                                    NblFram::$context->data->arquivo->name, 
                                                    RSC_PATH, 
                                                    false);
            $logo = $image->path;
        }
        
        $dto->edit = true;
        $dto->id = NblFram::$context->data->id;
        $dto->nome = NblFram::$context->data->nome;
        $dto->chave = NblFram::$context->data->chave;
        $dto->logo = $logo;
        $dto->destinatarios = NblFram::$context->data->destinatarios;
        $dto->last_mod = date('Y-m-d H:i:s');
        $dto->last_amod = NblFram::$context->token['data']['nome'];
        
        $obj->add($dto);
        if($obj->sync())
        {
            // remova uma possível logo anterior
            if($logo != $old_logo) {
                $this->removeLogos(array($old_logo));
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
     * Edita status 
     * 
     * @httpmethod PUT
     * @auth yes
     * @require id
     * @return array
     */
    public function changestat(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'SerieEstudoIgrejaBlock')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new SeriesEstudosDTO();
        $obj = new SeriesEstudosADO();
        
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
        if(!doIHavePermission(NblFram::$context->token, 'SerieEstudoIgreja')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new SeriesEstudosDTO();
        $obj = new SeriesEstudosADO();
        
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
     * @require chave
     * @return array
     */
    public function bychave(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'SerieEstudoIgreja')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new SeriesEstudosDTO();
        $obj = new SeriesEstudosADO();
        
        SeriesEstudosADO::addComparison($dto, 'chave', CMP_EQUAL, NblFram::$context->data->chave);
        if(!is_null($obj->getBy($dto)))
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
        if(!doIHavePermission(NblFram::$context->token, 'SerieEstudoIgreja,Dados')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }

        $dto = new SeriesEstudosDTO();
        $obj = new SeriesEstudosADO();
        $obj_count = new SeriesEstudosADO();

        $page = ($this->testInputString('page')) ? (int) NblFram::$context->data->page : -1;
        $pageSize = ($this->testInputString('pageSize')) ? (int) NblFram::$context->data->pageSize : -1;
        $pagination = $this->calcPagination($page, $pageSize);

        $searchBy = ($this->testInputString('searchBy')) ? NblFram::$context->data->searchBy : '';
        $orderBy = ($this->testInputString('orderBy')) ? NblFram::$context->data->orderBy : '';
        $groupBy = ($this->testInputString('groupBy')) ? NblFram::$context->data->groupBy : '';
        
        $stat = ($this->testInputString('stat')) ? NblFram::$context->data->stat : '';
        $chave = ($this->testInputString('chave')) ? NblFram::$context->data->chave : '';
        $ref = ($this->testInputString('ref')) ? NblFram::$context->data->ref : '';
        $ref_tp = ($this->testInputString('ref_tp')) ? NblFram::$context->data->ref_tp : '';
        $destinatarios = ($this->testInputString('destinatarios')) ? NblFram::$context->data->destinatarios : '';
        
        $igreja = ($this->testInputString('igreja')) ? NblFram::$context->data->igreja : '';

        $has_at_least_one_filter = false;
        
        if(!empty($searchBy))
        {   
            $has_at_least_one_filter = true;
            SeriesEstudosADO::addComparison($dto, 'nome', CMP_INCLUDE_INSIDE, $searchBy);
        }

        if(!empty($stat))
        {   
            $has_at_least_one_filter = true;
            SeriesEstudosADO::addComparison($dto, 'stat', CMP_EQUAL, $stat);
        }
        
        if(!empty($chave))
        {   
            $has_at_least_one_filter = true;
            SeriesEstudosADO::addComparison($dto, 'chave', CMP_EQUAL, $chave);
        }
        
        if(!empty($ref))
        {   
            $has_at_least_one_filter = true;
            SeriesEstudosADO::addComparison($dto, 'ref', CMP_EQUAL, $ref);
        }
        
        if(!empty($ref_tp))
        {   
            $has_at_least_one_filter = true;
            SeriesEstudosADO::addComparison($dto, 'ref_tp', CMP_EQUAL, $ref_tp);
        }
        
        if(!empty($destinatarios))
        {   
            $has_at_least_one_filter = true;
            $destinatarios_arr = explode(';', $destinatarios);
            foreach($destinatarios_arr as $destinatario) {
                SeriesEstudosADO::addComparison($dto, 'destinatarios', CMP_INCLUDE_INSIDE, '"checked":true,"label":"'.$destinatario.'"', OP_OR, true);
            }
        }
        
        if(!empty($igreja))
        {   
            $has_at_least_one_filter = true;
            SeriesEstudosADO::addComparison($dto, 'ref', CMP_EQUAL, $igreja);
            SeriesEstudosADO::addComparison($dto, 'ref_tp', CMP_EQUAL, References::IGREJA);
        }
        
        if(!empty($orderBy))
        {
            $has_at_least_one_filter = true;
            $order_by_v = explode(',',$orderBy);
            SeriesEstudosADO::addOrdering($dto, $order_by_v[0], ($order_by_v[1] == 'asc') ? ORDER_ASC : ORDER_DESC);
        }

        if(!empty($groupBy))
        {
            $has_at_least_one_filter = true;
            $groups = explode(',',$groupBy);
            foreach($groups as $g) {
                SeriesEstudosADO::addGrouping($dto, $g);
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
                        'ref' => $it->ref,
                        'ref_tp' => $it->ref_tp,
                        'nome' => $it->nome,
                        'chave' => $it->chave,
                        'logo' => $it->logo,
                        'destinatarios' => $it->destinatarios,
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
        if(!doIHavePermission(NblFram::$context->token, 'SerieEstudoIgrejaRemove')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new SeriesEstudosDTO();
        $obj = new SeriesEstudosADO();
        
        $dto->id = NblFram::$context->data->id;
        $r = $obj->get($dto);
        if(!is_null($r))
        {
            $r->delete = true;
            if($obj->sync())
            {
                $this->removeLogos(array($r->logo));
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
        if(!doIHavePermission(NblFram::$context->token, 'SerieEstudoIgrejaRemove')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new SeriesEstudosADO();
        
        $logos = $this->getLogos(NblFram::$context->data->ids);
        
        foreach(NblFram::$context->data->ids as $id) {
            $dto = new SeriesEstudosDTO();
            $dto->delete = true;
            $dto->id = $id;
            
            $obj->add($dto);
        }
        
        if($obj->sync())
        {
            $this->removeLogos($logos);
            
            return array('status' => 'ok', 'success' => true);
        }
        else 
        {
            return array('status' => 'no', 'success' => false, 'msg' => 'Recursos não encontrados');
        }
    }
}

