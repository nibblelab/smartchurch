<?php

/**
 * Busca as referências de contextos de um usuário pelo seu id
 * 
 * @param string $user_id id do usuário
 * @return array  array vazio se nada for encontrado
 */
function getContextsReferencesForUser($user_id): array
{
    require_once ADO_PATH . '/Contextos.class.php'; 
    require_once DAO_PATH . '/ContextosDAO.class.php'; 
    
    $contextos = array();
    $dto_c = new ContextosDTO();
    $obj_c = new ContextosADO();

    ContextosADO::addComparison($dto_c, 'usuario', CMP_EQUAL, $user_id);
    ContextosADO::addComparison($dto_c, 'stat', CMP_EQUAL, Status::ACTIVE);
    $contexto_map = $obj_c->multiMapAllByWithParam('usuario', $dto_c);
    if(!empty($contexto_map[$user_id])) {
        foreach($contexto_map[$user_id] as $contexto) {
            $contextos[] = array(
                'ref_tp' => $contexto['ref_tp'],
                'ref' => $contexto['ref'],
                'instancia' => $contexto['instancia'],
                'contexto' => $contexto['id']
            );
        }
    }
    return $contextos;
}

/**
 * Carregue o contexto de igreja
 * 
 * @param string $igreja_id id da igreja
 * @param string $instancia_id id da instância (módulos)
 * @param string $contexto_id id do contexto (permissões)
 * @return array  
 */
function loadIgrejaContext($igreja_id, $instancia_id, $contexto_id): array
{
    require_once ADO_PATH . '/Igrejas.class.php'; 
    require_once DAO_PATH . '/IgrejasDAO.class.php';
    
    $dto = new IgrejasDTO();
    $obj = new IgrejasADO();

    $dto->id = $igreja_id;
    $result = array(
        'id' => '',
            'key' => '',
            'nome' => '',
            'sinodo' => '',
            'presbiterio' => '',
            'mae' => '',
            'instancia' => '',
            'contexto' => ''
    );
    if(!is_null($obj->get($dto)))
    {
        $d = $obj->getDTODataObject();
        $result['id'] = $d->id;
        $result['key'] = sha1($igreja_id . $instancia_id . $contexto_id);
        $result['nome'] = $d->nome;
        $result['sinodo'] = $d->sinodo;
        $result['presbiterio'] = $d->presbiterio;
        $result['mae'] = $d->igreja;
        $result['instancia'] = $instancia_id;
        $result['contexto'] = $contexto_id;
    }
    
    return $result;
}

/**
 * Carregue o contexto de sociedade interna
 * 
 * @param string $sociedade_id id da sociedade
 * @param string $instancia_id id da instância da sociedade
 * @param string $contexto_id id do contexto (permissões)
 * @return array
 */
function loadSociedadeContext($sociedade_id, $instancia_id, $contexto_id): array
{
    require_once ADO_PATH . '/Sociedades.class.php'; 
    require_once DAO_PATH . '/SociedadesDAO.class.php'; 
    
    // busque os dados da sociedade
    $dto = new SociedadesDTO();
    $obj = new SociedadesADO();

    $dto->id = $sociedade_id;
    $result = array(
        'id' => '',
        'nome' => '',
        'key' => '',
        'igreja' => '',
        'federacao' => '',
        'sinodal' => '',
        'instancia' => '',
        'contexto' => '',
        'idade' => array(
            'crianca' => false,
            'adolescente' => false,
            'jovem' => false,
            'todos' => false
        )
    );
    if(!is_null($obj->get($dto)))
    {
        $d = $obj->getDTODataObject();
        $result['id'] = $d->id;
        $result['key'] = sha1($sociedade_id . $instancia_id . $contexto_id);
        $result['nome'] = $d->nome;
        $result['igreja'] = $d->igreja;
        $result['federacao'] = $d->federacao;
        $result['sinodal'] = $d->sinodal;
        $result['instancia'] = $instancia_id;
        $result['contexto'] = $contexto_id;
        
        $result['idade']['crianca'] = ($d->reference == References::UCP);
        $result['idade']['adolescente'] = ($d->reference == References::UPA);
        $result['idade']['jovem'] = ($d->reference == References::UMP);
        $result['idade']['todos'] = ($d->reference == References::UPH || $d->reference == References::SAF);
    }
    
    return $result;
}

/**
 * Carregue o contexto da federação
 * 
 * @param string $federacao_id id da federação
 * @param string $instancia_id id da instância da federação
 * @param string $contexto_id id do contexto (permissões)
 * @return array
 */
