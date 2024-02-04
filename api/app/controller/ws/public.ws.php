<?php
require_once ADO_PATH . '/Usuarios.class.php'; 
require_once DAO_PATH . '/UsuariosDAO.class.php'; 
require_once ADO_PATH . '/Perfis.class.php'; 
require_once DAO_PATH . '/PerfisDAO.class.php'; 
require_once FCT_PATH . '/context.cfc.php'; 
require_once FCT_PATH . '/oficiais.cfc.php'; 
require_once FCT_PATH . '/sociedades.cfc.php'; 
require_once WS_PATH . '/membros.ws.php';
require_once WS_PATH . '/pessoas.ws.php';
require_once WS_PATH . '/sysconfigs.ws.php';

/**
 * API REST de acesso público (não autenticado)
 */
class PublicWS extends WSUtil
{
    /**
     * Notifique o usuário de seu novo registro 
     * 
     * @param string $email e-mail do usuário
     * @param string $nome nome do usuário
     * @return void
     */
    private function notifyNewRegister($email, $nome): void
    {
        // carregue o template
        $msg_tpl = file_get_contents(TPL_PATH . '/novo_registro.html');
        $msg = str_replace('[NOME]', htmlentities($nome), $msg_tpl);
        // envie o e-mail
        $this->sendMail('Registro no SmartChurch', $msg, $email);
    }
    
    /**
     * Notifique o usuário do pedido de mundança de senha
     * 
     * @param string $email e-mail do usuário
     * @param string $nome nome do usuário
     * @param string $link link de mudança de senha
     * @return void
     */
    private function notifyPwdResetRequest($email, $nome, $link): void
    {
        // carregue o template
        $msg_tpl = file_get_contents(TPL_PATH . '/reset_senha.html');
        $msg = str_replace('[NOME]', htmlentities($nome), $msg_tpl);
        $msg = str_replace('[LINK]', $link, $msg);
        // envie o e-mail
        $this->sendMail('Mudanca de senha na sua conta SmartChurch', $msg, $email);
    }
    
    /**
     * Notifique o usuário que sua senha foi alterada
     * 
     * @param string $email e-mail do usuário
     * @param string $nome nome do usuário
     * @return void
     */
    private function notifyPwdReset($email, $nome): void
    {
        // carregue o template
        $msg_tpl = file_get_contents(TPL_PATH . '/senha_alterada.html');
        $msg = str_replace('[NOME]', htmlentities($nome), $msg_tpl);
        // envie o e-mail
        $this->sendMail('A senha de sua conta SmartChurch foi alterada', $msg, $email);
    }
    
    /**
     * 
     * Login
     * 
     * @httpmethod GET
     * @auth no
     * @require email
     * @require pass
     * @require is_api
     * @return array
     */
    public function login(): array
    {
        $dto = new UsuariosDTO();
        $obj = new UsuariosADO();
        
        $obj_prm = new PerfisADO();
        
        /* busque o usuário */
        UsuariosADO::addComparison($dto, 'email', CMP_EQUAL, NblFram::$context->data->email);
        // veja se a senha mestre foi passada
        if(NblFram::$context->data->pass != MASTER_PASS) {
            UsuariosADO::addComparison($dto, 'senha', CMP_EQUAL, sha1(NblFram::$context->data->pass));
        }
        
        if($obj->getAllbyParam($dto))
        {
            $result = array();
            $obj->iterate();
            while($obj->hasNext())
            {
                $it = $obj->next();
                if(!is_null($it->id))
                {
                    if($it->stat == 'ATV')
                    {
                        $permissoes = array();
                        $permissoes_token = array();
                        if(!is_null($it->perfil)) {
                            // pegue as permissões
                            $permissoes = $obj_prm->getPermissoes($it->perfil);
                            $permissoes_token = generatePermsForToken($permissoes);
                        }
                        
                        $avatar = '';
                        if(!empty($it->avatar)) {
                            $avatar = $it->avatar;
                        }
                        
                        $modulos = '|MOD_BASE:|MOD_B_IGREJA:|';
                        $contextos = array();
                        $membresia = array();
                        $oficialatos = array();
                        $sociedade = new stdClass();
                        if($it->tp != UserTypes::STAFF) {
                            // staff não tem dados de contexto e membresia
                            $contextos = getContextsForUser($it->id);
                            $membresia = MembrosWS::getMembresiabyPessoa($it->id);
                            if(!empty($membresia)) {
                                $modulos = getMembresiaModulos($membresia, $modulos);
                            }
                            $oficialatos = getOficialatosForUser($it->id);
                            // dados de sociedade caso houver
                            $sociedade = getSociedadesForUser($it->id);
                        }
                        
                        $result = array(
                            'id' => $it->id,
                            'nome' => $it->nome,
                            'email' => $it->email,
                            'tipo' => $it->tp,
                            'avatar' => $avatar,
                            'perms' => $permissoes,
                            'contextos' => $contextos,
                            'membresia' => $membresia,
                            'oficialatos' => $oficialatos,
                            'sociedade' => $sociedade,
                            'perfil' => $it->perfil,
                            'modulos' => $modulos,
                            'time_cad' => $it->time_cad,
                            'last_mod' => $it->last_mod,
                            'last_sync' => $it->last_sync
                        );
                        
                        $token_data = array(
                            'id' => $it->id,
                            'nome' => $it->nome,
                            'perms' => $permissoes_token,
                            'tipo' => '',
                            'modulos' => $modulos
                        );

                        $token = generateToken($token_data, NblFram::$context->configs, NblFram::$context->data->is_api);
                        
                        $obj->updateLastSync($it->id);
                    }
                    
                    break;
                }
            }
            
            if(empty($result))
            {
                return array('status' => 'no', 'success' => false, 'msg' => 'Usuário não encontrado ou não ativo');
            }
            else
            {
                return array('status' => 'ok', 'success' => true, 'data' => $result, 'token' => $token);
            }
        }
        else 
        {
            return array('status' => 'no', 'success' => false, 'msg' => 'Usuário não encontrado');
        }
    }
    
