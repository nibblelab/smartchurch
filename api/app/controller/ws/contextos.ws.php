<?php
require_once ADO_PATH . '/Contextos.class.php'; 
require_once DAO_PATH . '/ContextosDAO.class.php'; 


/**
 * API REST de Contextos
 */
class ContextosWS extends WSUtil
{
    /**
     * 
     * @var \ContextosWS singleton instance
     */
    private static $_Instance = null;
    
    /**
     * Get singleton instance
     * 
     * @return \ContextosWS
     */
    public static function getInstance(): \ContextosWS {
        if(self::$_Instance == null) {
            self::$_Instance = new self();
        }
        return self::$_Instance;
    }
    
    /**
     * Verifica se o usuário já possui contexto para a instância e referência opcionalmente
     * 
     * @param string $usuario id do usuário
     * @param string $instancia id da instância
     * @param string $ref id da referência [opcional]
     * @param \References $ref_tp tipo da referência [opcional]
     * @return bool
     */
    public static function exists($usuario, $instancia, $ref = '', $ref_tp = References::NONE): bool
    {
        $dto = new ContextosDTO();
        $obj = new ContextosADO();
        
        ContextosADO::addComparison($dto, 'usuario', CMP_EQUAL, $usuario);
        ContextosADO::addComparison($dto, 'instancia', CMP_EQUAL, $instancia);
        if(!empty($ref)) {
            ContextosADO::addComparison($dto, 'ref', CMP_EQUAL, $ref);
        }
        if($ref_tp != References::NONE) {
            ContextosADO::addComparison($dto, 'ref_tp', CMP_EQUAL, $ref_tp);
        }
        
        return ($obj->countBy($dto) > 0);
    }
    
