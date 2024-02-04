<?php
require_once ADO_PATH . '/Sysconfigs.class.php'; 
require_once DAO_PATH . '/SysconfigsDAO.class.php'; 

/**
 * API REST de Sysconfigs
 */
class SysconfigsWS extends WSUtil
{
    
    /**
     * Obtem o id da tag de evento na agenda
     * 
     * @return string
     */
    public static function getTagEventoId(): string
    {
        $obj = new SysconfigsADO();
        
        if($obj->getAll())
        {
            $obj->iterate();
            while($obj->hasNext())
            {
                $it = $obj->next();
                if(!is_null($it->id))
                {
                    return $it->tag_evento;
                }
            }
        }
        
        return '';
    }
    
    /**
     * Obtem o id de perfil de referência
     * 
     * @return string
     */
    public static function getPerfilReferencia(): string
    {
        $obj = new SysconfigsADO();
        
        if($obj->getAll())
        {
            $obj->iterate();
            while($obj->hasNext())
            {
                $it = $obj->next();
                if(!is_null($it->id))
                {
                    return $it->perfil_referencia;
                }
            }
        }
        
        return '';
    }
    
    /**
     * Obtem o id do perfil de membros (perfil padrão)
     * 
     * @return string
     */
    public static function getPerfilMembro(): string
    {
        $obj = new SysconfigsADO();
        
        if($obj->getAll())
        {
            $obj->iterate();
            while($obj->hasNext())
            {
                $it = $obj->next();
                if(!is_null($it->id))
                {
                    return $it->perfil_membro;
                }
            }
        }
        
        return '';
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
        if(!doIHavePermission(NblFram::$context->token, 'SysConfig')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new SysconfigsDTO();
        $obj = new SysconfigsADO();
        
        $dto->edit = true;
        $dto->id = NblFram::$context->data->id;
        $dto->perfil_membro = NblFram::$context->data->perfil_membro;
        $dto->perfil_cliente = NblFram::$context->data->perfil_cliente;
        $dto->perfil_diretoria_sociedade = NblFram::$context->data->perfil_diretoria_sociedade;
        $dto->perfil_federacao = NblFram::$context->data->perfil_federacao;
        $dto->perfil_sinodal = NblFram::$context->data->perfil_sinodal;
        $dto->perfil_superintendente = NblFram::$context->data->perfil_superintendente;
        $dto->perfil_secretario = NblFram::$context->data->perfil_secretario;
        $dto->perfil_ministerio = NblFram::$context->data->perfil_ministerio;
        $dto->perfil_pastor = NblFram::$context->data->perfil_pastor;
        $dto->perfil_evangelista = NblFram::$context->data->perfil_evangelista;
        $dto->perfil_presbitero = NblFram::$context->data->perfil_presbitero;
        $dto->perfil_diacono = NblFram::$context->data->perfil_diacono;
        $dto->perfil_referencia = NblFram::$context->data->perfil_referencia;
        $dto->perfil_professor = NblFram::$context->data->perfil_professor;
        $dto->tag_evento = NblFram::$context->data->tag_evento;
        $dto->tag_eleicao = NblFram::$context->data->tag_eleicao;
        
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
        if(!doIHavePermission(NblFram::$context->token, 'SysConfig')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new SysconfigsDTO();
        $obj = new SysconfigsADO();
        
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
     * @return array
     */
    public function all(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'SysConfig,Dados')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }

        $obj = new SysconfigsADO();
        $obj_count = new SysconfigsADO();

        $page = ($this->testInputString('page')) ? (int) NblFram::$context->data->page : -1;
        $pageSize = ($this->testInputString('pageSize')) ? (int) NblFram::$context->data->pageSize : -1;
        $pagination = $this->calcPagination($page, $pageSize);

        $obj_count->count(true);
        
        if($page == -1)
        {
            $ok = $obj->getAll();
        }
        else
        {
            $ok = $obj->getAll($pagination->page, $pagination->pagesize);
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
                        'perfil_membro' => $it->perfil_membro,
                        'perfil_cliente' => $it->perfil_cliente,
                        'perfil_diretoria_sociedade' => $it->perfil_diretoria_sociedade,
                        'perfil_federacao' => $it->perfil_federacao,
                        'perfil_sinodal' => $it->perfil_sinodal,
                        'perfil_superintendente' => $it->perfil_superintendente,
                        'perfil_secretario' => $it->perfil_secretario,
                        'perfil_ministerio' => $it->perfil_ministerio,
                        'perfil_pastor' => $it->perfil_pastor,
                        'perfil_evangelista' => $it->perfil_evangelista,
                        'perfil_presbitero' => $it->perfil_presbitero,
                        'perfil_diacono' => $it->perfil_diacono,
                        'perfil_referencia' => $it->perfil_referencia,
                        'perfil_professor' => $it->perfil_professor,
                        'tag_evento' => $it->tag_evento,
                        'tag_eleicao' => $it->tag_eleicao
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

}
