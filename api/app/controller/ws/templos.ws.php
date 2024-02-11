<?php
require_once ADO_PATH . '/Igrejas.class.php'; 
require_once DAO_PATH . '/IgrejasDAO.class.php'; 

/**
 * API REST de Templos (igrejas, congregações e pontos)
 */
class TemplosWS extends WSUtil
{
    /**
     * 
     * @var \TemplosWS singleton instance
     */
    private static $_Instance = null;
    
    /**
     * Get singleton instance
     * 
     * @return \TemplosWS
     */
    public static function getInstance(): \TemplosWS {
        if(self::$_Instance == null) {
            self::$_Instance = new self();
        }
        return self::$_Instance;
    }
    
    /**
     * Busque pelo id
     * 
     * @param string $id
     * @return \IgrejasDTO|null
     */
    public static function getById($id): ?\IgrejasDTO
    {
        $dto = new IgrejasDTO();
        $obj = new IgrejasADO();
        
        $dto->id = $id;
        return $obj->get($dto);
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
        $perms = array('Igreja','Ponto','Congregacao','Dados',
            'IgrejaFederacao','PontoFederacao','CongregacaoFederacao',
            'IgrejaSinodal','PontoSinodal','CongregacaoSinodal');
        if(!doIHavePermission(NblFram::$context->token, $this->stringifyArray($perms, ',', false))) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }

        $dto = new IgrejasDTO();
        $obj = new IgrejasADO();
        $obj_count = new IgrejasADO();

        $page = ($this->testInputString('page')) ? (int) NblFram::$context->data->page : -1;
        $pageSize = ($this->testInputString('pageSize')) ? (int) NblFram::$context->data->pageSize : -1;
        $pagination = $this->calcPagination($page, $pageSize);

        $searchBy = ($this->testInputString('searchBy')) ? NblFram::$context->data->searchBy : '';
        $orderBy = ($this->testInputString('orderBy')) ? NblFram::$context->data->orderBy : '';
        $groupBy = ($this->testInputString('groupBy')) ? NblFram::$context->data->groupBy : '';
        
        $stat = ($this->testInputString('stat')) ? NblFram::$context->data->stat : '';
        $sinodo = ($this->testInputString('sinodo')) ? NblFram::$context->data->sinodo : '';
        $presbiterio = ($this->testInputString('presbiterio')) ? NblFram::$context->data->presbiterio : '';
        $igreja = ($this->testInputString('igreja')) ? NblFram::$context->data->igreja : '';

        $has_at_least_one_filter = false;

        if(!empty($searchBy))
        {   
            $has_at_least_one_filter = true;
            IgrejasADO::addComparison($dto, 'nome', CMP_INCLUDE_INSIDE, $searchBy, OP_OR);
            IgrejasADO::addComparison($dto, 'sigla', CMP_INCLUDE_INSIDE, $searchBy, OP_OR);
        }

        if(!empty($stat))
        {   
            $has_at_least_one_filter = true;
            IgrejasADO::addComparison($dto, 'stat', CMP_EQUAL, $stat);
        }

        if(!empty($sinodo))
        {   
            $has_at_least_one_filter = true;
            IgrejasADO::addComparison($dto, 'sinodo', CMP_EQUAL, $sinodo);
        }

        if(!empty($presbiterio))
        {   
            $has_at_least_one_filter = true;
            IgrejasADO::addComparison($dto, 'presbiterio', CMP_EQUAL, $presbiterio);
        }

        if(!empty($igreja))
        {   
            $has_at_least_one_filter = true;
            IgrejasADO::addComparison($dto, 'igreja', CMP_EQUAL, $igreja);
        }
        
        if(!empty($orderBy))
        {
            $has_at_least_one_filter = true;
            $order_by_v = explode(',',$orderBy);
            IgrejasADO::addOrdering($dto, $order_by_v[0], ($order_by_v[1] == 'asc') ? ORDER_ASC : ORDER_DESC);
        }

        if(!empty($groupBy))
        {
            $has_at_least_one_filter = true;
            $groups = explode(',',$groupBy);
            foreach($groups as $g) {
                IgrejasADO::addGrouping($dto, $g);
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
                        'sinodo' => $it->sinodo,
                        'presbiterio' => $it->presbiterio,
                        'igreja' => $it->igreja,
                        'nome' => $it->nome,
                        'fundacao' => $it->fundacao,
                        'telefone' => $it->telefone,
                        'email' => $it->email,
                        'endereco' => $it->endereco,
                        'numero' => $it->numero,
                        'complemento' => $it->complemento,
                        'bairro' => $it->bairro,
                        'cidade' => $it->cidade,
                        'uf' => $it->uf,
                        'cep' => $it->cep,
                        'site' => $it->site,
                        'facebook' => $it->facebook,
                        'instagram' => $it->instagram,
                        'youtube' => $it->youtube,
                        'vimeo' => $it->vimeo,
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

}