    /**
     * Cria o contexto
     * 
     * @param string $usuario id do usuário
     * @param string $instancia id da instância
     * @param string $ref id da referência
     * @param \References|array $ref_tp tipo da referência
     * @param array $errs array com os erros da operação, se existirem
     * @param string $perfil id do perfil [Opcional]
     * @return bool
     */
    public static function create($usuario, $instancia, $ref, $ref_tp, &$errs, $perfil = null): bool
    {
        $obj = new ContextosADO();
        
        if(!is_array($ref_tp)) {
            $ref_tp = array($ref_tp);
        }
        
        foreach($ref_tp as $r_tp) {
            $dto = new ContextosDTO();
            $dto->add = true;
            $dto->id = NblPHPUtil::makeNumericId();
            $dto->usuario = $usuario;
            $dto->instancia = $instancia;
            $dto->ref = $ref;
            $dto->ref_tp = $r_tp;
            $dto->perfil = $perfil;
            $dto->stat = Status::ACTIVE;

            $obj->add($dto);
        } 
        
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
     * Altera o status do contexto pelo usuário e referência
     * 
     * @param \Status $stat novo status
     * @param string $usuario id do usuário
     * @param string $ref id da referência
     * @param \References|array $ref_tp tipo da referência
     * @return void
     */
    public static function changeStatByUserAndReference($stat, $usuario, $ref, $ref_tp): void
    {
        $dto = new ContextosDTO();
        $obj = new ContextosADO();
                
        ContextosADO::addComparison($dto, 'usuario', CMP_EQUAL, $usuario);
        ContextosADO::addComparison($dto, 'ref', CMP_EQUAL, $ref);
        
        if(is_array($ref_tp)) {
            foreach($ref_tp as $r_tp) {
                ContextosADO::addComparison($dto, 'ref_tp', CMP_EQUAL, $r_tp, OP_OR, true);
            }
        }
        else {
            ContextosADO::addComparison($dto, 'ref_tp', CMP_EQUAL, $ref_tp);
        }
        
        if($obj->getAllbyParam($dto))
        {
            $obj->iterate();
            while($obj->hasNext())
            {
                $it = $obj->next();
                if(!is_null($it->id))
                {
                    $it->edit = true;
                    $it->stat = $stat;
                }
            }
            
            $obj->sync();
        }
    }
    
    /**
     * Altera o perfil do contexto pelo usuário e referência
     * 
     * @param string $perfil id do perfil
     * @param string $usuario id do usuário
     * @param string $ref id da referência
     * @param \References $ref_tp tipo da referência
     * @return void
     */
    public static function changePerfilByUserAndReference($perfil, $usuario, $ref, $ref_tp): void
    {
        $dto = new ContextosDTO();
        $obj = new ContextosADO();
        
        ContextosADO::addComparison($dto, 'usuario', CMP_EQUAL, $usuario);
        ContextosADO::addComparison($dto, 'ref', CMP_EQUAL, $ref);
        ContextosADO::addComparison($dto, 'ref_tp', CMP_EQUAL, $ref_tp);
        
        if($obj->getAllbyParam($dto))
        {
            $obj->iterate();
            while($obj->hasNext())
            {
                $it = $obj->next();
                if(!is_null($it->id))
                {
                    $it->edit = true;
                    $it->perfil = $perfil;
                }
            }
            
            $obj->sync();
        }
    }
    
    /**
     * Busca o contexto por usuário e referência
     * 
     * @param string $usuario id do usuário
     * @param string $ref id da referência
     * @param \References $ref_tp tipo da referência
     * @return array
     */
    public static function getByUserAndReference($usuario, $ref, $ref_tp): array
    {
        $dto = new ContextosDTO();
        $obj = new ContextosADO();

        ContextosADO::addComparison($dto, 'usuario', CMP_EQUAL, $usuario);
        ContextosADO::addComparison($dto, 'ref', CMP_EQUAL, $ref);
        ContextosADO::addComparison($dto, 'ref_tp', CMP_EQUAL, $ref_tp);

        return $obj->mapAllByWithParam('usuario', $dto);
    }
    
    /**
     * Remove todos os contextos de um usuário em uma instância
     * 
     * @param string $usuario id do usuário
     * @param string $instancia id da instância
     * @return void
     */
    public static function removeByUserAndInstancia($usuario, $instancia): void
    {
        $dto = new ContextosDTO();
        $obj = new ContextosADO();
        
        ContextosADO::addComparison($dto, 'usuario', CMP_EQUAL, $usuario);
        ContextosADO::addComparison($dto, 'instancia', CMP_EQUAL, $instancia);
        
        if($obj->getAllbyParam($dto))
        {
            $obj->iterate();
            while($obj->hasNext())
            {
                $it = $obj->next();
                if(!is_null($it->id))
                {
                    $it->delete = true;
                }
            }
            
            $obj->sync();
        }
    }
    
    /**
     * Remove um contexto específico de um usuário
     * 
     * @param string $usuario id do usuário
     * @param string $instancia id da instância
     * @param string $ref id da referência
     * @param \References|array $ref_tp tipo da referência
     * @return void
     */
    public static function removeByUserInstanciaAndRefs($usuario, $instancia, $ref, $ref_tp): void 
    {
        $dto = new ContextosDTO();
        $obj = new ContextosADO();
        
        ContextosADO::addComparison($dto, 'usuario', CMP_EQUAL, $usuario);
        ContextosADO::addComparison($dto, 'instancia', CMP_EQUAL, $instancia);
        ContextosADO::addComparison($dto, 'ref', CMP_EQUAL, $ref);
        
        if(is_array($ref_tp)) {
            foreach($ref_tp as $r_tp) {
                ContextosADO::addComparison($dto, 'ref_tp', CMP_EQUAL, $r_tp, OP_OR, true);
            }
        }
        else {
            ContextosADO::addComparison($dto, 'ref_tp', CMP_EQUAL, $ref_tp);
        }
        
        if($obj->getAllbyParam($dto))
        {
            $obj->iterate();
            while($obj->hasNext())
            {
                $it = $obj->next();
                if(!is_null($it->id))
                {
                    $it->delete = true;
                }
            }
            
            $obj->sync();
        }
    }
    
    /**
     * Mapeia os contextos por referência
     * 
     * @param string $ref id da referência
     * @param \References $ref_tp tipo da referência
     * @return array
     */
    public static function getMapByReference($ref, $ref_tp): array
    {
        $dto = new ContextosDTO();
        $obj = new ContextosADO();
        
        ContextosADO::addComparison($dto, 'ref', CMP_EQUAL, $ref);
        ContextosADO::addComparison($dto, 'ref_tp', CMP_EQUAL, $ref_tp);
        
        return $obj->mapAllByWithParam('id', $dto);
    }
}

