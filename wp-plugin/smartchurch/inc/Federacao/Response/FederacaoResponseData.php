<?php

namespace SmartChurch\Federacao\Response;

class FederacaoResponseData implements \JsonSerializable
{
    private $id;
    private $sinodo;
    private $presbiterio;
    private $sinodal;
    private $reference;
    private $sigla;
    private $nome;
    private $fundacao;
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

    public function getSinodo() {
        return $this->sinodo;
    }

    public function getPresbiterio() {
        return $this->presbiterio;
    }

    public function getSinodal() {
        return $this->sinodal;
    }

    public function getReference() {
        return $this->reference;
    }

    public function getSigla() {
        return $this->sigla;
    }

    public function getNome() {
        return $this->nome;
    }

    public function getFundacao() {
        return $this->fundacao;
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

    public function setSinodo($sinodo): void {
        $this->sinodo = $sinodo;
    }

    public function setPresbiterio($presbiterio): void {
        $this->presbiterio = $presbiterio;
    }

    public function setSinodal($sinodal): void {
        $this->sinodal = $sinodal;
    }

    public function setReference($reference): void {
        $this->reference = $reference;
    }

    public function setSigla($sigla): void {
        $this->sigla = $sigla;
    }

    public function setNome($nome): void {
        $this->nome = $nome;
    }

    public function setFundacao($fundacao): void {
        $this->fundacao = $fundacao;
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
