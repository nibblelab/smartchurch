<?php

/**
 * Gera as informações de oficialatos ativos do usuário
 * 
 * @param string $id id do usuário
 * @return array
 */
function getOficialatosForUser($id): array
{
    require_once ADO_PATH . '/Oficiais.class.php'; 
    require_once DAO_PATH . '/OficiaisDAO.class.php'; 
    
    $dto = new OficiaisDTO();
    $obj = new OficiaisADO();

    OficiaisADO::addComparison($dto, 'pessoa', CMP_EQUAL, $id);
    OficiaisADO::addComparison($dto, 'stat', CMP_EQUAL, Status::ACTIVE);
    
    $result = array();
    if($obj->getAllbyParam($dto)) 
    {
        $obj->iterate();
        while($obj->hasNext())
        {
            $it = $obj->next();
            if(!is_null($it->id))
            {
                $chave = '';
                if($it->tipo == TipoOficiais::PASTOR) {
                    $chave = 'pastor';
                    if(!isset($result[$chave])) {
                        $result[$chave] = array();
                    }
                }
                else if($it->tipo == TipoOficiais::EVANGELISTA) {
                    $chave = 'evangelista';
                    if(!isset($result[$chave])) {
                        $result[$chave] = array();
                    }
                }
                else if($it->tipo == TipoOficiais::SEMINARISTA) {
                    $chave = 'seminarista';
                    if(!isset($result[$chave])) {
                        $result[$chave] = array();
                    }
                }
                else if($it->tipo == TipoOficiais::MISSIONARIO) {
                    $chave = 'missionario';
                    if(!isset($result[$chave])) {
                        $result[$chave] = array();
                    }
                }
                else if($it->tipo == TipoOficiais::PRESBITERO) {
                    $chave = 'presbitero';
                    if(!isset($result[$chave])) {
                        $result[$chave] = array();
                    }
                }
                else if($it->tipo == TipoOficiais::DIACONO) {
                    $chave = 'diacono';
                    if(!isset($result[$chave])) {
                        $result[$chave] = array();
                    }
                }
                else if($it->tipo == TipoOficiais::OUTRO) {
                    $chave = 'oficial';
                    if(!isset($result[$chave])) {
                        $result[$chave] = array();
                    }
                }
                
                
                $result[$chave][] = array(
                    'id' => $it->id,
                    'ref_tp' => $it->ref_tp,
                    'ref' => $it->ref
                );
            }
        }
    }
    
    return $result;
}
