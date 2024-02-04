<?php

namespace SmartChurch\Sociedade\Response;

class SociedadeResponseData implements \JsonSerializable
{
    private $id;
    private $igreja;
    private $federacao;
    private $sinodal;
    private $nacional;
    private $reference;
    private $nome;
    private $logo;
    private $haslogo;
    private $fundacao;
    private $email;
    private $telefone;
    private $ramal;
    private $stat;
    private $site;
    private $facebook;
    private $instagram;
    private $youtube;
    private $vimeo;
    private $time_cad;
    private $last_mod;
    private $last_amod;
    
    public function getId() {
        return $this->id;
    }

    public function getIgreja() {
        return $this->igreja;
    }

    public function getFederacao() {
        return $this->federacao;
    }

    public function getSinodal() {
        return $this->sinodal;
    }

    public function getNacional() {
        return $this->nacional;
    }

    public function getReference() {
        return $this->reference;
    }

    public function getNome() {
        return $this->nome;
    }

    public function getLogo() {
        return $this->logo;
    }

    public function getHaslogo() {
        return $this->haslogo;
    }

    public function getFundacao() {
        return $this->fundacao;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getTelefone() {
        return $this->telefone;
    }

    public function getRamal() {
        return $this->ramal;
    }

    public function getStat() {
        return $this->stat;
    }

    public function getSite() {
        return $this->site;
    }

    public function getFacebook() {
        return $this->facebook;
    }

    public function getInstagram() {
        return $this->instagram;
    }

    public function getYoutube() {
        return $this->youtube;
    }

    public function getVimeo() {
        return $this->vimeo;
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

    public function setIgreja($igreja): void {
        $this->igreja = $igreja;
    }

    public function setFederacao($federacao): void {
        $this->federacao = $federacao;
    }

    public function setSinodal($sinodal): void {
        $this->sinodal = $sinodal;
    }

    public function setNacional($nacional): void {
        $this->nacional = $nacional;
    }

    public function setReference($reference): void {
        $this->reference = $reference;
    }

    public function setNome($nome): void {
        $this->nome = $nome;
    }

    public function setLogo($logo): void {
        $this->logo = $logo;
    }
    
    public function setHaslogo($haslogo): void {
        $this->haslogo = $haslogo;
    }

    public function setFundacao($fundacao): void {
        $this->fundacao = $fundacao;
    }

    public function setEmail($email): void {
        $this->email = $email;
    }

    public function setTelefone($telefone): void {
        $this->telefone = $telefone;
    }

    public function setRamal($ramal): void {
        $this->ramal = $ramal;
    }

    public function setStat($stat): void {
        $this->stat = $stat;
    }

    public function setSite($site): void {
        $this->site = $site;
    }

    public function setFacebook($facebook): void {
        $this->facebook = $facebook;
    }

    public function setInstagram($instagram): void {
        $this->instagram = $instagram;
    }

    public function setYoutube($youtube): void {
        $this->youtube = $youtube;
    }

    public function setVimeo($vimeo): void {
        $this->vimeo = $vimeo;
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
