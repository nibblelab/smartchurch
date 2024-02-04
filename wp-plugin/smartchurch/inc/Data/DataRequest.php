<?php

namespace SmartChurch\Data;

use SmartChurch\Request\HTTPMethod;
use \SmartChurch\Error\Errors;
use SmartChurch\Request\RequestConfig;
use SmartChurch\Request\Request;
use SmartChurch\Data\Response\DataResponseData;
use SmartChurch\Data\Response\DataResponse;

class DataRequest extends Request
{
    private $url;
    
    private function generateData($data): \SmartChurch\Data\Response\DataResponseData 
    {
        $response_data = new DataResponseData();
        $response_data->setStatus($data->status);
        $response_data->setReferencias($data->referencias);
        $response_data->setReferenciasCargos($data->referencias_cargos);
        $response_data->setSociedades($data->sociedades);
        $response_data->setEscolaridade($data->escolaridade);
        $response_data->setEscolaridadeVoid($data->escolaridade_void);
        $response_data->setEstadoCivil($data->estado_civil);
        $response_data->setEstadoCivilVoid($data->estado_civil_void);
        $response_data->setSexo($data->sexo);
        $response_data->setRelacaoFamiliar($data->relacao_familiar);
        $response_data->setFrequencia($data->frequencia);
        $response_data->setPagamentoStatus($data->pagamento_status);
        $response_data->setAprovacaoAta($data->aprovacao_ata);
        $response_data->setStatusInscricao($data->status_inscricao);
        $response_data->setProfissaoFe($data->profissao_fe);
        $response_data->setRegistroFinanceiro($data->registro_financeiro);
        $response_data->setTipoOficiais($data->tipo_oficiais);
        $response_data->setDisponibilidadeOficiais($data->disponibilidade_oficiais);
        $response_data->setResponsaveisVirtuais($data->responsaveis_virtuais);
        $response_data->setFormasPagto($data->formas_pagto);
        $response_data->setFormularioInscricao($data->formulario_inscricao);
        $response_data->setOpcaoPagto($data->opcao_pagto);
        $response_data->setLotePagto($data->lote_pagto);
        $response_data->setTiposSecretario($data->tipos_secretario);
        $response_data->setUfs($data->ufs);
        $response_data->setCidades($data->cidades);
        
        return $response_data;
    }
        
    /**
     * Processa a resposta da requisição
     * 
     * @param string $json_str string com o json de resposta
     * @return \SmartChurch\Data\Response\DataResponse
     * @throws \Exception
     */
    private function decodeResponse($json_str, $multiple = true): \SmartChurch\Data\Response\DataResponse
    {
        $result_json = json_decode($json_str);
        if ($result_json === null && json_last_error() !== JSON_ERROR_NONE) {
            // erro ao decodificar 
            $json_except = new \Exception(json_last_error_msg(),json_last_error());
            throw new \Exception("O resultado da requisição não poderam ser interpretados",Errors::REQUEST_ERROR,$json_except);
        }
        
        // processe o retorno e gere os devidos objetos 
        $response = new DataResponse();
        $response->setSuccess($result_json->success);
        
        if($result_json->success) {
            // deu certo. Processe o retorno
            if($multiple) {
                foreach($result_json->datas as $data)
                {
                    $response->add($this->generateData($data));
                }
            }
            else {
                $response->add($this->generateData($result_json->datas));
            }
        }
        else {
            // deu errado
            $response->setMsg($result_json->msg);
        }
        
        return $response;
    }
    
    public function requestAll(RequestConfig $config): ?\SmartChurch\Data\Response\DataResponse
    {
        $this->url = $config->getUrl();
        try {
            $result = $this->doRequest($config->getUrl() . 'data/all', '', $config->getToken(), HTTPMethod::GET);
            return $this->decodeResponse($result, false);
        } catch (Exception $ex) {
            throw  $ex;
        }
    }
    
    public function getAll(RequestConfig $config): ?\SmartChurch\Data\Response\DataResponse
    {
        return $this->requestAll($config);
    }
}

