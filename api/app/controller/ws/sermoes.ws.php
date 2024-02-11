<?php
require_once ADO_PATH . '/Sermoes.class.php'; 
require_once DAO_PATH . '/SermoesDAO.class.php'; 
require_once HLP_PATH . '/ImageHelper.class.php'; 
require_once HLP_PATH . '/DocHelper.class.php'; 
require_once WS_PATH . '/pessoas.ws.php'; 

/**
 * API REST de Sermoes
 */
class SermoesWS extends WSUtil
{
    /**
     * 
     * @var \SermoesWS singleton instance
     */
    private static $_Instance = null;
    
    /**
     * Get singleton instance
     * 
     * @return \SermoesWS
     */
    public static function getInstance(): \SermoesWS {
        if(self::$_Instance == null) {
            self::$_Instance = new self();
        }
        return self::$_Instance;
    }
    
    /**
     * Obtenho o anexo do sermão
     * 
     * @param string $id id do sermão
     * @return string
     */
    private function getAnexo($id): string
    {
        $dto = new SermoesDTO();
        $obj = new SermoesADO();
        
        $dto->id = $id;
        if(!is_null($obj->get($dto)))
        {
            $d = $obj->getDTODataObject();
            return $d->anexo;
        }
        return '';
    }
    
    /**
     * Obtenha os anexos dos sermões pelos seus ids
     * 
     * @param array $ids array com os ids
     * @return array
     */
    private function getAnexos($ids): array
    {
        $dto = new SermoesDTO();
        $obj = new SermoesADO();
        
        SermoesADO::addComparison($dto, id, CMP_IN_LIST, $this->stringifyArray($ids));
        
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
     * Remova os anexos de sermões
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
        $dto = new SermoesDTO();
        $obj = new SermoesADO();
        
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
     * Obtenha as logos dos sermões pelos seus ids
     * 
     * @param array $ids array com os ids
     * @return array
     */
    private function getLogos($ids): array
    {
        $dto = new SermoesDTO();
        $obj = new SermoesADO();
        
        SermoesADO::addComparison($dto, id, CMP_IN_LIST, $this->stringifyArray($ids));
        
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
        $dto = new SermoesDTO();
        $obj = new SermoesADO();
        
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
     * Obtenha as logos (app) dos sermões pelos seus ids
     * 
     * @param array $ids array com os ids
     * @return array
     */
    private function getLogosApp($ids): array
    {
        $dto = new SermoesDTO();
        $obj = new SermoesADO();
        
        SermoesADO::addComparison($dto, id, CMP_IN_LIST, $this->stringifyArray($ids));
        
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
        if(!doIHavePermission(NblFram::$context->token, 'SermaoSave')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new SermoesDTO();
        $obj = new SermoesADO();
        
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
        $dto->igreja = NblFram::$context->data->igreja;
        $dto->serie = (empty(NblFram::$context->data->serie)) ? NULL : NblFram::$context->data->serie;
        $dto->autor = (empty(NblFram::$context->data->autor)) ? NULL : NblFram::$context->data->autor;
        $dto->titulo = NblFram::$context->data->titulo;
        $dto->chave = NblFram::$context->data->chave;
        $dto->logo = $logo;
        $dto->logo_app = $logo_app;
        $dto->data_sermao = (empty(NblFram::$context->data->data_sermao)) ? NULL : NblPHPUtil::HumanDate2DBDate(NblFram::$context->data->data_sermao);
        $dto->conteudo = NblFram::$context->data->conteudo;
        $dto->anexo = NblFram::$context->data->anexo;
        $dto->video = NblFram::$context->data->video;
        $dto->audio = NblFram::$context->data->audio;
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
     * Edita
     * 
     * @httpmethod PUT
     * @auth yes
     * @require id
     * @return array
     */
    public function edit(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'SermaoSave')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $old_anexo = $this->getAnexo(NblFram::$context->data->id);
        
        $dto = new SermoesDTO();
        $obj = new SermoesADO();
        
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
        $dto->data_sermao = (empty(NblFram::$context->data->data_sermao)) ? NULL : NblPHPUtil::HumanDate2DBDate(NblFram::$context->data->data_sermao);
        $dto->conteudo = NblFram::$context->data->conteudo;
        $dto->anexo = NblFram::$context->data->anexo;
        $dto->video = NblFram::$context->data->video;
        $dto->audio = NblFram::$context->data->audio;
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
        if(!doIHavePermission(NblFram::$context->token, 'SermaoBlock')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new SermoesDTO();
        $obj = new SermoesADO();
        
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
        if(!doIHavePermission(NblFram::$context->token, 'Sermao,Dados')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new SermoesDTO();
        $obj = new SermoesADO();
        
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
        if(!doIHavePermission(NblFram::$context->token, 'Sermao')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new SermoesDTO();
        $obj = new SermoesADO();
        
        SermoesADO::addComparison($dto, 'chave', CMP_EQUAL, NblFram::$context->data->chave);
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
        if(!doIHavePermission(NblFram::$context->token, 'Sermao,Dados')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new SermoesDTO();
        $obj = new SermoesADO();
        
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
        if(!doIHavePermission(NblFram::$context->token, 'Sermao,Dados')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }

        $dto = new SermoesDTO();
        $obj = new SermoesADO();
        $obj_count = new SermoesADO();

        $page = ($this->testInputString('page')) ? (int) NblFram::$context->data->page : -1;
        $pageSize = ($this->testInputString('pageSize')) ? (int) NblFram::$context->data->pageSize : -1;
        $pagination = $this->calcPagination($page, $pageSize);

        $searchBy = ($this->testInputString('searchBy')) ? NblFram::$context->data->searchBy : '';
        $orderBy = ($this->testInputString('orderBy')) ? NblFram::$context->data->orderBy : '';
        $groupBy = ($this->testInputString('groupBy')) ? NblFram::$context->data->groupBy : '';
        
        $stat = ($this->testInputString('stat')) ? NblFram::$context->data->stat : '';
        $igreja = ($this->testInputString('igreja')) ? NblFram::$context->data->igreja : '';
        $serie = ($this->testInputString('serie')) ? NblFram::$context->data->serie : '';
        $autor = ($this->testInputString('autor')) ? NblFram::$context->data->autor : '';
        $chave = ($this->testInputString('chave')) ? NblFram::$context->data->chave : '';
        $depois_de = ($this->testInputString('depois_de')) ? NblFram::$context->data->depois_de : '';
        $publicado_apos = ($this->testInputString('publicado_apos')) ? NblFram::$context->data->publicado_apos : '';
        
        $anexo = ($this->testInputBool('anexo')) ? true : false;
        $video = ($this->testInputBool('video')) ? true : false;
        $audio = ($this->testInputBool('audio')) ? true : false;

        $has_at_least_one_filter = false;
        
        if(!empty($searchBy))
        {   
            $has_at_least_one_filter = true;
            SermoesADO::addComparison($dto, 'titulo', CMP_INCLUDE_INSIDE, $searchBy, OP_OR);
            SermoesADO::addComparison($dto, 'conteudo', CMP_INCLUDE_INSIDE, $searchBy, OP_OR);
        }

        if(!empty($stat))
        {   
            $has_at_least_one_filter = true;
            SermoesADO::addComparison($dto, 'stat', CMP_EQUAL, $stat);
        }
        
        if(!empty($igreja))
        {   
            $has_at_least_one_filter = true;
            SermoesADO::addComparison($dto, 'igreja', CMP_EQUAL, $igreja);
        }
        
        if(!empty($serie))
        {   
            $has_at_least_one_filter = true;
            SermoesADO::addComparison($dto, 'serie', CMP_EQUAL, $serie);
        }
        
        if(!empty($autor))
        {   
            $has_at_least_one_filter = true;
            SermoesADO::addComparison($dto, 'autor', CMP_EQUAL, $autor);
        }
        
        if(!empty($chave))
        {   
            $has_at_least_one_filter = true;
            SermoesADO::addComparison($dto, 'chave', CMP_EQUAL, $chave);
        }
        
        if(!empty($depois_de))
        {   
            $has_at_least_one_filter = true;
            if($this->isDateStr($depois_de)) {
                SermoesADO::addComparison($dto, 'data_sermao', CMP_NOT_NULL, '', OP_AND, true);
                SermoesADO::addComparison($dto, 'data_sermao', CMP_GREATER_THEN_DATE, NblPHPUtil::HumanDate2DBDate($depois_de), OP_AND, true);
            }
            else if($this->isDateSqlStr($depois_de)) {
                SermoesADO::addComparison($dto, 'data_sermao', CMP_NOT_NULL, '', OP_AND, true);
                SermoesADO::addComparison($dto, 'data_sermao', CMP_GREATER_THEN_DATE, $depois_de, OP_AND, true);
            }
        }
        
        if(!empty($publicado_apos))
        {   
            $has_at_least_one_filter = true;
            if($this->isDateStr($publicado_apos)) {
                SermoesADO::addComparison($dto, 'time_cad', CMP_GREATER_THEN_DATE, NblPHPUtil::HumanDate2DBDate($publicado_apos));
            }
            else if($this->isDateSqlStr($publicado_apos)) {
                SermoesADO::addComparison($dto, 'time_cad', CMP_GREATER_THEN_DATE, $publicado_apos);
            }
        }
        
        if($anexo)
        {   
            $has_at_least_one_filter = true;
            SermoesADO::addComparison($dto, 'anexo', CMP_NOT_EMPTY);
        }
        
        if($video)
        {   
            $has_at_least_one_filter = true;
            SermoesADO::addComparison($dto, 'video', CMP_NOT_EMPTY);
        }
        
        if($audio)
        {   
            $has_at_least_one_filter = true;
            SermoesADO::addComparison($dto, 'audio', CMP_NOT_EMPTY);
        }
        
        if(!empty($orderBy))
        {
            $has_at_least_one_filter = true;
            $order_by_v = explode(',',$orderBy);
            SermoesADO::addOrdering($dto, $order_by_v[0], ($order_by_v[1] == 'asc') ? ORDER_ASC : ORDER_DESC);
        }

        if(!empty($groupBy))
        {
            $has_at_least_one_filter = true;
            $groups = explode(',',$groupBy);
            foreach($groups as $g) {
                SermoesADO::addGrouping($dto, $g);
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
                        'igreja' => $it->igreja,
                        'serie' => $it->serie,
                        'autor' => $it->autor,
                        'titulo' => $it->titulo,
                        'chave' => $it->chave,
                        'logo' => $it->logo,
                        'logo_app' => $it->logo_app,
                        'data_sermao' => $it->data_sermao,
                        'conteudo' => $it->conteudo,
                        'anexo' => $it->anexo,
                        'video' => $it->video,
                        'audio' => $it->audio,
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
        if(!doIHavePermission(NblFram::$context->token, 'SermaoRemove')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new SermoesDTO();
        $obj = new SermoesADO();
        
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
        if(!doIHavePermission(NblFram::$context->token, 'SermaoRemove')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new SermoesADO();
        
        $anexos = $this->getAnexos(NblFram::$context->data->ids);
        $logos = $this->getLogos(NblFram::$context->data->ids);
        $logos_app = $this->getLogosApp(NblFram::$context->data->ids);
        
        foreach(NblFram::$context->data->ids as $id) {
            $dto = new SermoesDTO();
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

    /**
     * 
     * Remove anexo
     * 
     * @httpmethod DELETE
     * @auth yes
     * @require id
     * @return array
     */
    public function removeAnexo(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'SermaoSave')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $old_anexo = $this->getAnexo(NblFram::$context->data->id);
        $this->removeAnexos(array($old_anexo));
        
        $dto = new SermoesDTO();
        $obj = new SermoesADO();
        
        $dto->edit = true;
        $dto->id = NblFram::$context->data->id;
        $dto->anexo = '';
        $dto->last_mod = date('Y-m-d H:i:s');
        $dto->last_amod = NblFram::$context->token['data']['nome'];
        
        $obj->add($dto);
        if($obj->sync())
        {
            return array('status' => 'ok', 'success' => true);
        }
        else
        {
            return array('status' => 'no', 'success' => false, 'msg' => 'Erro na operação', 'errs' => $obj->getErrs());
        }
    }
}

