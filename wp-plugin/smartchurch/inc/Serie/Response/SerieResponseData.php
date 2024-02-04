<?php

namespace SmartChurch\Serie\Response;

class SerieResponseData implements \JsonSerializable
{
    private $id;
    private $igreja;
    private $nome;
    private $chave;
    private $logo;
    private $haslogo;
    private $stat;
    private $time_cad;
    private $last_mod;
    private $last_amod;
    
    public function getId()
    {
        return $this->id;
    }

    public function getIgreja()
    {
        return $this->igreja;
    }

    public function getNome()
    {
        return $this->nome;
    }
    
    public function getChave()
    {
        return $this->chave;
    }

    public function getLogo()
    {
        return $this->logo;
    }
    
    public function getHaslogo()
    {
        return $this->haslogo;
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

    public function setIgreja($igreja)
    {
        $this->igreja = $igreja;
    }

    public function setNome($nome)
    {
        $this->nome = $nome;
    }

    public function setChave($chave)
    {
        $this->chave = $chave;
    }

    public function setLogo($logo)
    {
        $this->logo = $logo;
    }

    public function setHaslogo($haslogo)
    {
        $this->haslogo = $haslogo;
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
