<?php
require_once ADO_PATH . '/Familias.class.php'; 
require_once DAO_PATH . '/FamiliasDAO.class.php'; 

/**
 * API REST de Familias
 */
class FamiliasWS extends WSUtil
{
    /**
     * 
     * @var FamiliasWS singleton instance
     */
    private static $_Instance = null;
    
    /**
     * Get singleton instance
     * 
     * @return \FamiliasWS
     */
    public static function getInstance() {
        if(self::$_Instance == null) {
            self::$_Instance = new self();
        }
        return self::$_Instance;
    }
    
    /**
     * Obtem a família pelo id da pessoa
     * 
     * @param string $pessoa id da pessoa
     * @return array
     */
    public static function getByPessoa($pessoa): array
    {
        $dto = new FamiliasDTO();
        $obj = new FamiliasADO();
        
        FamiliasADO::addComparison($dto, 'pessoa', CMP_EQUAL, $pessoa);
        
        $result = array();
        if($obj->getAllbyParam($dto))
        {
            $obj->iterate();
            while($obj->hasNext())
            {
                $it = $obj->next();
                if(!is_null($it->id))
                {
                    $result[] = array(
                        'id' => $it->id,
                        'pessoa' => $it->pessoa,
                        'parente' => $it->parente,
                        'tipo' => $it->tipo,
                        'nome_externo' => $it->nome_externo
                    );
                }
            }
        }
        
        return $result;
    } 
    
    public static function getFilhosByPessoa($pessoa): array
    {
        $dto = new FamiliasDTO();
        $obj = new FamiliasADO();
        
        FamiliasADO::addComparison($dto, 'pessoa', CMP_EQUAL, $pessoa);
        FamiliasADO::addComparison($dto, 'tipo', CMP_EQUAL, RelacaoFamiliar::FILHO, OP_OR, true);
        FamiliasADO::addComparison($dto, 'tipo', CMP_EQUAL, RelacaoFamiliar::FILHA, OP_OR, true);
        
        $result = array();
        if($obj->getAllbyParam($dto))
        {
            $obj->iterate();
            while($obj->hasNext())
            {
                $it = $obj->next();
                if(!is_null($it->id))
                {
                    $result[] = array(
                        'id' => $it->id,
                        'pessoa' => $it->pessoa,
                        'parente' => $it->parente,
                        'tipo' => $it->tipo,
                        'nome_externo' => $it->nome_externo
                    );
                }
            }
        }
        
        return $result;
    } 
    
    public static function getConjugeByPessoa($pessoa): object
    {
        $dto = new FamiliasDTO();
        $obj = new FamiliasADO();
        
        FamiliasADO::addComparison($dto, 'pessoa', CMP_EQUAL, $pessoa);
        FamiliasADO::addComparison($dto, 'tipo', CMP_EQUAL, RelacaoFamiliar::ESPOSO, OP_OR, true);
        FamiliasADO::addComparison($dto, 'tipo', CMP_EQUAL, RelacaoFamiliar::ESPOSA, OP_OR, true);
        
        $result = new stdClass();
        if($obj->getAllbyParam($dto))
        {
            $obj->iterate();
            while($obj->hasNext())
            {
                $it = $obj->next();
                if(!is_null($it->id))
                {
                    $result->id = $it->id;
                    $result->pessoa = $it->pessoa;
                    $result->parente = $it->parente;
                    $result->tipo = $it->tipo;
                    $result->nome_externo = $it->nome_externo;
                }
            }
        }
        
        return $result;
    }
    
    /**
     * Gera o objeto de dados (DTO) de familia
     * 
     * @param object $data objeto com os dados vindos da interface
     * @param int $mode flag para indicar se é adição, edição ou remoção
     * @return \HistoricoMembresiaDTO objeto de dados
     */
    public static function generateDTO($data, $mode): \FamiliasDTO
    {
        $dto = new FamiliasDTO();
        $instance = self::getInstance();

        if($mode == DTOMode::ADD) {
            $dto->add = true;
            $dto->id = NblPHPUtil::makeNumericId();
            $dto->pessoa = $data->pessoa;
        }
        else if($mode == DTOMode::EDIT) {
            $dto->edit = true;
            $dto->id = $data->id;
        }
        else if($mode == DTOMode::DELETE) {
            $dto->delete = true;
            $dto->id = $data->id;
            return $dto;
        }
                
        $dto->parente = (!$instance->testInputString('parente', $data)) ? NULL : $data->parente;
        $dto->tipo = (!$instance->testInputString('tipo', $data)) ? RelacaoFamiliar::VOID : $data->tipo;
        $dto->nome_externo = (!$instance->testInputString('nome_externo', $data)) ? '' : $data->nome_externo;

        return $dto;
    }
    
