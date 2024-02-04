<?php

namespace SmartChurch\Shortcode;

use SmartChurch\SmartChurch as SmartChurch;

class SmartCodes 
{
    protected $api;
    protected $data;
    
    public function __construct(SmartChurch $api) {
        $this->api = $api;
        $this->load();
    }
    
    protected function loadDatas() {
        $api = $this->api->getDatas();
        if(!is_null($api)) {
            $this->data->datas = $api->get();
        }
    }
    
    protected function loadCargos() {
        $api = $this->api->getCargos();
        if(!is_null($api)) {
            $this->data->cargos = $api->getDatas();
            foreach($api->getDatas() as $cargo) {
                $referencia = array_filter($this->data->datas->getReferenciasCargos(), function($a) use ($cargo) {
                    return ($a->value == $cargo->getInstancia());
                });
                if(!empty($referencia)) {
                    $referencia = array_pop($referencia);
                    $cargo->setNome($cargo->getNome() . ' na ' . $referencia->label);
                }
            }
        }
    }
    
    protected function loadSinodos() {
        $api = $this->api->getSinodos();
        if(!is_null($api)) {
            $this->data->sinodos = $api->getDatas();
        }
    }
    
    protected function loadPresbiterios() {
        $api = $this->api->getPresbiterios();
        if(!is_null($api)) {
            $this->data->presbiterios = $api->getDatas();
        }
    }
    
    protected function loadIgrejas() {
        $api = $this->api->getTemplos();
        if(!is_null($api)) {
            $this->data->igrejas = $api->getDatas();
        }
    }
    
    protected function loadSinodais() {
        $api = $this->api->getSinodais();
        if(!is_null($api)) {
            $this->data->sinodais = $api->getDatas();
        }
    }
    
    protected function loadFederacoes() {
        $api = $this->api->getFederacoes();
        if(!is_null($api)) {
            $this->data->federacoes = $api->getDatas();
        }
    }
    
    protected function loadSociedades() {
        $api = $this->api->getSociedades();
        if(!is_null($api)) {
            $this->data->sociedades = $api->getDatas();
        }
    }
    
    protected function load() {
        $this->data = new \stdClass();
        $this->data->datas = array();
        $this->data->cargos = array();
        $this->data->sinodos = array();
        $this->data->presbiterios = array();
        $this->data->igrejas = array();
        $this->data->sinodais = array();
        $this->data->federacoes = array();
        $this->data->sociedades = array();
        
        $this->loadDatas();
        $this->loadCargos();
        $this->loadSinodos();
        $this->loadPresbiterios();
        $this->loadIgrejas();
        $this->loadSinodais();
        $this->loadFederacoes();
        $this->loadSociedades();
    }
    
    protected function getEncodedData() {
        return base64_encode(json_encode($this->data));
    }
    
}

