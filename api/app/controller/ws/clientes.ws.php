<?php
require_once ADO_PATH . '/Usuarios.class.php'; 
require_once ADO_PATH . '/Pessoas.class.php'; 
require_once DAO_PATH . '/PessoasDAO.class.php';
require_once ADO_PATH . '/Instancias.class.php'; 
require_once DAO_PATH . '/InstanciasDAO.class.php'; 
require_once ADO_PATH . '/Contextos.class.php'; 
require_once DAO_PATH . '/ContextosDAO.class.php'; 
require_once ADO_PATH . '/Igrejas.class.php'; 
require_once DAO_PATH . '/IgrejasDAO.class.php'; 
require_once WS_PATH . '/igrejas.ws.php'; 
require_once WS_PATH . '/pessoas.ws.php'; 
require_once WS_PATH . '/instancias.ws.php'; 
require_once WS_PATH . '/contextos.ws.php'; 


/**
 * API REST de Clientes
 */
class ClientesWS extends WSUtil
{
    /**
     * 
     * @var \ClientesWS singleton instance
     */
    private static $_Instance = null;
    
    /**
     * Get singleton instance
     * 
     * @return \ClientesWS
     */
    public static function getInstance(): \ClientesWS {
        if(self::$_Instance == null) {
            self::$_Instance = new self();
        }
        return self::$_Instance;
    }
    
    /**
     * Notifique o novo cliente do cadastro dele como admin
     * 
     * @param string $email e-mail do cliente
     * @param string $igreja nome da igreja
     * @param string $login e-mail de login
     * @param string $senha senha de login
     * @return void
     */
    private function notifyNewCliente($email, $igreja, $login, $senha): void
    {
        // carregue o template
        $msg_tpl = file_get_contents(TPL_PATH . '/novo_admin.html');
        $msg = str_replace('[IGREJA_NOME]', htmlentities($igreja), $msg_tpl);
        $msg = str_replace('[ADMIN_EMAIL]', $login, $msg);
        $msg = str_replace('[ADMIN_SENHA]', $senha, $msg);
        // envie o e-mail
        $this->sendMail('Cadastro ativo no Smartchurch como admin da ' . htmlentities($igreja), $msg, $email);
    }
    
