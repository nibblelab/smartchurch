<?php

namespace SmartChurch\Presbiterio\Response;

use SmartChurch\Response\Response;

class PresbiterioResponse extends Response
{
    public function __construct()
    {
        $this->datas = array();
    }
    
    public function add(\SmartChurch\Presbiterio\Response\PresbiterioResponseData $data): void
    {
        $this->datas[] = $data;
    }
    
    public function get(): ?\SmartChurch\Presbiterio\Response\PresbiterioResponseData
    {
        if(empty($this->datas)) {
            return null;
        }
        
        return array_pop($this->datas);
    }
}
