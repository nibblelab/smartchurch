<?php
require_once ADO_PATH . '/FreqSumarios.class.php'; 
require_once DAO_PATH . '/FreqSumariosDAO.class.php'; 

/**
 * API REST de FreqSumarios
 */
class FreqSumariosWS extends WSUtil
{
    
    /**
     * Crie o sumário
     * 
     * @param string $sala id da sala
     * @param string $dia dia do sumário. Formato: DD/MM/YYYY
     * @param int $presentes quantidade de presentes
     * @param int $ausentes quantidade de ausentes
     * @param int $visitantes quantidade de visitantes
     * @param int $total quantidade total de registros
     * @return bool
     */
    public static function create($sala, $dia, $presentes, $ausentes, $visitantes, $total): bool
    {
        $dto = new FreqSumariosDTO();
        $obj = new FreqSumariosADO();
        
        $dto->add = true;
        $dto->id = NblPHPUtil::makeNumericId();
        $dto->sala = $sala;
        $dto->dia = $dia;
        $dto->presentes = $presentes;
        $dto->ausentes = $ausentes;
        $dto->visitantes = $visitantes;
        $dto->total = $total;
        
        $obj->add($dto);
        return ($obj->sync());
    }

    /**
     * Edite o sumário
     * 
     * @param string $id id do sumário
     * @param string $sala id da sala
     * @param string $dia dia do sumário. Formato: DD/MM/YYYY
     * @param int $presentes quantidade de presentes
     * @param int $ausentes quantidade de ausentes
     * @param int $visitantes quantidade de visitantes
     * @param int $total quantidade total de registros
     * @return array
     */
    public static function edit($id, $sala, $dia, $presentes, $ausentes, $visitantes, $total): bool
    {
        $dto = new FreqSumariosDTO();
        $obj = new FreqSumariosADO();
        
        $dto->edit = true;
        $dto->id = $id;
        $dto->sala = $sala;
        $dto->dia = $dia;
        $dto->presentes = $presentes;
        $dto->ausentes = $ausentes;
        $dto->visitantes = $visitantes;
        $dto->total = $total;
        
        $obj->add($dto);
        return ($obj->sync());
    }
    
    /**
     * Busque o sumário por sala e dia
     * 
     * @param string $sala id da sala
     * @param string $dia dia do sumário. Formato: YYYY-MM-DD
     * @return string
     */
    public static function getBySalaAndDia($sala, $dia): string
    {
        $dto = new FreqSumariosDTO();
        $obj = new FreqSumariosADO();
        
        FreqSumariosADO::addComparison($dto, 'sala', CMP_EQUAL, $sala);
        FreqSumariosADO::addComparison($dto, 'dia', CMP_EQUAL, $dia);
        
        if(!is_null($obj->getBy($dto)))
        {
            $d = $obj->getDTODataObject();
            return $d->id;
        }
        else 
        {
            return '';
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
        if(!doIHavePermission(NblFram::$context->token, 'SumarioEBD')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }

        $dto = new FreqSumariosDTO();
        $obj = new FreqSumariosADO();
        $obj_count = new FreqSumariosADO();

        $page = ($this->testInputString('page')) ? (int) NblFram::$context->data->page : -1;
        $pageSize = ($this->testInputString('pageSize')) ? (int) NblFram::$context->data->pageSize : -1;
        $pagination = $this->calcPagination($page, $pageSize);

        $searchBy = ($this->testInputString('searchBy')) ? NblFram::$context->data->searchBy : '';
        $orderBy = ($this->testInputString('orderBy')) ? NblFram::$context->data->orderBy : '';
        $groupBy = ($this->testInputString('groupBy')) ? NblFram::$context->data->groupBy : '';
        
        $sala = ($this->testInputString('sala')) ? NblFram::$context->data->sala : '';
        $dia = ($this->testInputString('dia')) ? NblFram::$context->data->dia : '';

        $has_at_least_one_filter = false;

        if(!empty($sala)) {
            $has_at_least_one_filter = true;
            FreqSumariosADO::addComparison($dto, 'sala', CMP_EQUAL, $sala);
        }
        
        // filtro por dia no searchBy ou no campo dia
        $has_range_filter = false;
        if(!empty($dia)) {
            if($this->isDateRangeStr($dia)) {
                // pesquisa por faixa de datas
                $range = $this->getDateRangeFromStr($dia);
                if(!empty($range)) {
                    FrequenciasADO::addComparison($dto, 'dia', CMP_GREATER_THEN, NblPHPUtil::HumanDate2DBDate($range['start']), OP_AND, true);
                    FrequenciasADO::addComparison($dto, 'dia', CMP_LESSER_THEN, NblPHPUtil::HumanDate2DBDate($range['end']), OP_AND, true);
                    $has_range_filter = true;
                }
            }
            else if($this->isDateStr($dia)) {
                // pesquisa por data exata
                FrequenciasADO::addComparison($dto, 'dia', CMP_EQUAL, NblPHPUtil::HumanDate2DBDate($dia));
                $has_range_filter = true;
            }
        }
        else if(!empty($searchBy))
        {
            $dia = $searchBy;
            if($this->isDateRangeStr($dia)) {
                // pesquisa por faixa de datas
                $range = $this->getDateRangeFromStr($dia);
                if(!empty($range)) {
                    FrequenciasADO::addComparison($dto, 'dia', CMP_GREATER_THEN, NblPHPUtil::HumanDate2DBDate($range['start']), OP_AND, true);
                    FrequenciasADO::addComparison($dto, 'dia', CMP_LESSER_THEN, NblPHPUtil::HumanDate2DBDate($range['end']), OP_AND, true);
                    $has_range_filter = true;
                }
            }
            else if($this->isDateStr($dia)) {
                // pesquisa por data exata
                FrequenciasADO::addComparison($dto, 'dia', CMP_EQUAL, NblPHPUtil::HumanDate2DBDate($dia));
                $has_range_filter = true;
            }
        }
        
        if(!empty($orderBy))
        {
            $has_at_least_one_filter = true;
            $order_by_v = explode(',',$orderBy);
            FreqSumariosADO::addOrdering($dto, $order_by_v[0], ($order_by_v[1] == 'asc') ? ORDER_ASC : ORDER_DESC);
        }

        if(!empty($groupBy))
        {
            $has_at_least_one_filter = true;
            $groups = explode(',',$groupBy);
            foreach($groups as $g) {
                FreqSumariosADO::addGrouping($dto, $g);
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
                        'sala' => $it->sala,
                        'dia' => $it->dia,
                        'presentes' => $it->presentes,
                        'ausentes' => $it->ausentes,
                        'visitantes' => $it->visitantes,
                        'total' => $it->total
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