    /**
     * Verifica se o templo já possui algum usuário admin (cliente)
     * 
     * @httpmethod GET
     * @auth yes
     * @require email
     * @return array
     */
    public function checkTemploHasCliente(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'ClienteSave')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
    }
    
    /**
     * Verifica se o cliente já existe
     * 
     * @httpmethod GET
     * @auth yes
     * @require email
     * @return array
     */
    public function checkIsCliente(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'ClienteSave')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        return array('status' => 'ok', 'success' => true, 'exists' => PessoasWS::isAdminByEmail(NblFram::$context->data->email));
    }
    
    /**
     * Verifica se o templo já possui algum cliente
     * 
     * @httpmethod GET
     * @auth yes
     * @require igreja
     * @return array
     */
    public function checkClienteForTemplo(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'ClienteSave')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        // busque todos os contextos dos templo
        $contextos = ContextosWS::getMapByReference(NblFram::$context->data->igreja, References::IGREJA);
        if(empty($contextos)) {
            return array('status' => 'ok', 'success' => true, 'exists' => false);
        }
        // busque todos os admins
        $admins = PessoasWS::getAllAdminIds();
        foreach($contextos as $ctx) {
            if(in_array($ctx['usuario'], $admins)) {
                // há um contexto do templo que possui um usuário admin
                return array('status' => 'ok', 'success' => true, 'exists' => true);
            }
        }
                
        return array('status' => 'ok', 'success' => true, 'exists' => false);
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
        if(!doIHavePermission(NblFram::$context->token, 'ClienteSave')) {
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
        
        if($pessoa_ok) {
            // veja se a instância para a referência existe
            $instancia_id = InstanciasWS::getIdbyRef(NblFram::$context->data->igreja, References::IGREJA);
            if(empty($instancia_id)) {
                if(!InstanciasWS::create(NblFram::$context->data->igreja, 
                                        References::IGREJA, 
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
                                    NblFram::$context->data->igreja,  
                                    References::IGREJA,  
                                    $errs))
            {
                // notifique o cliente 
                $igreja = IgrejasWS::getById(NblFram::$context->data->igreja);
                if(!is_null($igreja)) {
                    // envie o e-mail de notificação
                    $this->notifyNewCliente(NblFram::$context->data->email, 
                                                $igreja->nome, 
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
        if(!doIHavePermission(NblFram::$context->token, 'ClienteSave')) {
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
            $contexto_map = ContextosWS::getByUserAndReference(NblFram::$context->data->id, 
                                            NblFram::$context->data->igreja, 
                                            References::IGREJA);
            
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
     * Edita status 
     * 
     * @httpmethod PUT
     * @auth yes
     * @require id
     * @return array
     */
    public function changestat(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'ClienteBlock')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new PessoasDTO();
        $obj = new PessoasADO();
        
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
        if(!doIHavePermission(NblFram::$context->token, 'Cliente')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new PessoasDTO();
        $obj = new PessoasADO();
        
        // busque o usuário 
        $dto->id = NblFram::$context->data->id;
        if(!is_null($obj->get($dto)))
        {
            $d = $obj->getDTODataObject();
            
            // busque o contexto de igreja
            $dto_c = new ContextosDTO();
            $obj_c = new ContextosADO();
            
            ContextosADO::addComparison($dto_c, 'ref_tp', CMP_EQUAL, References::IGREJA);
            ContextosADO::addComparison($dto_c, 'usuario', CMP_EQUAL, NblFram::$context->data->id);
            
            $contexto_map = $obj_c->mapAllByWithParam('usuario', $dto_c);
            if(!empty($contexto_map)) {
                
                // busque os módulos
                $dto_i = new InstanciasDTO();
                $obj_i = new InstanciasADO();
                $dto_i->id = $contexto_map[$d->id]['instancia'];
                if(!is_null($obj_i->get($dto_i)))
                {
                    $d_i = $obj_i->getDTODataObject();
                    
                    // busque os dados da igreja
                    $dto_ig = new IgrejasDTO();
                    $obj_ig = new IgrejasADO();

                    $dto_ig->id = $contexto_map[$d->id]['ref'];
                    if(!is_null($obj_ig->get($dto_ig)))
                    {
                        $d_ig = $obj_ig->getDTODataObject();

                        $data = array(
                            'igreja' => array(
                                'id' => $d_ig->id,
                                'sinodo' => $d_ig->sinodo,
                                'presbiterio' => $d_ig->presbiterio,
                                'igreja' => $d_ig->igreja,
                                'nome' => $d_ig->nome
                            ),
                            'mestre' => array(
                                'id' => $d->id,
                                'perfil' => $d->perfil,
                                'nome' => $d->nome,
                                'email' => $d->email,
                                'stat' => $d->stat,
                                'modulos' => $d_i->modulos
                            )
                        );

                        return array('status' => 'ok', 'success' => true, 'datas' => $data);
                    }
                    else 
                    {
                        return array('status' => 'no', 'success' => false, 'msg' => 'Erro na operação', 'errs' => $obj_i->getErrs());
                    }
                }
                else 
                {
                    return array('status' => 'no', 'success' => false, 'msg' => 'Erro na operação', 'errs' => $obj_i->getErrs());
                }
            }
            else 
            {
                return array('status' => 'no', 'success' => false, 'msg' => 'Erro na operação', 'errs' => $obj_c->getErrs());
            }
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
        if(!doIHavePermission(NblFram::$context->token, 'Cliente')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }

        $dto = new IgrejasDTO();
        $obj = new IgrejasADO();
        
        $page = ($this->testInputString('page')) ? (int) NblFram::$context->data->page : -1;
        $pageSize = ($this->testInputString('pageSize')) ? (int) NblFram::$context->data->pageSize : -1;
        $pagination = $this->calcPagination($page, $pageSize);

        $searchBy = ($this->testInputString('searchBy')) ? NblFram::$context->data->searchBy : '';
        $orderBy = ($this->testInputString('orderBy')) ? NblFram::$context->data->orderBy : '';
        $groupBy = ($this->testInputString('groupBy')) ? NblFram::$context->data->groupBy : '';
        
        $stat = ($this->testInputString('stat')) ? NblFram::$context->data->stat : '';
        
        $sinodo = ($this->testInputString('sinodo')) ? NblFram::$context->data->sinodo : '';
        $presbiterio = ($this->testInputString('presbiterio')) ? NblFram::$context->data->presbiterio : '';
        $igreja = ($this->testInputString('igreja')) ? NblFram::$context->data->igreja : '';
        
        /* mapeie os contextos */
        $dto_c = new ContextosDTO();
        $obj_c = new ContextosADO();
        
        ContextosADO::addComparison($dto_c, 'ref_tp', CMP_EQUAL, References::IGREJA);
        
        $contextos_map = $obj_c->multiMapAllByWithParam('ref', $dto_c);
        
        /* mapeie os usuários */
        $dto_u = new PessoasDTO();
        $obj_u = new PessoasADO();
        
        PessoasADO::addComparison($dto_u, 'is_master', CMP_EQUAL, UserLevel::MASTER);
        
        if(!empty($searchBy)) {
            PessoasADO::addComparison($dto_u, 'nome', CMP_INCLUDE_INSIDE, $searchBy);
        }
        
        if(!empty($stat)) {
            PessoasADO::addComparison($dto_u, 'stat', CMP_EQUAL, $stat);
        }
        
        $usuarios_map = $obj_u->mapAllByWithParam('id', $dto_u);

        /* busque os templos com os filtros aplicáveis */
        
        $has_at_least_one_filter = false;

        if(!empty($sinodo))
        {   
            $has_at_least_one_filter = true;
            IgrejasADO::addComparison($dto, 'sinodo', CMP_EQUAL, $sinodo);
        }

        if(!empty($presbiterio))
        {   
            $has_at_least_one_filter = true;
            IgrejasADO::addComparison($dto, 'presbiterio', CMP_EQUAL, $presbiterio);
        }

        if(!empty($igreja))
        {   
            $has_at_least_one_filter = true;
            IgrejasADO::addComparison($dto, 'igreja', CMP_EQUAL, $igreja);
        }
        
        $ok = (!$has_at_least_one_filter) ? $obj->getAll() : $obj->getAllbyParam($dto);
        
        $pre_result = array();
        if($ok)
        {
            $obj->iterate();
            while($obj->hasNext())
            {
                $it = $obj->next();
                if(!is_null($it->id))
                {
                    /* procure o usuário mestre */
                    $mestre = array();
                    if(isset($contextos_map[$it->id])) {
                        foreach($contextos_map[$it->id] as $context) {
                            if(isset($usuarios_map[$context['usuario']])) {
                                $usuario = $usuarios_map[$context['usuario']];
                                $mestre['id'] = $usuario['id'];
                                $mestre['perfil'] = $usuario['perfil'];
                                $mestre['nome'] = $usuario['nome'];
                                $mestre['email'] = $usuario['email'];
                                $mestre['stat'] = $usuario['stat'];
                                $mestre['time_cad'] = $usuario['time_cad'];
                                $mestre['last_mod'] = $usuario['last_mod'];
                                $mestre['last_amod'] = $usuario['last_amod'];
                                break;
                            }
                        }
                    }
                    
                    if(!empty($mestre)) {
                        // adicione ao pré resultado
                        $pre_result[] = array(
                            'igreja' => array(
                                'id' => $it->id,
                                'sinodo' => $it->sinodo,
                                'presbiterio' => $it->presbiterio,
                                'igreja' => $it->igreja,
                                'nome' => $it->nome
                            ),
                            'mestre' => $mestre,
                            // dados usados em ordenação
                            'nome' => $mestre['nome'],
                            'time_cad' => $mestre['time_cad'],
                            'last_mod' => $mestre['last_mod']
                        );
                    }
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
        if(!doIHavePermission(NblFram::$context->token, 'ClienteRemove')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new PessoasDTO();
        $obj = new PessoasADO();
        
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
        if(!doIHavePermission(NblFram::$context->token, 'ClienteRemove')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new PessoasADO();
        
        foreach(NblFram::$context->data->ids as $id) {
            $dto = new PessoasDTO();
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

