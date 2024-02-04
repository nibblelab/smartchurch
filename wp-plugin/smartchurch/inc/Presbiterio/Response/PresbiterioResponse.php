<?php

namespace SmartChurch\Presbiterio;

use SmartChurch\Request\HTTPMethod;
use \SmartChurch\Error\Errors;
use SmartChurch\Request\RequestConfig;
use SmartChurch\Request\Request;
use SmartChurch\Presbiterio\Response\PresbiterioResponseData;
use SmartChurch\Presbiterio\Response\PresbiterioResponse;

class PresbiterioRequest extends Request
{
    private $url;
    
    private function generatePresbiterioData($data): \SmartChurch\Presbiterio\Response\PresbiterioResponseData 
    {    
        $response_data = new PresbiterioResponseData();
        $response_data->setId($data->id);
        $response_data->setSinodo($data->sinodo);
        $response_data->setSigla($data->sigla);
        $response_data->setNome($data->nome);
        $response_data->setFundacao((is_null($data->fundacao)) ? null : new \DateTime($data->fundacao));
        $response_data->setLocalidades($data->localidades);
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
     * @return \SmartChurch\Presbiterio\Response\PresbiterioResponse
     * @throws \Exception
     */
    private function decodeResponse($json_str, $multiple = true): \SmartChurch\Presbiterio\Response\PresbiterioResponse
    {
        $result_json = json_decode($json_str);
        if ($result_json === null && json_last_error() !== JSON_ERROR_NONE) {
            // erro ao decodificar 
            $json_except = new \Exception(json_last_error_msg(),json_last_error());
            throw new \Exception("O resultado da requisição não poderam ser interpretados",Errors::REQUEST_ERROR,$json_except);
        }
        
        // processe o retorno e gere os devidos objetos 
        $response = new PresbiterioResponse();
        $response->setSuccess($result_json->success);
        
        if($result_json->success) {
            // deu certo. Processe o retorno
            if($multiple) {
                foreach($result_json->datas as $data)
                {
                    $response->add($this->generatePresbiterioData($data));
                }
            }
            else {
                $response->add($this->generatePresbiterioData($result_json->datas));
            }
        }
        else {
            // deu errado
            $response->setMsg($result_json->msg);
        }
        
        return $response;
    }
    
    public function requestAll(RequestConfig $config, $data): ?\SmartChurch\Presbiterio\Response\PresbiterioResponse
    {
        $this->url = $config->getUrl();
        try {
            $result = $this->doRequest($config->getUrl() . 'presbiterios/all', $data, $config->getToken(), HTTPMethod::GET);
            return $this->decodeResponse($result, true);
        } catch (Exception $ex) {
            throw  $ex;
        }
    }
    
    public function requestMe(RequestConfig $config, $data): ?\SmartChurch\Presbiterio\Response\PresbiterioResponse
    {
        $this->url = $config->getUrl();
        try {
            $result = $this->doRequest($config->getUrl() . 'presbiterios/me', $data, $config->getToken(), HTTPMethod::GET);
            return $this->decodeResponse($result, false);
        } catch (Exception $ex) {
            throw  $ex;
        }
    }
    
    public function getAll(RequestConfig $config, $page = -1, $pageSize = 5): ?\SmartChurch\Presbiterio\Response\PresbiterioResponse
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
    
    public function getMe(RequestConfig $config, $id): ?\SmartChurch\Presbiterio\Response\PresbiterioResponse
    {
        $data = array(
            'id' => $id
        );
        return $this->requestMe($config, $data);
    }
}

