<?php

/**
 * Gera as informações de sócio ativo do usuário
 * 
 * @param string $id id do usuário
 * @return object
 */
function getSociedadesForUser($id): object
{
    require_once ADO_PATH . '/Socios.class.php'; 
    require_once DAO_PATH . '/SociosDAO.class.php'; 
    require_once WS_PATH . '/sociedades.ws.php';
    
    $dto = new SociosDTO();
    $obj = new SociosADO();
    
    SociosADO::addComparison($dto, 'pessoa', CMP_EQUAL, $id);
    SociosADO::addComparison($dto, 'stat', CMP_EQUAL, Status::ACTIVE);
    SociosADO::addOrdering($dto, 'time_cad', ORDER_DESC);
    
    $result = new stdClass();
    if(!is_null($obj->getBy($dto)))
    {
        $d = $obj->getDTODataObject();
        $result->id = $d->sociedade;
        $sociedade = SociedadesWS::getById($d->sociedade);
        if(!is_null($sociedade)) {
            $result->nome = $sociedade->nome;
            $result->reference = $sociedade->reference;
        }
    }
    
    return $result;
}
