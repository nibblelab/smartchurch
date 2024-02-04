<?php

/**
 * Verifica se um módulo existe em uma instância
 * 
 * @param string $ref id da referência
 * @param \References $ref_tp tipo de referência
 * @param \Modules $module módulo a ser verificado
 * @return bool
 */
function checkModule($ref, $ref_tp, $module): bool {
    require_once WS_PATH . '/instancias.ws.php'; 
    
    $modulos = InstanciasWS::getModulosbyRef($ref, $ref_tp);
    if(empty($modulos)) {
        return false;
    }
    
    return (strpos($modulos, $module) !== false);
}
