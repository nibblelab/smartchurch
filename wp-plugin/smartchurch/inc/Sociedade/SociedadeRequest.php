<?php

namespace SmartChurch\Sociedade;

use SmartChurch\Config\Config;
use SmartChurch\Request\HTTPMethod;
use \SmartChurch\Error\Errors;
use SmartChurch\Request\RequestConfig;
use SmartChurch\Request\Request;
use SmartChurch\Sociedade\Response\SociedadeResponseData;
use SmartChurch\Sociedade\Response\SociedadeResponse;

class SociedadeRequest extends Request
{
    private $url;
    
    private function generateSociedadeData($data): \SmartChurch\Sociedade\Response\SociedadeResponseData 
    {
        $response_data = new SociedadeResponseData();
        $response_data->setId($data->id);
        $response_data->setIgreja($data->igreja);
        $response_data->setFederacao($data->federacao);
        $response_data->setSinodal($data->sinodal);
        $response_data->setNacional($data->nacional);
        $response_data->setReference($data->reference);
        $response_data->setNome($data->nome);
        if(!empty($data->logo)) {
            $response_data->setHaslogo(true);
            $response_data->setLogo($this->url . Config::RESOURCE_PATH . $data->logo);
        }
        else {
            $response_data->setHaslogo(false);
        }
        $response_data->setFundacao((is_null($data->fundacao)) ? null : new \DateTime($data->fundacao));
        $response_data->setEmail($data->email);
        $response_data->setTelefone($data->telefone);
        $response_data->setRamal($data->ramal);
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
     * @return \SmartChurch\Sociedade\Response\SociedadeResponse
     * @throws \Exception
     */
    private function decodeResponse($json_str, $multiple = true): \SmartChurch\Sociedade\Response\SociedadeResponse
    {
        $result_json = json_decode($json_str);
        if ($result_json === null && json_last_error() !== JSON_ERROR_NONE) {
            // erro ao decodificar 
            $json_except = new \Exception(json_last_error_msg(),json_last_error());
            throw new \Exception("O resultado da requisição não poderam ser interpretados",Errors::REQUEST_ERROR,$json_except);
        }
        
        // processe o retorno e gere os devidos objetos 
        $response = new SociedadeResponse();
        $response->setSuccess($result_json->success);
        
        if($result_json->success) {
            // deu certo. Processe o retorno
            if($multiple) {
                foreach($result_json->datas as $data)
                {
                    $response->add($this->generateSociedadeData($data));
                }
            }
            else {
                $response->add($this->generateSociedadeData($result_json->datas));
            }
        }
        else {
            // deu errado
            $response->setMsg($result_json->msg);
        }
        
        return $response;
    }
    
    public function requestAll(RequestConfig $config, $data): ?\SmartChurch\Sociedade\Response\SociedadeResponse
    {
        $this->url = $config->getUrl();
        try {
            $result = $this->doRequest($config->getUrl() . 'sociedades/all', $data, $config->getToken(), HTTPMethod::GET);
            return $this->decodeResponse($result, true);
        } catch (Exception $ex) {
            throw  $ex;
        }
    }
    
    public function requestMe(RequestConfig $config, $data): ?\SmartChurch\Sociedade\Response\SociedadeResponse
    {
        $this->url = $config->getUrl();
        try {
            $result = $this->doRequest($config->getUrl() . 'sociedades/me', $data, $config->getToken(), HTTPMethod::GET);
            return $this->decodeResponse($result, false);
        } catch (Exception $ex) {
            throw  $ex;
        }
    }
    
    public function getAll(RequestConfig $config, $page = -1, $pageSize = 5): ?\SmartChurch\Sociedade\Response\SociedadeResponse
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
    
    public function getMe(RequestConfig $config, $id): ?\SmartChurch\Sociedade\Response\SociedadeResponse
    {
        $data = array(
            'id' => $id
        );
        return $this->requestMe($config, $data);
    }
}

