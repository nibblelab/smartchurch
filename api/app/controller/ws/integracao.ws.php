<?php


/**
 * API REST de Integracao
 */
class IntegracaoWS extends WSUtil
{
    
    /**
     * 
     * Busca 
     * 
     * @httpmethod GET
     * @auth yes
     * @return array
     */
    public function tokenForIgreja(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'IntegracaoIgreja')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $token_data = array(
            'id' => NblFram::$context->token['data']['id'],
            'nome' => NblFram::$context->token['data']['nome'],
            'perms' => NblFram::$context->token['data']['perms'],
            'tipo' => NblFram::$context->token['data']['tipo'],
            'modulos' => NblFram::$context->token['data']['modulos']
        );

        $token = generateToken($token_data, NblFram::$context->configs, true);
        
        return array('status' => 'ok', 'success' => true, 'token' => $token);
    }
    
    /**
     * 
     * Busca 
     * 
     * @httpmethod GET
     * @auth yes
     * @return array
     */
    public function tokenForSinodal(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'IntegracaoSinodal')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $token_data = array(
            'id' => NblFram::$context->token['data']['id'],
            'nome' => NblFram::$context->token['data']['nome'],
            'perms' => NblFram::$context->token['data']['perms'],
            'tipo' => NblFram::$context->token['data']['tipo'],
            'modulos' => NblFram::$context->token['data']['modulos']
        );

        $token = generateToken($token_data, NblFram::$context->configs, true);
        
        return array('status' => 'ok', 'success' => true, 'token' => $token);
    }
}
