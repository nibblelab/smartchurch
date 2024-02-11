<?php
require_once ADO_PATH . '/Instancias.class.php'; 
require_once DAO_PATH . '/InstanciasDAO.class.php'; 


/**
 * API REST de Instancias
 */
class InstanciasWS extends WSUtil
{
    /**
     * 
     * @var \InstanciasWS singleton instance
     */
    private static $_Instance = null;
    
    /**
     * Get singleton instance
     * 
     * @return \InstanciasWS
     */
    public static function getInstance(): \InstanciasWS {
        if(self::$_Instance == null) {
            self::$_Instance = new self();
        }
        return self::$_Instance;
    }
    
    /**
     * Obtêm uma instância por suas referências
     * 
     * @param string $ref id da referência
     * @param \References $ref_tp tipo de referência
     * @return \InstanciasDTO|null
     */
    public static function getbyRef($ref, $ref_tp): ?\InstanciasDTO
    {
        $dto = new InstanciasDTO();
        $obj = new InstanciasADO();
        
        InstanciasADO::addComparison($dto, 'ref', CMP_EQUAL, $ref);
        InstanciasADO::addComparison($dto, 'ref_tp', CMP_EQUAL, $ref_tp);
        
        return $obj->getBy($dto);
    }
    
    
    public static function getbyId($id): ?\InstanciasDTO
    {
        $dto = new InstanciasDTO();
        $obj = new InstanciasADO();
        
        InstanciasADO::addComparison($dto, 'id', CMP_EQUAL, $id);
        
        return $obj->getBy($dto);
    }
    
    /**
     * Obtêm o id de uma instância por suas referências
     * 
     * @param string $ref id da referência
     * @param \References $ref_tp tipo de referência
     * @return string
     */
    public static function getIdbyRef($ref, $ref_tp): string
    {
        $dto = new InstanciasDTO();
        $obj = new InstanciasADO();
        
        InstanciasADO::addComparison($dto, 'ref', CMP_EQUAL, $ref);
        InstanciasADO::addComparison($dto, 'ref_tp', CMP_EQUAL, $ref_tp);
        
        if(!is_null($obj->getBy($dto)))
        {
            $d = $obj->getDTODataObject();
            return $d->id;
        }
        
        return '';
    }
    
    /**
     * Obtêm os módulos de uma instância por suas referências
     * 
     * @param string $ref id da referência
     * @param \References $ref_tp tipo de referência
     * @return string
     */
    public static function getModulosbyRef($ref, $ref_tp): string
    {
        $dto = new InstanciasDTO();
        $obj = new InstanciasADO();
        
        InstanciasADO::addComparison($dto, 'ref', CMP_EQUAL, $ref);
        InstanciasADO::addComparison($dto, 'ref_tp', CMP_EQUAL, $ref_tp);
        
        if(!is_null($obj->getBy($dto)))
        {
            $d = $obj->getDTODataObject();
            return $d->modulos;
        }
        
        return '';
    }
    
    /**
     * Cria uma instância
     * 
     * @param string $ref referência
     * @param \References $ref_tp tipo de referência
     * @param string $modulos módulos
     * @param string $pai instância pai. Padrão = null
     * @param string $id id da instância criada
     * @param array $errs erros, se ocorrerem
     * @return bool
     */
    public static function create($ref, $ref_tp, $modulos, $pai, &$id, &$errs): bool
    {
        $dto = new InstanciasDTO();
        $obj = new InstanciasADO();

        $dto->add = true;
        $dto->id = NblPHPUtil::makeNumericId();
        $dto->ref = $ref;
        $dto->ref_tp = $ref_tp;
        $dto->modulos = $modulos;
        $dto->pai = $pai;

        $obj->add($dto);
        if($obj->sync())
        {
            $id = $dto->id;
            return true;
        }
        else
        {
            $id = '';
            $errs = $obj->getErrs();
            return false;
        }
    }
    
    /**
     * Altera os módulos de uma instância
     * 
     * @param string $id id da instância
     * @param string $modulos módulos
     * @param array $errs erros, se ocorrerem
     * @return bool
     */
    public static function editModulos($id, $modulos, &$errs): bool
    {
        $dto = new InstanciasDTO();
        $obj = new InstanciasADO();
        
        $dto->edit = true;
        $dto->id = $id;
        $dto->modulos = $modulos;
        
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
     * Gere o mapa de instâncias
     * 
     * @return array
     */
    public static function getMap(): array
    {
        $obj = new InstanciasADO();
        
        return $obj->mapAllBy('id');
    }
}

