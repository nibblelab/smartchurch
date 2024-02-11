<?php
require_once ADO_PATH . '/Inscricoes.class.php'; 
require_once DAO_PATH . '/InscricoesDAO.class.php'; 
require_once WS_PATH . '/pessoas.ws.php'; 
require_once WS_PATH . '/eventos.ws.php'; 
require_once WS_PATH . '/credenciais.ws.php'; 
require_once WS_PATH . '/membros.ws.php'; 

/**
 * API REST de Inscricoes
 */
class InscricoesWS extends WSUtil
{
    /**
     * 
     * @var \InscricoesWS singleton instance
     */
    private static $_Instance = null;
    
    /**
     * Get singleton instance
     * 
     * @return \InscricoesWS
     */
    public static function getInstance(): \InscricoesWS {
        if(self::$_Instance == null) {
            self::$_Instance = new self();
        }
        return self::$_Instance;
    }
    
    /**
     * Notifique uma pessoa recém cadastrada no sistema através de uma inscrição de evento
     * para que sete a senha de acesso
     * 
     * @param string $email e-mail do usuário
     * @param string $nome nome do usuário
     * @param string $nome_evento nome do evento
     * @param string $link link de mudança de senha
     * @return void
     */
    private function notifyPwdResetEvent($email, $nome, $nome_evento, $link): void
    {
        // carregue o template
        $msg_tpl = file_get_contents(TPL_PATH . '/senha_evento.html');
        $msg = str_replace('[NOME]', htmlentities($nome), $msg_tpl);
        $msg = str_replace('[NOME_EVENTO]', $nome_evento, $msg);
        $msg = str_replace('[LINK]', $link, $msg);
        // envie o e-mail
        $this->sendMail('Inscricao em evento', $msg, $email);
    }
    
    /**
     * Notifique um inscrito que a inscrição dele foi confirmada
     * 
     * @param string $email e-mail do usuário
     * @param string $nome nome do usuário
     * @param string $nome_evento nome do evento
     * @param string $responsavel nome do organizador
     * @param string $email_responsavel e-mail do organizador
     * @return void
     */
    private function notifyConfirmEvent($email, $nome, $nome_evento, $responsavel, $email_responsavel): void
    {
        // carregue o template
        $msg_tpl = file_get_contents(TPL_PATH . '/confirmacao_evento.html');
        $msg = str_replace('[NOME]', htmlentities($nome), $msg_tpl);
        $msg = str_replace('[NOME_EVENTO]', $nome_evento, $msg);
        $msg = str_replace('[RESPONSAVEL]', $responsavel, $msg);
        $msg = str_replace('[EMAIL_RESPONSAVEL]', $email_responsavel, $msg);
        // envie o e-mail
        $this->sendMail('Inscricao confirmada', $msg, $email);
    }
    
    /**
     * Notifique um inscrito que a inscrição dele foi negada
     * 
     * @param string $email e-mail do usuário
     * @param string $nome nome do usuário
     * @param string $nome_evento nome do evento
     * @param string $responsavel nome do organizador
     * @param string $email_responsavel e-mail do organizador
     * @param string $motivo_rescusa motivo da negação
     * @return void
     */
    private function notifyDeniedEvent($email, $nome, $nome_evento, $responsavel, $email_responsavel, $motivo_rescusa): void
    {
        // carregue o template
        $msg_tpl = file_get_contents(TPL_PATH . '/recusa_evento.html');
        $msg = str_replace('[NOME]', htmlentities($nome), $msg_tpl);
        $msg = str_replace('[NOME_EVENTO]', $nome_evento, $msg);
        $msg = str_replace('[RESPONSAVEL]', $responsavel, $msg);
        $msg = str_replace('[EMAIL_RESPONSAVEL]', $email_responsavel, $msg);
        $msg = str_replace('[MOTIVO_RECUSA]', $motivo_rescusa, $msg);
        // envie o e-mail
        $this->sendMail('Inscricao nao confirmada', $msg, $email);
    }
    
