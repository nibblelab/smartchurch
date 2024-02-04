<?php

namespace SmartChurch\Serie;

use SmartChurch\Config\Config;
use SmartChurch\Request\HTTPMethod;
use \SmartChurch\Error\Errors;
use SmartChurch\Request\RequestConfig;
use SmartChurch\Request\Request;
use SmartChurch\Serie\Response\SerieResponseData;
use SmartChurch\Serie\Response\SerieResponse;

class SerieRequest extends Request
{
    private $url;
    
    private function generateSerieData($data): \SmartChurch\Serie\Response\SerieResponseData 
    {
        $response_data = new SerieResponseData();
        $response_data->setId($data->id);
        $response_data->setIgreja($data->igreja);
        $response_data->setNome($data->nome);
        $response_data->setChave($data->chave);
        if(!empty($data->logo)) {
            $response_data->setHaslogo(true);
            $response_data->setLogo($this->url . Config::RESOURCE_PATH . $data->logo);
        }
        else {
            $response_data->setHaslogo(false);
        }
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
     * @return \SmartChurch\Serie\Response\SerieResponse
     * @throws \Exception
     */
    private function decodeResponse($json_str, $multiple = true): \SmartChurch\Serie\Response\SerieResponse
    {
        $result_json = json_decode($json_str);
        if ($result_json === null && json_last_error() !== JSON_ERROR_NONE) {
            // erro ao decodificar 
            $json_except = new \Exception(json_last_error_msg(),json_last_error());
            throw new \Exception("O resultado da requisição não poderam ser interpretados",Errors::REQUEST_ERROR,$json_except);
        }
        
        // processe o retorno e gere os devidos objetos 
        $response = new SerieResponse();
        $response->setSuccess($result_json->success);
        
        if($result_json->success) {
            // deu certo. Processe o retorno
            if($multiple) {
                foreach($result_json->datas as $data)
                {
                    $response->add($this->generateSerieData($data));
                }
            }
            else {
                $response->add($this->generateSerieData($result_json->datas));
            }
        }
        else {
            // deu errado
            $response->setMsg($result_json->msg);
        }
        
        return $response;
    }
    
    public function requestAll(RequestConfig $config, $data): ?\SmartChurch\Serie\Response\SerieResponse
    {
        $this->url = $config->getUrl();
        try {
            $result = $this->doRequest($config->getUrl() . 'seriesdesermoes/all', $data, $config->getToken(), HTTPMethod::GET);
            return $this->decodeResponse($result, true);
        } catch (Exception $ex) {
            throw  $ex;
        }
    }
    
    public function requestMe(RequestConfig $config, $data): ?\SmartChurch\Serie\Response\SerieResponse
    {
        $this->url = $config->getUrl();
        try {
            $result = $this->doRequest($config->getUrl() . 'seriesdesermoes/me', $data, $config->getToken(), HTTPMethod::GET);
            return $this->decodeResponse($result, false);
        } catch (Exception $ex) {
            throw  $ex;
        }
    }
    
    public function requestByChave(RequestConfig $config, $data): ?\SmartChurch\Serie\Response\SerieResponse
    {
        $this->url = $config->getUrl();
        try {
            $result = $this->doRequest($config->getUrl() . 'seriesdesermoes/bychave', $data, $config->getToken(), HTTPMethod::GET);
            return $this->decodeResponse($result, false);
        } catch (Exception $ex) {
            throw  $ex;
        }
    }
    
    public function getAll(RequestConfig $config, $igreja, $page = -1, $pageSize = 5): ?\SmartChurch\Serie\Response\SerieResponse
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
    
    public function getMe(RequestConfig $config, $id): ?\SmartChurch\Serie\Response\SerieResponse
    {
        $data = array(
            'id' => $id
        );
        return $this->requestMe($config, $data);
    }
    
    public function getByChave(RequestConfig $config, $chave): ?\SmartChurch\Serie\Response\SerieResponse
    {
        $data = array(
            'chave' => $chave
        );
        return $this->requestByChave($config, $data);
    }
}

