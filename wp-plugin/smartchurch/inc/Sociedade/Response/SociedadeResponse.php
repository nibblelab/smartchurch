<?php

namespace SmartChurch\Sociedade\Response;

use SmartChurch\Response\Response;

class SociedadeResponse extends Response
{
    public function __construct()
    {
        $this->datas = array();
    }
    
    public function add(\SmartChurch\Sociedade\Response\SociedadeResponseData $data): void
    {
        $this->datas[] = $data;
    }
    
    public function get(): ?\SmartChurch\Sociedade\Response\SociedadeResponseData
    {
        if(empty($this->datas)) {
            return null;
        }
        
        return array_pop($this->datas);
    }
}
