<?php

namespace SmartChurch\Igreja\Response;

use SmartChurch\Response\Response;

class IgrejaResponse extends Response
{
    public function __construct()
    {
        $this->datas = array();
    }
    
    public function add(\SmartChurch\Igreja\Response\IgrejaResponseData $data): void
    {
        $this->datas[] = $data;
    }
    
    public function get(): ?\SmartChurch\Igreja\Response\IgrejaResponseData
    {
        if(empty($this->datas)) {
            return null;
        }
        
        return array_pop($this->datas);
    }
}
