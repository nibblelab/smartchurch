<?php
require_once WS_PATH . '/pessoas.ws.php'; 
require_once WS_PATH . '/instancias.ws.php'; 
require_once WS_PATH . '/contextos.ws.php'; 
require_once WS_PATH . '/igrejas.ws.php'; 
require_once WS_PATH . '/federacoes.ws.php'; 
require_once WS_PATH . '/sinodais.ws.php'; 


/**
 * API REST de Admins
 */
class AdminsWS extends WSUtil
{
    /**
     * Notifique o novo cliente do cadastro dele como admin
     * 
     * @param string $email e-mail do cliente
     * @param string $instancia nome da instância (igreja, federação, sinodal, etc)
     * @param string $login e-mail de login
     * @param string $senha senha de login
     * @return void
     */
    private function notifyNewCliente($email, $instancia, $login, $senha): void
    {
        // carregue o template
        $msg_tpl = file_get_contents(TPL_PATH . '/novo_admin.html');
        $msg = str_replace('[INSTANCIA_NOME]', htmlentities($instancia), $msg_tpl);
        $msg = str_replace('[ADMIN_EMAIL]', $login, $msg);
        $msg = str_replace('[ADMIN_SENHA]', $senha, $msg);
        // envie o e-mail
        $this->sendMail('Cadastro ativo no Smartchurch como admin da ' . htmlentities($instancia), $msg, $email);
    }
    
    /**
     * 
     * Cria um usuário admin para uma instância
     * 
     * @httpmethod POST
     * @auth yes
     * @require email e-mail do usuário
     * @require perfil id do perfil do usuário
     * @require nome nome do usuário
     * @require senha senha do usuário
     * @optional ref id de referência da instância
     * @optional ref_tp tipo de referência da instância
     * @optional instancia objeto DTO da instancia
     * @return array
     */
    public function create(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'FederacaoAdmin')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        // veja se já existe um usuário para esse e-mail
        $old_user = PessoasWS::getUserbyEmail(NblFram::$context->data->email);
        $pessoa_ok = false;
        $pessoa_id = '';
        $errs = array();
        if(is_null($old_user)) {
            // não existe. Crie!
            $pessoa_ok = PessoasWS::createMinimal(NblFram::$context->data->perfil, 
                                        NblFram::$context->data->nome, 
                                        NblFram::$context->data->email, 
                                        NblFram::$context->data->senha, 
                                        UserTypes::ADMIN, 
                                        UserLevel::MASTER, 
                                        NblFram::$context->token['data']['nome'], 
                                        $pessoa_id, 
                                        $errs);
        }
        else {
            // existe. Apenas altere os campos que precisar
            $pessoa_id = $old_user->id;
            $pessoa_ok = PessoasWS::editMininal($old_user->id, 
                                        NblFram::$context->data->perfil, 
                                        NblFram::$context->data->nome, 
                                        NblFram::$context->data->senha, 
                                        UserTypes::ADMIN, 
                                        UserLevel::MASTER, 
                                        NblFram::$context->token['data']['nome'], 
                                        $errs);
        }
        
