<?php

namespace SmartChurch\Data\Response;

class DataResponseData implements \JsonSerializable
{
    private $status;
    private $referencias;
    private $referencias_cargos;
    private $sociedades;
    private $escolaridade;
    private $escolaridade_void;
    private $estado_civil;
    private $estado_civil_void;
    private $sexo;
    private $relacao_familiar;
    private $frequencia;
    private $pagamento_status;
    private $aprovacao_ata;
    private $status_inscricao;
    private $profissao_fe;
    private $registro_financeiro;
    private $tipo_oficiais;
    private $disponibilidade_oficiais;
    private $responsaveis_virtuais;
    private $formas_pagto;
    private $formulario_inscricao;
    private $opcao_pagto;
    private $lote_pagto;
    private $tipos_secretario;
    private $ufs;
    private $cidades;

    public function getStatus() {
        return $this->status;
    }

    public function getReferencias() {
        return $this->referencias;
    }

    public function getReferenciasCargos() {
        return $this->referencias_cargos;
    }

    public function getSociedades() {
        return $this->sociedades;
    }

    public function getEscolaridade() {
        return $this->escolaridade;
    }

    public function getEscolaridadeVoid() {
        return $this->escolaridade_void;
    }

    public function getEstadoCivil() {
        return $this->estado_civil;
    }

    public function getEstadoCivilVoid() {
        return $this->estado_civil_void;
    }

    public function getSexo() {
        return $this->sexo;
    }

    public function getRelacaoFamiliar() {
        return $this->relacao_familiar;
    }

    public function getFrequencia() {
        return $this->frequencia;
    }

    public function getPagamentoStatus() {
        return $this->pagamento_status;
    }

    public function getAprovacaoAta() {
        return $this->aprovacao_ata;
    }

    public function getStatusInscricao() {
        return $this->status_inscricao;
    }

    public function getProfissaoFe() {
        return $this->profissao_fe;
    }

    public function getRegistroFinanceiro() {
        return $this->registro_financeiro;
    }

    public function getTipoOficiais() {
        return $this->tipo_oficiais;
    }

    public function getDisponibilidadeOficiais() {
        return $this->disponibilidade_oficiais;
    }

    public function getResponsaveisVirtuais() {
        return $this->responsaveis_virtuais;
    }

    public function getFormasPagto() {
        return $this->formas_pagto;
    }

    public function getFormularioInscricao() {
        return $this->formulario_inscricao;
    }

    public function getOpcaoPagto() {
        return $this->opcao_pagto;
    }

    public function getLotePagto() {
        return $this->lote_pagto;
    }

    public function getTiposSecretario() {
        return $this->tipos_secretario;
    }

    public function getUfs() {
        return $this->ufs;
    }

    public function getCidades() {
        return $this->cidades;
    }

    public function setStatus($status): void {
        $this->status = $status;
    }

    public function setReferencias($referencias): void {
        $this->referencias = $referencias;
    }

    public function setReferenciasCargos($referencias_cargos): void {
        $this->referencias_cargos = $referencias_cargos;
    }

    public function setSociedades($sociedades): void {
        $this->sociedades = $sociedades;
    }

    public function setEscolaridade($escolaridade): void {
        $this->escolaridade = $escolaridade;
    }

    public function setEscolaridadeVoid($escolaridade_void): void {
        $this->escolaridade_void = $escolaridade_void;
    }

    public function setEstadoCivil($estado_civil): void {
        $this->estado_civil = $estado_civil;
    }

    public function setEstadoCivilVoid($estado_civil_void): void {
        $this->estado_civil_void = $estado_civil_void;
    }

    public function setSexo($sexo): void {
        $this->sexo = $sexo;
    }

    public function setRelacaoFamiliar($relacao_familiar): void {
        $this->relacao_familiar = $relacao_familiar;
    }

    public function setFrequencia($frequencia): void {
        $this->frequencia = $frequencia;
    }

    public function setPagamentoStatus($pagamento_status): void {
        $this->pagamento_status = $pagamento_status;
    }

    public function setAprovacaoAta($aprovacao_ata): void {
        $this->aprovacao_ata = $aprovacao_ata;
    }

    public function setStatusInscricao($status_inscricao): void {
        $this->status_inscricao = $status_inscricao;
    }

    public function setProfissaoFe($profissao_fe): void {
        $this->profissao_fe = $profissao_fe;
    }

    public function setRegistroFinanceiro($registro_financeiro): void {
        $this->registro_financeiro = $registro_financeiro;
    }

    public function setTipoOficiais($tipo_oficiais): void {
        $this->tipo_oficiais = $tipo_oficiais;
    }

    public function setDisponibilidadeOficiais($disponibilidade_oficiais): void {
        $this->disponibilidade_oficiais = $disponibilidade_oficiais;
    }

    public function setResponsaveisVirtuais($responsaveis_virtuais): void {
        $this->responsaveis_virtuais = $responsaveis_virtuais;
    }

    public function setFormasPagto($formas_pagto): void {
        $this->formas_pagto = $formas_pagto;
    }

    public function setFormularioInscricao($formulario_inscricao): void {
        $this->formulario_inscricao = $formulario_inscricao;
    }

    public function setOpcaoPagto($opcao_pagto): void {
        $this->opcao_pagto = $opcao_pagto;
    }

    public function setLotePagto($lote_pagto): void {
        $this->lote_pagto = $lote_pagto;
    }

    public function setTiposSecretario($tipos_secretario): void {
        $this->tipos_secretario = $tipos_secretario;
    }

    public function setUfs($ufs): void {
        $this->ufs = $ufs;
    }

    public function setCidades($cidades): void {
        $this->cidades = $cidades;
    }

    public function jsonSerialize() {
        return get_object_vars($this);
    }

}
