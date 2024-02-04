<?php

namespace SmartChurch\Evento\Response;

class EventoResponseData implements \JsonSerializable
{
    private $id;
    private $agenda;
    private $chave;
    private $nome;
    private $logo;
    private $haslogo;
    private $descricao;
    private $ref;
    private $ref_tp;
    private $inscricoes_ativas;
    private $formulario_inscricao;
    private $valor;
    private $opcoes_pagto;
    private $lotes;
    private $fim_inscricao;
    private $time_ini;
    private $time_end;
    private $tem_eleicoes;
    private $stat;
    private $time_cad;
    private $last_mod;
    private $last_amod;
    
    
    public function getId() {
        return $this->id;
    }

    public function getAgenda() {
        return $this->agenda;
    }

    public function getChave() {
        return $this->chave;
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

    public function getDescricao() {
        return $this->descricao;
    }

    public function getRef() {
        return $this->ref;
    }

    public function getRef_tp() {
        return $this->ref_tp;
    }

    public function getInscricoesAtivas() {
        return $this->inscricoes_ativas;
    }

    public function getFormularioInscricao() {
        return $this->formulario_inscricao;
    }

    public function getValor() {
        return $this->valor;
    }

    public function getOpcoesPagto() {
        return $this->opcoes_pagto;
    }

    public function getLotes() {
        return $this->lotes;
    }

    public function getFimInscricao() {
        return $this->fim_inscricao;
    }

    public function getTimeIni() {
        return $this->time_ini;
    }

    public function getTimeEnd() {
        return $this->time_end;
    }

    public function getTemEleicoes() {
        return $this->tem_eleicoes;
    }

    public function getStat() {
        return $this->stat;
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

    public function setAgenda($agenda): void {
        $this->agenda = $agenda;
    }

    public function setChave($chave): void {
        $this->chave = $chave;
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

    public function setDescricao($descricao): void {
        $this->descricao = $descricao;
    }

    public function setRef($ref): void {
        $this->ref = $ref;
    }

    public function setRef_tp($ref_tp): void {
        $this->ref_tp = $ref_tp;
    }

    public function setInscricoesAtivas($inscricoes_ativas): void {
        $this->inscricoes_ativas = $inscricoes_ativas;
    }

    public function setFormularioInscricao($formulario_inscricao): void {
        $this->formulario_inscricao = $formulario_inscricao;
    }

    public function setValor($valor): void {
        $this->valor = $valor;
    }

    public function setOpcoesPagto($opcoes_pagto): void {
        $this->opcoes_pagto = $opcoes_pagto;
    }

    public function setLotes($lotes): void {
        $this->lotes = $lotes;
    }

    public function setFimInscricao($fim_inscricao): void {
        $this->fim_inscricao = $fim_inscricao;
    }

    public function setTimeIni($time_ini): void {
        $this->time_ini = $time_ini;
    }

    public function setTimeEnd($time_end): void {
        $this->time_end = $time_end;
    }

    public function setTemEleicoes($tem_eleicoes): void {
        $this->tem_eleicoes = $tem_eleicoes;
    }

    public function setStat($stat): void {
        $this->stat = $stat;
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
