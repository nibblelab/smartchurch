<?php
require_once ADO_PATH . '/Membros.class.php'; 
require_once DAO_PATH . '/MembrosDAO.class.php'; 
require_once ADO_PATH . '/Socios.class.php'; 
require_once DAO_PATH . '/SociosDAO.class.php'; 
require_once FCT_PATH . '/data.cfc.php'; 
require_once WS_PATH . '/pessoas.ws.php'; 
require_once WS_PATH . '/necessidadesespeciais.ws.php'; 
require_once WS_PATH . '/doacoes.ws.php'; 
require_once WS_PATH . '/sociedades.ws.php'; 
require_once WS_PATH . '/federacoes.ws.php'; 
require_once WS_PATH . '/sinodais.ws.php'; 
require_once WS_PATH . '/presbiterios.ws.php'; 

/**
 * API REST de Home
 */
class HomeWS extends WSUtil
{
    
    /**************************************************************************
     *                  RELATÓRIOS DA IGREJA 
     **************************************************************************/
    
    /**
     * 
     * Relatório: Membros x Idade
     * 
     * @httpmethod GET
     * @auth yes
     * @require igreja
     * @return array
     */
    public function relMembroPorIdade(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'RelMembroAno')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new MembrosADO();
        
        $count = $obj->countByMesAndAno(NblFram::$context->data->igreja);
        return array('status' => 'ok', 'success' => true, 'datas' => $count);
    }
    
    /**
     * 
     * Relatório: Membros x Grupo de Idade
     * 
     * @httpmethod GET
     * @auth yes
     * @require igreja
     * @return array
     */
    public function relMembroPorGrupoIdade(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'RelMembroGrpIdade')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new MembrosADO();
        
        $count = $obj->countByAno(NblFram::$context->data->igreja);
        $years_range_criancas = getDateRangeFromYearRange(CRIANCA_MIN_ANO, CRIANCA_MAX_ANO);
        $years_range_adolescentes = getDateRangeFromYearRange(ADOLESCENTE_MIN_ANO, ADOLESCENTE_MAX_ANO);
        $years_range_jovens = getDateRangeFromYearRange(JOVEM_MIN_ANO, JOVEM_MAX_ANO);
        $years_range_adultos = getDateRangeFromYearRange(ADULTO_MIN_ANO, ADULTO_MAX_ANO);
        $years_range_idosos = getDateRangeFromYearRange(IDOSO_MIN_ANO, IDOSO_MAX_ANO);
        
        $result = array(
            'criancas' => 0,
            'adolescentes' => 0,
            'jovens' => 0,
            'adultos' => 0,
            'idosos' => 0
        );
        foreach($count as $c) {
            $c_ano = $c['ano'];
            if($c_ano >= ((int) $years_range_criancas->ini->format('Y')) && $c_ano <= ((int) $years_range_criancas->end->format('Y')))
            {
                $result['criancas'] += $c['total'];
            }
            else if($c_ano >= ((int) $years_range_adolescentes->ini->format('Y')) && $c_ano <= ((int) $years_range_adolescentes->end->format('Y')))
            {
                $result['adolescentes'] += $c['total'];
            }
            else if($c_ano >= ((int) $years_range_jovens->ini->format('Y')) && $c_ano <= ((int) $years_range_jovens->end->format('Y')))
            {
                $result['jovens'] += $c['total'];
            }
            else if($c_ano >= ((int) $years_range_adultos->ini->format('Y')) && $c_ano <= ((int) $years_range_adultos->end->format('Y')))
            {
                $result['adultos'] += $c['total'];
            }
            else if($c_ano <= ((int) $years_range_idosos->end->format('Y')))
            {
                $result['idosos'] += $c['total'];
            }
        }
        
        return array('status' => 'ok', 'success' => true, 'datas' => $result);
    }
    
    /**
     * 
     * Relatório: Membros x Sexo
     * 
     * @httpmethod GET
     * @auth yes
     * @require igreja
     * @return array
     */
    public function relMembroPorSexo(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'RelMembroSexo')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new MembrosADO();
        
        $count = $obj->countBySexo(NblFram::$context->data->igreja);
        $sexos = getSexoListWithVoid();
        foreach($sexos as $s) {
            if(!isset($count[$s['value']])) {
                $count[$s['value']] = array('total' => 0, 'sexo' => '');
            }
            
            $count[$s['value']]['sexo'] = $s['label'];
        }
        return array('status' => 'ok', 'success' => true, 'datas' => $count);
    }
    
    /**
     * 
     * Relatório: Membros x Estado Civil
     * 
     * @httpmethod GET
     * @auth yes
     * @require igreja
     * @return array
     */
    public function relMembroPorEstadoCivil(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'RelMembroEstadoCivil')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new MembrosADO();
        
        $count = $obj->countByEstadoCivil(NblFram::$context->data->igreja);
        $estados = getEstadoCivilListWithVoid();
        foreach($estados as $e) {
            if(!isset($count[$e['value']])) {
                $count[$e['value']] = array('total' => 0, 'estado_civil' => '');
            }
            
            $count[$e['value']]['estado_civil'] = $e['label'];
        }
        return array('status' => 'ok', 'success' => true, 'datas' => $count);
    }
    
    /**
     * 
     * Relatório: Membros x Escolaridade
     * 
     * @httpmethod GET
     * @auth yes
     * @require igreja
     * @return array
     */
    public function relMembroPorEscolaridade(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'RelMembroEscolaridade')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new MembrosADO();
        
        $count = $obj->countByEscolaridade(NblFram::$context->data->igreja);
        $niveis = getEscolaridadeListWithVoid();
        foreach($niveis as $n) {
            if(!isset($count[$n['value']])) {
                $count[$n['value']] = array('total' => 0, 'escolaridadel' => '');
            }
            
            $count[$n['value']]['escolaridade'] = $n['label'];
        }
        // ordene os dados conforme a escolaridade
        $escolaridade_ordenada = getEscolaridadeOrderedList();
        uksort($count, function($a, $b) use ($escolaridade_ordenada) {
            $a_index = array_search($a, $escolaridade_ordenada);
            $b_index = array_search($b, $escolaridade_ordenada);
            if($a_index == $b_index) {
                return 0;
            }
            
            return ($a_index < $b_index) ? -1 : 1;
        });
        
        return array('status' => 'ok', 'success' => true, 'datas' => $count);
    }
    
    /**
     * 
     * Relatório: Membros x Profissão de Fé
     * 
     * @httpmethod GET
     * @auth yes
     * @require igreja
     * @return array
     */
    public function relMembroPorProfissaoFe(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'RelMembroComungante')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new MembrosADO();
        
        $count = $obj->countByProfissaoFe(NblFram::$context->data->igreja);
        $profissao_fe = getGenericHaveList();
        foreach($profissao_fe as $p) {
            if(!isset($count[$p['value']])) {
                $count[$p['value']] = array('total' => 0, 'comungante' => '');
            }
            
            $count[$p['value']]['comungante'] = $p['label'];
        }
        return array('status' => 'ok', 'success' => true, 'datas' => $count);
    }
    
    /**
     * 
     * Relatório: Membros x Bairro
     * 
     * @httpmethod GET
     * @auth yes
     * @require igreja
     * @return array
     */
    public function relMembroPorBairro(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'RelMembroBairro')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new MembrosDTO();
        $obj = new MembrosADO();
        
        MembrosADO::addComparison($dto, 'igreja', CMP_EQUAL, NblFram::$context->data->igreja);
        
        $p_ids = [];
        if($obj->getAllbyParam($dto)) {
            $obj->iterate();
            while($obj->hasNext()) {
                $it = $obj->next();
                if(!is_null($it->id)) {
                    $p_ids[] = $it->pessoa;
                }
            }
        }
        
        $pessoas = PessoasWS::getAllByIds($p_ids);
        $result = [];
        foreach($pessoas as $p) {
            if(!isset($result[$p['bairro']])) {
                $result[$p['bairro']] = 0;
            }
            
            $result[$p['bairro']]++;
        }
        
        return array('status' => 'ok', 'success' => true, 'datas' => $result);
    }
    
    /**
     * 
     * Relatório: Arrolados x Visitantes
     * 
     * @httpmethod GET
     * @auth yes
     * @require igreja
     * @return array
     */
    public function relMembrosEVisitantes(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'RelMembroVisitante')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new MembrosADO();
        
        $count = $obj->countByArrolamento(NblFram::$context->data->igreja);
        $arrolamento = getGenericHaveList();
        foreach($arrolamento as $a) {
            if(!isset($count[$a['value']])) {
                $count[$a['value']] = array('total' => 0, 'arrolado' => '');
            }
            
            $count[$a['value']]['arrolado'] = $a['label'];
        }
        return array('status' => 'ok', 'success' => true, 'datas' => $count);
    }
    
    /**
     * 
     * Relatório: Membros x Redes Sociais
     * 
     * @httpmethod GET
     * @auth yes
     * @require igreja
     * @return array
     */
    public function relMembrosPorRedeSocial(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'RelMembroSocialNet')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new MembrosDTO();
        $obj = new MembrosADO();
        
        MembrosADO::addComparison($dto, 'igreja', CMP_EQUAL, NblFram::$context->data->igreja);
        
        $p_ids = [];
        if($obj->getAllbyParam($dto)) {
            $obj->iterate();
            while($obj->hasNext()) {
                $it = $obj->next();
                if(!is_null($it->id)) {
                    $p_ids[] = $it->pessoa;
                }
            }
        }
        
        $pessoas = PessoasWS::getAllByIds($p_ids);
        $result = array(
            'Facebook' => 0,
            'Instagram' => 0,
            'YouTube' => 0,
            'Vimeo' => 0,
            'no' => 0
        );
        foreach($pessoas as $p) {
            $has_one = false;
            if(!empty($p['facebook'])) {
                $has_one = true;
                $result['Facebook']++;
            }
            if(!empty($p['instagram'])) {
                $has_one = true;
                $result['Instagram']++;
            }
            if(!empty($p['youtube'])) {
                $has_one = true;
                $result['YouTube']++;
            }
            if(!empty($p['vimeo'])) {
                $has_one = true;
                $result['Vimeo']++;
            }
            if(!$has_one) {
                $result['no']++;
            }
        }
        
        return array('status' => 'ok', 'success' => true, 'datas' => $result);
    }
    
    /**
     * 
     * Relatório: Membros x Necessidades Especiais
     * 
     * @httpmethod GET
     * @auth yes
     * @require igreja
     * @return array
     */
    public function relMembrosPorNecessidadesEspeciais(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'RelMembroNecessidade')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new MembrosADO();
        
        $count = $obj->countByNecessidade(NblFram::$context->data->igreja);
        $necessidades = NecessidadesEspeciaisWS::getAll();
        foreach($necessidades as $n) {
            if(!isset($count[$n['id']])) {
                $count[$n['id']] = array('total' => 0, 'necessidade' => '');
            }
            
            $count[$n['id']]['necessidade'] = $n['nome'];
        }
        return array('status' => 'ok', 'success' => true, 'datas' => $count);
    }
    
    /**
     * 
     * Relatório: Membros x Doações
     * 
     * 
     * @httpmethod GET
     * @auth yes
     * @require igreja
     * @return array
     */
    public function relMembrosPorDoacao(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'RelMembroDoacao')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new MembrosADO();
        
        $count = $obj->countByDoacao(NblFram::$context->data->igreja);
        $doacoes = DoacoesWS::getAll();
        foreach($doacoes as $d) {
            if(!isset($count[$d['id']])) {
                $count[$d['id']] = array('total' => 0, 'doacao' => '');
            }
            
            $count[$d['id']]['doacao'] = $d['nome'];
        }
        return array('status' => 'ok', 'success' => true, 'datas' => $count);
    }
    
    /**
     * 
     * Relatório: Membros x Especial
     * 
     * 
     * @httpmethod GET
     * @auth yes
     * @require igreja
     * @return array
     */
    public function relMembrosPorEspecial(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'RelMembroEspecial')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new MembrosADO();
        
        $count = $obj->countByEspecial(NblFram::$context->data->igreja);
        $arrolamento = getGenericHaveList();
        foreach($arrolamento as $a) {
            if(!isset($count[$a['value']])) {
                $count[$a['value']] = array('total' => 0, 'especial' => '');
            }
            
            $count[$a['value']]['especial'] = $a['label'];
        }
        return array('status' => 'ok', 'success' => true, 'datas' => $count);
    }
    
    /**
     * 
     * Relatório: Membros x Ano Admissão
     * 
     * @httpmethod GET
     * @auth yes
     * @require igreja
     * @return array
     */
    public function relMembrosPorAnoDeAdmissao(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'RelMembroAdmissao')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new MembrosADO();
        
        $count = $obj->countByAnoAdmissao(NblFram::$context->data->igreja);
        return array('status' => 'ok', 'success' => true, 'datas' => $count);
    }
    
    /**
     * 
     * Relatório: Membros x Ano Demissão
     * 
     * @httpmethod GET
     * @auth yes
     * @require igreja
     * @return array
     */
    public function relMembrosPorAnoDeDemissao(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'RelMembroDemissao')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new MembrosADO();
        
        $count = $obj->countByAnoDemissao(NblFram::$context->data->igreja);
        return array('status' => 'ok', 'success' => true, 'datas' => $count);
    }
    
    /**
     * 
     * Relatório: Membros x Preenchimento do Perfil
     * 
     * @httpmethod GET
     * @auth yes
     * @require igreja
     * @return array
     */
    public function relMembrosPorPreenchimentoPerfil(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'RelMembroPreenchimento')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new MembrosDTO();
        $obj = new MembrosADO();
        
        MembrosADO::addComparison($dto, 'igreja', CMP_EQUAL, NblFram::$context->data->igreja);
        
        $p_ids = [];
        if($obj->getAllbyParam($dto)) {
            $obj->iterate();
            while($obj->hasNext()) {
                $it = $obj->next();
                if(!is_null($it->id)) {
                    $p_ids[] = $it->pessoa;
                }
            }
        }
        
        $pessoas = PessoasWS::getAllByIds($p_ids);
        $result = array(
            'basico' => 0,
            'minimo' => 0,
            'medio' => 0,
            'total' => 0
        );
        foreach($pessoas as $p) {
            /**
             * máscara de preenchimento de perfil:
             * básico | mínimo | médio | avançado
             */
            $preenchimento_mask = 0;
            if(!empty($p['nome']) && !empty($p['email'])) {
                $preenchimento_mask = $preenchimento_mask | 8;
            }
            if(!empty($p['sexo']) && $p['sexo'] != Sexo::VOID && !empty($p['data_nascimento'])) {
                $preenchimento_mask = $preenchimento_mask | 4;
            }
            if(!empty($p['endereco']) && !empty($p['bairro']) && !empty($p['uf']) && ($p['uf'] != Voids::UF)) {
                $preenchimento_mask = $preenchimento_mask | 2;
            }
            if(!empty($p['estado_civil']) && ($p['estado_civil'] != EstadoCivil::VOID) && 
                    !empty($p['escolaridade']) && ($p['escolaridade'] != Escolaridade::VOID) &&
                    !empty($p['celular_1'])) {
                $preenchimento_mask = $preenchimento_mask | 1;
            }
            
            if($preenchimento_mask == 15) {
                $result['total']++;
            }
            else if($preenchimento_mask & 2) {
                $result['medio']++;
            }
            else if($preenchimento_mask & 4) {
                $result['minimo']++;
            }
            else {
                $result['basico']++;
            }
        }
        
        return array('status' => 'ok', 'success' => true, 'datas' => $result);
    }
    
    /**
     * 
     * Relatório: Membros Com Filhos x Membros Sem Filhos
     * 
     * @httpmethod GET
     * @auth yes
     * @require igreja
     * @return array
     */
    public function relMembrosPorTemFilho(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'RelMembroFilhos')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new MembrosADO();
        
        $count = $obj->countByTemFilhos(NblFram::$context->data->igreja);
        $tem_filhos = getGenericHaveList();
        foreach($tem_filhos as $t) {
            if(!isset($count[$t['value']])) {
                $count[$t['value']] = array('total' => 0, 'tem_filhos' => '');
            }
            
            $count[$t['value']]['tem_filhos'] = $t['label'];
        }
        return array('status' => 'ok', 'success' => true, 'datas' => $count);
    }
    
    /**************************************************************************
     *                 RELATÓRIOS DA SOCIEDADE INTERNA
     **************************************************************************/
    
    /**
     * 
     * Relatório: Sócios x Idade
     * 
     * @httpmethod GET
     * @auth yes
     * @require sociedade
     * @return array
     */
    public function relSocioPorIdade(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'RelSocioAno')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new SociosADO();
        
        $count = $obj->countByMesAndAno(NblFram::$context->data->sociedade);
        return array('status' => 'ok', 'success' => true, 'datas' => $count);
    }
    
    /**
     * 
     * Relatório: Sócios x Sexo
     * 
     * @httpmethod GET
     * @auth yes
     * @require sociedade
     * @return array
     */
    public function relSocioPorSexo(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'RelSocioSexo')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new SociosADO();
        
        $count = $obj->countBySexo(NblFram::$context->data->sociedade);
        $sexos = getSexoListWithVoid();
        foreach($sexos as $s) {
            if(!isset($count[$s['value']])) {
                $count[$s['value']] = array('total' => 0, 'sexo' => '');
            }
            
            $count[$s['value']]['sexo'] = $s['label'];
        }
        return array('status' => 'ok', 'success' => true, 'datas' => $count);
    }
    
    /**
     * 
     * Relatório: Sócios x Estado Civil
     * 
     * @httpmethod GET
     * @auth yes
     * @require sociedade
     * @return array
     */
    public function relSocioPorEstadoCivil(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'RelSocioEstadoCivil')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new SociosADO();
        
        $count = $obj->countByEstadoCivil(NblFram::$context->data->sociedade);
        $estados = getEstadoCivilListWithVoid();
        foreach($estados as $e) {
            if(!isset($count[$e['value']])) {
                $count[$e['value']] = array('total' => 0, 'estado_civil' => '');
            }
            
            $count[$e['value']]['estado_civil'] = $e['label'];
        }
        return array('status' => 'ok', 'success' => true, 'datas' => $count);
    }
    
    /**
     * 
     * Relatório: Sócios x Escolaridade
     * 
     * @httpmethod GET
     * @auth yes
     * @require sociedade
     * @return array
     */
    public function relSocioPorEscolaridade(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'RelSocioEscolaridade')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new SociosADO();
        
        $count = $obj->countByEscolaridade(NblFram::$context->data->sociedade);
        $niveis = getEscolaridadeListWithVoid();
        foreach($niveis as $n) {
            if(!isset($count[$n['value']])) {
                $count[$n['value']] = array('total' => 0, 'escolaridade' => '');
            }
            
            $count[$n['value']]['escolaridade'] = $n['label'];
        }
        // ordene os dados conforme a escolaridade
        $escolaridade_ordenada = getEscolaridadeOrderedList();
        uksort($count, function($a, $b) use ($escolaridade_ordenada) {
            $a_index = array_search($a, $escolaridade_ordenada);
            $b_index = array_search($b, $escolaridade_ordenada);
            if($a_index == $b_index) {
                return 0;
            }
            
            return ($a_index < $b_index) ? -1 : 1;
        });
        
        return array('status' => 'ok', 'success' => true, 'datas' => $count);
    }
    
    /**
     * 
     * Relatório: Sócios x Profissão de Fé
     * 
     * @httpmethod GET
     * @auth yes
     * @require sociedade
     * @return array
     */
    public function relSocioPorProfissaoFe(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'RelSocioComungante')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new SociosADO();
        
        $count = $obj->countByProfissaoFe(NblFram::$context->data->sociedade);
        $profissao_fe = getGenericHaveList();
        foreach($profissao_fe as $p) {
            if(!isset($count[$p['value']])) {
                $count[$p['value']] = array('total' => 0, 'comungante' => '');
            }
            
            $count[$p['value']]['comungante'] = $p['label'];
        }
        return array('status' => 'ok', 'success' => true, 'datas' => $count);
    }
    
    /**
     * 
     * Relatório: Sócios x Tem Filhos
     * 
     * @httpmethod GET
     * @auth yes
     * @require sociedade
     * @return array
     */
    public function relSocioPorTemFilho(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'RelSocioFilho')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new SociosADO();
        
        $count = $obj->countByTemFilhos(NblFram::$context->data->sociedade);
        $tem_filhos = getGenericHaveList();
        foreach($tem_filhos as $t) {
            if(!isset($count[$t['value']])) {
                $count[$t['value']] = array('total' => 0, 'tem_filhos' => '');
            }
            
            $count[$t['value']]['tem_filhos'] = $t['label'];
        }
        return array('status' => 'ok', 'success' => true, 'datas' => $count);
    }
    
    /**
     * 
     * Relatório: Sócios x Tem Filhos x Sexo
     * 
     * @httpmethod GET
     * @auth yes
     * @require sociedade
     * @return array
     */
    public function relSocioPorTemFilhoPorSexo(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'RelSocioFilhoSexo')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new SociosADO();
        
        $count = $obj->countByTemFilhosAndSexo(NblFram::$context->data->sociedade);
        $keys = array('S_F', 'N_F', 'S_M', 'N_M');
        foreach($keys as $k) {
            if(!isset($count[$k])) {
                $count[$k] = array('total' => 0, 'tem_filhos' => '', 'sexo' => '');
            }
        }
        return array('status' => 'ok', 'success' => true, 'datas' => $count);
    }
    
    /**
     * 
     * Relatório: Sócios x Necessidade Especial
     * 
     * @httpmethod GET
     * @auth yes
     * @require sociedade
     * @return array
     */
    public function relSocioPorNecessidade(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'RelSocioNecessidade')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new SociosADO();
        
        $count = $obj->countByNecessidade(NblFram::$context->data->sociedade);
        $necessidades = NecessidadesEspeciaisWS::getAll();
        foreach($necessidades as $n) {
            if(!isset($count[$n['id']])) {
                $count[$n['id']] = array('total' => 0, 'necessidade' => '');
            }
            
            $count[$n['id']]['necessidade'] = $n['nome'];
        }
        return array('status' => 'ok', 'success' => true, 'datas' => $count);
    }
    
    /**
     * 
     * Relatório: Sócios x Doações
     * 
     * 
     * @httpmethod GET
     * @auth yes
     * @require sociedade
     * @return array
     */
    public function relSocioPorDoacao(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'RelSocioDoacao')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new SociosADO();
        
        $count = $obj->countByDoacao(NblFram::$context->data->sociedade);
        $doacoes = DoacoesWS::getAll();
        foreach($doacoes as $d) {
            if(!isset($count[$d['id']])) {
                $count[$d['id']] = array('total' => 0, 'doacao' => '');
            }
            
            $count[$d['id']]['doacao'] = $d['nome'];
        }
        return array('status' => 'ok', 'success' => true, 'datas' => $count);
    }
    
    /**
     * 
     * Relatório: Sócios x Arrolamento
     * 
     * 
     * @httpmethod GET
     * @auth yes
     * @require sociedade
     * @return array
     */
    public function relSocioPorArrolamento(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'RelSocioArrolamento')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new SociosADO();
        
        $count = $obj->countByArrolamento(NblFram::$context->data->sociedade);
        $arroalmentos = getGenericHaveList();
        foreach($arroalmentos as $a) {
            if(!isset($count[$a['value']])) {
                $count[$a['value']] = array('total' => 0, 'cooperador' => '');
            }
            
            $count[$a['value']]['cooperador'] = $a['label'];
        }
        return array('status' => 'ok', 'success' => true, 'datas' => $count);
    }
    
    /**************************************************************************
     *                  RELATÓRIOS DA FEDERAÇÃO
     **************************************************************************/
    
    /**
     * 
     * Relatório [Federação]: Sociedades Ativas x Inativas
     * 
     * @httpmethod GET
     * @auth yes
     * @require federacao
     * @return array
     */
    public function relSociedadesAtivasForFederacao(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'RelFedSociedades')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $todas = count(FederacoesWS::getIgrejasIds(NblFram::$context->data->federacao));
        $ativas = count(SociedadesWS::getIgrejaDasSociedadesAtivasByFederacao(NblFram::$context->data->federacao));
        $nao_ativas = $todas - $ativas;
        
        $result = array(
            'todas' => $todas,
            'ativas' => $ativas,
            'nao_ativas' => $nao_ativas
        );
        
        return array('status' => 'ok', 'success' => true, 'datas' => $result);
    }
    
    /**
     * 
     * Relatório [Federação]: Sócios x Idade
     * 
     * @httpmethod GET
     * @auth yes
     * @require federacao
     * @return array
     */
    public function relSocioPorIdadeForFederacao(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'RelFedSocioAno')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new SociosADO();
        
        $count = $obj->countByMesAndAnoForSociedades($this->stringifyArray(SociedadesWS::getIdsByFederacao(NblFram::$context->data->federacao)));
        return array('status' => 'ok', 'success' => true, 'datas' => $count);
    }
    
    /**
     * 
     * Relatório [Federação]: Sócios x Sexo
     * 
     * @httpmethod GET
     * @auth yes
     * @require federacao
     * @return array
     */
    public function relSocioPorSexoForFederacao(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'RelFedSocioSexo')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new SociosADO();
        
        $count = $obj->countBySexoForSociedades($this->stringifyArray(SociedadesWS::getIdsByFederacao(NblFram::$context->data->federacao)));
        $sexos = getSexoListWithVoid();
        $result = [];
        foreach($sexos as $s) {
            $result[$s['value']] = array('total' => 0, 'sexo' => $s['label']);
        }
        
        foreach($count as $sexo => $c) {
            foreach($c as $sociedade) {
                $result[$sexo]['total'] += $sociedade['total'];
            }
        }
        return array('status' => 'ok', 'success' => true, 'datas' => $result);
    }
    
    /**
     * 
     * Relatório [Federação]: Sócios x Estado Civil
     * 
     * @httpmethod GET
     * @auth yes
     * @require federacao
     * @return array
     */
    public function relSocioPorEstadoCivilForFederacao(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'RelFedSocioEstadoCivil')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new SociosADO();
        
        $count = $obj->countByEstadoCivilForSociedades($this->stringifyArray(SociedadesWS::getIdsByFederacao(NblFram::$context->data->federacao)));
        $estados = getEstadoCivilListWithVoid();
        $result = [];
        foreach($estados as $e) {
            $result[$e['value']] = array('total' => 0, 'estado_civil' => $e['label']);
        }
        
        foreach($count as $estado => $c) {
            foreach($c as $sociedade) {
                $result[$estado]['total'] += $sociedade['total'];
            }
        }
        return array('status' => 'ok', 'success' => true, 'datas' => $result);
    }
    
    /**
     * 
     * Relatório [Federação]: Sócios x Escolaridade
     * 
     * @httpmethod GET
     * @auth yes
     * @require federacao
     * @return array
     */
    public function relSocioPorEscolaridadeForFederacao(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'RelFedSocioEscolaridade')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new SociosADO();
        
        $count = $obj->countByEscolaridadeForSociedades($this->stringifyArray(SociedadesWS::getIdsByFederacao(NblFram::$context->data->federacao)));
        $niveis = getEscolaridadeListWithVoid();
        $result = [];
        foreach($niveis as $n) {
            $result[$n['value']] = array('total' => 0, 'escolaridade' => $n['label']);
        }
        
        foreach($count as $escolaridade => $c) {
            foreach($c as $sociedade) {
                $result[$escolaridade]['total'] += $sociedade['total'];
            }
        }
        
        // ordene os dados conforme a escolaridade
        $escolaridade_ordenada = getEscolaridadeOrderedList();
        uksort($result, function($a, $b) use ($escolaridade_ordenada) {
            $a_index = array_search($a, $escolaridade_ordenada);
            $b_index = array_search($b, $escolaridade_ordenada);
            if($a_index == $b_index) {
                return 0;
            }
            
            return ($a_index < $b_index) ? -1 : 1;
        });
        
        return array('status' => 'ok', 'success' => true, 'datas' => $result);
    }
    
    /**
     * 
     * Relatório [Federação]: Sócios x Profissão de Fé
     * 
     * @httpmethod GET
     * @auth yes
     * @require federacao
     * @return array
     */
    public function relSocioPorProfissaoFeForFederacao(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'RelFedSocioComungante')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new SociosADO();
        
        $count = $obj->countByProfissaoFeForSociedades($this->stringifyArray(SociedadesWS::getIdsByFederacao(NblFram::$context->data->federacao)));
        $profissao_fe = getGenericHaveList();
        $result = [];
        foreach($profissao_fe as $p) {
            $result[$p['value']] = array('total' => 0, 'comungante' => $p['label']);
        }
        
        foreach($count as $p_fe => $c) {
            foreach($c as $sociedade) {
                $result[$p_fe]['total'] += $sociedade['total'];
            }
        }
        
        return array('status' => 'ok', 'success' => true, 'datas' => $result);
    }
    
    /**
     * 
     * Relatório [Federação]: Sócios x Tem Filhos
     * 
     * @httpmethod GET
     * @auth yes
     * @require federacao
     * @return array
     */
    public function relSocioPorTemFilhoForFederacao(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'RelFedSocioFilho')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new SociosADO();
        
        $count = $obj->countByTemFilhosForSociedades($this->stringifyArray(SociedadesWS::getIdsByFederacao(NblFram::$context->data->federacao)));
        $tem_filhos = getGenericHaveList();
        $result = [];
        foreach($tem_filhos as $t) {
            $result[$t['value']] = array('total' => 0, 'tem_filhos' => $t['label']);
        }
        
        foreach($count as $t_filho => $c) {
            foreach($c as $sociedade) {
                $result[$t_filho]['total'] += $sociedade['total'];
            }
        }
        
        return array('status' => 'ok', 'success' => true, 'datas' => $result);
    }
    
    /**
     * 
     * Relatório [Federação]: Sócios x Tem Filhos x Sexo
     * 
     * @httpmethod GET
     * @auth yes
     * @require federacao
     * @return array
     */
    public function relSocioPorTemFilhoPorSexoForFederacao(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'RelFedSocioFilhoSexo')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new SociosADO();
        
        $count = $obj->countByTemFilhosAndSexoForSociedades($this->stringifyArray(SociedadesWS::getIdsByFederacao(NblFram::$context->data->federacao)));
        $keys = array('S_F', 'N_F', 'S_M', 'N_M');
        $result = [];
        foreach($keys as $k) {
            $result[$k] = array('total' => 0, 'tem_filhos' => '', 'sexo' => '');
        }
        
        foreach($count as $t_filho_sx => $c) {
            foreach($c as $sociedade) {
                $result[$t_filho_sx]['total'] += $sociedade['total'];
                $result[$t_filho_sx]['tem_filhos'] = $sociedade['tem_filhos'];
                $result[$t_filho_sx]['sexo'] = $sociedade['sexo'];
            }
        }
        
        return array('status' => 'ok', 'success' => true, 'datas' => $result);
    }
    
    /**
     * 
     * Relatório [Federação]: Sócios x Necessidade Especial
     * 
     * @httpmethod GET
     * @auth yes
     * @require federacao
     * @return array
     */
    public function relSocioPorNecessidadeForFederacao(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'RelFedSocioNecessidade')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new SociosADO();
        
        $count = $obj->countByNecessidadeForSociedades($this->stringifyArray(SociedadesWS::getIdsByFederacao(NblFram::$context->data->federacao)));
        $necessidades = NecessidadesEspeciaisWS::getAll();
        $result = [];
        foreach($necessidades as $n) {
            $result[$n['id']] = array('total' => 0, 'necessidade' => $n['nome']);
        }
        
        foreach($count as $necessidade => $c) {
            foreach($c as $sociedade) {
                $result[$necessidade]['total'] += $sociedade['total'];
            }
        }
        
        return array('status' => 'ok', 'success' => true, 'datas' => $result);
    }
    
    /**
     * 
     * Relatório [Federação]: Sócios x Doações
     * 
     * 
     * @httpmethod GET
     * @auth yes
     * @require federacao
     * @return array
     */
    public function relSocioPorDoacaoForFederacao(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'RelFedSocioDoacao')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new SociosADO();
        
        $count = $obj->countByDoacaoForSociedades($this->stringifyArray(SociedadesWS::getIdsByFederacao(NblFram::$context->data->federacao)));
        $doacoes = DoacoesWS::getAll();
        $result = [];
        foreach($doacoes as $d) {
            $result[$d['id']] = array('total' => 0, 'doacao' => $d['nome']);
        }
        
        foreach($count as $doacao => $c) {
            foreach($c as $sociedade) {
                $result[$doacao]['total'] += $sociedade['total'];
            }
        }
        
        return array('status' => 'ok', 'success' => true, 'datas' => $result);
    }
    
    /**
     * 
     * Relatório [Federação]: Sócios x Arrolamento
     * 
     * 
     * @httpmethod GET
     * @auth yes
     * @require federacao
     * @return array
     */
    public function relSocioPorArrolamentoForFederacao(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'RelFedSocioArrolamento')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new SociosADO();
        
        $count = $obj->countByArrolamentoForSociedades($this->stringifyArray(SociedadesWS::getIdsByFederacao(NblFram::$context->data->federacao)));
        $arrolamentos = getGenericHaveList();
        $result = [];
        foreach($arrolamentos as $a) {
            $result[$a['value']] = array('total' => 0, 'cooperador' => $a['label']);
        }
        
        foreach($count as $arrolamento => $c) {
            foreach($c as $sociedade) {
                $result[$arrolamento]['total'] += $sociedade['total'];
            }
        }
        
        return array('status' => 'ok', 'success' => true, 'datas' => $result);
    }
    
    /**************************************************************************
     *                  RELATÓRIOS DA SINODAL
     **************************************************************************/
    
    /**
     * 
     * Relatório [Sinodal]: Federações Ativas x Inativas
     * 
     * @httpmethod GET
     * @auth yes
     * @require sinodal
     * @return array
     */
    public function relFederacoesAtivasForSinodal(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'RelSinFederacoes')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $presbiterios = count(PresbiteriosWS::getIdsBySinodo(SinodaisWS::getSinodoId(NblFram::$context->data->sinodal)));
        $federacoes = count(FederacoesWS::getIdsAtivosBySinodal(NblFram::$context->data->sinodal));
        $nao_ativas = $presbiterios - $federacoes;
        
        $result = array(
            'todas' => $presbiterios,
            'ativas' => $federacoes,
            'nao_ativas' => $nao_ativas
        );
        
        return array('status' => 'ok', 'success' => true, 'datas' => $result);
    }
    
    /**
     * 
     * Relatório [Sinodal]: Sociedades Ativas x Inativas
     * 
     * @httpmethod GET
     * @auth yes
     * @require sinodal
     * @return array
     */
    public function relSociedadesAtivasForSinodal(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'RelSinSociedades')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $todas = count(SinodaisWS::getIgrejasIds(NblFram::$context->data->sinodal));
        $ativas = count(SociedadesWS::getIgrejaDasSociedadesAtivasBySinodal(NblFram::$context->data->sinodal));
        $nao_ativas = $todas - $ativas;
        
        $result = array(
            'todas' => $todas,
            'ativas' => $ativas,
            'nao_ativas' => $nao_ativas
        );
        
        return array('status' => 'ok', 'success' => true, 'datas' => $result);
    }
    
    /**
     * 
     * Relatório [Sinodal]: Sócios x Idade
     * 
     * @httpmethod GET
     * @auth yes
     * @require sinodal
     * @return array
     */
    public function relSocioPorIdadeForSinodal(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'RelSinSocioAno')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new SociosADO();
        
        $count = $obj->countByMesAndAnoForSociedades($this->stringifyArray(SociedadesWS::getIdsBySinodal(NblFram::$context->data->sinodal)));
        return array('status' => 'ok', 'success' => true, 'datas' => $count);
    }
    
    /**
     * 
     * Relatório [Sinodal]: Sócios x Sexo
     * 
     * @httpmethod GET
     * @auth yes
     * @require sinodal
     * @return array
     */
    public function relSocioPorSexoForSinodal(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'RelSinSocioSexo')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new SociosADO();
        
        $count = $obj->countBySexoForSociedades($this->stringifyArray(SociedadesWS::getIdsBySinodal(NblFram::$context->data->sinodal)));
        $sexos = getSexoListWithVoid();
        $result = [];
        foreach($sexos as $s) {
            $result[$s['value']] = array('total' => 0, 'sexo' => $s['label']);
        }
        
        foreach($count as $sexo => $c) {
            foreach($c as $sociedade) {
                $result[$sexo]['total'] += $sociedade['total'];
            }
        }
        return array('status' => 'ok', 'success' => true, 'datas' => $result);
    }
    
    /**
     * 
     * Relatório [Sinodal]: Sócios x Estado Civil
     * 
     * @httpmethod GET
     * @auth yes
     * @require sinodal
     * @return array
     */
    public function relSocioPorEstadoCivilForSinodal(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'RelSinSocioEstadoCivil')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new SociosADO();
        
        $count = $obj->countByEstadoCivilForSociedades($this->stringifyArray(SociedadesWS::getIdsBySinodal(NblFram::$context->data->sinodal)));
        $estados = getEstadoCivilListWithVoid();
        $result = [];
        foreach($estados as $e) {
            $result[$e['value']] = array('total' => 0, 'estado_civil' => $e['label']);
        }
        
        foreach($count as $estado => $c) {
            foreach($c as $sociedade) {
                $result[$estado]['total'] += $sociedade['total'];
            }
        }
        return array('status' => 'ok', 'success' => true, 'datas' => $result);
    }
    
    /**
     * 
     * Relatório [Sinodal]: Sócios x Escolaridade
     * 
     * @httpmethod GET
     * @auth yes
     * @require sinodal
     * @return array
     */
    public function relSocioPorEscolaridadeForSinodal(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'RelSinSocioEscolaridade')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new SociosADO();
        
        $count = $obj->countByEscolaridadeForSociedades($this->stringifyArray(SociedadesWS::getIdsBySinodal(NblFram::$context->data->sinodal)));
        $niveis = getEscolaridadeListWithVoid();
        $result = [];
        foreach($niveis as $n) {
            $result[$n['value']] = array('total' => 0, 'escolaridade' => $n['label']);
        }
        
        foreach($count as $escolaridade => $c) {
            foreach($c as $sociedade) {
                $result[$escolaridade]['total'] += $sociedade['total'];
            }
        }
        
        // ordene os dados conforme a escolaridade
        $escolaridade_ordenada = getEscolaridadeOrderedList();
        uksort($result, function($a, $b) use ($escolaridade_ordenada) {
            $a_index = array_search($a, $escolaridade_ordenada);
            $b_index = array_search($b, $escolaridade_ordenada);
            if($a_index == $b_index) {
                return 0;
            }
            
            return ($a_index < $b_index) ? -1 : 1;
        });
        
        return array('status' => 'ok', 'success' => true, 'datas' => $result);
    }
    
    /**
     * 
     * Relatório [Sinodal]: Sócios x Profissão de Fé
     * 
     * @httpmethod GET
     * @auth yes
     * @require sinodal
     * @return array
     */
    public function relSocioPorProfissaoFeForSinodal(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'RelSinSocioComungante')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new SociosADO();
        
        $count = $obj->countByProfissaoFeForSociedades($this->stringifyArray(SociedadesWS::getIdsBySinodal(NblFram::$context->data->sinodal)));
        $profissao_fe = getGenericHaveList();
        $result = [];
        foreach($profissao_fe as $p) {
            $result[$p['value']] = array('total' => 0, 'comungante' => $p['label']);
        }
        
        foreach($count as $p_fe => $c) {
            foreach($c as $sociedade) {
                $result[$p_fe]['total'] += $sociedade['total'];
            }
        }
        
        return array('status' => 'ok', 'success' => true, 'datas' => $result);
    }
    
    /**
     * 
     * Relatório [Sinodal]: Sócios x Tem Filhos
     * 
     * @httpmethod GET
     * @auth yes
     * @require sinodal
     * @return array
     */
    public function relSocioPorTemFilhoForSinodal(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'RelSinSocioFilho')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new SociosADO();
        
        $count = $obj->countByTemFilhosForSociedades($this->stringifyArray(SociedadesWS::getIdsBySinodal(NblFram::$context->data->sinodal)));
        $tem_filhos = getGenericHaveList();
        $result = [];
        foreach($tem_filhos as $t) {
            $result[$t['value']] = array('total' => 0, 'tem_filhos' => $t['label']);
        }
        
        foreach($count as $t_filho => $c) {
            foreach($c as $sociedade) {
                $result[$t_filho]['total'] += $sociedade['total'];
            }
        }
        
        return array('status' => 'ok', 'success' => true, 'datas' => $result);
    }
    
    /**
     * 
     * Relatório [Sinodal]: Sócios x Tem Filhos x Sexo
     * 
     * @httpmethod GET
     * @auth yes
     * @require sinodal
     * @return array
     */
    public function relSocioPorTemFilhoPorSexoForSinodal(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'RelSinSocioFilhoSexo')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new SociosADO();
        
        $count = $obj->countByTemFilhosAndSexoForSociedades($this->stringifyArray(SociedadesWS::getIdsBySinodal(NblFram::$context->data->sinodal)));
        $keys = array('S_F', 'N_F', 'S_M', 'N_M');
        $result = [];
        foreach($keys as $k) {
            $result[$k] = array('total' => 0, 'tem_filhos' => '', 'sexo' => '');
        }
        
        foreach($count as $t_filho_sx => $c) {
            foreach($c as $sociedade) {
                $result[$t_filho_sx]['total'] += $sociedade['total'];
                $result[$t_filho_sx]['tem_filhos'] = $sociedade['tem_filhos'];
                $result[$t_filho_sx]['sexo'] = $sociedade['sexo'];
            }
        }
        
        return array('status' => 'ok', 'success' => true, 'datas' => $result);
    }
    
    /**
     * 
     * Relatório [Sinodal]: Sócios x Necessidade Especial
     * 
     * @httpmethod GET
     * @auth yes
     * @require sinodal
     * @return array
     */
    public function relSocioPorNecessidadeForSinodal(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'RelSinSocioNecessidade')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new SociosADO();
        
        $count = $obj->countByNecessidadeForSociedades($this->stringifyArray(SociedadesWS::getIdsBySinodal(NblFram::$context->data->sinodal)));
        $necessidades = NecessidadesEspeciaisWS::getAll();
        $result = [];
        foreach($necessidades as $n) {
            $result[$n['id']] = array('total' => 0, 'necessidade' => $n['nome']);
        }
        
        foreach($count as $necessidade => $c) {
            foreach($c as $sociedade) {
                $result[$necessidade]['total'] += $sociedade['total'];
            }
        }
        
        return array('status' => 'ok', 'success' => true, 'datas' => $result);
    }
    
    /**
     * 
     * Relatório [Sinodal]: Sócios x Doações
     * 
     * 
     * @httpmethod GET
     * @auth yes
     * @require sinodal
     * @return array
     */
    public function relSocioPorDoacaoForSinodal(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'RelSinSocioDoacao')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new SociosADO();
        
        $count = $obj->countByDoacaoForSociedades($this->stringifyArray(SociedadesWS::getIdsBySinodal(NblFram::$context->data->sinodal)));
        $doacoes = DoacoesWS::getAll();
        $result = [];
        foreach($doacoes as $d) {
            $result[$d['id']] = array('total' => 0, 'doacao' => $d['nome']);
        }
        
        foreach($count as $doacao => $c) {
            foreach($c as $sociedade) {
                $result[$doacao]['total'] += $sociedade['total'];
            }
        }
        
        return array('status' => 'ok', 'success' => true, 'datas' => $result);
    }
    
    /**
     * 
     * Relatório [Sinodal]: Sócios x Arrolamento
     * 
     * 
     * @httpmethod GET
     * @auth yes
     * @require sinodal
     * @return array
     */
    public function relSocioPorArrolamentoForSinodal(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'RelSinSocioArrolamento')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new SociosADO();
        
        $count = $obj->countByArrolamentoForSociedades($this->stringifyArray(SociedadesWS::getIdsBySinodal(NblFram::$context->data->sinodal)));
        $arrolamentos = getGenericHaveList();
        $result = [];
        foreach($arrolamentos as $a) {
            $result[$a['value']] = array('total' => 0, 'cooperador' => $a['label']);
        }
        
        foreach($count as $arrolamento => $c) {
            foreach($c as $sociedade) {
                $result[$arrolamento]['total'] += $sociedade['total'];
            }
        }
        
        return array('status' => 'ok', 'success' => true, 'datas' => $result);
    }
    
}

