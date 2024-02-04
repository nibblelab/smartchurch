<?php

namespace SmartChurch\Templo\Response;

class TemploResponseData implements \JsonSerializable
{
    private $id;
    private $sinodo;
    private $presbiterio;
    private $igreja;
    private $nome;
    private $fundacao;
    private $organizada;
    private $telefone;
    private $email;
    private $stat;
    private $endereco;
    private $numero;
    private $complemento;
    private $bairro;
    private $cidade;
    private $uf;
    private $cep;
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

    public function getTemplo() {
        return $this->igreja;
    }

    public function getNome() {
        return $this->nome;
    }

    public function getFundacao() {
        return $this->fundacao;
    }

    public function getOrganizada() {
        return $this->organizada;
    }

    public function getTelefone() {
        return $this->telefone;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getStat() {
        return $this->stat;
    }

    public function getEndereco() {
        return $this->endereco;
    }

    public function getNumero() {
        return $this->numero;
    }

    public function getComplemento() {
        return $this->complemento;
    }

    public function getBairro() {
        return $this->bairro;
    }

    public function getCidade() {
        return $this->cidade;
    }

    public function getUf() {
        return $this->uf;
    }

    public function getCep() {
        return $this->cep;
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

    public function setTemplo($igreja): void {
        $this->igreja = $igreja;
    }

    public function setNome($nome): void {
        $this->nome = $nome;
    }

    public function setFundacao($fundacao): void {
        $this->fundacao = $fundacao;
    }

    public function setOrganizada($organizada): void {
        $this->organizada = $organizada;
    }

    public function setTelefone($telefone): void {
        $this->telefone = $telefone;
    }

    public function setEmail($email): void {
        $this->email = $email;
    }

    public function setStat($stat): void {
        $this->stat = $stat;
    }

    public function setEndereco($endereco): void {
        $this->endereco = $endereco;
    }

    public function setNumero($numero): void {
        $this->numero = $numero;
    }

    public function setComplemento($complemento): void {
        $this->complemento = $complemento;
    }

    public function setBairro($bairro): void {
        $this->bairro = $bairro;
    }

    public function setCidade($cidade): void {
        $this->cidade = $cidade;
    }

    public function setUf($uf): void {
        $this->uf = $uf;
    }

    public function setCep($cep): void {
        $this->cep = $cep;
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
