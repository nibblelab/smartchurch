<?php

namespace SmartChurch\Mensagem\Response;

class MensagemResponseData implements \JsonSerializable
{    
    private $id;
    private $igreja;
    private $serie;
    private $autor;
    private $titulo;
    private $chave;
    private $data_sermao;
    private $conteudo;
    private $anexo;
    private $video;
    private $audio;
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

    public function getSerie()
    {
        return $this->serie;
    }

    public function getAutor()
    {
        return $this->autor;
    }

    public function getTitulo()
    {
        return $this->titulo;
    }
    
    public function getChave()
    {
        return $this->chave;
    }

    public function getDataSermao()
    {
        return $this->data_sermao;
    }

    public function getConteudo()
    {
        return $this->conteudo;
    }

    public function getAnexo()
    {
        return $this->anexo;
    }

    public function getVideo()
    {
        return $this->video;
    }

    public function getAudio()
    {
        return $this->audio;
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

    public function setSerie($serie)
    {
        $this->serie = $serie;
    }

    public function setAutor($autor)
    {
        $this->autor = $autor;
    }

    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;
    }

    public function setChave($chave)
    {
        $this->chave = $chave;
    }
 
    public function setDataSermao($data_sermao)
    {
        $this->data_sermao = $data_sermao;
    }

    public function setConteudo($conteudo)
    {
        $this->conteudo = $conteudo;
    }

    public function setAnexo($anexo)
    {
        $this->anexo = $anexo;
    }

    public function setVideo($video)
    {
        $this->video = $video;
    }

    public function setAudio($audio)
    {
        $this->audio = $audio;
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