<?php
require_once dirname(dirname(dirname(__FILE__))) . '/config/conf.cfg.php';
require_once HLP_PATH . '/Auto.class.php';
require_once ADO_PATH . '/UFs.class.php'; 
require_once DAO_PATH . '/UFsDAO.class.php'; 
require_once ADO_PATH . '/Cidades.class.php'; 
require_once DAO_PATH . '/CidadesDAO.class.php'; 
require_once COMPOSER_PATH . '/autoload.php';

use \IBGEApiClient\IBGEApiClient;

class IBGESync extends Auto
{
    private $api;
    private $ufs;
    
    public function __construct()
    {
        $this->logfile = RSC_PATH . '/ibge.auto.log';
        $this->api = new IBGEApiClient();
    }
    
    private function hasIdInArray($array, $id, &$object) {
        foreach($array as $a) {
            if($a->getId() == $id) {
                $object = $a;
                return true;
            }
        }
        
        return false;
    }
    
    private function syncUFs()
    {
        try
        {
            // busque os estados no ibge
            $response = $this->api->buscarUFs();
            $this->ufs = $response->getData();
            
            // busque os estados já cadastrados no sistema
            $obj = new UFsADO();
            $ufs_ids = '';
            if($obj->getAll()) 
            {
                $obj->iterate();
                while($obj->hasNext())
                {
                    $it = $obj->next();
                    if(!is_null($it->id))
                    {
                        $uf = null;
                        if($this->hasIdInArray($this->ufs, $it->id, $uf)) {
                            $it->edit = true;
                            $it->nome = $uf->getNome();
                            $it->sigla = $uf->getSigla();
                            $ufs_ids .= '|' . $it->id . '|' ;
                        }
                    }
                }
            }
            
            foreach($this->ufs as $uf) {
                if(strpos($ufs_ids, '|' . $uf->getId() . '|' ) === false) {
                    $dto = new UFsDTO();
                
                    $dto->add = true;
                    $dto->id = $uf->getId();
                    $dto->nome = $uf->getNome();
                    $dto->sigla = $uf->getSigla();
                    $obj->add($dto);
                }
            }
            
            $obj->sync();
        } catch (Exception $ex) {
            $this->logThis($ex->getMessage());
        }
    }
    
    private function syncCidadesByUF($uf)
    {
        try
        {
            // busque as cidades do estado no ibge
            $response = $this->api->buscarMunicipiosByUF($uf->getId());
            $cidades = $response->getData();
            
            // busque as cidades já cadastradas no sistema
            $obj = new CidadesADO();
            $dto = new CidadesDTO();
            
            CidadesADO::addComparison($dto, 'uf', CMP_EQUAL, $uf->getId());
            $cidades_ids = '';
            if($obj->getAllbyParam($dto)) 
            {
                $obj->iterate();
                while($obj->hasNext())
                {
                    $it = $obj->next();
                    if(!is_null($it->id))
                    {
                        $cidade = null;
                        if($this->hasIdInArray($cidades, $it->id, $cidade)) {
                            $it->edit = true;
                            $it->nome = addslashes($cidade->getNome());
                            $cidades_ids .= '|' . $it->id . '|' ;
                        }
                    }
                }
            }
            
            foreach($cidades as $cidade) {
                if(strpos($cidades_ids, '|' . $cidade->getId() . '|' ) === false) {
                    $dto = new CidadesDTO();
                
                    $dto->add = true;
                    $dto->id = $cidade->getId();
                    $dto->uf = $uf->getId();
                    $dto->nome = addslashes($cidade->getNome());
                    $obj->add($dto);
                }
            }
            
            $obj->sync();
        } catch (Exception $ex) {
            $this->logThis($ex->getMessage());
        }
    }
    
    private function syncCidades()
    {
        foreach($this->ufs as $uf) {
            $this->syncCidadesByUF($uf);
        }
    }
    
    public function sync()
    {
        $this->syncUFs();
        $this->syncCidades();
    }
}

$run = new IBGESync();
$run->sync();