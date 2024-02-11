<?php
require_once ADO_PATH . '/Membros.class.php'; 
require_once DAO_PATH . '/MembrosDAO.class.php'; 
require_once WS_PATH . '/socios.ws.php';
require_once WS_PATH . '/templos.ws.php';
require_once WS_PATH . '/pessoas.ws.php';
require_once WS_PATH . '/igrejas.ws.php';
require_once WS_PATH . '/sociedades.ws.php';
require_once WS_PATH . '/oficiais.ws.php';

/**
 * API REST de Membros
 */
class MembrosWS extends WSUtil
{
    /**
     * 
     * @var \MembrosWS singleton instance
     */
    private static $_Instance = null;
    
    /**
     * Get singleton instance
     * 
     * @return \MembrosWS
     */
    public static function getInstance(): \MembrosWS {
        if(self::$_Instance == null) {
            self::$_Instance = new self();
        }
        return self::$_Instance;
    }
    
    /**
     * Obtêm dados básicos de membresia de uma pessoa
     * 
     * @param string $pessoa id da pessoa
     * @return array
     */
    public static function getMembresiabyPessoa($pessoa): array
    {
        $dto = new MembrosDTO();
        $obj = new MembrosADO();
        
        // busque os dados de membresia da pessoa, ordenando pelos mais recentes
        MembrosADO::addComparison($dto, 'pessoa', CMP_EQUAL, $pessoa);
        MembrosADO::addOrdering($dto, 'last_mod', ORDER_DESC);
        
        $result = array(
            'id' => '',
            'igreja' => '',
            'presbiterio' => '',
            'sinodo' => '',
            'arrolado' => false,
        );
        
        if($obj->getAllbyParam($dto))
        {
            $obj->iterate();
            if($obj->hasNext()) 
            {
                $it = $obj->next();
                if(!is_null($it))
                {
                    $result['id'] = $it->id;
                    $result['igreja'] = $it->igreja;
                    $result['arrolado'] = ($it->arrolado == GenericHave::YES);

                    $igreja = TemplosWS::getById($it->igreja);
                    if(!is_null($igreja)) {
                        $result['presbiterio'] = $igreja->presbiterio;
                        $result['sinodo'] = $igreja->sinodo;
                    }
                }
            }
        }
        
        return $result;
    }
    
    /**
     * Gera o código do membro
     * 
     * @return string
     */
    private function getCode(): string
    {
        $code = NblPHPUtil::makeRandomNumericCode(5);
        
        $dto = new MembrosDTO();
        $obj = new MembrosADO();
        
        MembrosADO::addComparison($dto, 'igreja', CMP_EQUAL, NblFram::$context->data->igreja);
        if($obj->countBy($dto)) {
            $n = $obj->count() + 1;
            $code = sprintf("%05d", $n);
        }
        
        return $code;
    }
    
    /**
     * Verifica se o membro já existe na igreja e caso exista, retorne o id de membresia dele
     * 
     * @return string
     */
    private function checkOlder(): string
    {
        $dto = new MembrosDTO();
        $obj = new MembrosADO();
        
        MembrosADO::addComparison($dto, 'pessoa', CMP_EQUAL, 
                (is_object(NblFram::$context->data->pessoa)) ? NblFram::$context->data->pessoa->id : NblFram::$context->data->pessoa);
        MembrosADO::addComparison($dto, 'igreja', CMP_EQUAL, NblFram::$context->data->igreja);
        
        if($obj->getAllbyParam($dto))
        {
            $obj->iterate();
            if($obj->hasNext()) {
                $it = $obj->next();
                if(!is_null($it)) {
                    return $it->id;
                }
            }
        }
        
        return '';
    }
    
