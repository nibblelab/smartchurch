<?php
require_once ADO_PATH . '/Mural.class.php'; 
require_once DAO_PATH . '/MuralDAO.class.php'; 
require_once HLP_PATH . '/ImageHelper.class.php'; 
require_once HLP_PATH . '/DocHelper.class.php'; 
require_once WS_PATH . '/pessoas.ws.php'; 
require_once WS_PATH . '/bookmarks.ws.php'; 

/**
 * API REST de Mural
 */
class MuralWS extends WSUtil
{
    /**
     * 
     * @var \MuralWS singleton instance
     */
    private static $_Instance = null;
    
    /**
     * Get singleton instance
     * 
     * @return \MuralWS
     */
    public static function getInstance(): \MuralWS {
        if(self::$_Instance == null) {
            self::$_Instance = new self();
        }
        return self::$_Instance;
    }
        
    /**
     * Obtenha a imagem 
     * 
     * @return string
     */
    private function getImg(): string
    {
        $dto = new MuralDTO();
        $obj = new MuralADO();
        
        $dto->id = NblFram::$context->data->id;
        if(!is_null($obj->get($dto)))
        {
            $d = $obj->getDTODataObject();
            return $d->img;
        }
        else 
        {
            return '';
        }
    }
    
    /**
     * Obtenha as imagens pelos seus ids
     * 
     * @param array $ids array com os ids
     * @return array
     */
    private function getImgs($ids): array
    {
        $dto = new MuralDTO();
        $obj = new MuralADO();
        
        MuralADO::addComparison($dto, id, CMP_IN_LIST, $this->stringifyArray($ids));
        
        $result = array();
        if($obj->getAllbyParam($dto))
        {
            $obj->iterate();
            while($obj->hasNext())
            {
                $it = $obj->next();
                if(!is_null($it->id))
                {
                    $result[] = $it->img;
                }
            }
        }
        
        return $result;
    }
    
