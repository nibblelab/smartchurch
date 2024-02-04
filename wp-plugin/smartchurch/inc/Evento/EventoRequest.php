<?php

namespace SmartChurch\Evento;

use SmartChurch\Config\Config;
use SmartChurch\Request\HTTPMethod;
use \SmartChurch\Error\Errors;
use SmartChurch\Request\RequestConfig;
use SmartChurch\Request\Request;
use SmartChurch\Evento\Response\EventoResponseData;
use SmartChurch\Evento\Response\EventoResponse;

class EventoRequest extends Request
{
    private $url;
    
    private function generateEventoData($data): \SmartChurch\Evento\Response\EventoResponseData 
    {
        $response_data = new EventoResponseData();
        $response_data->setId($data->id);
        $response_data->setAgenda($data->agenda);
        $response_data->setChave($data->chave);
        $response_data->setNome($data->nome);
        if(!empty($data->logo)) {
            $response_data->setHaslogo(true);
            $response_data->setLogo($this->url . Config::RESOURCE_PATH . $data->logo);
        }
        else {
            $response_data->setHaslogo(false);
        }
        $response_data->setDescricao($data->descricao);
        $response_data->setRef($data->ref);
        $response_data->setRef_tp($data->ref_tp);
        $response_data->setInscricoesAtivas($data->inscricoes_ativas);
        $response_data->setFormularioInscricao($data->formulario_inscricao);
        $response_data->setValor($data->valor);
        $response_data->setOpcoesPagto($data->opcoes_pagto);
        $response_data->setLotes($data->lotes);
        $response_data->setFimInscricao($data->fim_inscricao);
        $response_data->setTimeIni($data->time_ini);
        $response_data->setTimeEnd($data->time_end);
        $response_data->setTemEleicoes($data->tem_eleicoes);
        $response_data->setStat($data->stat);
        $response_data->setTimeCad(new \DateTime($data->time_cad));
        $response_data->setLastMod(new \DateTime($data->last_mod));
        $response_data->setLastAmod($data->last_amod);
        
        return $response_data;
    }
        
    /**
     * Processa a resposta da requisição
     * 
     * @param string $json_str string com o json de resposta
     * @return \SmartChurch\Evento\Response\EventoResponse
     * @throws \Exception
     */
    private function decodeResponse($json_str, $multiple = true): \SmartChurch\Evento\Response\EventoResponse
    {
        $result_json = json_decode($json_str);
        if ($result_json === null && json_last_error() !== JSON_ERROR_NONE) {
            // erro ao decodificar 
            $json_except = new \Exception(json_last_error_msg(),json_last_error());
            throw new \Exception("O resultado da requisição não poderam ser interpretados",Errors::REQUEST_ERROR,$json_except);
        }
        
        // processe o retorno e gere os devidos objetos 
        $response = new EventoResponse();
        $response->setSuccess($result_json->success);
        
        if($result_json->success) {
            // deu certo. Processe o retorno
            if($multiple) {
                foreach($result_json->datas as $data)
                {
                    $response->add($this->generateEventoData($data));
                }
            }
            else {
                $response->add($this->generateEventoData($result_json->datas));
            }
        }
        else {
            // deu errado
            $response->setMsg($result_json->msg);
        }
        
        return $response;
    }
    
    public function requestAll(RequestConfig $config, $data): ?\SmartChurch\Evento\Response\EventoResponse
    {
        $this->url = $config->getUrl();
        try {
            $result = $this->doRequest($config->getUrl() . 'eventos/all', $data, $config->getToken(), HTTPMethod::GET);
            return $this->decodeResponse($result, true);
        } catch (Exception $ex) {
            throw  $ex;
        }
    }
    
    public function requestMe(RequestConfig $config, $data): ?\SmartChurch\Evento\Response\EventoResponse
    {
        $this->url = $config->getUrl();
        try {
            $result = $this->doRequest($config->getUrl() . 'eventos/me', $data, $config->getToken(), HTTPMethod::GET);
            return $this->decodeResponse($result, false);
        } catch (Exception $ex) {
            throw  $ex;
        }
    }
    
    public function requestByChave(RequestConfig $config, $data): ?\SmartChurch\Evento\Response\EventoResponse
    {
        $this->url = $config->getUrl();
        try {
            $result = $this->doRequest($config->getUrl() . 'eventos/byChave', $data, $config->getToken(), HTTPMethod::GET);
            return $this->decodeResponse($result, false);
        } catch (Exception $ex) {
            throw  $ex;
        }
    }
    
    public function getAllByRef(RequestConfig $config, $ref, $ref_tp, $page = -1, $pageSize = 5): ?\SmartChurch\Evento\Response\EventoResponse
    {
        $data = array(
            'ref' => $ref,
            'ref_tp' => $ref_tp,
            'stat' => 'ATV',
            'orderBy' => 'time_cad,desc'
        );
        
        if($page != -1) {
            $data['page'] = $page;
            $data['pageSize'] = $pageSize;
        }
        
        return $this->requestAll($config, $data);
    }
    
    public function getMe(RequestConfig $config, $id): ?\SmartChurch\Evento\Response\EventoResponse
    {
        $data = array(
            'id' => $id
        );
        return $this->requestMe($config, $data);
    }
    
    public function getByChave(RequestConfig $config, $chave): ?\SmartChurch\Evento\Response\EventoResponse
    {
        $data = array(
            'chave' => $chave
        );
        return $this->requestByChave($config, $data);
    }
}

