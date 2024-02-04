<?php

namespace SmartChurch\Transmissao\Response;

use SmartChurch\Response\Response;

class TransmissaoResponse extends Response 
{
    public function __construct()
    {
        $this->datas = array();
    }
    
    public function add(\SmartChurch\Transmissao\Response\TransmissaoResponseData $data): void
    {
        $this->datas[] = $data;
    }
    
    public function get(): ?\SmartChurch\Transmissao\Response\TransmissaoResponseData
    {
        if(empty($this->datas)) {
            return null;
        }
        
        return array_pop($this->datas);
    }
}