    /**
     * Gera o objeto de dados (DTO) de membros
     * 
     * @param object $data objeto com os dados vindos da interface
     * @param int $mode flag para indicar se é adição, edição ou remoção
     * @param string $requester quem está requisitando a criação do objeto. [opcional] Default: usuário logado
     * @return MembrosDTO objeto de dados
     */
    public static function generateDTO($data, $mode, $requester = ''): \MembrosDTO
    {
        $dto = new MembrosDTO();
        $instance = self::getInstance();
        
        if($mode == DTOMode::ADD) {
            $dto->add = true;
            $dto->id = NblPHPUtil::makeNumericId();
            $dto->codigo = $instance->getCode();
            $dto->stat = Status::ACTIVE;
            $dto->time_cad = date('Y-m-d H:i:s');
            $dto->last_mod = $dto->time_cad;
        }
        else if($mode == DTOMode::EDIT) {
            $dto->edit = true;
            $dto->id = $data->id;
            $dto->last_mod = date('Y-m-d H:i:s');
        }
        else if($mode == DTOMode::DELETE) {
            $dto->delete = true;
            $dto->id = $data->id;
            return $dto;
        }
        
        $dto->pessoa = (is_object(NblFram::$context->data->pessoa)) ? NblFram::$context->data->pessoa->id : NblFram::$context->data->pessoa;
        $dto->igreja = NblFram::$context->data->igreja;
        $dto->comungante = (NblFram::$context->data->comungante) ? GenericHave::YES : GenericHave::NO;
        $dto->especial = (NblFram::$context->data->especial) ? GenericHave::YES : GenericHave::NO;
        $dto->arrolado = (NblFram::$context->data->arrolado) ? GenericHave::YES : GenericHave::NO;
        $dto->data_admissao = (empty(NblFram::$context->data->data_admissao)) ? NULL : NblPHPUtil::HumanDate2DBDate(NblFram::$context->data->data_admissao);
        $dto->data_demissao = (empty(NblFram::$context->data->data_demissao)) ? NULL : NblPHPUtil::HumanDate2DBDate(NblFram::$context->data->data_demissao);
        $dto->last_amod = NblFram::$context->token['data']['nome'];
        $dto->last_amod = (empty($requester)) ? NblFram::$context->token['data']['nome'] : $requester;

        return $dto;
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
        if(!doIHavePermission(NblFram::$context->token, 'MembroIgrejaSave,MeSave')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        
        $obj = new MembrosADO();
        
        $old_id = $this->checkOlder();
        if(!empty($old_id)) {
            // está tentando recriar um membro que já existe para essa igreja. É edição ao invés de adição
            NblFram::$context->data->id = $old_id;
            return $this->edit();
        }
        
        $dto = $this->generateDTO(NblFram::$context->data, DTOMode::ADD);
        
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
        if(!doIHavePermission(NblFram::$context->token, 'MembroIgrejaSave,MeSave')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new MembrosADO();
        
        $dto = $this->generateDTO(NblFram::$context->data, DTOMode::EDIT);
        
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
     * Edita status 
     * 
     * @httpmethod PUT
     * @auth yes
     * @require id
     * @return array
     */
    public function changestat(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'MembroIgrejaSave')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new MembrosDTO();
        $obj = new MembrosADO();
        
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
     * Edita status de arrolamento
     * 
     * @httpmethod PUT
     * @auth yes
     * @require id
     * @require arrolado
     * @return array
     */
    public function arrolar(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'MembroIgrejaSave')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new MembrosDTO();
        $obj = new MembrosADO();
        
        $dto->edit = true;
        $dto->id = NblFram::$context->data->id;
        $dto->arrolado = (NblFram::$context->data->arrolado) ? GenericHave::YES : GenericHave::NO;
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
        if(!doIHavePermission(NblFram::$context->token, 'MembroIgreja,Me')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new MembrosDTO();
        $obj = new MembrosADO();
        
        $dto->id = NblFram::$context->data->id;
        if(!is_null($obj->get($dto)))
        {
            $d = $obj->getDTODataObject();
            $d->comungante = ($d->comungante == GenericHave::YES);
            $d->especial = ($d->especial == GenericHave::YES);
            $d->arrolado = ($d->arrolado == GenericHave::YES);
            
            return array('status' => 'ok', 'success' => true, 'datas' => $d);
        }
        else 
        {
            return array('status' => 'no', 'success' => false, 'msg' => 'Erro na operação', 'errs' => $obj->getErrs());
        }
    }
    
    /**
     * Testa a data de nascimento com os parâmetros de sociedade
     * 
     * @param \DateTime $data_nascimento data de nascimento
     * @param string $sociedade sociedade a ser verificada se a data de nascimento atende: ucp, upa, ump, uph_saf
     * @return boolean
     */
    private function testSociedadeDataRange($data_nascimento, $sociedade): bool
    {
        // faixas de anos por sociedade
        $ranges = array(
            'ucp' => $this->getYearsRange(true, false, false, false, false),
            'upa' => $this->getYearsRange(false, true, false, false, false),
            'ump' => $this->getYearsRange(false, false, true, false, false),
            'uph_saf' => $this->getYearsRange(false, false, false, true, true)
        );
        
        if(!isset($ranges[$sociedade])) {
            // se a sociedade não existe, retorne false
            return false;
        }
        
        // obtenha a faixa de anos da sociedade
        if(!empty($ranges[$sociedade]))
        {
            $years_range = reset($ranges[$sociedade]);
            // transforme a faixa de anos em datas para comparação
            $date_range = getDateRangeFromYearRange($years_range['range']['ini'], $years_range['range']['end']);
            if(!is_null($date_range)) {
                // veja se a data de nascimento é menor do que a data máxima da sociedade
                if($data_nascimento < $date_range->end) {
                    // se há data de mínima para a sociedade, veja se a data de nascimento é maior que ela
                    if(!is_null($date_range->ini)) {
                        if($data_nascimento > $date_range->ini) {
                            // é. Membro está na sociedade
                            return true;
                        }
                    }
                    else {
                        // essa sociedade só tem data máxima e o nascimento a antecede. Membro está na sociedade
                        return true;
                    }
                }
            }
        }
        
        // se não achou um true até aqui, então é false
        return false;
    }
    
    /**
     * Obtenha os dados da sociedade do membro e se ele é ou não associado a alguma sociedade interna
     * 
     * @param string $pessoa id da pessoa
     * @param \DateTime $data_nascimento data de nascimento
     * @param string $sexo sexo do membro
     * @param string $igreja igreja do membro
     * @return array
     */
    private function getSociedadeOfMembro($pessoa, $data_nascimento, $sexo, $igreja): array
    {
        $result = array(
            'socio' => false,
            'reference' => '',
            'sociedade' => '',
            'federacao' => '',
            'sinodal' => '',
            'nacional' => ''
        );
        
        // teste a data de nascimento com as sociedades
        $sociedade = null;
        if($this->testSociedadeDataRange($data_nascimento, 'ucp')) {
            // membro está na faixa de idade de ucp. 
            $result['reference'] = References::UCP;
            // Veja se a igreja dele possui a sociedade
            $sociedade = SociedadesWS::getByIgrejaAndReference($igreja, References::UCP);
        }
        else if($this->testSociedadeDataRange($data_nascimento, 'upa')) {
            // membro está na faixa de idade de upa. 
            $result['reference'] = References::UPA;
            // Veja se a igreja dele possui a sociedade
            $sociedade = SociedadesWS::getByIgrejaAndReference($igreja, References::UPA);
        }
        else if($this->testSociedadeDataRange($data_nascimento, 'ump')) {
            // membro está na faixa de idade de ump. 
            $result['reference'] = References::UMP;
            // Veja se a igreja dele possui a sociedade
            $sociedade = SociedadesWS::getByIgrejaAndReference($igreja, References::UMP);
        }
        else if($this->testSociedadeDataRange($data_nascimento, 'uph_saf')) {
            // membro está na faixa de idade de uph ou saf. Decida pelo sexo.
            if($sexo == Sexo::MASCULINO) {
                $result['reference'] = References::UPH;
                // Veja se a igreja dele possui a sociedade
                $sociedade = SociedadesWS::getByIgrejaAndReference($igreja, References::UPH);
            }
            else {
                $result['reference'] = References::SAF;
                // Veja se a igreja dele possui a sociedade
                $sociedade = SociedadesWS::getByIgrejaAndReference($igreja, References::SAF);
            }
        }
        
        if(!is_null($sociedade)) {
            // Igreja possui sociedade. Pegue os dados e teste e veja se ele é sócio
            $result['sociedade'] = $sociedade->id;
            $result['federacao'] = $sociedade->federacao;
            $result['sinodal'] = $sociedade->sinodal;
            $result['nacional'] = $sociedade->nacional;
            $result['socio'] = SociosWS::checkSocio($pessoa, $sociedade->id);
        }
        
        return $result;
    }
    
    /**
     * Busca a informação de associação e sociedades do membro pelo id de pessoa 
     * 
     * @httpmethod GET
     * @auth yes
     * @require pessoa
     * @return array
     */
    public function associacaobypessoa(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'Dados')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        // busque o membro pelo id de pessoa
        
        $dto = new MembrosDTO();
        $obj = new MembrosADO();
        
        MembrosADO::addComparison($dto, 'pessoa', CMP_EQUAL, NblFram::$context->data->pessoa);
        
        if(!is_null($obj->getBy($dto)))
        {
            $result = array(
                'igreja' => '',
                'presbiterio' => '',
                'sinodo' => '',
                'socio' => false,
                'reference' => '',
                'sociedade' => '',
                'federacao' => '',
                'sinodal' => '',
                'nacional' => ''
            );
            
            $membro = $obj->getDTODataObject();
            $result['igreja'] = $membro->igreja;
            
            // busque os dados da igreja, presbitério e sínodo
            $igreja = IgrejasWS::getById($membro->igreja);
            if(!is_null($igreja)) {
                $result['presbiterio'] = $igreja->presbiterio;
                $result['sinodo'] = $igreja->sinodo;
            } 
            
            // busque os dados de sociedade, de acordo com a idade do membro
            $pessoa = PessoasWS::getById(NblFram::$context->data->pessoa);
            if(!is_null($pessoa->data_nascimento)) {
                $data_nascimento = new DateTime($pessoa->data_nascimento);
                $sociedade = $this->getSociedadeOfMembro($pessoa->id, $data_nascimento, $pessoa->sexo, $membro->igreja);
                $result['socio'] = $sociedade['socio'];
                $result['reference'] = $sociedade['reference'];
                $result['sociedade'] = $sociedade['sociedade'];
                $result['federacao'] = $sociedade['federacao'];
                $result['sinodal'] = $sociedade['sinodal'];
                $result['nacional'] = $sociedade['nacional'];
            }
            
            return array('status' => 'ok', 'success' => true, 'datas' => $result);
        }
        else 
        {
            return array('status' => 'no', 'success' => false, 'msg' => 'Erro na operação', 'errs' => $obj->getErrs());
        }
    }
    
    /**
     * Gere os períodos de anos com base nos filtros
     * 
     * @param bool $criancas  filtrar por crianças
     * @param bool $adolescentes  filtrar por adolescentes
     * @param bool $jovens   filtrar por jovens
     * @param bool $adultos  filtrar por adultos
     * @param bool $idosos  filtrar por idosos
     * @return array
     */
    private function getYearsRange($criancas, $adolescentes, $jovens, $adultos, $idosos): array
    {
        /* mapa binário das idades */
        $ages_bin_map = array(
            16 => array('ini' => CRIANCA_MIN_ANO, 'end' => CRIANCA_MAX_ANO), // crianca: 2 ^ 4 = 16
            8 => array('ini' => ADOLESCENTE_MIN_ANO, 'end' => ADOLESCENTE_MAX_ANO), // adolescente: 2 ^ 3 = 8
            4 => array('ini' => JOVEM_MIN_ANO, 'end' => JOVEM_MAX_ANO), // jovem: 2 ^ 2 = 4
            2 => array('ini' => ADULTO_MIN_ANO, 'end' => ADULTO_MAX_ANO), // adulto: 2 ^ 1 = 2
            1 => array('ini' => IDOSO_MIN_ANO, 'end' => IDOSO_MAX_ANO)  // idoso: 2 ^ 0 = 1
        );

        $ages = array();
        /* máscara binária para os filtros, convertendos os bool em int:
         * criança|adolescente|jovem|adulto|idoso
         *    1   |     1     |  1  |   1  |  1
         */
        $mask = '' . ((int) $criancas) . // criança
                '' . ((int) $adolescentes). // adolescente
                '' . ((int) $jovens). // jovem
                '' . ((int) $adultos) . // adulto
                '' . ((int) $idosos); // idoso
        /* itere a máscara e verifique quem foi setado, gerando o array das idades */
        for($i = 0; $i < 5; $i++)
        {
            if($mask[$i] & 0x00000001) {
                /* se está setado, calcule o índice do mapeamento:
                 * índice = 2 ^ (1 + (quantidade a complementar - iteração atual))
                 * 
                 * - a quantidade a complementar, para gerar o expoente 4, será no máximo 3, 
                 *    visto que só entra nesse cálculo quem está setado e portanto tem valor 1
                 * - a cada iteração, a quantidade a complementar reduz
                 */
                $indx = 2 ** (((int) $mask[$i]) + (3 - $i));
                if(isset($ages_bin_map[$indx])) {
                    $ages[] = array('range' => $ages_bin_map[$indx], 'remove' => false);
                }
            }
        }
        
        /* simplifique o array condensado períodos próximos */
        $last_end = 0;
        foreach($ages as $k => $v)
        {
            if($k == 0) {
                // primeira iteração
                $last_end = $v['range']['end'];
                continue;
            }

            $diff_end = $v['range']['ini'] - $last_end;
            if($diff_end == 1) {
                /* se a diferença entre o início do período atual e o final do anterior é 1, 
                 * condense os períodos num só */
                $j = $k - 1;
                $ages[$k]['range']['ini'] = $ages[$j]['range']['ini'];
                $ages[$j]['remove'] = true; // marque para remoção posterior
            }

            $last_end = $v['range']['end'];
        }

        // remova os períodos marcados
        foreach($ages as $k => $v) {
            if($v['remove']) {
                unset($ages[$k]);
            }
        }

        return $ages;
    }
    
    /**
     * 
     * Verifique se a pessoa já está como membro da igreja
     * 
     * @httpmethod GET
     * @auth yes
     * @require pessoa
     * @require igreja
     * @return array
     */
    public function check(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'MembroIgreja')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new MembrosDTO();
        $obj = new MembrosADO();
        
        MembrosADO::addComparison($dto, 'pessoa', CMP_EQUAL, NblFram::$context->data->pessoa);
        MembrosADO::addComparison($dto, 'igreja', CMP_EQUAL, NblFram::$context->data->igreja);
        
        return array('status' => 'ok', 'success' => true, 'exists' => ($obj->countBy($dto) > 0));
    }
    
    private function getFaixaEtariaFilter($criancas, $adolescentes, $jovens, $adultos, $idosos): array
    {
        $faixas = array();
        
        $years_range = $this->getYearsRange($criancas, $adolescentes, $jovens, $adultos, $idosos);
        if(!empty($years_range)) {
            $has_at_least_one_filter_pessoa = true;
            foreach($years_range as $range)
            {
                $date_range = getDateRangeFromYearRange($range['range']['ini'], $range['range']['end']);
                if(!is_null($date_range)) {
                    $faixa = new stdClass();
                    if(!is_null($date_range->ini)) {
                        $faixa->ini = $date_range->ini->format('Y-m-d');
                    }
                    $faixa->end = $date_range->end->format('Y-m-d');
                    $faixas[] = $faixa;
                }
            }
        }
        
        return $faixas;
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
        if(!doIHavePermission(NblFram::$context->token, 'MembroIgreja,Dados')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }

        $dto = new MembrosDTO();
        $obj = new MembrosADO();
        
        
        $page = ($this->testInputString('page')) ? (int) NblFram::$context->data->page : -1;
        $pageSize = ($this->testInputString('pageSize')) ? (int) NblFram::$context->data->pageSize : -1;
        $pagination = $this->calcPagination($page, $pageSize);

        $searchBy = ($this->testInputString('searchBy')) ? NblFram::$context->data->searchBy : '';
        $orderBy = ($this->testInputString('orderBy')) ? NblFram::$context->data->orderBy : '';
        $groupBy = ($this->testInputString('groupBy')) ? NblFram::$context->data->groupBy : '';
        
        $stat = ($this->testInputString('stat')) ? NblFram::$context->data->stat : '';
        $pessoa = ($this->testInputString('pessoa')) ? NblFram::$context->data->pessoa : '';
        $igreja = ($this->testInputString('igreja')) ? NblFram::$context->data->igreja : '';
        $sexo = ($this->testInputString('sexo')) ? NblFram::$context->data->sexo : '';
        $escolaridade = ($this->testInputString('escolaridade')) ? NblFram::$context->data->escolaridade : '';
        
        $comungante = ($this->testInputBool('comungante')) ? true : false;
        $nao_comungante = ($this->testInputBool('nao_comungante')) ? true : false;
        $arrolado = ($this->testInputBool('arrolado')) ? true : false;
        $nao_arrolado = ($this->testInputBool('nao_arrolado')) ? true : false;
        $especial = ($this->testInputBool('especial')) ? true : false;
        
        $criancas = ($this->testInputBool('criancas')) ? true : false;
        $adolescentes = ($this->testInputBool('adolescentes')) ? true : false;
        $jovens = ($this->testInputBool('jovens')) ? true : false;
        $adultos = ($this->testInputBool('adultos')) ? true : false;
        $idosos = ($this->testInputBool('idosos')) ? true : false;
        $aniversariantes = ($this->testInputBool('aniversariantes')) ? true : false;
        
        $tem_filhos = ($this->testInputBool('tem_filhos')) ? true : false;
        $sem_filhos = ($this->testInputBool('sem_filhos')) ? true : false;
        $estado_civil = ($this->testInputString('estado_civil')) ? NblFram::$context->data->estado_civil : '';
        
        $exceto = ($this->testInputString('exceto')) ? NblFram::$context->data->exceto : '';
        
        if(!empty($pessoa))
        {   
            // filtre por uma pessoa diretamente
            MembrosADO::addComparison($dto, 'pessoa', CMP_EQUAL, $pessoa);
            
            $pessoas = PessoasWS::mapBasicDataById();
        }
        else 
        {
            // filtros por faixa etária 
            $faixas = $this->getFaixaEtariaFilter($criancas, $adolescentes, $jovens, $adultos, $idosos);
            
            // filtro por exceção
            if(!empty($exceto) && !empty($igreja)) {
                $oficiais = OficiaisWS::getAllByTipoAndRef($exceto, $igreja, References::IGREJA);
            }
            
            // filtros nos dados de pessoa
            if(!empty($searchBy) || !empty($sexo) || !empty($estado_civil) || !empty($escolaridade) || 
                    $tem_filhos || $sem_filhos || $aniversariantes || !empty($faixas))
            {
                $pessoas = PessoasWS::mapBasicDataByIdWithFilters($searchBy, $sexo, $estado_civil, 
                                                    $escolaridade, $tem_filhos, $sem_filhos, $aniversariantes, $faixas);
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
            
            MembrosADO::addComparison($dto, 'pessoa', CMP_IN_LIST, $id_list);
        }
        
        // filtros na membresia
        if(!empty($stat))
        {   
            MembrosADO::addComparison($dto, 'stat', CMP_EQUAL, $stat);
        }

        if(!empty($igreja))
        {   
            MembrosADO::addComparison($dto, 'igreja', CMP_EQUAL, $igreja);
        }

        if($comungante)
        {   
            MembrosADO::addComparison($dto, 'comungante', CMP_EQUAL, GenericHave::YES);
        }

        if($nao_comungante)
        {   
            MembrosADO::addComparison($dto, 'comungante', CMP_EQUAL, GenericHave::NO);
        }

        if($arrolado)
        {   
            MembrosADO::addComparison($dto, 'arrolado', CMP_EQUAL, GenericHave::YES);
        }

        if($nao_arrolado)
        {   
            MembrosADO::addComparison($dto, 'arrolado', CMP_EQUAL, GenericHave::NO);
        }

        if($especial)
        {   
            MembrosADO::addComparison($dto, 'especial', CMP_EQUAL, GenericHave::YES);
        }
                
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
                    $pessoa_perfil = '';
                    $pessoa_data_nascimento = '';
                    $pessoa_email = '';
                    $pessoa_telefone = '';
                    $pessoa_celular_1 = '';
                    if(isset($pessoas[$it->pessoa])) {
                        $pessoa_nome = $pessoas[$it->pessoa]['nome'];
                        $pessoa_perfil = $pessoas[$it->pessoa]['perfil'];
                        $pessoa_data_nascimento = $pessoas[$it->pessoa]['data_nascimento'];
                        $pessoa_email = $pessoas[$it->pessoa]['email'];
                        $pessoa_telefone = $pessoas[$it->pessoa]['telefone'];
                        $pessoa_celular_1 = $pessoas[$it->pessoa]['celular_1'];
                    }
                    
                    $pre_result[] = array(
                        'id' => $it->id,
                        'pessoa' => $it->pessoa,
                        'nome' => $pessoa_nome,
                        'perfil' => $pessoa_perfil,
                        'data_nascimento' => $pessoa_data_nascimento,
                        'email' => $pessoa_email,
                        'telefone' => $pessoa_telefone,
                        'celular_1' => $pessoa_celular_1,
                        'igreja' => $it->igreja,
                        'codigo' => $it->codigo,
                        'comungante' => ($it->comungante == GenericHave::YES),
                        'especial' => ($it->especial == GenericHave::YES),
                        'arrolado' => ($it->arrolado == GenericHave::YES),
                        'data_admissao' => $it->data_admissao,
                        'data_demissao' => $it->data_demissao,
                        'stat' => $it->stat,
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
        if(!doIHavePermission(NblFram::$context->token, 'MembroIgrejaRemove')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new MembrosDTO();
        $obj = new MembrosADO();
        
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
        if(!doIHavePermission(NblFram::$context->token, 'MembroIgrejaRemove')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new MembrosADO();
        
        foreach(NblFram::$context->data->ids as $id) {
            $dto = new MembrosDTO();
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