function loadFederacaoContext($federacao_id, $instancia_id, $contexto_id): array
{
    require_once ADO_PATH . '/Federacoes.class.php'; 
    require_once DAO_PATH . '/FederacoesDAO.class.php'; 
    
    // busque os dados da federação
    $dto = new FederacoesDTO();
    $obj = new FederacoesADO();

    $dto->id = $federacao_id;
    $result = array(
        'id' => '',
        'key' => '',
        'nome' => '',
        'sigla' => '',
        'presbiterio' => '',
        'sinodo' => '',
        'sinodal' => '',
        'reference' => '',
        'instancia' => '',
        'contexto' => ''
    );
    if(!is_null($obj->get($dto)))
    {
        $d = $obj->getDTODataObject();
        
        $references = getReferenceList();
        $ref = $d->reference;
        $sociedade = array_filter($references, function($a) use ($ref) {
            return ($a['value'] == $ref);
        });
        $sociedade = array_pop($sociedade);
        
        $result['id'] = $d->id;
        $result['key'] = sha1($federacao_id . $instancia_id . $contexto_id);
        $result['nome'] = $d->nome;
        $result['sigla'] = $d->sigla;
        $result['presbiterio'] = $d->presbiterio;
        $result['sinodo'] = $d->sinodo;
        $result['sinodal'] = $d->sinodal;
        $result['reference'] = $d->reference;
        $result['reference_nome'] = $sociedade['label'];
        $result['instancia'] = $instancia_id;
        $result['contexto'] = $contexto_id;
    }
    
    return $result;
}

/**
 * Carregue o contexto da sinodal
 * 
 * @param string $sinodal_id id da sinodal
 * @param string $instancia_id id da instância da sinodal
 * @param string $contexto_id id do contexto (permissões)
 * @return array
 */
function loadSinodalContext($sinodal_id, $instancia_id, $contexto_id): array
{
    require_once ADO_PATH . '/Sinodais.class.php'; 
    require_once DAO_PATH . '/SinodaisDAO.class.php'; 
    
    // busque os dados da sinodal
    $dto = new SinodaisDTO();
    $obj = new SinodaisADO();

    $dto->id = $sinodal_id;
    $result = array(
        'id' => '',
        'key' => '',
        'nome' => '',
        'sigla' => '',
        'sinodo' => '',
        'nacional' => '',
        'reference' => '',
        'instancia' => '',
        'contexto' => ''
    );
    if(!is_null($obj->get($dto)))
    {
        $d = $obj->getDTODataObject();
        
        $references = getReferenceList();
        $ref = $d->reference;
        $sociedade = array_filter($references, function($a) use ($ref) {
            return ($a['value'] == $ref);
        });
        $sociedade = array_pop($sociedade);
        
        $result['id'] = $d->id;
        $result['key'] = sha1($sinodal_id . $instancia_id . $contexto_id);
        $result['nome'] = $d->nome;
        $result['sigla'] = $d->sigla;
        $result['sinodo'] = $d->sinodo;
        $result['nacional'] = $d->nacional;
        $result['reference'] = $d->reference;
        $result['reference_nome'] = $sociedade['label'];
        $result['instancia'] = $instancia_id;
        $result['contexto'] = $contexto_id;
    }
    
    return $result;
}

/**
 * Carregue o contexto da secretaria
 * 
 * @param string $secretaria_id id da secretaria
 * @param string $instancia_id id da instância da secretaria
 * @param string $contexto_id id do contexto (permissões)
 * @return array
 */
