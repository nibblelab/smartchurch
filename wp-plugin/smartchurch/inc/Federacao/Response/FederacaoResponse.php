<?php

namespace SmartChurch\Federacao\Response;

use SmartChurch\Response\Response;

class FederacaoResponse extends Response
{
    public function __construct()
    {
        $this->datas = array();
    }
    
    public function add(\SmartChurch\Federacao\Response\FederacaoResponseData $data): void
    {
        $this->datas[] = $data;
    }
    
    public function get(): ?\SmartChurch\Federacao\Response\FederacaoResponseData
    {
        if(empty($this->datas)) {
            return null;
        }
        
        return array_pop($this->datas);
    }
}
