<?php

namespace SmartChurch\Mensagem\Response;

use SmartChurch\Response\Response;

class MensagemResponse extends Response
{
    public function __construct()
    {
        $this->datas = array();
    }
    
    public function add(\SmartChurch\Mensagem\Response\MensagemResponseData $data): void
    {
        $this->datas[] = $data;
    }
    
    public function addDownload(\SmartChurch\Mensagem\Response\MensagemDownloadData $data): void
    {
        $this->datas[] = $data;
    }
    
    public function get(): ?\SmartChurch\Mensagem\Response\MensagemResponseData
    {
        if(empty($this->datas)) {
            return null;
        }
        
        return array_pop($this->datas);
    }
    
    public function getDownload(): ?\SmartChurch\Mensagem\Response\MensagemDownloadData
    {
        if(empty($this->datas)) {
            return null;
        }
        
        return array_pop($this->datas);
    }
}