function loadSecretariaContext($secretaria_id, $instancia_id, $contexto_id): array
{
    require_once ADO_PATH . '/Secretarias.class.php'; 
    require_once DAO_PATH . '/SecretariasDAO.class.php'; 
    require_once WS_PATH . '/sociedades.ws.php'; 
    require_once WS_PATH . '/igrejas.ws.php'; 
    require_once WS_PATH . '/ministerios.ws.php'; 
    require_once WS_PATH . '/sinodais.ws.php'; 
    require_once WS_PATH . '/federacoes.ws.php'; 
    
    // busque os dados da sociedade
    $dto = new SecretariasDTO();
    $obj = new SecretariasADO();

    $dto->id = $secretaria_id;
    $result = array(
        'id' => '',
        'key' => '',
        'nome' => '',
        'ref' => '',
        'ref_tp' => '',
        'reference' => '',
        'instancia' => '',
        'contexto' => ''
    );
    if(!is_null($obj->get($dto)))
    {
        $d = $obj->getDTODataObject();
        if($d->stat == Status::ACTIVE) 
        {
            $result['id'] = $d->id;
            $result['key'] = sha1($secretaria_id . $instancia_id . $contexto_id);
            $result['nome'] = $d->nome;
            $result['ref'] = $d->ref;
            $result['ref_tp'] = $d->ref_tp;
            $result['instancia'] = $instancia_id;
            $result['contexto'] = $contexto_id;
            
            /* obtenha o nome da referência que controla a secretaria */
            if($d->ref_tp == References::IGREJA) {
                $ref_obj = IgrejasWS::getById($d->ref);
                if(!is_null($ref_obj)) {
                    $result['reference'] = $ref_obj->nome;
                }
            }
            else if($d->ref_tp == References::SOCIEDADE) {
                $ref_obj = SociedadesWS::getById($d->ref);
                if(!is_null($ref_obj)) {
                    $result['reference'] = $ref_obj->nome;
                }
            }
            else if($d->ref_tp == References::MINISTERIO) {
                $ref_obj = MinisteriosWS::getById($d->ref);
                if(!is_null($ref_obj)) {
                    $result['reference'] = $ref_obj->nome;
                }
            }
            else if($d->ref_tp == References::SINODAL) {
                $ref_obj = SinodaisWS::getById($d->ref);
                if(!is_null($ref_obj)) {
                    $result['reference'] = $ref_obj->nome;
                }
            }
            else if($d->ref_tp == References::FEDERACAO) {
                $ref_obj = FederacoesWS::getById($d->ref);
                if(!is_null($ref_obj)) {
                    $result['reference'] = $ref_obj->nome;
                }
            }
        }
    }
    
    return $result;
}

/**
 * Carregue o contexto do ministério
 * 
 * @param string $ministerio_id id do ministério
 * @param string $instancia_id id da instância da secretaria
 * @param string $contexto_id id do contexto (permissões)
 * @return array
 */
function loadMinisterioContext($ministerio_id, $instancia_id, $contexto_id): array
{
    require_once ADO_PATH . '/Ministerios.class.php'; 
    require_once DAO_PATH . '/MinisteriosDAO.class.php'; 
    require_once WS_PATH . '/sociedades.ws.php'; 
    require_once WS_PATH . '/igrejas.ws.php'; 
    
    // busque os dados do ministério
    $dto = new MinisteriosDTO();
    $obj = new MinisteriosADO();

    $dto->id = $ministerio_id;
    $result = array(
        'id' => '',
        'key' => '',
        'nome' => '',
        'igreja' => '',
        'reference' => '',
        'instancia' => '',
        'contexto' => ''
    );
    if(!is_null($obj->get($dto)))
    {
        $d = $obj->getDTODataObject();
        if($d->stat == Status::ACTIVE) 
        {
            $result['id'] = $d->id;
            $result['key'] = sha1($ministerio_id . $instancia_id . $contexto_id);
            $result['nome'] = $d->nome;
            $result['igreja'] = $d->igreja;
            $result['instancia'] = $instancia_id;
            $result['contexto'] = $contexto_id;
            
            /* obtenha o nome da igreja que controla o ministério */
            $ref_obj = IgrejasWS::getById($d->igreja);
            if(!is_null($ref_obj)) {
                $result['reference'] = $ref_obj->nome;
            }
        }
    }
    
    return $result;
}

/**
 * Verifica se um contexto já foi carregado
 * 
 * @param array $context_arr array com os contextos
 * @param string $ref_id id da referência
 * @param string $instancia_id id da instância da secretaria
 * @param string $contexto_id id do contexto (permissões)
 * @return bool
 */
function contextAlreadyLoaded($context_arr, $ref_id, $instancia_id, $contexto_id): bool 
{
    $key = sha1($ref_id . $instancia_id . $contexto_id);
    foreach($context_arr as $ctx) {
        if($ctx['key'] == $key) {
            return true;
        }
    }
    
    return false;
}

/**
 * Carrega os contextos a partir do array de referência de contextos
 * 
 * @param type $ref_contexts
 * @return array
 */
