<?php
require_once ADO_PATH . '/Usuarios.class.php'; 
require_once DAO_PATH . '/UsuariosDAO.class.php'; 
require_once FCT_PATH . '/context.cfc.php'; 

/**
 * API REST de Usuarios
 */
class UsuariosWS extends WSUtil
{
    /**
     * 
     * @var \UsuariosWS singleton instance
     */
    private static $_Instance = null;
    
    /**
     * Get singleton instance
     * 
     * @return \UsuariosWS
     */
    public static function getInstance(): \UsuariosWS {
        if(self::$_Instance == null) {
            self::$_Instance = new self();
        }
        return self::$_Instance;
    }
    
    /**
     * Altere o perfil do usuário
     * 
     * @param string $user_id id do usuário
     * @param string $perfil_id id do perfil
     * @param array $errs array com os erros da operação, se existirem
     * @return bool
     */
    public static function changePerfil($user_id, $perfil_id, &$errs): bool
    {
        $dto = new UsuariosDTO();
        $obj = new UsuariosADO();
        
        $dto->edit = true;
        $dto->id = $user_id;
        $dto->perfil = $perfil_id;
        
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
        if(!doIHavePermission(NblFram::$context->token, 'MeSave')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new UsuariosDTO();
        $obj = new UsuariosADO();
        
        $dto->edit = true;
        $dto->id = NblFram::$context->data->id;
        $dto->perfil = NblFram::$context->data->perfil;
        $dto->nome = NblFram::$context->data->nome;
        $dto->email = NblFram::$context->data->email;
        $dto->senha = NblFram::$context->data->senha;
        $dto->stat = NblFram::$context->data->stat;
        $dto->tp = NblFram::$context->data->tp;
        $dto->avatar = NblFram::$context->data->avatar;
        $dto->is_master = NblFram::$context->data->is_master;
        $dto->is_android = NblFram::$context->data->is_android;
        $dto->fcm_token = NblFram::$context->data->fcm_token;
        $dto->reset_code = NblFram::$context->data->reset_code;
        $dto->time_cad = NblFram::$context->data->time_cad;
        $dto->last_mod = NblFram::$context->data->last_mod;
        $dto->last_sync = NblFram::$context->data->last_sync;
        $dto->last_amod = NblFram::$context->data->last_amod;
        
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
     * Busca 
     * 
     * @httpmethod GET
     * @auth yes
     * @require id
     * @return array
     */
    public function me(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'Me')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new UsuariosDTO();
        $obj = new UsuariosADO();
        
        $dto->id = NblFram::$context->data->id;
        if(!is_null($obj->get($dto)))
        {
            $d = $obj->getDTODataObject();
            $d->senha = '';
            
            return array('status' => 'ok', 'success' => true, 'datas' => $d);
        }
        else 
        {
            return array('status' => 'no', 'success' => false, 'msg' => 'Erro na operação', 'errs' => $obj->getErrs());
        }
    }

    /**
     * 
     * Busca o token do usuário para uma instância de seu contexto
     * 
     * @httpmethod GET
     * @auth yes
     * @require id
     * @require instancia
     * @require contexto
     * @return array
     */
    public function tokeninstancia(): array
    {
//        if(!doIHavePermission(NblFram::$context->token, 'Admin')) {
//            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
//        }
        
        $modulos = getModulosFromInstancia(NblFram::$context->data->instancia);
        $perms = getPermissoesFromContexto(NblFram::$context->data->contexto);
        $perms = array_merge(NblFram::$context->token['data']['perms'], $perms);
        $token_data = array(
            'id' => NblFram::$context->token['data']['id'],
            'nome' => NblFram::$context->token['data']['nome'],
            'perms' => $perms,
            'tipo' => NblFram::$context->token['data']['tipo'],
            'modulos' => $modulos
        );

        $token = generateToken($token_data, NblFram::$context->configs, false);
        
        return array('status' => 'ok', 'success' => true, 'token' => $token, 'modulos' => $modulos, 'perms' => $perms);
    }
    
    
    /**
     * 
     * Verifica se um usuário existe pelo seu nome
     * 
     * @httpmethod GET
     * @auth yes
     * @require nome
     * @return array
     */
    public function checkbynome(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'Admin')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }

        $dto = new UsuariosDTO();
        $obj = new UsuariosADO();

        $nome = NblFram::$context->data->nome;
        UsuariosADO::addComparison($dto, 'nome', CMP_EQUAL, $nome);
        
        $total = 0;
        
        $ok = $obj->getAllbyParam($dto);
        
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
                        'perfil' => $it->perfil,
                        'nome' => $it->nome,
                        'email' => $it->email,
                        'senha' => '',
                        'stat' => $it->stat,
                        'tp' => $it->tp,
                        'avatar' => $it->avatar,
                        'is_master' => $it->is_master,
                        'is_android' => $it->is_android,
                        'fcm_token' => $it->fcm_token,
                        'reset_code' => $it->reset_code,
                        'time_cad' => $it->time_cad,
                        'last_mod' => $it->last_mod,
                        'last_sync' => $it->last_sync,
                        'last_amod' => $it->last_amod
                    );
                }
                
                $total++;
            }

            return array('status' => 'ok', 'success' => true, 'datas' => $result, 'total' => $total);
        }
        else
        {
            return array('status' => 'no', 'success' => false, 'msg' => 'Erro na operação', 'errs' => $obj->getErrs());
        }
        
    }

}

