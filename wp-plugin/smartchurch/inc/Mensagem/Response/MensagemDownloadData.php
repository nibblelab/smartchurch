<?php

namespace SmartChurch\Mensagem\Response;

class MensagemDownloadData
{
    private $name;
    private $type;
    private $content;
    private $size;
    
    public function getName()
    {
        return $this->name;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function setSize($size)
    {
        $this->size = $size;
    }


}

