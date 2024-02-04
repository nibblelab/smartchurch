<?php
require_once ADO_PATH . '/Credenciais.class.php'; 
require_once DAO_PATH . '/CredenciaisDAO.class.php'; 
require_once WS_PATH . '/pessoas.ws.php'; 
require_once WS_PATH . '/eventos.ws.php';
require_once WS_PATH . '/cargos.ws.php';

/**
 * API REST de Credenciais
 */
class CredenciaisWS extends WSUtil
{
    
    /**
     * Obtêm a credencial pelo seu id
     * 
     * @param string $id id da credencial
     * @return \CredenciaisDTO|null
     */
    public static function getById($id): ?\CredenciaisDTO
    {
        $dto = new CredenciaisDTO();
        $obj = new CredenciaisADO();

        $dto->id = $id;
        return $obj->get($dto);
    }
    
    
    /**
     * Gera o objeto de dados (DTO) de credencial
     * 
     * @param object $data objeto com os dados vindos da interface
     * @param int $mode flag para indicar se é adição, edição ou remoção
     * @param string $requester quem está requisitando a criação do objeto. [opcional] Default: usuário logado
     * @return \CredenciaisDTO objeto de dados
     */
    public static function generateDTO($data, $mode, $requester = ''): \CredenciaisDTO
    {
        $dto = new CredenciaisDTO();

        if($mode == DTOMode::ADD) {
            $dto->add = true;
            $dto->id = (empty($data->id)) ? NblPHPUtil::makeNumericId() : $data->id;
            
            $pessoa = PessoasWS::getUserbyEmail($data->email_responsavel);
            if(!is_null($pessoa)) {
                $dto->id_responsavel = $pessoa->id;
                $dto->nome_responsavel = $pessoa->nome;
                $dto->email_responsavel = $pessoa->email;
                $dto->telefone_responsavel = $pessoa->celular_1;
            }
            else {
                $dto->id_responsavel = NULL;
                $dto->nome_responsavel = $data->nome_responsavel;
                $dto->email_responsavel = $data->email_responsavel;
                $dto->telefone_responsavel = $data->telefone_responsavel;
            }
            
            $dto->assinatura_inscrito = hash('sha256', NblFram::$context->data->id). ' - '. date('d/m/Y H:i:s');
            $dto->assinatura_responsavel = '';
            $dto->assinatura_instancia = '';
            $dto->assinatura_plataforma = '';
            $dto->stat = Status::ACTIVE;
            $dto->time_cad = date('Y-m-d H:i:s');
            $dto->last_mod = $dto->time_cad;
        }
        else if($mode == DTOMode::EDIT) {
            $dto->edit = true;
            $dto->id = $data->id;
            $dto->nome_responsavel = $data->nome_responsavel;
            $dto->email_responsavel = $data->email_responsavel;
            $dto->telefone_responsavel = $data->telefone_responsavel;
            $dto->last_mod = date('Y-m-d H:i:s');
        }
        else if($mode == DTOMode::DELETE) {
            $dto->delete = true;
            $dto->id = $data->id;
            return $dto;
        }
        
        $dto->last_amod = (empty($requester)) ? NblFram::$context->token['data']['nome'] : $requester;

        return $dto;
    }
    
    /**
     * Cria uma credencial a partir de uma inscrição
     * 
     * @param object $data objeto com os dados
     * @param array $errs erros, se ocorrerem [referência]
     * @return bool
     */
    public static function createFromInscricao($data, &$errs): bool
    {
        $obj = new CredenciaisADO();
        $dto = CredenciaisWS::generateDTO($data, DTOMode::ADD);
        
        $obj->add($dto);
        if($obj->sync())
        {
            return true;
        }
        else
        {
            $errs = $obj->getErrs();
            return false;
        }
    }
    
    /**
     * Edita uma credencial a partir de uma inscrição
     * 
     * @param object $data objeto com os dados
     * @param array $errs erros, se ocorrerem [referência]
     * @return bool
     */
    public static function editFromInscricao($data, &$errs): bool
    {
        $obj = new CredenciaisADO();
        $dto = CredenciaisWS::generateDTO($data, DTOMode::EDIT);
        
        $obj->add($dto);
        if($obj->sync())
        {
            return true;
        }
        else
        {
            $errs = $obj->getErrs();
            return false;
        }
    }
    
