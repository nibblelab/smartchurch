<?php
require_once ADO_PATH . '/Agendas.class.php'; 
require_once DAO_PATH . '/AgendasDAO.class.php'; 
require_once ADO_PATH . '/TagsDaAgenda.class.php'; 
require_once DAO_PATH . '/TagsDaAgendaDAO.class.php'; 

/**
 * API REST de Agendas
 */
class AgendasWS extends WSUtil
{
    /**
     * 
     * @var \AgendasWS singleton instance
     */
    private static $_Instance = null;
    
    /**
     * Get singleton instance
     * 
     * @return \AgendasWS
     */
    public static function getInstance(): \AgendasWS {
        if(self::$_Instance == null) {
            self::$_Instance = new self();
        }
        return self::$_Instance;
    }
    
    /**
     * Obtem a agenda pelo id
     * 
     * @param string $id id da agenda
     * @return object|null
     */
    public static function getById($id): ?object
    {
       $dto = new AgendasDTO();
        $obj = new AgendasADO();
        
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
     * 
     * Cria
     * 
     * @httpmethod POST
     * @auth yes
     * @return array
     */
    public function create(): array
    {
        $perms = array('AgendaIgrejaSave','AgendaPastorSave','AgendaEvangelistaSave','AgendaSociedadeSave','AgendaSinodalSave','AgendaMinisterioSave');
        if(!doIHavePermission(NblFram::$context->token, $this->stringifyArray($perms, ',', false))) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new AgendasDTO();
        $obj = new AgendasADO();
        
        $dto->add = true;
        $dto->id = NblPHPUtil::makeNumericId();
        $dto->nome = NblFram::$context->data->nome;
        $dto->logo = NblFram::$context->data->logo;
        $dto->ref = NblFram::$context->data->ref;
        $dto->ref_tp = NblFram::$context->data->ref_tp;
        $dto->responsavel = NblFram::$context->data->responsavel;
        $dto->time_ini = (empty(NblFram::$context->data->time_ini)) ? NULL : NblFram::$context->data->time_ini;
        $dto->time_end = (empty(NblFram::$context->data->time_end)) ? NULL : NblFram::$context->data->time_end;
        $dto->observacoes = NblFram::$context->data->observacoes;
        $dto->recorrente = (NblFram::$context->data->recorrente) ? GenericHave::YES : GenericHave::NO;
        $dto->dias_horarios = NblFram::$context->data->dias_horarios;
        $dto->endereco = NblFram::$context->data->endereco;
        $dto->numero = NblFram::$context->data->numero;
        $dto->complemento = NblFram::$context->data->complemento;
        $dto->bairro = NblFram::$context->data->bairro;
        $dto->cidade = NblFram::$context->data->cidade;
        $dto->uf = NblFram::$context->data->uf;
        $dto->cep = NblFram::$context->data->cep;
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
            if(!empty(NblFram::$context->data->tags))
            {
                $obj_t = new TagsDaAgendaADO();
                
                foreach(NblFram::$context->data->tags as $tag) {
                    if($tag->checked) {
                        $dto_t = new TagsDaAgendaDTO();
                        $dto_t->add = true;
                        $dto_t->agenda = $dto->id;
                        $dto_t->tag = $tag->id;
                        
                        $obj_t->add($dto_t);
                    }
                }
                
                $obj_t->sync();
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
     * Cria para federação
     * 
     * @httpmethod POST
     * @auth yes
     * @require federacao
     * @return array
     */
    public function createForFederacao(): array
    {
        NblFram::$context->data->ref = NblFram::$context->data->federacao;
        NblFram::$context->data->ref_tp = References::FEDERACAO;
        return $this->create();
    }
    
    /**
     * 
     * Cria para sinodal
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
        $perms = array('AgendaIgrejaSave','AgendaPastorSave','AgendaEvangelistaSave','AgendaSociedadeSave','AgendaSinodalSave','AgendaMinisterioSave');
        if(!doIHavePermission(NblFram::$context->token, $this->stringifyArray($perms, ',', false))) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new AgendasDTO();
        $obj = new AgendasADO();
        
        $dto->edit = true;
        $dto->id = NblFram::$context->data->id;
        $dto->nome = NblFram::$context->data->nome;
        $dto->logo = NblFram::$context->data->logo;
        $dto->responsavel = NblFram::$context->data->responsavel;
        $dto->time_ini = (empty(NblFram::$context->data->time_ini)) ? NULL : NblFram::$context->data->time_ini;
        $dto->time_end = (empty(NblFram::$context->data->time_end)) ? NULL : NblFram::$context->data->time_end;
        $dto->observacoes = NblFram::$context->data->observacoes;
        $dto->recorrente = (NblFram::$context->data->recorrente) ? GenericHave::YES : GenericHave::NO;
        $dto->dias_horarios = NblFram::$context->data->dias_horarios;
        $dto->endereco = NblFram::$context->data->endereco;
        $dto->numero = NblFram::$context->data->numero;
        $dto->complemento = NblFram::$context->data->complemento;
        $dto->bairro = NblFram::$context->data->bairro;
        $dto->cidade = NblFram::$context->data->cidade;
        $dto->uf = NblFram::$context->data->uf;
        $dto->cep = NblFram::$context->data->cep;
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
            if(!empty(NblFram::$context->data->tags))
            {
                $obj_t = new TagsDaAgendaADO();
                
                foreach(NblFram::$context->data->tags as $tag) {
                    $dto_t = new TagsDaAgendaDTO();
                    $dto_t->agenda = $dto->id;
                    $dto_t->tag = $tag->id;
                    if($tag->checked && !$tag->old_checked) {
                        $dto_t->add = true;
                    }
                    else if(!$tag->checked && $tag->old_checked) {
                        $dto_t->delete = true;
                    }
                    $obj_t->add($dto_t);
                }
                
                $obj_t->sync();
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
        $perms = array('AgendaIgrejaBlock','AgendaPastorBlock','AgendaEvangelistaBlock','AgendaSociedadeBlock','AgendaSinodalBlock','AgendaMinisterioBlock');
        if(!doIHavePermission(NblFram::$context->token, $this->stringifyArray($perms, ',', false))) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new AgendasDTO();
        $obj = new AgendasADO();
        
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
        $perms = array('AgendaIgreja','AgendaPastor','AgendaEvangelista','AgendaSociedade','AgendaSinodal','AgendaMinisterio');
        if(!doIHavePermission(NblFram::$context->token, $this->stringifyArray($perms, ',', false))) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new AgendasDTO();
        $obj = new AgendasADO();
        
        $dto->id = NblFram::$context->data->id;
        if(!is_null($obj->get($dto)))
        {
            $d = $obj->getDTODataObject();
            $d->recorrente = ($d->recorrente == GenericHave::YES);
            
            $tags = [];
            $dto_t = new TagsDaAgendaDTO();
            $obj_t = new TagsDaAgendaADO();
            TagsDaAgendaADO::addComparison($dto_t, 'agenda', CMP_EQUAL, $d->id);
            if($obj_t->getAllbyParam($dto_t)) {
                $obj_t->iterate();
                while($obj_t->hasNext())
                {
                    $it_t = $obj_t->next();
                    if(!is_null($it_t->tag))
                    {
                        $tags[] = $it_t->tag;
                    }
                }
            }
            
            $d->tags = $tags;
            
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
        $perms = array('Dados','AgendaIgreja','AgendaPastor','AgendaEvangelista','AgendaSociedade','AgendaSinodal','AgendaMinisterio');
        if(!doIHavePermission(NblFram::$context->token, $this->stringifyArray($perms, ',', false))) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }

        $dto = new AgendasDTO();
        $obj = new AgendasADO();
        $obj_count = new AgendasADO();
        
        $obj_t = new TagsDaAgendaADO();
        $map_tags = $obj_t->multiMapAllBy('agenda');

        $page = ($this->testInputString('page')) ? (int) NblFram::$context->data->page : -1;
        $pageSize = ($this->testInputString('pageSize')) ? (int) NblFram::$context->data->pageSize : -1;
        $pagination = $this->calcPagination($page, $pageSize);

        $searchBy = ($this->testInputString('searchBy')) ? NblFram::$context->data->searchBy : '';
        $orderBy = ($this->testInputString('orderBy')) ? NblFram::$context->data->orderBy : '';
        $groupBy = ($this->testInputString('groupBy')) ? NblFram::$context->data->groupBy : '';
        
        $stat = ($this->testInputString('stat')) ? NblFram::$context->data->stat : '';
        $ref = ($this->testInputString('ref')) ? NblFram::$context->data->ref : '';
        $ref_tp = ($this->testInputString('ref_tp')) ? NblFram::$context->data->ref_tp : '';
        $responsavel = ($this->testInputString('responsavel')) ? NblFram::$context->data->responsavel : '';
        $inicio = ($this->testInputString('inicio')) ? NblFram::$context->data->inicio : '';
        $termino = ($this->testInputString('termino')) ? NblFram::$context->data->termino : '';
        $tags = ($this->testInputString('tags')) ? NblFram::$context->data->tags : '';
        
        $recorrente = ($this->testInputBool('recorrente')) ? true : false;
        $domingo = ($this->testInputBool('domingo')) ? true : false;
        $segunda = ($this->testInputBool('segunda')) ? true : false;
        $terca = ($this->testInputBool('terca')) ? true : false;
        $quarta = ($this->testInputBool('quarta')) ? true : false;
        $quinta = ($this->testInputBool('quinta')) ? true : false;
        $sexta = ($this->testInputBool('sexta')) ? true : false;
        $sabado = ($this->testInputBool('sabado')) ? true : false;
        
        $igreja = ($this->testInputString('igreja')) ? NblFram::$context->data->igreja : '';
        $federacao = ($this->testInputString('federacao')) ? NblFram::$context->data->federacao : '';
        $sinodal = ($this->testInputString('sinodal')) ? NblFram::$context->data->sinodal : '';

        $has_at_least_one_filter = false;
        
        /* filtre pelas tags, se foram passadas */
        if(!empty($tags))
        {   
            $has_at_least_one_filter = true;
            
            $ids_list = '';
            $tags_list = $this->generateFilterFromInputList($tags);
            
            $dto_t = new TagsDaAgendaDTO();
            TagsDaAgendaADO::addComparison($dto_t, 'tag', CMP_IN_LIST, $tags_list);
            if($obj_t->getAllbyParam($dto_t)) {
                $obj_t->iterate();
                while($obj_t->hasNext())
                {
                    $it_t = $obj_t->next();
                    if(!is_null($it_t->agenda))
                    {
                        if(!empty($ids_list)) {
                            $ids_list .= ',';
                        }
                        
                        $ids_list .= "'{$it_t->agenda}'";
                    }
                }
            }
            
            if(empty($ids_list)) {
                // existe filtro por tag, porém não há evento na agenda que use a tag. Retorne vazio
                return array('status' => 'ok', 'success' => true, 'datas' => [], 'total' => 0);
            }
            
            AgendasADO::addComparison($dto, 'a.id', CMP_IN_LIST, $ids_list);
        }
        
        /* filtros diretos na agenda */
        
        if(!empty($searchBy))
        {   
            $has_at_least_one_filter = true;
            AgendasADO::addComparison($dto, 'nome', CMP_INCLUDE_INSIDE, $searchBy);
        }

        if(!empty($stat))
        {   
            $has_at_least_one_filter = true;
            AgendasADO::addComparison($dto, 'stat', CMP_EQUAL, $stat);
        }

        if(!empty($ref))
        {   
            $has_at_least_one_filter = true;
            AgendasADO::addComparison($dto, 'ref', CMP_EQUAL, $ref);
        }

        if(!empty($ref_tp))
        {   
            $has_at_least_one_filter = true;
            AgendasADO::addComparison($dto, 'ref_tp', CMP_EQUAL, $ref_tp);
        }

        if(!empty($igreja))
        {   
            $has_at_least_one_filter = true;
            AgendasADO::addComparison($dto, 'ref_tp', CMP_EQUAL, References::IGREJA);
            AgendasADO::addComparison($dto, 'ref', CMP_EQUAL, $igreja);
        }

        if(!empty($federacao))
        {   
            $has_at_least_one_filter = true;
            AgendasADO::addComparison($dto, 'ref_tp', CMP_EQUAL, References::FEDERACAO);
            AgendasADO::addComparison($dto, 'ref', CMP_EQUAL, $federacao);
        }

        if(!empty($sinodal))
        {   
            $has_at_least_one_filter = true;
            AgendasADO::addComparison($dto, 'ref_tp', CMP_EQUAL, References::SINODAL);
            AgendasADO::addComparison($dto, 'ref', CMP_EQUAL, $sinodal);
        }

        if(!empty($responsavel))
        {   
            $has_at_least_one_filter = true;
            AgendasADO::addComparison($dto, 'responsavel', CMP_EQUAL, $responsavel);
        }

        if(!empty($inicio))
        {   
            $has_at_least_one_filter = true;
            AgendasADO::addComparison($dto, 'time_ini', CMP_GREATER_THEN_DATE, NblPHPUtil::HumanDate2DBDate($inicio), OP_OR, true);
            AgendasADO::addComparison($dto, 'time_ini', CMP_IS_NULL, '', OP_OR, true);
        }

        if(!empty($termino))
        {   
            $has_at_least_one_filter = true;
            AgendasADO::addComparison($dto, 'time_end', CMP_LESSER_THEN_DATE, NblPHPUtil::HumanDate2DBDate($termino), OP_OR, true);
            AgendasADO::addComparison($dto, 'time_end', CMP_IS_NULL, '', OP_OR, true);
        }

        if($recorrente)
        {   
            $has_at_least_one_filter = true;
            AgendasADO::addComparison($dto, 'recorrente', CMP_EQUAL, GenericHave::YES);
        }

        if($domingo)
        {   
            $has_at_least_one_filter = true;
            AgendasADO::addComparison($dto, 'dias_horarios', CMP_INCLUDE_INSIDE, '"domingo": true', OP_AND, true);
        }

        if($segunda)
        {   
            $has_at_least_one_filter = true;
            AgendasADO::addComparison($dto, 'dias_horarios', CMP_INCLUDE_INSIDE, '"segunda": true', OP_AND, true);
        }

        if($terca)
        {   
            $has_at_least_one_filter = true;
            AgendasADO::addComparison($dto, 'dias_horarios', CMP_INCLUDE_INSIDE, '"terca": true', OP_AND, true);
        }

        if($quarta)
        {   
            $has_at_least_one_filter = true;
            AgendasADO::addComparison($dto, 'dias_horarios', CMP_INCLUDE_INSIDE, '"quarta": true', OP_AND, true);
        }

        if($quinta)
        {   
            $has_at_least_one_filter = true;
            AgendasADO::addComparison($dto, 'dias_horarios', CMP_INCLUDE_INSIDE, '"quinta": true', OP_AND, true);
        }

        if($sexta)
        {   
            $has_at_least_one_filter = true;
            AgendasADO::addComparison($dto, 'dias_horarios', CMP_INCLUDE_INSIDE, '"sexta": true', OP_AND, true);
        }

        if($sabado)
        {   
            $has_at_least_one_filter = true;
            AgendasADO::addComparison($dto, 'dias_horarios', CMP_INCLUDE_INSIDE, '"sabado": true', OP_AND, true);
        }
        
        if(!empty($orderBy))
        {
            $has_at_least_one_filter = true;
            $order_by_v = explode(',',$orderBy);
            AgendasADO::addOrdering($dto, $order_by_v[0], ($order_by_v[1] == 'asc') ? ORDER_ASC : ORDER_DESC);
        }

        if(!empty($groupBy))
        {
            $has_at_least_one_filter = true;
            $groups = explode(',',$groupBy);
            foreach($groups as $g) {
                AgendasADO::addGrouping($dto, $g);
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
                    $tags = [];
                    if(isset($map_tags[$it->id])) {
                        foreach($map_tags[$it->id] as $tag) {
                            $tags[] = $tag['tag'];
                        }
                    }
                    
                    $result[] = array(
                        'id' => $it->id,
                        'tags' => $tags,
                        'nome' => $it->nome,
                        'logo' => $it->logo,
                        'ref' => $it->ref,
                        'ref_tp' => $it->ref_tp,
                        'responsavel' => $it->responsavel,
                        'time_ini' => $it->time_ini,
                        'time_end' => $it->time_end,
                        'observacoes' => $it->observacoes,
                        'recorrente' => ($it->recorrente == GenericHave::YES),
                        'dias_horarios' => $it->dias_horarios,
                        'endereco' => $it->endereco,
                        'numero' => $it->numero,
                        'complemento' => $it->complemento,
                        'bairro' => $it->bairro,
                        'cidade' => $it->cidade,
                        'uf' => $it->uf,
                        'cep' => $it->cep,
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
        $perms = array('AgendaIgrejaRemove','AgendaPastorRemove','AgendaEvangelistaRemove','AgendaSociedadeRemove','AgendaSinodalRemove','AgendaMinisterioRemove');
        if(!doIHavePermission(NblFram::$context->token, $this->stringifyArray($perms, ',', false))) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new AgendasDTO();
        $obj = new AgendasADO();
        
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
        $perms = array('AgendaIgrejaRemove','AgendaPastorRemove','AgendaEvangelistaRemove','AgendaSociedadeRemove','AgendaSinodalRemove','AgendaMinisterioRemove');
        if(!doIHavePermission(NblFram::$context->token, $this->stringifyArray($perms, ',', false))) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new AgendasADO();
        
        foreach(NblFram::$context->data->ids as $id) {
            $dto = new AgendasDTO();
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

