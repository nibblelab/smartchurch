<?php

namespace SmartChurch\Sinodo\Response;

use SmartChurch\Response\Response;

class SinodoResponse extends Response
{
    public function __construct()
    {
        $this->datas = array();
    }
    
    public function add(\SmartChurch\Sinodo\Response\SinodoResponseData $data): void
    {
        $this->datas[] = $data;
    }
    
    public function get(): ?\SmartChurch\Sinodo\Response\SinodoResponseData
    {
        if(empty($this->datas)) {
            return null;
        }
        
        return array_pop($this->datas);
    }
}