    /**
     * Remova as imagens 
     * 
     * @param array $imagens array com os nomes dos anexos
     * @return void
     */
    private function removeImgs($imagens): void
    {
        if(empty($imagens)) {
            return;
        }
        
        $helper = new DocHelper();
        
        $imagens_to_remove = [];
        foreach($imagens as $imagem)
        {
            $imagens_to_remove[] = RSC_PATH . '/' . $imagem;
        }
        
        $helper->delete($imagens_to_remove);
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
        if(!doIHavePermission(NblFram::$context->token, 'MuralSave')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new MuralDTO();
        $obj = new MuralADO();
        
        // trate uma possível imagem
        $imagem = '';
        if($this->isBase64Img(NblFram::$context->data->arquivo->content)) {
            $imagehlp = new ImageHelper();
            $image = $imagehlp->generateFromBase64(NblFram::$context->data->arquivo->content, 
                                                    NblFram::$context->data->arquivo->name, 
                                                    RSC_PATH, 
                                                    false);
            $imagem = $image->path;
        }
        
        $dto->add = true;
        $dto->id = NblPHPUtil::makeNumericId();
        $dto->ref = NblFram::$context->data->ref;
        $dto->ref_tp = NblFram::$context->data->ref_tp;
        $dto->titulo = NblFram::$context->data->titulo;
        $dto->chave = NblFram::$context->data->chave;
        $dto->img = $imagem;
        $dto->conteudo = NblFram::$context->data->conteudo;
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
        if(!doIHavePermission(NblFram::$context->token, 'MuralSave')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new MuralDTO();
        $obj = new MuralADO();
        
        // veja se tem uma imagem anterior
        $old_imagem = $this->getImg();

        // trate uma possível remoção
        $image_rm = false;
        if(property_exists(NblFram::$context->data->arquivo, 'removed')) {
            $image_rm = NblFram::$context->data->arquivo->removed;
        }
        
        // trate uma possível imagem 
        $imagem = $old_imagem;
        if($this->isBase64Img(NblFram::$context->data->arquivo->content)) {
            $imagehlp = new ImageHelper();
            $image = $imagehlp->generateFromBase64(NblFram::$context->data->arquivo->content, 
                                                    NblFram::$context->data->arquivo->name, 
                                                    RSC_PATH, 
                                                    false);
            $imagem = $image->path;
        }
        
        $dto->edit = true;
        $dto->id = NblFram::$context->data->id;
        $dto->titulo = NblFram::$context->data->titulo;
        $dto->chave = NblFram::$context->data->chave;
        $dto->img = ($image_rm) ? '' : $imagem;
        $dto->conteudo = NblFram::$context->data->conteudo;
        $dto->video = NblFram::$context->data->video;
        $dto->audio = NblFram::$context->data->audio;
        $dto->destinatarios = NblFram::$context->data->destinatarios;
        $dto->last_mod = date('Y-m-d H:i:s');
        $dto->last_amod = NblFram::$context->token['data']['nome'];
        
        $obj->add($dto);
        if($obj->sync())
        {
            // remova uma possível imagem anterior
            if($imagem != $old_imagem || $image_rm) {
                $this->removeImgs(array($old_imagem));
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
        if(!doIHavePermission(NblFram::$context->token, 'MuralBlock')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new MuralDTO();
        $obj = new MuralADO();
        
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
        if(!doIHavePermission(NblFram::$context->token, 'Mural,Dados')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new MuralDTO();
        $obj = new MuralADO();
        
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
     * @auth no
     * @require id
     * @return array
     */
    public function getById(): array
    {
        $dto = new MuralDTO();
        $obj = new MuralADO();
        
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
     * @require chave
     * @return array
     */
    public function bychave(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'Mural')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new MuralDTO();
        $obj = new MuralADO();
        
        MuralADO::addComparison($dto, 'chave', CMP_EQUAL, NblFram::$context->data->chave);
        if(!is_null($obj->getBy($dto)))
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
        if(!doIHavePermission(NblFram::$context->token, 'Mural,Dados')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }

        $dto = new MuralDTO();
        $obj = new MuralADO();
        $obj_count = new MuralADO();

        $page = ($this->testInputString('page')) ? (int) NblFram::$context->data->page : -1;
        $pageSize = ($this->testInputString('pageSize')) ? (int) NblFram::$context->data->pageSize : -1;
        $pagination = $this->calcPagination($page, $pageSize);

        $searchBy = ($this->testInputString('searchBy')) ? NblFram::$context->data->searchBy : '';
        $orderBy = ($this->testInputString('orderBy')) ? NblFram::$context->data->orderBy : '';
        $groupBy = ($this->testInputString('groupBy')) ? NblFram::$context->data->groupBy : '';
        
        $stat = ($this->testInputString('stat')) ? NblFram::$context->data->stat : '';
        $ref = ($this->testInputString('ref')) ? NblFram::$context->data->ref : '';
        $ref_tp = ($this->testInputString('ref_tp')) ? NblFram::$context->data->ref_tp : '';
        $chave = ($this->testInputString('chave')) ? NblFram::$context->data->chave : '';
        $publicado_apos = ($this->testInputString('publicado_apos')) ? NblFram::$context->data->publicado_apos : '';
        $destinatarios = ($this->testInputString('destinatarios')) ? NblFram::$context->data->destinatarios : '';
        
        $igreja = ($this->testInputString('igreja')) ? NblFram::$context->data->igreja : '';
        $pessoa = ($this->testInputString('pessoa')) ? NblFram::$context->data->pessoa : '';
        
        $video = ($this->testInputBool('video')) ? true : false;
        $audio = ($this->testInputBool('audio')) ? true : false;
        $bookmarded = ($this->testInputBool('bookmarded')) ? true : false;

        $has_at_least_one_filter = false;
        
        if(!empty($searchBy))
        {   
            $has_at_least_one_filter = true;
            MuralADO::addComparison($dto, 'titulo', CMP_INCLUDE_INSIDE, $searchBy, OP_OR);
            MuralADO::addComparison($dto, 'conteudo', CMP_INCLUDE_INSIDE, $searchBy, OP_OR);
        }

        if(!empty($stat))
        {   
            $has_at_least_one_filter = true;
            MuralADO::addComparison($dto, 'stat', CMP_EQUAL, $stat);
        }
                
        if(!empty($chave))
        {   
            $has_at_least_one_filter = true;
            MuralADO::addComparison($dto, 'chave', CMP_EQUAL, $chave);
        }
        
        if(!empty($ref))
        {   
            $has_at_least_one_filter = true;
            MuralADO::addComparison($dto, 'ref', CMP_EQUAL, $ref);
        }
        
        if(!empty($ref_tp))
        {   
            $has_at_least_one_filter = true;
            MuralADO::addComparison($dto, 'ref_tp', CMP_EQUAL, $ref_tp);
        }
        
        if(!empty($igreja))
        {   
            $has_at_least_one_filter = true;
            MuralADO::addComparison($dto, 'ref', CMP_EQUAL, $igreja);
            MuralADO::addComparison($dto, 'ref_tp', CMP_EQUAL, References::IGREJA);
        }
        
        if(!empty($publicado_apos))
        {   
            $has_at_least_one_filter = true;
            if($this->isDateStr($publicado_apos)) {
                MuralADO::addComparison($dto, 'time_cad', CMP_GREATER_THEN_DATE, NblPHPUtil::HumanDate2DBDate($publicado_apos));
            }
            else if($this->isDateSqlStr($publicado_apos)) {
                MuralADO::addComparison($dto, 'time_cad', CMP_GREATER_THEN_DATE, $publicado_apos);
            }
        }
        
        if(!empty($destinatarios))
        {   
            $has_at_least_one_filter = true;
            $destinatarios_arr = explode(';', $destinatarios);
            foreach($destinatarios_arr as $destinatario) {
                MuralADO::addComparison($dto, 'destinatarios', CMP_INCLUDE_INSIDE, '"checked":true,"label":"'.$destinatario.'"', OP_OR, true);
            }
        }
        
        if($video)
        {   
            $has_at_least_one_filter = true;
            MuralADO::addComparison($dto, 'video', CMP_NOT_EMPTY);
        }
        
        if($audio)
        {   
            $has_at_least_one_filter = true;
            MuralADO::addComparison($dto, 'audio', CMP_NOT_EMPTY);
        }
        
        $bookmarks = array();
        if(!empty($pessoa) && !$bookmarded) {
            $bookmarks = BookmarksWS::getAllIdsByMural($pessoa);
        }
        else if(empty($pessoa) && $bookmarded) {
            $bookmarks = BookmarksWS::getAllIdsByMural();
            if(!empty($bookmarks)) {
                $ids = $this->stringifyArray($bookmarks, ',', true, true);
                if(!empty($ids)) {
                    $has_at_least_one_filter = true;
                    MuralADO::addComparison($dto, 'id', CMP_IN_LIST, $ids);
                }
            }
        }
        else if(!empty($pessoa) && $bookmarded) {
            $bookmarks = BookmarksWS::getAllIdsByMural($pessoa);
            if(!empty($bookmarks)) {
                $ids = $this->stringifyArray($bookmarks, ',', true, true);
                if(!empty($ids)) {
                    $has_at_least_one_filter = true;
                    MuralADO::addComparison($dto, 'id', CMP_IN_LIST, $ids);
                }
            }
        }
        
        if(!empty($orderBy))
        {
            $has_at_least_one_filter = true;
            $order_by_v = explode(',',$orderBy);
            MuralADO::addOrdering($dto, $order_by_v[0], ($order_by_v[1] == 'asc') ? ORDER_ASC : ORDER_DESC);
        }

        if(!empty($groupBy))
        {
            $has_at_least_one_filter = true;
            $groups = explode(',',$groupBy);
            foreach($groups as $g) {
                MuralADO::addGrouping($dto, $g);
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
                    $marked = (!empty($bookmarks) && array_key_exists($it->id, $bookmarks));
                    $bookmark_id = ($marked) ? $bookmarks[$it->id] : '';
                    
                    $result[] = array(
                        'id' => $it->id,
                        'ref' => $it->ref,
                        'ref_tp' => $it->ref_tp,
                        'titulo' => $it->titulo,
                        'chave' => $it->chave,
                        'img' => $it->img,
                        'conteudo' => $it->conteudo,
                        'video' => $it->video,
                        'audio' => $it->audio,
                        'destinatarios' => $it->destinatarios,
                        'stat' => $it->stat,
                        'marked' => $marked,
                        'mark_id' => $bookmark_id,
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
     * Busca 
     * 
     * @httpmethod GET
     * @auth yes
     * @return array
     */
    public function bookmarked(): array
    {
        NblFram::$context->data->bookmarded = true;
        return $this->all();
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
        if(!doIHavePermission(NblFram::$context->token, 'MuralRemove')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new MuralDTO();
        $obj = new MuralADO();
        
        $dto->id = NblFram::$context->data->id;
        $r = $obj->get($dto);
        if(!is_null($r))
        {
            $r->delete = true;
            if($obj->sync())
            {
                $this->removeImgs(array($r->img));
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
        if(!doIHavePermission(NblFram::$context->token, 'MuralRemove')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new MuralADO();
        
        $imagens = $this->getImgs(NblFram::$context->data->ids);
        
        foreach(NblFram::$context->data->ids as $id) {
            $dto = new MuralDTO();
            $dto->delete = true;
            $dto->id = $id;
            
            $obj->add($dto);
        }
        
        if($obj->sync())
        {
            $this->removeImgs($imagens);
            
            return array('status' => 'ok', 'success' => true);
        }
        else 
        {
            return array('status' => 'no', 'success' => false, 'msg' => 'Recursos não encontrados');
        }
    }
}

