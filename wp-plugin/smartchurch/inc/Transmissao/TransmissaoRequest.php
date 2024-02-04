<?php

namespace SmartChurch\Transmissao;

use SmartChurch\Config\Config;
use SmartChurch\Request\HTTPMethod;
use \SmartChurch\Error\Errors;
use SmartChurch\Request\RequestConfig;
use SmartChurch\Request\Request;
use SmartChurch\Transmissao\Response\TransmissaoResponseData;
use SmartChurch\Transmissao\Response\TransmissaoResponse;

class TransmissaoRequest extends Request 
{
    private $url;
    
    private function generateTransmissaoData($data): \SmartChurch\Transmissao\Response\TransmissaoResponseData
    {
        $response_data = new TransmissaoResponseData();
        $response_data->setId($data->id);
        $response_data->setVideo($data->video);
        $response_data->setStat($data->stat);
        $response_data->setTimeCad(new \DateTime($data->time_cad));
        $response_data->setLastMod(new \DateTime($data->last_mod));
        $response_data->setLastAmod($data->last_amod);
        
        return $response_data;
    }
    
    private function decodeResponse($json_str, $multiple = true): \SmartChurch\Transmissao\Response\TransmissaoResponse
    {
        $result_json = json_decode($json_str);
        if ($result_json === null && json_last_error() !== JSON_ERROR_NONE) {
            // erro ao decodificar 
            $json_except = new \Exception(json_last_error_msg(),json_last_error());
            throw new \Exception("O resultado da requisição não poderam ser interpretados",Errors::REQUEST_ERROR,$json_except);
        }
        
        // processe o retorno e gere os devidos objetos 
        $response = new TransmissaoResponse();
        $response->setSuccess($result_json->success);
        
        if($result_json->success) {
            // deu certo. Processe o retorno
            if($multiple) {
                foreach($result_json->datas as $data)
                {
                    $response->add($this->generateTransmissaoData($data));
                }
            }
            else {
                $response->add($this->generateTransmissaomData($result_json->datas));
            }
        }
        else {
            // deu errado
            $response->setMsg($result_json->msg);
        }
        
        return $response;
    }
    
    public function requestAll(RequestConfig $config, $data): ?\SmartChurch\Transmissao\Response\TransmissaoResponse
    {
        $this->url = $config->getUrl();
        try {
            $result = $this->doRequest($config->getUrl() . 'transmissoes/all', $data, $config->getToken(), HTTPMethod::GET);
            return $this->decodeResponse($result);
        } catch (Exception $ex) {
            throw  $ex;
        }
    }
    
    public function getAll(RequestConfig $config, $igreja, $page = -1, $pageSize = 5): ?\SmartChurch\Transmissao\Response\TransmissaoResponse
    {
        $data = array(
            'igreja' => $igreja,
            'stat' => 'ATV',
            'orderBy' => 'time_cad,desc'
        );
        
        if($page != -1) {
            $data['page'] = $page;
            $data['pageSize'] = $pageSize;
        }
        
        return $this->requestAll($config, $data);
    }
    
    public function getDaIgreja(RequestConfig $config, $igreja): ?\SmartChurch\Transmissao\Response\TransmissaoResponse 
    {
        return $this->getAll($config, $igreja);
    }
}