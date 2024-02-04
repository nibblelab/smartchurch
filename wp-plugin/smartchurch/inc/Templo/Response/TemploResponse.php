<?php

namespace SmartChurch\Templo\Response;

use SmartChurch\Response\Response;

class TemploResponse extends Response
{
    public function __construct()
    {
        $this->datas = array();
    }
    
    public function add(\SmartChurch\Templo\Response\TemploResponseData $data): void
    {
        $this->datas[] = $data;
    }
    
    public function get(): ?\SmartChurch\Templo\Response\TemploResponseData
    {
        if(empty($this->datas)) {
            return null;
        }
        
        return array_pop($this->datas);
    }
}