        if($pessoa_ok)
        {
            // veja se a instância para a referência existe
            $instancia_id = InstanciasWS::getIdbyRef(NblFram::$context->data->ref, NblFram::$context->data->ref_tp);
            if(empty($instancia_id)) {
                if(!InstanciasWS::create(NblFram::$context->data->ref, 
                                        NblFram::$context->data->ref_tp, 
                                        NblFram::$context->data->modulos,
                                        null, 
                                        $instancia_id, 
                                        $errs))
                {
                    /* falhou ao criar instância. Remova o usuário */
                    $errs_del = array();
                    PessoasWS::deleteById($pessoa_id, $errs_del);
                    return array('status' => 'no', 'success' => false, 'msg' => 'Erro na operação', 'errs' => $errs);
                }
            }
            
            
            // adicione o contexto
            if(ContextosWS::create($pessoa_id,   
                                    $instancia_id, 
                                    NblFram::$context->data->ref,  
                                    NblFram::$context->data->ref_tp,  
                                    $errs))
            {
                // notifique o cliente 
                if(!is_null(NblFram::$context->data->instancia)) {
                    // envie o e-mail de notificação
                    $this->notifyNewCliente(NblFram::$context->data->email, 
                                                NblFram::$context->data->instancia->nome, 
                                                NblFram::$context->data->email,
                                                NblFram::$context->data->senha);
                }

                return array('status' => 'ok', 'success' => true, 'id' => $pessoa_id);
            }
            else 
            {
                /* Falhou ao criar o contexto. Remova o usuário também */
                $errs_del = array();
                PessoasWS::deleteById($pessoa_id, $errs_del);

                return array('status' => 'no', 'success' => false, 'msg' => 'Erro na operação', 'errs' => $errs);
            }
        }
        else
        {
            return array('status' => 'no', 'success' => false, 'msg' => 'Erro na operação', 'errs' => $errs);
        }
    }
    
    /**
     * Cria usuário admin para a igreja
     * 
     * @httpmethod POST
     * @auth yes
     * @require email e-mail do usuário
     * @require perfil id do perfil do usuário
     * @require nome nome do usuário
     * @require senha senha do usuário
     * @require igreja id da igreja
     * @return array
     */
    public function createForIgreja(): array
    {
        NblFram::$context->data->ref = NblFram::$context->data->igreja;
        NblFram::$context->data->ref_tp = References::IGREJA;
        NblFram::$context->data->instancia = IgrejasWS::getById(NblFram::$context->data->igreja);
        return $this->create();
    }
    
    /**
     * 
     * Cria usuário admin para a federação
     * 
     * @httpmethod POST
     * @auth yes
     * @require email e-mail do usuário
     * @require perfil id do perfil do usuário
     * @require nome nome do usuário
     * @require senha senha do usuário
     * @require federacao id da federação
     * @return array
     */
    public function createForFederacao(): array
    {
        NblFram::$context->data->ref = NblFram::$context->data->federacao;
        NblFram::$context->data->ref_tp = References::FEDERACAO;
        NblFram::$context->data->instancia = FederacoesWS::getById(NblFram::$context->data->federacao);
        return $this->create();
    }
    
    /**
     * 
     * Cria usuário admin para a sinodal
     * 
     * @httpmethod POST
     * @auth yes
     * @require email e-mail do usuário
     * @require perfil id do perfil do usuário
     * @require nome nome do usuário
     * @require senha senha do usuário
     * @require sinodal
     * @return array
     */
    public function createForSinodal(): array
    {
        NblFram::$context->data->ref = NblFram::$context->data->sinodal;
        NblFram::$context->data->ref_tp = References::SINODAL;
        NblFram::$context->data->instancia = SinodaisWS::getById(NblFram::$context->data->sinodal);
        return $this->create();
    }

    /**
     * 
     * Edita
     * 
     * @httpmethod PUT
     * @auth yes
     * @require id id do admin
     * @require email e-mail do usuário
     * @require perfil id do perfil do usuário
     * @require nome nome do usuário
     * @optional senha senha do usuário
     * @return array
     */
    public function edit(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'FederacaoAdmin')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $errs = array();
        if(PessoasWS::editMininal(NblFram::$context->data->id, 
                                        NblFram::$context->data->perfil, 
                                        NblFram::$context->data->nome, 
                                        NblFram::$context->data->senha, 
                                        UserTypes::ADMIN, 
                                        UserLevel::MASTER, 
                                        NblFram::$context->token['data']['nome'], 
                                        $errs))
        {
            // busque o contexto de igreja
            $contexto_map = array();
            if(property_exists(NblFram::$context->data, 'federacao')) 
            {
                $contexto_map = ContextosWS::getByUserAndReference(NblFram::$context->data->id, 
                                            NblFram::$context->data->federacao, 
                                            References::FEDERACAO);
            }
            else if(property_exists(NblFram::$context->data, 'sinodal')) 
            {
                $contexto_map = ContextosWS::getByUserAndReference(NblFram::$context->data->id, 
                                            NblFram::$context->data->sinodal, 
                                            References::SINODAL);
            }
            
            if(!empty($contexto_map)) {
                // salve alguma possível alteração nos módulos
                InstanciasWS::editModulos($contexto_map[NblFram::$context->data->id]['instancia'], 
                                            NblFram::$context->data->modulos,
                                            $errs);
            }
            
            return array('status' => 'ok', 'success' => true, 'id' => NblFram::$context->data->id);
        }
        else
        {
            return array('status' => 'no', 'success' => false, 'msg' => 'Erro na operação', 'errs' => $errs);
        }
    }

    /**
     * 
     * Busca usuários admin
     * 
     * @httpmethod GET
     * @auth yes
     * @optional federacao id da federação
     * @optional sinodal id da sinodal
     * @return array
     */
    public function all(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'FederacaoAdmin')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        // mapeie as instâncias
        $instancias = InstanciasWS::getMap();
        
        // filtre pelo contexto
        $federacao = ($this->testInputString('federacao')) ? NblFram::$context->data->federacao : '';
        $sinodal = ($this->testInputString('sinodal')) ? NblFram::$context->data->sinodal : '';
        
        $contextos = array();
        if(!empty($federacao))
        {
            $contextos = array_merge($contextos, ContextosWS::getMapByReference($federacao, References::FEDERACAO));
        }
        
        if(!empty($sinodal))
        {
            $contextos = array_merge($contextos, ContextosWS::getMapByReference($sinodal, References::SINODAL));
        }
        
        $result = array();
        if(!empty($contextos))
        {
            // itere os contextos, pegue os ids dos usuários e os módulos da instância
            $pessoas_ids = array();
            $modulos = '';
            foreach($contextos as $ctx) {
                $pessoas_ids[] = $ctx['usuario'];
                if(isset($instancias[$ctx['instancia']])) {
                    $modulos = $instancias[$ctx['instancia']]['modulos'];
                }
            }
            
            $pessoas = PessoasWS::getAllByIds($pessoas_ids);
            foreach($pessoas as $pessoa)
            {
                $result[] = array(
                    'id' => $pessoa['id'],
                    'modulos' => $modulos,
                    'perfil' => $pessoa['perfil'],
                    'nome' => $pessoa['nome'],
                    'email' => $pessoa['email'],
                    'stat' => $pessoa['stat'],
                    'time_cad' => $pessoa['time_cad'],
                    'last_mod' => $pessoa['last_mod'],
                    'last_amod' => $pessoa['last_amod']
                );
            }
        }
        
        return array('status' => 'ok', 'success' => true, 'datas' => $result, 'total' => count($result));
    }

}