    /**
     * Assinatura da plataforma sobre uma credencial (validação)
     * 
     * @param string $id id da credencial
     * @param array $errs erros, se ocorrerem [referência]
     * @return bool
     */
    public static function assinaturaPlataforma($id, &$errs): bool
    {
        $dto = new CredenciaisDTO();
        $obj = new CredenciaisADO();
        
        $dto->id = $id;
        $d = $obj->get($dto);
        if(!is_null($d))
        {
            $d->edit = true;
            $d->assinatura_plataforma = hash('sha256',$d->assinatura_inscrito.$d->assinatura_responsavel.$d->assinatura_instancia) . 
                                        ' - '. date('d/m/Y H:i:s') . ' - Plataforma SmartChurch';
            $dto->last_mod = date('Y-m-d H:i:s');
            $dto->last_amod = 'Plataforma SmartChurch';
            if($obj->sync())
            {
                return true;
            }
            else
            {
                $errs = $obj->getErrs();
                return false;
            }
        }
    }
    
    /**
     * Remove uma credencial
     * 
     * @param string $id id da credencial
     * @param array $errs erros, se ocorrerem [referência]
     * @return bool
     */
    public static function removeFromInscricao($id, &$errs): bool
    {
        $dto = new CredenciaisDTO();
        $obj = new CredenciaisADO();
        
        $dto->id = $id;
        $r = $obj->get($dto);
        if(!is_null($r))
        {
            $r->delete = true;
            if($obj->sync())
            {
                return true;
            }
            else
            {
                $errs = $obj->getErrs();
                return false;
            }
        }
        else 
        {
            return false;
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
        if(!doIHavePermission(NblFram::$context->token, 'EventoIgrejaSave,EventoSinodalSave')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new CredenciaisADO();
        
        $dto = CredenciaisWS::generateDTO(NblFram::$context->data, DTOMode::ADD);
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
        if(!doIHavePermission(NblFram::$context->token, 'EventoIgrejaSave,EventoSinodalSave')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new CredenciaisADO();
        $dto = CredenciaisWS::generateDTO(NblFram::$context->data, DTOMode::EDIT);
        
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
     * Atualiza assinatura do inscrito
     * 
     * @httpmethod PUT
     * @auth yes
     * @require id
     * @return array
     */
    public function updateAssinaturaInscrito(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'EventoIgrejaSave,EventoSinodalSave')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new CredenciaisADO();
        $dto = new CredenciaisDTO();
        
        $dto->edit = true;
        $dto->id = NblFram::$context->data->id;
        $dto->assinatura_inscrito = hash('sha256', NblFram::$context->data->id). ' - '. date('d/m/Y H:i:s');
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
     * Atualiza assinatura do responsável
     * 
     * @httpmethod PUT
     * @auth yes
     * @require id
     * @return array
     */
    public function updateAssinaturaResponsavel(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'EventoIgrejaSave,EventoSinodalSave')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new CredenciaisADO();
        $dto = new CredenciaisDTO();
        
        $dto->edit = true;
        $dto->id = NblFram::$context->data->id;
        $dto->assinatura_responsavel = hash('sha256', NblPHPUtil::makeHexId()). ' - '. date('d/m/Y H:i:s') . ' - ' . NblFram::$context->data->responsavel;
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
     * Atualiza assinatura do responsável
     * 
     * @httpmethod PUT
     * @auth no
     * @require id
     * @return array
     */
    public function signResponsavel(): array
    {
        $dto = new CredenciaisDTO();
        $obj = new CredenciaisADO();
        
        $dto->id = NblFram::$context->data->id;
        $d = $obj->get($dto);
        if(!is_null($d))
        {
            $d->edit = true;
            $d->assinatura_responsavel = hash('sha256', NblPHPUtil::makeHexId()). ' - '. date('d/m/Y H:i:s') . ' - ' . $d->nome_responsavel;
            $d->last_mod = date('Y-m-d H:i:s');
            $d->last_amod = $d->nome_responsavel;
            if($obj->sync())
            {
                return array('status' => 'ok', 'success' => true, 'assinatura' => $d->assinatura_responsavel);
            }
            else
            {
                return array('status' => 'no', 'success' => false, 'msg' => 'Erro na operação', 'errs' => $obj->getErrs());
            }
        }
        else 
        {
            return array('status' => 'no', 'success' => false, 'msg' => 'Erro na operação', 'errs' => $obj->getErrs());
        }
    }
    
    /**
     * 
     * Atualiza assinatura da instância
     * 
     * @httpmethod PUT
     * @auth yes
     * @require id
     * @return array
     */
    public function updateAssinaturaInstancia(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'EventoIgrejaSave,EventoSinodalSave')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new CredenciaisADO();
        $dto = new CredenciaisDTO();
        
        $dto->edit = true;
        $dto->id = NblFram::$context->data->id;
        $dto->assinatura_instancia = hash('sha256', NblPHPUtil::makeHexId()). ' - '. date('d/m/Y H:i:s') . ' - ' . NblFram::$context->data->instancia;
        $dto->last_mod = date('Y-m-d H:i:s');
        $dto->last_amod = NblFram::$context->token['data']['nome'];
        
        $obj->add($dto);
        if($obj->sync())
        {
            // faça a plataforma assinar também
            $errs = [];
            CredenciaisWS::assinaturaPlataforma(NblFram::$context->data->id, $errs);
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
        
        $dto = new CredenciaisDTO();
        $obj = new CredenciaisADO();
        
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
        
        $dto = new CredenciaisDTO();
        $obj = new CredenciaisADO();
        
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
     * Busca por responsável (pública)
     * 
     * @httpmethod GET
     * @auth no
     * @require chave
     * @return array
     */
    public function byChave(): array
    {
        $obj = new CredenciaisADO();
        
        // chave: evento_id|email_responsavel
        $chave_denc = explode('|', base64_decode(NblFram::$context->data->chave));      
        $evento = EventosWS::getById($chave_denc[0]);
        if(!is_null($evento)) 
        {
            $credenciais = $obj->mapAllCredentialsForResponsavelInEvento($chave_denc[1], $chave_denc[0]);
            if(!empty($credenciais))
            {
                return array('status' => 'ok', 
                                'success' => true, 
                                'credenciais' => $credenciais, 
                                'evento' => $evento->nome, 
                                'instancias' => getReferenceList(),
                                'cargos' => CargoWS::getAll()
                        );
            }
            else
            {
                return array('status' => 'no', 'success' => false, 'msg' => 'Não há credenciais para validação');
            }
        }
        else 
        {
            return array('status' => 'no', 'success' => false, 'msg' => 'Chave inválida!');
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

        $dto = new CredenciaisDTO();
        $obj = new CredenciaisADO();
        $obj_count = new CredenciaisADO();

        $page = ($this->testInputString('page')) ? (int) NblFram::$context->data->page : -1;
        $pageSize = ($this->testInputString('pageSize')) ? (int) NblFram::$context->data->pageSize : -1;
        $pagination = $this->calcPagination($page, $pageSize);

        $searchBy = ($this->testInputString('searchBy')) ? NblFram::$context->data->searchBy : '';
        $orderBy = ($this->testInputString('orderBy')) ? NblFram::$context->data->orderBy : '';
        $groupBy = ($this->testInputString('groupBy')) ? NblFram::$context->data->groupBy : '';
        
        $responsavel = ($this->testInputString('responsavel')) ? NblFram::$context->data->responsavel : '';
        $stat = ($this->testInputString('stat')) ? NblFram::$context->data->stat : '';

        $has_at_least_one_filter = false;

        if(!empty($searchBy))
        {   
            $has_at_least_one_filter = true;
            CredenciaisADO::addComparison($dto, 'nome_responsavel', CMP_INCLUDE_INSIDE, $searchBy, OP_OR);
            CredenciaisADO::addComparison($dto, 'email_responsavel', CMP_INCLUDE_INSIDE, $searchBy, OP_OR);
            CredenciaisADO::addComparison($dto, 'telefone_responsavel', CMP_INCLUDE_INSIDE, $searchBy, OP_OR);
        }

        if(!empty($responsavel))
        {   
            $has_at_least_one_filter = true;
            CredenciaisADO::addComparison($dto, 'id_responsavel', CMP_EQUAL, $responsavel);
        }

        if(!empty($stat))
        {   
            $has_at_least_one_filter = true;
            CredenciaisADO::addComparison($dto, 'stat', CMP_EQUAL, $stat);
        }

        if(!empty($orderBy))
        {
            $has_at_least_one_filter = true;
            $order_by_v = explode(',',$orderBy);
            CredenciaisADO::addOrdering($dto, $order_by_v[0], ($order_by_v[1] == 'asc') ? ORDER_ASC : ORDER_DESC);
        }

        if(!empty($groupBy))
        {
            $has_at_least_one_filter = true;
            $groups = explode(',',$groupBy);
            foreach($groups as $g) {
                CredenciaisADO::addGrouping($dto, $g);
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
                        'id_responsavel' => $it->id_responsavel,
                        'nome_responsavel' => $it->nome_responsavel,
                        'email_responsavel' => $it->email_responsavel,
                        'telefone_responsavel' => $it->telefone_responsavel,
                        'assinatura_inscrito' => $it->assinatura_inscrito,
                        'assinatura_responsavel' => $it->assinatura_responsavel,
                        'assinatura_instancia' => $it->assinatura_instancia,
                        'assinatura_plataforma' => $it->assinatura_plataforma,
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
        
        $dto = new CredenciaisDTO();
        $obj = new CredenciaisADO();
        
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
        
        $obj = new CredenciaisADO();
        
        foreach(NblFram::$context->data->ids as $id) {
            $dto = new CredenciaisDTO();
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

