<?php

namespace SmartChurch\Sinodal\Response;

use SmartChurch\Response\Response;

class SinodalResponse extends Response
{
    public function __construct()
    {
        $this->datas = array();
    }
    
    public function add(\SmartChurch\Sinodal\Response\SinodalResponseData $data): void
    {
        $this->datas[] = $data;
    }
    
    public function get(): ?\SmartChurch\Sinodal\Response\SinodalResponseData
    {
        if(empty($this->datas)) {
            return null;
        }
        
        return array_pop($this->datas);
    }
}
