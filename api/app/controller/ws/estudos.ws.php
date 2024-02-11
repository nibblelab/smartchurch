<?php
require_once ADO_PATH . '/Estudos.class.php'; 
require_once DAO_PATH . '/EstudosDAO.class.php'; 
require_once HLP_PATH . '/ImageHelper.class.php'; 
require_once HLP_PATH . '/DocHelper.class.php'; 
require_once WS_PATH . '/pessoas.ws.php'; 

/**
 * API REST de Estudos
 */
class EstudosWS extends WSUtil
{
    /**
     * 
     * @var \EstudosWS singleton instance
     */
    private static $_Instance = null;
    
    /**
     * Get singleton instance
     * 
     * @return \EstudosWS
     */
    public static function getInstance(): \EstudosWS {
        if(self::$_Instance == null) {
            self::$_Instance = new self();
        }
        return self::$_Instance;
    }
    
    /**
     * Obtenho o anexo do estudo
     * 
     * @param string $id id do sermão
     * @return string
     */
    private function getAnexo($id): string
    {
        $dto = new EstudosDTO();
        $obj = new EstudosADO();
        
        $dto->id = $id;
        if(!is_null($obj->get($dto)))
        {
            $d = $obj->getDTODataObject();
            return $d->anexo;
        }
        return '';
    }
    
    /**
     * Obtenha os anexos dos estudos pelos seus ids
     * 
     * @param array $ids array com os ids
     * @return array
     */
    private function getAnexos($ids): array
    {
        $dto = new EstudosDTO();
        $obj = new EstudosADO();
        
        EstudosADO::addComparison($dto, id, CMP_IN_LIST, $this->stringifyArray($ids));
        
        $result = array();
        if($obj->getAllbyParam($dto))
        {
            $obj->iterate();
            while($obj->hasNext())
            {
                $it = $obj->next();
                if(!is_null($it->id))
                {
                    $result[] = $it->anexo;
                }
            }
        }
        
        return $result;
    }
    
    /**
     * Remova os anexos de estudos
     * 
     * @param array $anexos array com os nomes dos anexos
     * @return void
     */
    private function removeAnexos($anexos): void
    {
        if(empty($anexos)) {
            return;
        }
        
        $helper = new DocHelper();
        
        $anexos_to_remove = [];
        foreach($anexos as $anexo)
        {
            $anexos_to_remove[] = RSC_SERMOES_PATH . '/' . $anexo;
        }
        
        $helper->delete($anexos_to_remove);
    }
    
    /**
     * Obtenha a logo 
     * 
     * @return string
     */
    private function getLogo(): string
    {
        $dto = new EstudosDTO();
        $obj = new EstudosADO();
        
        $dto->id = NblFram::$context->data->id;
        if(!is_null($obj->get($dto)))
        {
            $d = $obj->getDTODataObject();
            return $d->logo;
        }
        else 
        {
            return '';
        }
    }
    
    /**
     * Obtenha as logos dos estudos pelos seus ids
     * 
     * @param array $ids array com os ids
     * @return array
     */
    private function getLogos($ids): array
    {
        $dto = new EstudosDTO();
        $obj = new EstudosADO();
        
        EstudosADO::addComparison($dto, id, CMP_IN_LIST, $this->stringifyArray($ids));
        
        $result = array();
        if($obj->getAllbyParam($dto))
        {
            $obj->iterate();
            while($obj->hasNext())
            {
                $it = $obj->next();
                if(!is_null($it->id))
                {
                    $result[] = $it->logo;
                }
            }
        }
        
        return $result;
    }
    
    /**
     * Remova as logos 
     * 
     * @param array $logos array com os nomes dos anexos
     * @return void
     */
    private function removeLogos($logos): void
    {
        if(empty($logos)) {
            return;
        }
        
        $helper = new DocHelper();
        
        $logos_to_remove = [];
        foreach($logos as $logo)
        {
            $logos_to_remove[] = RSC_PATH . '/' . $logo;
        }
        
        $helper->delete($logos_to_remove);
    }
    
    /**
     * Obtenha a logo (app)
     * 
     * @return string
     */
    private function getLogoApp(): string
    {
        $dto = new EstudosDTO();
        $obj = new EstudosADO();
        
        $dto->id = NblFram::$context->data->id;
        if(!is_null($obj->get($dto)))
        {
            $d = $obj->getDTODataObject();
            return $d->logo_app;
        }
        else 
        {
            return '';
        }
    }
    