    /**
     * Verifica se uma pessoa está inscrita num evento
     * 
     * @param string $pessoa id da pessoa
     * @param string $evento id do evento
     * @return type
     */
    private function checkByPessoaAndEvento($pessoa, $evento)
    {
        $dto = new InscricoesDTO();
        $obj = new InscricoesADO();
        
        InscricoesADO::addComparison($dto, 'pessoa', CMP_EQUAL, $pessoa);
        InscricoesADO::addComparison($dto, 'evento', CMP_EQUAL, $evento);
        
        return ($obj->countBy($dto) > 0);
    }
    
    /**
     * Verifica se os dados da credencial digital foram fornecidos
     * 
     * @return bool
     */
    private function validateCredencialDigital(): bool
    {
        if(property_exists(NblFram::$context->data, "credencial_data")) {
            return (!empty(NblFram::$context->data->credencial_data->nome_responsavel) && 
                    !empty(NblFram::$context->data->credencial_data->email_responsavel) &&
                    !empty(NblFram::$context->data->credencial_data->telefone_responsavel));
        }
        else if(property_exists(NblFram::$context->data, "credencial_digital_data")) {
            return (!empty(NblFram::$context->data->credencial_digital_data->nome_responsavel) && 
                    !empty(NblFram::$context->data->credencial_digital_data->email_responsavel) &&
                    !empty(NblFram::$context->data->credencial_digital_data->telefone_responsavel));
        }
        return false;
    }
    
    /**
     * Atualize a inscrição com dados de membresia, caso os mesmos não estejam presentes na inscrição
     * 
     * @param string $pessoa id da pessoa
     * @return void
     */
    private function updateInscricaoWithPessoaData($pessoa): void
    {
        $membresia = MembrosWS::getMembresiabyPessoa($pessoa);
        
        if(empty(NblFram::$context->data->igreja) && !empty($membresia['igreja'])) {
            NblFram::$context->data->igreja = $membresia['igreja'];
        }
        if(empty(NblFram::$context->data->presbiterio) && !empty($membresia['presbiterio'])) {
            NblFram::$context->data->presbiterio = $membresia['presbiterio'];
        }
        if(empty(NblFram::$context->data->sinodo) && !empty($membresia['sinodo'])) {
            NblFram::$context->data->sinodo = $membresia['sinodo'];
        }
    }
    
    /**
     * Adicione a credencial à inscrição
     * 
     * @param string $id id da inscrição/credencial
     * @return bool
     */
    private function addCredencial($id): bool
    {
        $dto = new InscricoesDTO();
        $obj = new InscricoesADO();
        
        $dto->edit = true;
        $dto->id = $id;
        $dto->credencial_digital = $id;
        
        $obj->add($dto);
        return ($obj->sync());
    }
    