function loadContextsFromArray($ref_contexts): array
{
    require_once FCT_PATH . '/instancias.cfc.php'; 
    
    $contextos = array();
    foreach($ref_contexts as $ref)
    {
        if($ref['ref_tp'] == References::IGREJA) 
        {
            if(!isset($contextos[Contexts::IGREJAS])) {
                $contextos[Contexts::IGREJAS] = array();
            }
            
            if(!contextAlreadyLoaded($contextos[Contexts::IGREJAS], $ref['ref'], $ref['instancia'], $ref['contexto'])) {
                $contextos[Contexts::IGREJAS][] = loadIgrejaContext($ref['ref'], $ref['instancia'], $ref['contexto']);
            }
        }
        else if($ref['ref_tp'] == References::PASTOR) 
        {
            if(!isset($contextos[Contexts::PASTORES])) {
                $contextos[Contexts::PASTORES] = array();
            }
            
            if(!contextAlreadyLoaded($contextos[Contexts::PASTORES], $ref['ref'], $ref['instancia'], $ref['contexto'])) {
                $contextos[Contexts::PASTORES][] = loadIgrejaContext($ref['ref'], $ref['instancia'], $ref['contexto']);
            }
        }
        else if($ref['ref_tp'] == References::EVANGELISTA) 
        {
            if(!isset($contextos[Contexts::EVANGELISTAS])) {
                $contextos[Contexts::EVANGELISTAS] = array();
            }
            
            if(!contextAlreadyLoaded($contextos[Contexts::EVANGELISTAS], $ref['ref'], $ref['instancia'], $ref['contexto'])) {
                $contextos[Contexts::EVANGELISTAS][] = loadIgrejaContext($ref['ref'], $ref['instancia'], $ref['contexto']);
            }
        }
        else if($ref['ref_tp'] == References::CONSELHO) 
        {
            if(!isset($contextos[Contexts::CONSELHOS])) {
                $contextos[Contexts::CONSELHOS] = array();
            }
            
            if(!contextAlreadyLoaded($contextos[Contexts::CONSELHOS], $ref['ref'], $ref['instancia'], $ref['contexto'])) {
                $contextos[Contexts::CONSELHOS][] = loadIgrejaContext($ref['ref'], $ref['instancia'], $ref['contexto']);
            }
        }
        else if($ref['ref_tp'] == References::JUNTA) 
        {
            if(!isset($contextos[Contexts::JUNTAS])) {
                $contextos[Contexts::JUNTAS] = array();
            }
            
            if(!contextAlreadyLoaded($contextos[Contexts::JUNTAS], $ref['ref'], $ref['instancia'], $ref['contexto'])) {
                $contextos[Contexts::JUNTAS][] = loadIgrejaContext($ref['ref'], $ref['instancia'], $ref['contexto']);
            }
        }
        else if($ref['ref_tp'] == References::SOCIEDADE)
        {
            if(!isset($contextos[Contexts::SOCIEDADES])) {
                $contextos[Contexts::SOCIEDADES] = array();
            }
            
            if(!contextAlreadyLoaded($contextos[Contexts::SOCIEDADES], $ref['ref'], $ref['instancia'], $ref['contexto'])) {
                $contextos[Contexts::SOCIEDADES][] = loadSociedadeContext($ref['ref'], $ref['instancia'], $ref['contexto']);
            }
        }
        else if($ref['ref_tp'] == References::FEDERACAO)
        {
            if(!isset($contextos[Contexts::FEDERACOES])) {
                $contextos[Contexts::FEDERACOES] = array();
            }
            
            if(!contextAlreadyLoaded($contextos[Contexts::FEDERACOES], $ref['ref'], $ref['instancia'], $ref['contexto'])) {
                $contextos[Contexts::FEDERACOES][] = loadFederacaoContext($ref['ref'], $ref['instancia'], $ref['contexto']);
            }
        }
        else if($ref['ref_tp'] == References::SINODAL)
        {
            if(!isset($contextos[Contexts::SINODAIS])) {
                $contextos[Contexts::SINODAIS] = array();
            }
            
            if(!contextAlreadyLoaded($contextos[Contexts::SINODAIS], $ref['ref'], $ref['instancia'], $ref['contexto'])) {
                $contextos[Contexts::SINODAIS][] = loadSinodalContext($ref['ref'], $ref['instancia'], $ref['contexto']);
            }
        }
        else if($ref['ref_tp'] == References::SECRETARIA)
        {
            if(!isset($contextos[Contexts::SECRETARIAS])) {
                $contextos[Contexts::SECRETARIAS] = array();
            }
            
            if(!contextAlreadyLoaded($contextos[Contexts::SECRETARIAS], $ref['ref'], $ref['instancia'], $ref['contexto'])) {
                $contextos[Contexts::SECRETARIAS][] = loadSecretariaContext($ref['ref'], $ref['instancia'], $ref['contexto']);
            }
        }
        else if($ref['ref_tp'] == References::MINISTERIO)
        {
            if(!isset($contextos[Contexts::MINISTERIOS])) {
                $contextos[Contexts::MINISTERIOS] = array();
            }
            
            if(!contextAlreadyLoaded($contextos[Contexts::MINISTERIOS], $ref['ref'], $ref['instancia'], $ref['contexto'])) {
                $contextos[Contexts::MINISTERIOS][] = loadMinisterioContext($ref['ref'], $ref['instancia'], $ref['contexto']);
            }
        }
        else if($ref['ref_tp'] == References::EBD)
        {
            if(!isset($contextos[Contexts::EBDS])) {
                $contextos[Contexts::EBDS] = array();
            }
            
            if(!contextAlreadyLoaded($contextos[Contexts::EBDS], $ref['ref'], $ref['instancia'], $ref['contexto'])) {
                // escola dominical é um subcontexto do contexto de igreja
                $contextos[Contexts::EBDS][] = loadIgrejaContext($ref['ref'], $ref['instancia'], $ref['contexto']);
            }
        }
    }
    
    return $contextos;
}

