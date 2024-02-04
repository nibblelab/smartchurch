<?php

namespace SmartChurch\Sinodal;

use SmartChurch\Request\HTTPMethod;
use \SmartChurch\Error\Errors;
use SmartChurch\Request\RequestConfig;
use SmartChurch\Request\Request;
use SmartChurch\Sinodal\Response\SinodalResponseData;
use SmartChurch\Sinodal\Response\SinodalResponse;

class SinodalRequest extends Request
{
    private $url;
    
    private function generateSinodalData($data): \SmartChurch\Sinodal\Response\SinodalResponseData 
    {
        $response_data = new SinodalResponseData();
        $response_data->setId($data->id);
        $response_data->setSinodo($data->sinodo);
        $response_data->setNacional($data->nacional);
        $response_data->setReference($data->reference);
        $response_data->setSigla($data->sigla);
        $response_data->setNome($data->nome);
        $response_data->setFundacao((is_null($data->fundacao)) ? null : new \DateTime($data->fundacao));
        $response_data->setStat($data->stat);
        $response_data->setSite($data->site);
        $response_data->setFacebook($data->facebook);
        $response_data->setInstagram($data->instagram);
        $response_data->setYoutube($data->youtube);
        $response_data->setVimeo($data->vimeo);
        $response_data->setTimeCad(new \DateTime($data->time_cad));
        $response_data->setLastMod(new \DateTime($data->last_mod));
        $response_data->setLastAmod($data->last_amod);
        
        return $response_data;
    }
        
    /**
     * Processa a resposta da requisição
     * 
     * @param string $json_str string com o json de resposta
     * @return \SmartChurch\Sinodal\Response\SinodalResponse
     * @throws \Exception
     */
    private function decodeResponse($json_str, $multiple = true): \SmartChurch\Sinodal\Response\SinodalResponse
    {
        $result_json = json_decode($json_str);
        if ($result_json === null && json_last_error() !== JSON_ERROR_NONE) {
            // erro ao decodificar 
            $json_except = new \Exception(json_last_error_msg(),json_last_error());
            throw new \Exception("O resultado da requisição não poderam ser interpretados",Errors::REQUEST_ERROR,$json_except);
        }
        
        // processe o retorno e gere os devidos objetos 
        $response = new SinodalResponse();
        $response->setSuccess($result_json->success);
        
        if($result_json->success) {
            // deu certo. Processe o retorno
            if($multiple) {
                foreach($result_json->datas as $data)
                {
                    $response->add($this->generateSinodalData($data));
                }
            }
            else {
                $response->add($this->generateSinodalData($result_json->datas));
            }
        }
        else {
            // deu errado
            $response->setMsg($result_json->msg);
        }
        
        return $response;
    }
    
    public function requestAll(RequestConfig $config, $data): ?\SmartChurch\Sinodal\Response\SinodalResponse
    {
        $this->url = $config->getUrl();
        try {
            $result = $this->doRequest($config->getUrl() . 'sinodais/all', $data, $config->getToken(), HTTPMethod::GET);
            return $this->decodeResponse($result, true);
        } catch (Exception $ex) {
            throw  $ex;
        }
    }
    
    public function requestMe(RequestConfig $config, $data): ?\SmartChurch\Sinodal\Response\SinodalResponse
    {
        $this->url = $config->getUrl();
        try {
            $result = $this->doRequest($config->getUrl() . 'sinodais/me', $data, $config->getToken(), HTTPMethod::GET);
            return $this->decodeResponse($result, false);
        } catch (Exception $ex) {
            throw  $ex;
        }
    }
    
    public function getAll(RequestConfig $config, $page = -1, $pageSize = 5): ?\SmartChurch\Sinodal\Response\SinodalResponse
    {
        $data = array(
            'stat' => 'ATV',
            'orderBy' => 'nome,asc'
        );
        
        if($page != -1) {
            $data['page'] = $page;
            $data['pageSize'] = $pageSize;
        }
        
        return $this->requestAll($config, $data);
    }
    
    public function getMe(RequestConfig $config, $id): ?\SmartChurch\Sinodal\Response\SinodalResponse
    {
        $data = array(
            'id' => $id
        );
        return $this->requestMe($config, $data);
    }
}

