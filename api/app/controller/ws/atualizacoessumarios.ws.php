<?php
require_once ADO_PATH . '/AtualizacoesSumarios.class.php'; 
require_once DAO_PATH . '/AtualizacoesSumariosDAO.class.php'; 

/**
 * API REST de AtualizacoesSumarios
 */
class AtualizacoesSumariosWS extends WSUtil
{
    /**
     * 
     * @var \AtualizacoesSumariosWS singleton instance
     */
    private static $_Instance = null;
    
    /**
     * Get singleton instance
     * 
     * @return \AtualizacoesSumariosWS
     */
    public static function getInstance(): \AtualizacoesSumariosWS {
        if(self::$_Instance == null) {
            self::$_Instance = new self();
        }
        return self::$_Instance;
    }
    
    /**
     * Crie registro de atualização de sumário
     * 
     * @param string $sala id da sala
     * @param string $dia dia do sumário. Formato: DD/MM/YYYY
     * @return bool
     */
    public static function create($sala, $dia): bool
    {
        $dto = new AtualizacoesSumariosDTO();
        $obj = new AtualizacoesSumariosADO();
        
        $dto->add = true;
        $dto->id = NblPHPUtil::makeNumericId();
        $dto->sala = $sala;
        $dto->dia = $dia;
        
        $obj->add($dto);
        return ($obj->sync());
    }

    /**
     * Edite o registro de atualização de sumário
     * 
     * @param string $id id do sumário
     * @param string $sala id da sala
     * @param string $dia dia do sumário. Formato: DD/MM/YYYY
     * @return array
     */
    public static function edit($id, $sala, $dia): array
    {
        $dto = new AtualizacoesSumariosDTO();
        $obj = new AtualizacoesSumariosADO();
        
        $dto->edit = true;
        $dto->id = $id;
        $dto->sala = $sala;
        $dto->dia = $dia;
        
        $obj->add($dto);
        return ($obj->sync());
    }
    
    /**
     * Obtêm o id do registro de atualização de sumário por sala e dia
     * 
     * @param string $sala id da sala
     * @param string $dia dia do sumário. Formato: YYYY-MM-DD
     * @return string
     */
    public static function getBySalaAndDia($sala, $dia): string
    {
        $dto = new AtualizacoesSumariosDTO();
        $obj = new AtualizacoesSumariosADO();
        
        AtualizacoesSumariosADO::addComparison($dto, 'sala', CMP_EQUAL, $sala);
        AtualizacoesSumariosADO::addComparison($dto, 'dia', CMP_EQUAL, $dia);
        
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
     * Busque os registros de atualização de sumário por salas
     * 
     * @param array $salas array com os ids das salas
     * @return array
     */
    public static function getBySalas($salas): array 
    {
        $dto = new AtualizacoesSumariosDTO();
        $obj = new AtualizacoesSumariosADO();
        
        AtualizacoesSumariosADO::addComparison($dto, 'sala', CMP_IN_LIST, parent::stringifyArray($salas));
        $result = [];
        if($obj->getAllbyParam($dto)) {
            $obj->iterate();
            while($obj->hasNext())
            {
                $it = $obj->next();
                if(!is_null($it->id))
                {
                    $result[] = array(
                        'id' => $it->id,
                        'sala' => $it->sala,
                        'dia' => $it->dia
                    );
                }
            }
        }
        
        return $result;
    }
    
    /**
     * Apague o registro de atualização de sumário
     * 
     * @param string $id id do registro 
     * @return bool
     */
    public static function remove($id): bool
    {
        $dto = new AtualizacoesSumariosDTO();
        $obj = new AtualizacoesSumariosADO();
        
        $dto->id = $id;
        $r = $obj->get($dto);
        if(!is_null($r))
        {
            $r->delete = true;
            return ($obj->sync());
        }
        else 
        {
            return false;
        }
    }
    
}