/**
 * Busque os contextos para o usuário
 * 
 * @param string $user_id id do usuário
 * @return array
 */
function getContextsForUser($user_id): array
{
    $refs = getContextsReferencesForUser($user_id);
    if(empty($refs)) {
        return array();
    }
    
    return loadContextsFromArray($refs);
}

/**
 * Busca os módulos da instância
 * 
 * @param string $instancia_id id da instância
 * @return string
 */
function getModulosFromInstancia($instancia_id): string
{
    require_once ADO_PATH . '/Instancias.class.php'; 
    require_once DAO_PATH . '/InstanciasDAO.class.php';
    
    $dto = new InstanciasDTO();
    $obj = new InstanciasADO();

    $dto->id = $instancia_id;
    if(!is_null($obj->get($dto)))
    {
        $d = $obj->getDTODataObject();
        if(!is_null($d->pai) && !empty($d->pai)) {
            // instância herda módulos de outra. Procure os módulos da instância pai
            return getModulosFromInstancia($d->pai);
        }
        else {
            return $d->modulos;
        }
    }
    
    return '';
}

/**
 * Obtenha as permissões do contexto, caso existam
 * 
 * @param string $contexto_id id do contexto
 * @return array
 */
function getPermissoesFromContexto($contexto_id): array
{
    require_once ADO_PATH . '/Contextos.class.php'; 
    require_once DAO_PATH . '/ContextosDAO.class.php'; 
    require_once ADO_PATH . '/Perfis.class.php'; 
    require_once DAO_PATH . '/PerfisDAO.class.php'; 
    
    $dto = new ContextosDTO();
    $obj = new ContextosADO();
    
    $obj_prm = new PerfisADO();

    $dto->id = $contexto_id;
    if(!is_null($obj->get($dto)))
    {
        $d = $obj->getDTODataObject();
        if(!is_null($d->perfil) && !empty($d->perfil)) {
            // contexto tem permissão. Obtenha
            return $obj_prm->getPermissoes($d->perfil);
        }
    }
    
    return array();
}

/**
 * Remova as módulos duplicados da string de módulos
 * 
 * @param string $modulos string com os módulos
 * @return string
 */
function removeDuplicateModulos($modulos): string
{
    $mods_v = explode('|', $modulos);
    $mods_arr = [];
    
    foreach($mods_v as $m) {
        if(!empty($m) && !in_array($m, $mods_arr)) {
            $mods_arr[] = str_replace('|', '', $m);
        }
    }
    
    $new_modulos = '|';
    foreach($mods_arr as $m) {
        $new_modulos .= $m . '|';
    }
    
    return $new_modulos;
}

/**
 * Obtêm os módulos de contexto da membresia
 * 
 * @param array $membresia dados de membresia
 * @param string $mods módulos que o membro já possui
 * @return string
 */
function getMembresiaModulos($membresia, $mods): string
{
    require_once WS_PATH . '/instancias.ws.php';
    
    $modulos = $mods;
    
    if(!empty($membresia['igreja'])) {
        $modulos .= InstanciasWS::getModulosbyRef($membresia['igreja'], References::IGREJA);
    }
    
    if(!empty($membresia['presbiterio'])) {
        $modulos .= InstanciasWS::getModulosbyRef($membresia['presbiterio'], References::PRESBITERIO);
    }
    
    if(!empty($membresia['sinodo'])) {
        $modulos .= InstanciasWS::getModulosbyRef($membresia['sinodo'], References::SINODO);
    }
    
    return removeDuplicateModulos($modulos);
}