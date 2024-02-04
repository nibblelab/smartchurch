<?php

namespace SmartChurch\Cargo\Response;

use SmartChurch\Response\Response;

class CargoResponse extends Response
{
    public function __construct()
    {
        $this->datas = array();
    }
    
    public function add(\SmartChurch\Cargo\Response\CargoResponseData $data): void
    {
        $this->datas[] = $data;
    }
    
    public function get(): ?\SmartChurch\Cargo\Response\CargoResponseData
    {
        if(empty($this->datas)) {
            return null;
        }
        
        return array_pop($this->datas);
    }
}
