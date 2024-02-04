<?php

namespace SmartChurch\Cargo\Response;

class CargoResponseData implements \JsonSerializable
{
    private $id;
    private $perfil;
    private $nome;
    private $instancia;
    private $time_cad;
    private $last_mod;
    private $last_amod;
    
    public function getId() {
        return $this->id;
    }

    public function getPerfil() {
        return $this->perfil;
    }

    public function getNome() {
        return $this->nome;
    }

    public function getInstancia() {
        return $this->instancia;
    }

    public function getTimeCad() {
        return $this->time_cad;
    }

    public function getLastMod() {
        return $this->last_mod;
    }

    public function getLastAmod() {
        return $this->last_amod;
    }

    public function setId($id): void {
        $this->id = $id;
    }

    public function setPerfil($perfil): void {
        $this->perfil = $perfil;
    }

    public function setNome($nome): void {
        $this->nome = $nome;
    }

    public function setInstancia($instancia): void {
        $this->instancia = $instancia;
    }

    public function setTimeCad($time_cad): void {
        $this->time_cad = $time_cad;
    }

    public function setLastMod($last_mod): void {
        $this->last_mod = $last_mod;
    }

    public function setLastAmod($last_amod): void {
        $this->last_amod = $last_amod;
    }

    public function jsonSerialize() {
        return get_object_vars($this);
    }

}