    /**
     * 
     * Cria
     * 
     * @httpmethod POST
     * @auth yes
     * @require pessoa
     * @require evento
     * @return array
     */
    public function create(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'InscricaoSave,Inscrever,IntegracaoIgreja,IntegracaoFederacao,IntegracaoSinodal')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        /* verifica se pessoa já se inscreveu nesse evento */
        if($this->checkByPessoaAndEvento(NblFram::$context->data->pessoa, NblFram::$context->data->evento)) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você já está inscrito nesse evento');
        }
        
        // busque dados do evento 
        $evento = EventosWS::getById(NblFram::$context->data->evento);
        if(is_null($evento)) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Evento não encontrado!', 'errs' => []);
        }
        
        // veja se o evento tem eleições e caso exista se credenciais digitais são usadas
        $credential_needed = ($evento->tem_eleicoes && $evento->credencial_digital && NblFram::$context->data->delegado);
        if($credential_needed && !$this->validateCredencialDigital()) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Dados da credencial não encontrados!', 'errs' => []);
        }
        
        $dto = new InscricoesDTO();
        $obj = new InscricoesADO();
        
        $dto->add = true;
        $dto->id = NblPHPUtil::makeNumericId();
        $dto->pessoa = NblFram::$context->data->pessoa;
        $dto->evento = NblFram::$context->data->evento;
        $dto->motivo_recusa = NULL;
        $dto->credencial_digital = NULL;
        $dto->igreja = (empty(NblFram::$context->data->igreja)) ? NULL : NblFram::$context->data->igreja;
        $dto->presbiterio = (empty(NblFram::$context->data->presbiterio)) ? NULL : NblFram::$context->data->presbiterio;
        $dto->sinodo = (empty(NblFram::$context->data->sinodo)) ? NULL : NblFram::$context->data->sinodo;
        $dto->sociedade = (empty(NblFram::$context->data->sociedade)) ? NULL : NblFram::$context->data->sociedade;
        $dto->federacao = (empty(NblFram::$context->data->federacao)) ? NULL : NblFram::$context->data->federacao;
        $dto->sinodal = (empty(NblFram::$context->data->sinodal)) ? NULL : NblFram::$context->data->sinodal;
        $dto->qrcode = NblPHPUtil::makeRandomNumericCode();
        $dto->delegado = (NblFram::$context->data->delegado) ? GenericHave::YES : GenericHave::NO;
        $dto->cargo_ref = (empty(NblFram::$context->data->cargo_ref)) ? NULL : NblFram::$context->data->cargo_ref;
        $dto->cargo = (empty(NblFram::$context->data->cargo)) ? NULL : NblFram::$context->data->cargo;
        $dto->credencial = '';
        $dto->stat = StatusInscricao::AGUARDANDO;
        $dto->forma_pagto = (NblFram::$context->data->has_pagto) ? NblFram::$context->data->forma_pagto : FormasPagto::NONE;
        $dto->stat_pagto = (NblFram::$context->data->has_pagto) ? NblFram::$context->data->stat_pagto : PagamentoStatus::NONE;
        $dto->valor_pago = (empty(NblFram::$context->data->valor_pago)) ? '0.00' : NblFram::$context->data->valor_pago;
        $dto->gateway_code = '';
        $dto->data_pagto = (empty(NblFram::$context->data->data_pagto)) ? NULL : NblPHPUtil::HumanDate2DBDate(NblFram::$context->data->data_pagto);
        $dto->time_cad = date('Y-m-d H:i:s');
        $dto->last_mod = $dto->time_cad;
        $dto->last_amod = NblFram::$context->token['data']['nome'];
        
        $obj->add($dto);
        if($obj->sync())
        {
            if($credential_needed) {
                $errs = [];
                NblFram::$context->data->credencial_digital_data->id = $dto->id;
                if(CredenciaisWS::createFromInscricao(NblFram::$context->data->credencial_digital_data, $errs)) {
                    $this->addCredencial($dto->id);
                    return array('status' => 'ok', 'success' => true, 'id' => $dto->id);
                }
                else {
                    return array('status' => 'no', 'success' => false, 'msg' => 'Erro ao criar credencial', 'errs' => $errs);
                }
            }
            else {
                return array('status' => 'ok', 'success' => true, 'id' => $dto->id);
            }
        }
        else
        {
            return array('status' => 'no', 'success' => false, 'msg' => 'Erro na operação', 'errs' => $obj->getErrs());
        }
    }
    
    /**
     * 
     * Cria uma inscrição pela página pública ou API
     * 
     * @httpmethod POST
     * @auth yes
     * @return array
     */
    public function inscrever(): array
    {
        // busque dados do evento 
        $evento = EventosWS::getById(NblFram::$context->data->evento);
        if(is_null($evento)) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Evento não encontrado!', 'errs' => []);
        }
        
        // veja se o evento tem eleições e caso exista se credenciais digitais são usadas
        $credential_needed = ($evento->tem_eleicoes && $evento->credencial_digital && NblFram::$context->data->delegado);
        if($credential_needed && !$this->validateCredencialDigital()) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Dados da credencial não encontrados!', 'errs' => []);
        }
        
        // veja se a pessoa já existe
        $pessoa = PessoasWS::getUserbyEmail(NblFram::$context->data->email);
        if(is_null($pessoa)) {
            // tente criar a pessoa
            $pessoa_id = '';
            $errs = [];
            if(!PessoasWS::createForInscricao(NblFram::$context->data->inscrito_data->nome, 
                                                NblFram::$context->data->inscrito_data->email, 
                                                NblFram::$context->data->inscrito_data->sexo, 
                                                NblFram::$context->data->inscrito_data->data_nascimento, 
                                                NblFram::$context->data->inscrito_data->estado_civil, 
                                                NblFram::$context->data->inscrito_data->telefone, 
                                                NblFram::$context->data->inscrito_data->celular_1,
                                                NblFram::$context->data->inscrito_data->celular_2, 
                                                $pessoa_id, 
                                                $errs))
            {
                return array('status' => 'no', 'success' => false, 'msg' => 'Erro na operação', 'errs' => $errs);
            }
            
            // notifique a pessoa da criação da conta no smartchurch para que ela resete a senha
            $request_code = PessoasWS::requestPwdChangeAuthCode($pessoa_id, NblFram::$context->data->nome, $errs);
            if(!empty($request_code))
            {
                $link = PAINEL_URL . '/alterarsenha?authcode=' . $request_code;
                $this->notifyPwdResetEvent(NblFram::$context->data->email, 
                                                NblFram::$context->data->nome, 
                                                NblFram::$context->data->nome_evento, 
                                                $link);
            }
        }
        else {
            // atualize os dados da pessoa com os fornecidos na inscrição
            $errs = [];
            if(!PessoasWS::updatePessoaFromInscricao($pessoa->id, 
                                                NblFram::$context->data->inscrito_data->nome, 
                                                NblFram::$context->data->inscrito_data->email, 
                                                NblFram::$context->data->inscrito_data->sexo, 
                                                NblFram::$context->data->inscrito_data->data_nascimento, 
                                                NblFram::$context->data->inscrito_data->estado_civil, 
                                                NblFram::$context->data->inscrito_data->telefone, 
                                                NblFram::$context->data->inscrito_data->celular_1,
                                                NblFram::$context->data->inscrito_data->celular_2,
                                                $errs))
            {
                return array('status' => 'no', 'success' => false, 'msg' => 'Erro na operação', 'errs' => $errs);
            }
            $pessoa_id = $pessoa->id;
            // atualize campos da inscrição que não foram fornecidos, mas existem no cadastro da pessoa
            $this->updateInscricaoWithPessoaData($pessoa_id);
        }
        
        NblFram::$context->data->pessoa = $pessoa_id;
        NblFram::$context->data->credential_needed = $credential_needed;
        NblFram::$context->data->credencial_data->requester = NblFram::$context->data->inscrito_data->nome;
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
        if(!doIHavePermission(NblFram::$context->token, 'InscricaoSave')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        // busque dados do evento 
        $evento = EventosWS::getById(NblFram::$context->data->evento);
        if(is_null($evento)) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Evento não encontrado!', 'errs' => []);
        }
        
        // veja se o evento tem eleições e caso exista se credenciais digitais são usadas
        $credential_needed = ($evento->tem_eleicoes && $evento->credencial_digital && NblFram::$context->data->delegado);
        
        $dto = new InscricoesDTO();
        $obj = new InscricoesADO();
        
        $dto->edit = true;
        $dto->id = NblFram::$context->data->id;
        $dto->igreja = (empty(NblFram::$context->data->igreja)) ? NULL : NblFram::$context->data->igreja;
        $dto->presbiterio = (empty(NblFram::$context->data->presbiterio)) ? NULL : NblFram::$context->data->presbiterio;
        $dto->sinodo = (empty(NblFram::$context->data->sinodo)) ? NULL : NblFram::$context->data->sinodo;
        $dto->sociedade = (empty(NblFram::$context->data->sociedade)) ? NULL : NblFram::$context->data->sociedade;
        $dto->federacao = (empty(NblFram::$context->data->federacao)) ? NULL : NblFram::$context->data->federacao;
        $dto->sinodal = (empty(NblFram::$context->data->sinodal)) ? NULL : NblFram::$context->data->sinodal;
        $dto->delegado = (NblFram::$context->data->delegado) ? GenericHave::YES : GenericHave::NO;
        $dto->cargo_ref = (empty(NblFram::$context->data->cargo_ref)) ? NULL : NblFram::$context->data->cargo_ref;
        $dto->cargo = (empty(NblFram::$context->data->cargo)) ? NULL : NblFram::$context->data->cargo;
        $dto->forma_pagto = (NblFram::$context->data->has_pagto) ? NblFram::$context->data->forma_pagto : FormasPagto::NONE;
        $dto->stat_pagto = (NblFram::$context->data->has_pagto) ? NblFram::$context->data->stat_pagto : PagamentoStatus::NONE;
        $dto->valor_pago = (empty(NblFram::$context->data->valor_pago)) ? '0.00' : NblFram::$context->data->valor_pago;
        $dto->data_pagto = (empty(NblFram::$context->data->data_pagto)) ? NULL : NblPHPUtil::HumanDate2DBDate(NblFram::$context->data->data_pagto);
        $dto->last_mod = date('Y-m-d H:i:s');
        $dto->last_amod = NblFram::$context->token['data']['nome'];
        
        $obj->add($dto);
        if($obj->sync())
        {
            if($credential_needed && is_null(NblFram::$context->data->credencial_digital)) {
                $errs = [];
                NblFram::$context->data->credencial_digital_data->id = $dto->id;
                if(CredenciaisWS::createFromInscricao(NblFram::$context->data->credencial_digital_data, $errs)) {
                    $this->addCredencial($dto->id);
                    return array('status' => 'ok', 'success' => true, 'id' => $dto->id);
                }
                else {
                    return array('status' => 'no', 'success' => false, 'msg' => 'Erro ao criar credencial', 'errs' => $errs);
                }
            }
            else if($credential_needed && !is_null(NblFram::$context->data->credencial_digital)) {
                $errs = [];
                NblFram::$context->data->credencial_digital_data->id = NblFram::$context->data->credencial_digital;
                if(CredenciaisWS::editFromInscricao(NblFram::$context->data->credencial_digital_data, $errs)) {
                    return array('status' => 'ok', 'success' => true, 'id' => $dto->id);
                }
                else {
                    return array('status' => 'no', 'success' => false, 'msg' => 'Erro ao atualizar credencial', 'errs' => $errs);
                }
            }
            else {
                return array('status' => 'ok', 'success' => true, 'id' => $dto->id);
            }
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
        if(!doIHavePermission(NblFram::$context->token, 'InscricaoBlock')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new InscricoesDTO();
        $obj = new InscricoesADO();
        
        $dto->edit = true;
        $dto->id = NblFram::$context->data->id;
        $dto->stat = NblFram::$context->data->stat;
        $dto->last_mod = date('Y-m-d H:i:s');
        $dto->last_amod = NblFram::$context->token['data']['nome'];
        
        $obj->add($dto);
        if($obj->sync())
        {
            $evento = EventosWS::getById(NblFram::$context->data->evento);
            $responsavel = EventosWS::getOrganizadorByEventoId(NblFram::$context->data->evento);
            if(!is_null($evento) && !is_null($responsavel)) {
                if($dto->stat == StatusInscricao::APROVADA) {
                    $this->notifyConfirmEvent(NblFram::$context->data->email, 
                                                NblFram::$context->data->nome, 
                                                $evento->nome, 
                                                $responsavel->nome, 
                                                $responsavel->email);
                }
                else if($dto->stat == StatusInscricao::RECUSADA) {
                    $this->notifyDeniedEvent(NblFram::$context->data->email, 
                                                NblFram::$context->data->nome, 
                                                $evento->nome, 
                                                $responsavel->nome, 
                                                $responsavel->email, 
                                                NblFram::$context->data->motivo_rescusa);
                } 
            }
            
            return array('status' => 'ok', 'success' => true, 'stat' => $dto->stat);
        }
        else
        {
            return array('status' => 'no', 'success' => false, 'msg' => 'Erro na operação', 'errs' => $obj->getErrs());
        }
    }
    
    /**
     * 
     * Gera a chave de validação do responsável
     * 
     * @httpmethod GET
     * @auth yes
     * @require evento_id
     * @require credencial_id
     * @return array
     */
    public function chave_responsavel(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'Inscricao')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $credencial = CredenciaisWS::getById(NblFram::$context->data->credencial_id);
        if(!is_null($credencial))
        {
            $chave = base64_encode(NblFram::$context->data->evento_id . '|' . $credencial->email_responsavel);
            return array('status' => 'ok', 'success' => true, 'chave' => $chave);
        }
        else 
        {
            return array('status' => 'no', 'success' => false, 'msg' => 'Credencial não encontrada', 'errs' => []);
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
        if(!doIHavePermission(NblFram::$context->token, 'Inscricao')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new InscricoesDTO();
        $obj = new InscricoesADO();
        
        $dto->id = NblFram::$context->data->id;
        if(!is_null($obj->get($dto)))
        {
            $d = $obj->getDTODataObject();
            $d->delegado = ($d->delegado == GenericHave::YES);
            $d->valor_pago = (float) $d->valor_pago;
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
        if(!doIHavePermission(NblFram::$context->token, 'Inscricao')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }

        $dto = new InscricoesDTO();
        $obj = new InscricoesADO();
        
        $page = ($this->testInputString('page')) ? (int) NblFram::$context->data->page : -1;
        $pageSize = ($this->testInputString('pageSize')) ? (int) NblFram::$context->data->pageSize : -1;
        $pagination = $this->calcPagination($page, $pageSize);

        $searchBy = ($this->testInputString('searchBy')) ? NblFram::$context->data->searchBy : '';
        $orderBy = ($this->testInputString('orderBy')) ? NblFram::$context->data->orderBy : '';
        $groupBy = ($this->testInputString('groupBy')) ? NblFram::$context->data->groupBy : '';
        
        $pessoa = ($this->testInputString('pessoa')) ? NblFram::$context->data->pessoa : '';
        $evento = ($this->testInputString('evento')) ? NblFram::$context->data->evento : '';
        $stat = ($this->testInputString('stat')) ? NblFram::$context->data->stat : '';
        $stat_pagto = ($this->testInputString('stat_pagto')) ? NblFram::$context->data->stat_pagto : '';
        $forma_pagto = ($this->testInputString('forma_pagto')) ? NblFram::$context->data->forma_pagto : '';
        $cargo = ($this->testInputString('cargo')) ? NblFram::$context->data->forma_pagto : '';
        $delegado = ($this->testInputBool('delegado')) ? true : false;
        
        $sinodal = ($this->testInputString('sinodal')) ? NblFram::$context->data->sinodal : '';
        $federacao = ($this->testInputString('federacao')) ? NblFram::$context->data->federacao : '';
        $sociedade = ($this->testInputString('sociedade')) ? NblFram::$context->data->sociedade : '';
        
        $sinodo = ($this->testInputString('sinodo')) ? NblFram::$context->data->sinodo : '';
        $presbiterio = ($this->testInputString('presbiterio')) ? NblFram::$context->data->presbiterio : '';
        $igreja = ($this->testInputString('igreja')) ? NblFram::$context->data->igreja : '';
        
        // filtros que aplicam em pessoa
        $sexo = ($this->testInputString('sexo')) ? NblFram::$context->data->sexo : '';
        $estado_civil = ($this->testInputString('estado_civil')) ? NblFram::$context->data->estado_civil : '';
        $escolaridade = ($this->testInputString('escolaridade')) ? NblFram::$context->data->escolaridade : '';
        $com_filhos = ($this->testInputBool('com_filhos')) ? true : false;
        $sem_filhos = ($this->testInputBool('sem_filhos')) ? true : false;
        
        if(!empty($pessoa))
        {   
            // filtre por uma pessoa diretamente
            InscricoesADO::addComparison($dto, 'pessoa', CMP_EQUAL, $pessoa);
            
            $pessoas = PessoasWS::mapBasicDataById();
        }
        else
        {
            // filtros nos dados de pessoa
            if(!empty($searchBy) || !empty($sexo) || !empty($estado_civil) || !empty($escolaridade) || $com_filhos || $sem_filhos)
            {
                $pessoas = PessoasWS::mapBasicDataByIdWithFilters($searchBy, $sexo, $estado_civil, $escolaridade, $com_filhos, $sem_filhos);
            }
            else {
                $pessoas = PessoasWS::mapBasicDataById();
            }
            
            // gere a lista dos ids das pessoas e inclua nos filtros
            $id_list = '';
            foreach($pessoas as $id => $pessoa)
            {
                if(!empty($id_list)) {
                    $id_list .= ',';
                }

                $id_list .= "'$id'";
            }

            if(empty($id_list)) {
                return array('status' => 'ok', 'success' => true, 'datas' => array(), 'total' => 0);
            }
            
            InscricoesADO::addComparison($dto, 'pessoa', CMP_IN_LIST, $id_list);
        }
        
        // filtros de inscrição propriamente dita
        if(!empty($stat))
        {   
            InscricoesADO::addComparison($dto, 'stat', CMP_EQUAL, $stat);
        }
        
        if(!empty($evento))
        {   
            InscricoesADO::addComparison($dto, 'evento', CMP_EQUAL, $evento);
        }
        
        if(!empty($stat_pagto))
        {   
            InscricoesADO::addComparison($dto, 'stat_pagto', CMP_EQUAL, $stat_pagto);
        }
        
        if(!empty($forma_pagto))
        {   
            InscricoesADO::addComparison($dto, 'forma_pagto', CMP_EQUAL, $forma_pagto);
        }
        
        if(!empty($cargo))
        {   
            InscricoesADO::addComparison($dto, 'cargo', CMP_EQUAL, $cargo);
        }
        
        if(!empty($igreja))
        {   
            InscricoesADO::addComparison($dto, 'igreja', CMP_EQUAL, $igreja);
        }
        
        if(!empty($presbiterio))
        {   
            InscricoesADO::addComparison($dto, 'presbiterio', CMP_EQUAL, $presbiterio);
        }
        
        if(!empty($sinodo))
        {   
            InscricoesADO::addComparison($dto, 'sinodo', CMP_EQUAL, $sinodo);
        }
        
        if(!empty($sociedade))
        {   
            InscricoesADO::addComparison($dto, 'sociedade', CMP_EQUAL, $sociedade);
        }
        
        if(!empty($federacao))
        {   
            InscricoesADO::addComparison($dto, 'federacao', CMP_EQUAL, $federacao);
        }
        
        if(!empty($sinodal))
        {   
            InscricoesADO::addComparison($dto, 'sinodal', CMP_EQUAL, $sinodal);
        }
        
        if($delegado)
        {   
            InscricoesADO::addComparison($dto, 'delegado', CMP_EQUAL, GenericHave::YES);
        }
        
        if(!empty($searchBy))
        {   
            InscricoesADO::addComparison($dto, 'gateway_code', CMP_INCLUDE_INSIDE, $searchBy, OP_OR);
        }
        
        InscricoesADO::addComparison($dto, 'pessoa', CMP_IN_LIST, $id_list);

        $ok = $obj->getAllbyParam($dto);
        
        $pre_result = array();
        if($ok)
        {
            $obj->iterate();
            while($obj->hasNext())
            {
                $it = $obj->next();
                if(!is_null($it->id))
                {
                    $pessoa_nome = '';
                    $pessoa_data_nascimento = '';
                    $pessoa_email = '';
                    $pessoa_telefone = '';
                    $pessoa_celular_1 = '';
                    $pessoa_celular_2 = '';
                    $pessoa_sexo = '';
                    $pessoa_estado_civil = '';
                    if(isset($pessoas[$it->pessoa])) {
                        $pessoa_nome = $pessoas[$it->pessoa]['nome'];
                        $pessoa_data_nascimento = $pessoas[$it->pessoa]['data_nascimento'];
                        $pessoa_email = $pessoas[$it->pessoa]['email'];
                        $pessoa_telefone = $pessoas[$it->pessoa]['telefone'];
                        $pessoa_celular_1 = $pessoas[$it->pessoa]['celular_1'];
                        $pessoa_celular_2 = $pessoas[$it->pessoa]['celular_2'];
                        $pessoa_sexo = $pessoas[$it->pessoa]['sexo'];
                        $pessoa_estado_civil = $pessoas[$it->pessoa]['estado_civil'];
                    }
                    
                    $pre_result[] = array(
                        'id' => $it->id,
                        'pessoa' => $it->pessoa,
                        'nome' => $pessoa_nome,
                        'data_nascimento' => $pessoa_data_nascimento,
                        'email' => $pessoa_email,
                        'telefone' => $pessoa_telefone,
                        'celular_1' => $pessoa_celular_1,
                        'celular_2' => $pessoa_celular_2,
                        'sexo' => $pessoa_sexo,
                        'estado_civil' => $pessoa_estado_civil,
                        'evento' => $it->evento,
                        'igreja' => $it->igreja,
                        'presbiterio' => $it->presbiterio,
                        'sinodo' => $it->sinodo,
                        'sociedade' => $it->sociedade,
                        'federacao' => $it->federacao,
                        'sinodal' => $it->sinodal,
                        'qrcode' => $it->qrcode,
                        'delegado' => ($it->delegado == GenericHave::YES),
                        'cargo_ref' => $it->cargo_ref,
                        'cargo' => $it->cargo,
                        'credencial' => $it->credencial,
                        'stat' => $it->stat,
                        'forma_pagto' => $it->forma_pagto,
                        'stat_pagto' => $it->stat_pagto,
                        'valor_pago' => (float) $it->valor_pago,
                        'gateway_code' => $it->gateway_code,
                        'data_pagto' => $it->data_pagto,
                        'time_cad' => $it->time_cad,
                        'last_mod' => $it->last_mod,
                        'last_amod' => $it->last_amod
                    );
                }
            }
            
            /* ordene */
            if(!empty($orderBy))
            {
                $pre_result = $this->orderBy($pre_result);
            }
            
            /* agora pagine, se aplicável */
            $total = count($pre_result);
            
            $result = array();
            if($page == -1) {
                $result = $pre_result;
            }
            else {
                $result = array_slice($pre_result, $pagination->page, $pagination->pagesize);
            }

            return array('status' => 'ok', 'success' => true, 'datas' => $result, 'total' => $total);
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
        if(!doIHavePermission(NblFram::$context->token, 'InscricaoRemove')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new InscricoesDTO();
        $obj = new InscricoesADO();
        
        $dto->id = NblFram::$context->data->id;
        $r = $obj->get($dto);
        if(!is_null($r))
        {
            $credencial_digital = $r->credencial_digital;
            $r->delete = true;
            if($obj->sync())
            {
                if(!is_null($credencial_digital)) {
                    $errs = [];
                    CredenciaisWS::removeFromInscricao($credencial_digital, $errs);
                }
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
        if(!doIHavePermission(NblFram::$context->token, 'InscricaoRemove')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new InscricoesADO();
        
        foreach(NblFram::$context->data->ids as $id) {
            $dto = new InscricoesDTO();
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

