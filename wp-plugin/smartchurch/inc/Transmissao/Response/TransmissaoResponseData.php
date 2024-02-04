<?php

namespace SmartChurch\Transmissao\Response;

class TransmissaoResponseData implements \JsonSerializable
{
    private $id;
    private $video;
    private $stat;
    private $time_cad;
    private $last_mod;
    private $last_amod;
    
    public function getId()
    {
        return $this->id;
    }

    public function getVideo()
    {
        return $this->video;
    }
    
    public function getStat()
    {
        return $this->stat;
    }

    public function getTimeCad()
    {
        return $this->time_cad;
    }

    public function getLastMod()
    {
        return $this->last_mod;
    }

    public function getLastAmod()
    {
        return $this->last_amod;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setVideo($video)
    {
        $this->video = $video;
    }

    public function setStat($stat)
    {
        $this->stat = $stat;
    }

    public function setTimeCad($time_cad)
    {
        $this->time_cad = $time_cad;
    }

    public function setLastMod($last_mod)
    {
        $this->last_mod = $last_mod;
    }

    public function setLastAmod($last_amod)
    {
        $this->last_amod = $last_amod;
    }

    public function jsonSerialize() {
        return get_object_vars($this);
    }

}
