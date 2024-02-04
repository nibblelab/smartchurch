<?php
require_once ADO_PATH . '/Eventos.class.php'; 
require_once DAO_PATH . '/EventosDAO.class.php'; 
require_once WS_PATH . '/agendas.ws.php'; 
require_once WS_PATH . '/igrejas.ws.php'; 
require_once WS_PATH . '/sinodais.ws.php'; 

/**
 * API REST de Eventos
 */
class EventosWS extends WSUtil
{
    
    /**
     * Obtêm o evento pelo seu id
     * 
     * @param string $id id do evento
     * @return \EventosDTO|null
     */
    public static function getById($id): ?\EventosDTO
    {
        $dto = new EventosDTO();
        $obj = new EventosADO();

        $dto->id = $id;
        return $obj->get($dto);
    }
    
    /**
     * Obtem o organizador pelo id do evento
     * 
     * @param string $id id do evento
     * @return object|null
     */
    public static function getOrganizadorByEventoId($id): ?object
    {
        $evento = EventosWS::getById($id);
        if(is_null($evento)) {
            return null;
        }
        
        if($evento->ref_tp == References::IGREJA) {
            return IgrejasWS::getById($evento->ref);
        }
        else if($evento->ref_tp == References::SINODAL) {
            return SinodaisWS::getById($evento->ref);
        }
        
        return null;
    }
    