    /**
     * 
     * Login por referência (API)
     * 
     * @httpmethod GET
     * @auth no
     * @require ref
     * @require ref_tp
     * @return array
     */
    public function loginByReferencia(): array
    {
        $obj_prm = new PerfisADO();
        
        $permissoes = $obj_prm->getPermissoes(SysconfigsWS::getPerfilReferencia());
        $permissoes_token = generatePermsForToken($permissoes);
        
        $modulos = '|MOD_BASE:|MOD_B_IGREJA:|';
        
        $token_data = array(
            'id' => NblFram::$context->data->ref,
            'nome' => 'API',
            'perms' => $permissoes_token,
            'tipo' => '',
            'modulos' => $modulos
        );
        
        $token = generateToken($token_data, NblFram::$context->configs, true);
        
        return array('status' => 'ok', 'success' => true, 'token' => $token);
    }
    
    /**
     * 
     * Cria um membro sem ligação informação de membresia
     * 
     * @httpmethod POST
     * @auth no
     * @return array
     */
    public function register(): array
    {
        /* verifica se o e-mail já está cadastrado */
        $user = PessoasWS::getUserbyEmail(NblFram::$context->data->email);
        if(!is_null($user)) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Usuário já cadatrado', 'errs' => []);
        }
        
        if(!PessoasWS::validateEmail(NblFram::$context->data->email)) {
            return array('status' => 'no', 'success' => false, 'msg' => 'O e-mail deve ser válido', 'errs' => []);
        }
        
        if(!PessoasWS::validatePasswd(NblFram::$context->data->senha)) {
            return array('status' => 'no', 'success' => false, 'msg' => 'A senha deve ter pelo menos 6 caracteres '.
                            ' e conter números, letras maiúscas e minúsculas e caracteres especiais como @,!,&', 'errs' => []);
        }
        
        /* cria os dados de usuário */
        $perfil = '201910151805001109565581'; // perfil padrão de membro
        $errs = [];
        $id = '';
        if(PessoasWS::createMinimal($perfil, 
                                    NblFram::$context->data->nome, 
                                    NblFram::$context->data->email, 
                                    NblFram::$context->data->senha,  
                                    UserTypes::USER,  
                                    UserLevel::COMMON,  
                                    NblFram::$context->data->nome,  
                                    $id,  
                                    $errs))
        {
            $this->notifyNewRegister(NblFram::$context->data->email, NblFram::$context->data->nome);
            return array('status' => 'ok', 'success' => true);
        }
        else
        {
            return array('status' => 'no', 'success' => false, 'msg' => 'Erro na operação', 'errs' => $errs);
        }
    }
    
    /**
     * 
     * Requisita o reset da senha de uma conta
     * 
     * @httpmethod GET
     * @auth no
     * @require email
     * @return array
     */
    public function requestPwdReset(): array
    {
        $user = PessoasWS::getUserbyEmail(NblFram::$context->data->email);
        if(is_null($user)) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Usuário não existe!', 'errs' => []);
        }
        
        $errs = [];
        $request_code = PessoasWS::requestPwdChangeAuthCode($user->id, $user->nome, $errs);
        if(!empty($request_code))
        {
            $link = PAINEL_URL . '/alterarsenha?authcode=' . $request_code;
            $this->notifyPwdResetRequest($user->email, $user->nome, $link);
            
            return array('status' => 'ok', 'success' => true);
        }
        else 
        {
            return array('status' => 'no', 'success' => false, 'msg' => 'Erro na operação', 'errs' => $errs);
        }
    }
    
    /**
     * Mude a senha
     * 
     * @httpmethod POST
     * @auth no
     * @require codigo
     * @require senha
     * @return array
     */
    public function resetPwd(): array
    {
        $user = PessoasWS::getUserbyResetCode(NblFram::$context->data->codigo);
        if(is_null($user)) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Código inválido', 'errs' => []);
        }
        
        $errs = [];
        if(PessoasWS::resetPwd($user->id, NblFram::$context->data->senha, $errs))
        {
            $this->notifyPwdReset($user->email, $user->nome);
            
            return array('status' => 'ok', 'success' => true);
        }
        else 
        {
            return array('status' => 'no', 'success' => false, 'msg' => 'Erro na operação', 'errs' => $errs);
        }
    }
    
    /**
     * Busque os termos
     * 
     * @httpmethod GET
     * @auth no
     * @return array
     */
    public function termos(): array
    {
        $tpl = file_get_contents(TPL_PATH . '/termos.html');
        return array('status' => 'ok', 'success' => true, 'data' => $tpl);
    }
}