    /**
     * Obtenha as logos (app) dos estudos pelos seus ids
     * 
     * @param array $ids array com os ids
     * @return array
     */
    private function getLogosApp($ids): array
    {
        $dto = new EstudosDTO();
        $obj = new EstudosADO();
        
        EstudosADO::addComparison($dto, id, CMP_IN_LIST, $this->stringifyArray($ids));
        
        $result = array();
        if($obj->getAllbyParam($dto))
        {
            $obj->iterate();
            while($obj->hasNext())
            {
                $it = $obj->next();
                if(!is_null($it->id))
                {
                    $result[] = $it->logo_app;
                }
            }
        }
        
        return $result;
    }
    
    /**
     * Remova as logos (app)
     * 
     * @param array $logos array com os nomes dos anexos
     * @return void
     */
    private function removeLogosApp($logos): void
    {
        if(empty($logos)) {
            return;
        }
        
        $helper = new DocHelper();
        
        $logos_to_remove = [];
        foreach($logos as $logo)
        {
            $logos_to_remove[] = RSC_PATH . '/' . $logo;
        }
        
        $helper->delete($logos_to_remove);
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
        if(!doIHavePermission(NblFram::$context->token, 'EstudoIgrejaSave')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new EstudosDTO();
        $obj = new EstudosADO();
        
        // trate uma possível imagem de logo
        $logo = '';
        if($this->isBase64Img(NblFram::$context->data->arquivo->content)) {
            $imagehlp = new ImageHelper();
            $image = $imagehlp->generateFromBase64(NblFram::$context->data->arquivo->content, 
                                                    NblFram::$context->data->arquivo->name, 
                                                    RSC_PATH, 
                                                    false);
            $logo = $image->path;
        }
        
        // trate uma possível imagem de logo (app)
        $logo_app = '';
        if($this->isBase64Img(NblFram::$context->data->arquivo_app->content)) {
            $imagehlp = new ImageHelper();
            $image = $imagehlp->generateFromBase64(NblFram::$context->data->arquivo_app->content, 
                                                    NblFram::$context->data->arquivo_app->name, 
                                                    RSC_PATH, 
                                                    false);
            $logo_app = $image->path;
        }
        
        $dto->add = true;
        $dto->id = NblPHPUtil::makeNumericId();
        $dto->ref = NblFram::$context->data->ref;
        $dto->ref_tp = NblFram::$context->data->ref_tp;
        $dto->serie = (empty(NblFram::$context->data->serie)) ? NULL : NblFram::$context->data->serie;
        $dto->autor = (empty(NblFram::$context->data->autor)) ? NULL : NblFram::$context->data->autor;
        $dto->titulo = NblFram::$context->data->titulo;
        $dto->chave = NblFram::$context->data->chave;
        $dto->logo = $logo;
        $dto->logo_app = $logo_app;
        $dto->conteudo = NblFram::$context->data->conteudo;
        $dto->anexo = NblFram::$context->data->anexo;
        $dto->video = NblFram::$context->data->video;
        $dto->audio = NblFram::$context->data->audio;
        $dto->destinatarios = NblFram::$context->data->destinatarios;
        $dto->stat = Status::ACTIVE;
        $dto->time_cad = date('Y-m-d H:i:s');
        $dto->last_mod = $dto->time_cad;
        $dto->last_amod = NblFram::$context->token['data']['nome'];
        
        $obj->add($dto);
        if($obj->sync())
        {
            /* anexo */
            if(!empty(NblFram::$context->data->anexo)) {
                $doc = array(
                    'old' => RSC_TMP_PATH . '/' . NblFram::$context->data->anexo,
                    'new' => RSC_SERMOES_PATH . '/' . NblFram::$context->data->anexo
                );
                
                $helper = new DocHelper();
                $helper->move(array($doc));
            }
            
            return array('status' => 'ok', 'success' => true, 'id' => $dto->id);
        }
        else
        {
            return array('status' => 'no', 'success' => false, 'msg' => 'Erro na operação', 'errs' => $obj->getErrs());
        }
    }
    
    /**
     * 
     * Cria
     * 
     * @httpmethod POST
     * @auth yes
     * @return array
     */
    public function createForIgreja(): array
    {
        NblFram::$context->data->ref = NblFram::$context->data->igreja;
        NblFram::$context->data->ref_tp = References::IGREJA;
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
        if(!doIHavePermission(NblFram::$context->token, 'EstudoIgrejaSave')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $old_anexo = $this->getAnexo(NblFram::$context->data->id);
        
        $dto = new EstudosDTO();
        $obj = new EstudosADO();
        
        // veja se tem uma logo anterior
        $old_logo = $this->getLogo();

        // trate uma possível imagem de logo
        $logo = $old_logo;
        if($this->isBase64Img(NblFram::$context->data->arquivo->content)) {
            $imagehlp = new ImageHelper();
            $image = $imagehlp->generateFromBase64(NblFram::$context->data->arquivo->content, 
                                                    NblFram::$context->data->arquivo->name, 
                                                    RSC_PATH, 
                                                    false);
            $logo = $image->path;
        }
        
        // veja se tem uma logo (app) anterior
        $old_logo_app = $this->getLogoApp();

        // trate uma possível imagem de logo (app)
        $logo_app = $old_logo_app;
        if($this->isBase64Img(NblFram::$context->data->arquivo_app->content)) {
            $imagehlp = new ImageHelper();
            $image = $imagehlp->generateFromBase64(NblFram::$context->data->arquivo_app->content, 
                                                    NblFram::$context->data->arquivo_app->name, 
                                                    RSC_PATH, 
                                                    false);
            $logo_app = $image->path;
        }
        
        $dto->edit = true;
        $dto->id = NblFram::$context->data->id;
        $dto->serie = (empty(NblFram::$context->data->serie)) ? NULL : NblFram::$context->data->serie;
        $dto->autor = (empty(NblFram::$context->data->autor)) ? NULL : NblFram::$context->data->autor;
        $dto->titulo = NblFram::$context->data->titulo;
        $dto->chave = NblFram::$context->data->chave;
        $dto->logo = $logo;
        $dto->logo_app = $logo_app;
        $dto->conteudo = NblFram::$context->data->conteudo;
        $dto->anexo = NblFram::$context->data->anexo;
        $dto->video = NblFram::$context->data->video;
        $dto->audio = NblFram::$context->data->audio;
        $dto->destinatarios = NblFram::$context->data->destinatarios;
        $dto->last_mod = date('Y-m-d H:i:s');
        $dto->last_amod = NblFram::$context->token['data']['nome'];
        
        $obj->add($dto);
        if($obj->sync())
        {
            /* anexo */
            if(NblFram::$context->data->anexo != $old_anexo) {
                $helper = new DocHelper();
                
                // remova o antigo
                $helper->delete(array(RSC_SERMOES_PATH . '/' .$old_anexo));
                
                // atualize com o novo
                $doc = array(
                    'old' => RSC_TMP_PATH . '/' . NblFram::$context->data->anexo,
                    'new' => RSC_SERMOES_PATH . '/' . NblFram::$context->data->anexo
                );
                
                $helper->move(array($doc));
            }
            
            // remova uma possível logo anterior
            if($logo != $old_logo) {
                $this->removeLogos(array($old_logo));
            }
            
            // remova uma possível logo (app) anterior
            if($logo_app != $old_logo_app) {
                $this->removeLogosApp(array($old_logo_app));
            }
            
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
        if(!doIHavePermission(NblFram::$context->token, 'EstudoIgrejaBlock')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new EstudosDTO();
        $obj = new EstudosADO();
        
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
        if(!doIHavePermission(NblFram::$context->token, 'EstudoIgreja,Dados')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new EstudosDTO();
        $obj = new EstudosADO();
        
        $dto->id = NblFram::$context->data->id;
        if(!is_null($obj->get($dto)))
        {
            $d = $obj->getDTODataObject();
            
            $d->nome = '';
            if(!is_null($d->autor) && $d->autor != '') {
                $autor = PessoasWS::getById($d->autor);
                if(!is_null($autor)) {
                    $d->nome = $autor->nome;
                }
            }
            
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
     * @require chave
     * @return array
     */
    public function bychave(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'EstudoIgreja')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new EstudosDTO();
        $obj = new EstudosADO();
        
        EstudosADO::addComparison($dto, 'chave', CMP_EQUAL, NblFram::$context->data->chave);
        if(!is_null($obj->getBy($dto)))
        {
            $d = $obj->getDTODataObject();
            
            $d->nome = '';
            if(!is_null($d->autor) && $d->autor != '') {
                $autor = PessoasWS::getById($d->autor);
                if(!is_null($autor)) {
                    $d->nome = $autor->nome;
                }
            }
            
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
     * @require id
     * @return array
     */
    public function download(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'EstudoIgreja,Dados')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new EstudosDTO();
        $obj = new EstudosADO();
        
        $dto->id = NblFram::$context->data->id;
        if(!is_null($obj->get($dto)))
        {
            $d = $obj->getDTODataObject();
            return $this->prepareDownload(RSC_SERMOES_PATH . '/' .$d->anexo, $d->anexo);
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
        if(!doIHavePermission(NblFram::$context->token, 'EstudoIgreja,Dados')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }

        $dto = new EstudosDTO();
        $obj = new EstudosADO();
        $obj_count = new EstudosADO();

        $page = ($this->testInputString('page')) ? (int) NblFram::$context->data->page : -1;
        $pageSize = ($this->testInputString('pageSize')) ? (int) NblFram::$context->data->pageSize : -1;
        $pagination = $this->calcPagination($page, $pageSize);

        $searchBy = ($this->testInputString('searchBy')) ? NblFram::$context->data->searchBy : '';
        $orderBy = ($this->testInputString('orderBy')) ? NblFram::$context->data->orderBy : '';
        $groupBy = ($this->testInputString('groupBy')) ? NblFram::$context->data->groupBy : '';
        
        $stat = ($this->testInputString('stat')) ? NblFram::$context->data->stat : '';
        $ref = ($this->testInputString('ref')) ? NblFram::$context->data->ref : '';
        $ref_tp = ($this->testInputString('ref_tp')) ? NblFram::$context->data->ref_tp : '';
        $serie = ($this->testInputString('serie')) ? NblFram::$context->data->serie : '';
        $autor = ($this->testInputString('autor')) ? NblFram::$context->data->autor : '';
        $chave = ($this->testInputString('chave')) ? NblFram::$context->data->chave : '';
        $publicado_apos = ($this->testInputString('publicado_apos')) ? NblFram::$context->data->publicado_apos : '';
        $destinatarios = ($this->testInputString('destinatarios')) ? NblFram::$context->data->destinatarios : '';
        
        $igreja = ($this->testInputString('igreja')) ? NblFram::$context->data->igreja : '';
        
        $anexo = ($this->testInputBool('anexo')) ? true : false;
        $video = ($this->testInputBool('video')) ? true : false;
        $audio = ($this->testInputBool('audio')) ? true : false;

        $has_at_least_one_filter = false;
        
        if(!empty($searchBy))
        {   
            $has_at_least_one_filter = true;
            EstudosADO::addComparison($dto, 'titulo', CMP_INCLUDE_INSIDE, $searchBy, OP_OR);
            EstudosADO::addComparison($dto, 'conteudo', CMP_INCLUDE_INSIDE, $searchBy, OP_OR);
        }

        if(!empty($stat))
        {   
            $has_at_least_one_filter = true;
            EstudosADO::addComparison($dto, 'stat', CMP_EQUAL, $stat);
        }
        
        if(!empty($serie))
        {   
            $has_at_least_one_filter = true;
            EstudosADO::addComparison($dto, 'serie', CMP_EQUAL, $serie);
        }
        
        if(!empty($autor))
        {   
            $has_at_least_one_filter = true;
            EstudosADO::addComparison($dto, 'autor', CMP_EQUAL, $autor);
        }
        
        if(!empty($chave))
        {   
            $has_at_least_one_filter = true;
            EstudosADO::addComparison($dto, 'chave', CMP_EQUAL, $chave);
        }
        
        if(!empty($ref))
        {   
            $has_at_least_one_filter = true;
            EstudosADO::addComparison($dto, 'ref', CMP_EQUAL, $ref);
        }
        
        if(!empty($ref_tp))
        {   
            $has_at_least_one_filter = true;
            EstudosADO::addComparison($dto, 'ref_tp', CMP_EQUAL, $ref_tp);
        }
        
        if(!empty($igreja))
        {   
            $has_at_least_one_filter = true;
            EstudosADO::addComparison($dto, 'ref', CMP_EQUAL, $igreja);
            EstudosADO::addComparison($dto, 'ref_tp', CMP_EQUAL, References::IGREJA);
        }
        
        if(!empty($publicado_apos))
        {   
            $has_at_least_one_filter = true;
            if($this->isDateStr($publicado_apos)) {
                EstudosADO::addComparison($dto, 'time_cad', CMP_GREATER_THEN_DATE, NblPHPUtil::HumanDate2DBDate($publicado_apos));
            }
            else if($this->isDateSqlStr($publicado_apos)) {
                EstudosADO::addComparison($dto, 'time_cad', CMP_GREATER_THEN_DATE, $publicado_apos);
            }
        }
        
        if(!empty($destinatarios))
        {   
            $has_at_least_one_filter = true;
            $destinatarios_arr = explode(';', $destinatarios);
            foreach($destinatarios_arr as $destinatario) {
                EstudosADO::addComparison($dto, 'destinatarios', CMP_INCLUDE_INSIDE, '"checked":true,"label":"'.$destinatario.'"', OP_OR, true);
            }
        }
        
        if($anexo)
        {   
            $has_at_least_one_filter = true;
            EstudosADO::addComparison($dto, 'anexo', CMP_NOT_EMPTY);
        }
        
        if($video)
        {   
            $has_at_least_one_filter = true;
            EstudosADO::addComparison($dto, 'video', CMP_NOT_EMPTY);
        }
        
        if($audio)
        {   
            $has_at_least_one_filter = true;
            EstudosADO::addComparison($dto, 'audio', CMP_NOT_EMPTY);
        }
        
        if(!empty($orderBy))
        {
            $has_at_least_one_filter = true;
            $order_by_v = explode(',',$orderBy);
            EstudosADO::addOrdering($dto, $order_by_v[0], ($order_by_v[1] == 'asc') ? ORDER_ASC : ORDER_DESC);
        }

        if(!empty($groupBy))
        {
            $has_at_least_one_filter = true;
            $groups = explode(',',$groupBy);
            foreach($groups as $g) {
                EstudosADO::addGrouping($dto, $g);
            }
        }

        if (!$has_at_least_one_filter) {
            $obj_count->count(true);
        }
        else {
            $obj_count->countBy($dto);
        }
        
        if($page == -1)
        {
            $ok = (!$has_at_least_one_filter) ? $obj->getAll() : $obj->getAllbyParam($dto);
        }
        else
        {
            $ok = (!$has_at_least_one_filter) ? $obj->getAll($pagination->page, $pagination->pagesize) : $obj->getAllbyParam($dto, $pagination->page, $pagination->pagesize);
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
                        'ref' => $it->ref,
                        'ref_tp' => $it->ref_tp,
                        'serie' => $it->serie,
                        'autor' => $it->autor,
                        'titulo' => $it->titulo,
                        'chave' => $it->chave,
                        'logo' => $it->logo,
                        'logo_app' => $it->logo_app,
                        'conteudo' => $it->conteudo,
                        'anexo' => $it->anexo,
                        'video' => $it->video,
                        'audio' => $it->audio,
                        'destinatarios' => $it->destinatarios,
                        'stat' => $it->stat,
                        'time_cad' => $it->time_cad,
                        'last_mod' => $it->last_mod,
                        'last_amod' => $it->last_amod
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
        if(!doIHavePermission(NblFram::$context->token, 'EstudoIgrejaRemove')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new EstudosDTO();
        $obj = new EstudosADO();
        
        $dto->id = NblFram::$context->data->id;
        $r = $obj->get($dto);
        if(!is_null($r))
        {
            $r->delete = true;
            if($obj->sync())
            {
                $this->removeAnexos(array($r->anexo));
                $this->removeLogos(array($r->logo));
                $this->removeLogosApp(array($r->logo_app));
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
        if(!doIHavePermission(NblFram::$context->token, 'EstudoIgrejaRemove')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new EstudosADO();
        
        $anexos = $this->getAnexos(NblFram::$context->data->ids);
        $logos = $this->getLogos(NblFram::$context->data->ids);
        $logos_app = $this->getLogosApp(NblFram::$context->data->ids);
        
        foreach(NblFram::$context->data->ids as $id) {
            $dto = new EstudosDTO();
            $dto->delete = true;
            $dto->id = $id;
            
            $obj->add($dto);
        }
        
        if($obj->sync())
        {
            $this->removeAnexos($anexos);
            $this->removeLogos($logos);
            $this->removeLogosApp($logos_app);
            
            return array('status' => 'ok', 'success' => true);
        }
        else 
        {
            return array('status' => 'no', 'success' => false, 'msg' => 'Recursos não encontrados');
        }
    }
}