    /**
     * Gera o objeto de dados (DTO) de evento
     * 
     * @param object $data objeto com os dados vindos da interface
     * @param int $mode flag para indicar se é adição, edição ou remoção
     * @param string $requester quem está requisitando a criação do objeto. [opcional] Default: usuário logado
     * @return EventosDTO objeto de dados
     */
    public static function generateDTO($data, $mode, $requester = ''): \EventosDTO
    {
        $dto = new EventosDTO();

        if($mode == DTOMode::ADD) {
            $dto->add = true;
            $dto->id = NblPHPUtil::makeNumericId();
            $dto->agenda = $data->agenda->id;
            $dto->chave = sha1($dto->id);
            $dto->ref = $data->ref;
            $dto->ref_tp = $data->ref_tp;
            $dto->stat = Status::ACTIVE;
            $dto->time_cad = date('Y-m-d H:i:s');
            $dto->last_mod = $dto->time_cad;
        }
        else if($mode == DTOMode::EDIT) {
            $dto->edit = true;
            $dto->id = $data->id;
            $dto->last_mod = date('Y-m-d H:i:s');
        }
        else if($mode == DTOMode::DELETE) {
            $dto->delete = true;
            $dto->id = $data->id;
            return $dto;
        }
        
        $dto->nome = $data->nome;
        $dto->logo = $data->logo;
        $dto->descricao = $data->descricao;
        $dto->inscricoes_ativas = ($data->inscricoes_ativas) ? GenericHave::YES : GenericHave::NO;
        $dto->formulario_inscricao = $data->formulario_inscricao;
        $dto->valor = (empty($data->valor)) ? '0.00' : $data->valor;
        $dto->opcoes_pagto = $data->opcoes_pagto;
        $dto->lotes = $data->lotes;
        $dto->fim_inscricao = (empty($data->fim_inscricao)) ? NULL : $data->fim_inscricao;
        $dto->time_ini = (empty($data->time_ini)) ? NULL : $data->time_ini;
        $dto->time_end = (empty($data->time_end)) ? NULL : $data->time_end;
        $dto->tem_eleicoes = ($data->tem_eleicoes) ? GenericHave::YES : GenericHave::NO;
        $dto->credencial_digital = ($data->credencial_digital) ? GenericHave::YES : GenericHave::NO;
        $dto->data_delegados = (empty($data->data_delegados)) ? NULL : $data->data_delegados;
        $dto->last_amod = (empty($requester)) ? NblFram::$context->token['data']['nome'] : $requester;

        return $dto;
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
        if(!doIHavePermission(NblFram::$context->token, 'EventoIgrejaSave,EventoSinodalSave')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new EventosADO();
        
        $dto = EventosWS::generateDTO(NblFram::$context->data, DTOMode::ADD);
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
        if(!doIHavePermission(NblFram::$context->token, 'EventoIgrejaSave,EventoSinodalSave')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new EventosADO();
        $dto = EventosWS::generateDTO(NblFram::$context->data, DTOMode::EDIT);
        
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
        if(!doIHavePermission(NblFram::$context->token, 'EventoIgrejaBlock,EventoSinodalBlock')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new EventosDTO();
        $obj = new EventosADO();
        
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
        if(!doIHavePermission(NblFram::$context->token, 'EventoIgreja,EventoSinodal')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new EventosDTO();
        $obj = new EventosADO();
        
        $dto->id = NblFram::$context->data->id;
        if(!is_null($obj->get($dto)))
        {
            $d = $obj->getDTODataObject();
            $d->inscricoes_ativas = ($d->inscricoes_ativas == GenericHave::YES);
            $d->tem_eleicoes = ($d->tem_eleicoes == GenericHave::YES);
            $d->credencial_digital = ($d->credencial_digital == GenericHave::YES);
            $d->valor = (float) $d->valor;
            return array('status' => 'ok', 'success' => true, 'datas' => $d);
        }
        else 
        {
            return array('status' => 'no', 'success' => false, 'msg' => 'Erro na operação', 'errs' => $obj->getErrs());
        }
    }
    
    /**
     * 
     * Busca pela chave
     * 
     * @httpmethod GET
     * @auth yes
     * @require chave
     * @return array
     */
    public function byChave(): array
    {        
        if(!doIHavePermission(NblFram::$context->token, 'Dados,EventoIgreja,EventoSinodal')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new EventosDTO();
        $obj = new EventosADO();
        
        EventosADO::addComparison($dto, 'chave', CMP_EQUAL, NblFram::$context->data->chave);
        
        if(!is_null($obj->getBy($dto)))
        {
            $d = $obj->getDTODataObject();
            $d->inscricoes_ativas = ($d->inscricoes_ativas == GenericHave::YES);
            $d->tem_eleicoes = ($d->tem_eleicoes == GenericHave::YES);
            $d->valor = (float) $d->valor;
            
            if(!is_null($d->agenda)) {
                $agenda = AgendasWS::getById($d->agenda);
                $d->agenda = $agenda;
            }
            
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
        if(!doIHavePermission(NblFram::$context->token, 'EventoIgreja,EventoSinodal')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }

        $dto = new EventosDTO();
        $obj = new EventosADO();
        $obj_count = new EventosADO();

        $page = ($this->testInputString('page')) ? (int) NblFram::$context->data->page : -1;
        $pageSize = ($this->testInputString('pageSize')) ? (int) NblFram::$context->data->pageSize : -1;
        $pagination = $this->calcPagination($page, $pageSize);

        $searchBy = ($this->testInputString('searchBy')) ? NblFram::$context->data->searchBy : '';
        $orderBy = ($this->testInputString('orderBy')) ? NblFram::$context->data->orderBy : '';
        $groupBy = ($this->testInputString('groupBy')) ? NblFram::$context->data->groupBy : '';
        
        $agenda = ($this->testInputString('agenda')) ? NblFram::$context->data->agenda : '';
        $stat = ($this->testInputString('stat')) ? NblFram::$context->data->stat : '';
        $ref = ($this->testInputString('ref')) ? NblFram::$context->data->ref : '';
        $ref_tp = ($this->testInputString('ref_tp')) ? NblFram::$context->data->ref_tp : '';
        $inicio = ($this->testInputString('inicio')) ? NblFram::$context->data->inicio : '';
        $termino = ($this->testInputString('termino')) ? NblFram::$context->data->termino : '';
        
        $inscricao_ativa = ($this->testInputBool('inscricao_ativa')) ? true : false;
        $tem_eleicoes = ($this->testInputBool('tem_eleicoes')) ? true : false;
        $credencial_digital = ($this->testInputBool('credencial_digital')) ? true : false;
        
        $igreja = ($this->testInputString('igreja')) ? NblFram::$context->data->igreja : '';
        $sinodal = ($this->testInputString('sinodal')) ? NblFram::$context->data->sinodal : '';

        $has_at_least_one_filter = false;

        if(!empty($searchBy))
        {   
            $has_at_least_one_filter = true;
            EventosADO::addComparison($dto, 'nome', CMP_INCLUDE_INSIDE, $searchBy, OP_OR);
            EventosADO::addComparison($dto, 'descricao', CMP_INCLUDE_INSIDE, $searchBy, OP_OR);
        }

        if(!empty($agenda))
        {   
            $has_at_least_one_filter = true;
            EventosADO::addComparison($dto, 'agenda', CMP_EQUAL, $agenda);
        }

        if(!empty($stat))
        {   
            $has_at_least_one_filter = true;
            EventosADO::addComparison($dto, 'stat', CMP_EQUAL, $stat);
        }

        if(!empty($ref))
        {   
            $has_at_least_one_filter = true;
            EventosADO::addComparison($dto, 'ref', CMP_EQUAL, $ref);
        }

        if(!empty($ref_tp))
        {   
            $has_at_least_one_filter = true;
            EventosADO::addComparison($dto, 'ref_tp', CMP_EQUAL, $ref_tp);
        }

        if($inscricao_ativa)
        {   
            $has_at_least_one_filter = true;
            EventosADO::addComparison($dto, 'inscricoes_ativas', CMP_EQUAL, GenericHave::YES);
        }

        if($tem_eleicoes)
        {   
            $has_at_least_one_filter = true;
            EventosADO::addComparison($dto, 'tem_eleicoes', CMP_EQUAL, GenericHave::YES);
        }

        if($credencial_digital)
        {   
            $has_at_least_one_filter = true;
            EventosADO::addComparison($dto, 'credencial_digital', CMP_EQUAL, GenericHave::YES);
        }

        if(!empty($igreja))
        {   
            $has_at_least_one_filter = true;
            EventosADO::addComparison($dto, 'ref_tp', CMP_EQUAL, References::IGREJA);
            EventosADO::addComparison($dto, 'ref', CMP_EQUAL, $igreja);
        }

        if(!empty($sinodal))
        {   
            $has_at_least_one_filter = true;
            EventosADO::addComparison($dto, 'ref_tp', CMP_EQUAL, References::SINODAL);
            EventosADO::addComparison($dto, 'ref', CMP_EQUAL, $sinodal);
        }
        
        if(!empty($inicio))
        {   
            $has_at_least_one_filter = true;
            EventosADO::addComparison($dto, 'time_ini', CMP_GREATER_THEN_DATE, NblPHPUtil::HumanDate2DBDate($inicio), OP_OR, true);
            EventosADO::addComparison($dto, 'time_ini', CMP_IS_NULL, '', OP_OR, true);
        }

        if(!empty($termino))
        {   
            $has_at_least_one_filter = true;
            EventosADO::addComparison($dto, 'time_end', CMP_LESSER_THEN_DATE, NblPHPUtil::HumanDate2DBDate($termino), OP_OR, true);
            EventosADO::addComparison($dto, 'time_end', CMP_IS_NULL, '', OP_OR, true);
        }
        
        if(!empty($orderBy))
        {
            $has_at_least_one_filter = true;
            $order_by_v = explode(',',$orderBy);
            EventosADO::addOrdering($dto, $order_by_v[0], ($order_by_v[1] == 'asc') ? ORDER_ASC : ORDER_DESC);
        }

        if(!empty($groupBy))
        {
            $has_at_least_one_filter = true;
            $groups = explode(',',$groupBy);
            foreach($groups as $g) {
                EventosADO::addGrouping($dto, $g);
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
                        'chave' => $it->chave,
                        'nome' => $it->nome,
                        'logo' => $it->logo,
                        'descricao' => $it->descricao,
                        'ref' => $it->ref,
                        'ref_tp' => $it->ref_tp,
                        'inscricoes_ativas' => ($it->inscricoes_ativas == GenericHave::YES),
                        'formulario_inscricao' => $it->formulario_inscricao,
                        'valor' => (float) $it->valor,
                        'opcoes_pagto' => $it->opcoes_pagto,
                        'lotes' => $it->lotes,
                        'fim_inscricao' => $it->fim_inscricao,
                        'time_ini' => $it->time_ini,
                        'time_end' => $it->time_end,
                        'tem_eleicoes' => ($it->tem_eleicoes == GenericHave::YES),
                        'credencial_digital' => ($it->credencial_digital == GenericHave::YES),
                        'data_delegados' => $it->data_delegados,
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
        if(!doIHavePermission(NblFram::$context->token, 'EventoIgrejaRemove,EventoSinodalRemove')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new EventosDTO();
        $obj = new EventosADO();
        
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
        if(!doIHavePermission(NblFram::$context->token, 'EventoIgrejaRemove,EventoSinodalRemove')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new EventosADO();
        
        foreach(NblFram::$context->data->ids as $id) {
            $dto = new EventosDTO();
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