    /**
     * Associa uma pessoa a uma família
     * 
     * @param object $data
     * @param type $id
     * @param type $errs
     * @return bool
     */
    public static function associate($parente, $pessoa, $tipo, $externo, &$id, &$errs): bool
    {
        $obj = new FamiliasADO();
        
        $data = new stdClass();
        $data->parente = $parente;
        $data->pessoa = $pessoa;
        $data->tipo = $tipo;
        $data->nome_externo = (is_null($parente)) ? $externo : '';
        
        $dto = FamiliasWS::generateDTO($data, DTOMode::ADD);
        $obj->add($dto);
        
        if($obj->sync())
        {
            $id = $dto->id;
            return true;
        }
        else
        {
            $errs = $obj->getErrs();
            return false;
        }
    }
    
    public static function updateAssociation($id, $parente, $tipo, $externo, &$errs): bool
    {
        $obj = new FamiliasADO();
        
        $data = new stdClass();
        $data->id = $id;
        $data->parente = $parente;
        $data->tipo = $tipo;
        $data->nome_externo = (is_null($parente)) ? $externo : '';
        
        $dto = FamiliasWS::generateDTO($data, DTOMode::EDIT);
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
    
    public static function desassociate($id, &$errs): bool
    {
        $obj = new FamiliasADO();
        
        $data = new stdClass();
        $data->id = $id;
        
        $dto = FamiliasWS::generateDTO($data, DTOMode::DELETE);
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
        if(!doIHavePermission(NblFram::$context->token, '')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new FamiliasDTO();
        $obj = new FamiliasADO();
        
        $dto->edit = true;
        $dto->id = NblFram::$context->data->id;
        $dto->pessoa = NblFram::$context->data->pessoa;
        $dto->parente = NblFram::$context->data->parente;
        $dto->tipo = NblFram::$context->data->tipo;
        
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
        if(!doIHavePermission(NblFram::$context->token, '')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new FamiliasDTO();
        $obj = new FamiliasADO();
        
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
        if(!doIHavePermission(NblFram::$context->token, '')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }

        $dto = new FamiliasDTO();
        $obj = new FamiliasADO();
        $obj_count = new FamiliasADO();

        $page = ($this->testInputString('page')) ? (int) NblFram::$context->data->page : -1;
        $pageSize = ($this->testInputString('pageSize')) ? (int) NblFram::$context->data->pageSize : -1;
        $pagination = $this->calcPagination($page, $pageSize);

        $searchBy = ($this->testInputString('searchBy')) ? NblFram::$context->data->searchBy : '';
        $orderBy = ($this->testInputString('orderBy')) ? NblFram::$context->data->orderBy : '';
        $groupBy = ($this->testInputString('groupBy')) ? NblFram::$context->data->groupBy : '';

        $has_at_least_one_filter = false;

        if(!empty($searchBy))
        {   
            $has_at_least_one_filter = true;
            FamiliasADO::addComparison($dto, '', CMP_INCLUDE_INSIDE, $searchBy);
            FamiliasADO::addComparison($dto, '', CMP_INCLUDE_INSIDE, $searchBy, OP_OR);
        }
        
        if(!empty($orderBy))
        {
            $has_at_least_one_filter = true;
            $order_by_v = explode(',',$orderBy);
            FamiliasADO::addOrdering($dto, $order_by_v[0], ($order_by_v[1] == 'asc') ? ORDER_ASC : ORDER_DESC);
        }

        if(!empty($groupBy))
        {
            $has_at_least_one_filter = true;
            $groups = explode(',',$groupBy);
            foreach($groups as $g) {
                FamiliasADO::addGrouping($dto, $g);
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
                        'pessoa' => $it->pessoa,
                        'parente' => $it->parente,
                        'tipo' => $it->tipo
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
        if(!doIHavePermission(NblFram::$context->token, '')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new FamiliasDTO();
        $obj = new FamiliasADO();
        
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
        if(!doIHavePermission(NblFram::$context->token, '')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new FamiliasADO();
        
        foreach(NblFram::$context->data->ids as $id) {
            $dto = new FamiliasDTO();
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

