<?php

namespace SmartChurch\Igreja;

use SmartChurch\Request\HTTPMethod;
use \SmartChurch\Error\Errors;
use SmartChurch\Request\RequestConfig;
use SmartChurch\Request\Request;
use SmartChurch\Igreja\Response\IgrejaResponseData;
use SmartChurch\Igreja\Response\IgrejaResponse;

class IgrejaRequest extends Request
{
    private $url;
    
    private function generateIgrejaData($data): \SmartChurch\Igreja\Response\IgrejaResponseData 
    {
        $response_data = new IgrejaResponseData();
        $response_data->setId($data->id);
        $response_data->setSinodo($data->sinodo);
        $response_data->setPresbiterio($data->presbiterio);
        $response_data->setIgreja($data->igreja);
        $response_data->setNome($data->nome);
        $response_data->setFundacao((is_null($data->fundacao)) ? null : new \DateTime($data->fundacao));
        $response_data->setOrganizada($data->organizada);
        $response_data->setTelefone($data->telefone);
        $response_data->setEmail($data->email);
        $response_data->setStat($data->stat);
        $response_data->setEndereco($data->endereco);
        $response_data->setNumero($data->numero);
        $response_data->setComplemento($data->complemento);
        $response_data->setBairro($data->bairro);
        $response_data->setCidade($data->cidade);
        $response_data->setUf($data->uf);
        $response_data->setCep($data->cep);
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
     * @return \SmartChurch\Igreja\Response\IgrejaResponse
     * @throws \Exception
     */
    private function decodeResponse($json_str, $multiple = true): \SmartChurch\Igreja\Response\IgrejaResponse
    {
        $result_json = json_decode($json_str);
        if ($result_json === null && json_last_error() !== JSON_ERROR_NONE) {
            // erro ao decodificar 
            $json_except = new \Exception(json_last_error_msg(),json_last_error());
            throw new \Exception("O resultado da requisição não poderam ser interpretados",Errors::REQUEST_ERROR,$json_except);
        }
        
        // processe o retorno e gere os devidos objetos 
        $response = new IgrejaResponse();
        $response->setSuccess($result_json->success);
        
        if($result_json->success) {
            // deu certo. Processe o retorno
            if($multiple) {
                foreach($result_json->datas as $data)
                {
                    $response->add($this->generateIgrejaData($data));
                }
            }
            else {
                $response->add($this->generateIgrejaData($result_json->datas));
            }
        }
        else {
            // deu errado
            $response->setMsg($result_json->msg);
        }
        
        return $response;
    }
    
    public function requestAll(RequestConfig $config, $data): ?\SmartChurch\Igreja\Response\IgrejaResponse
    {
        $this->url = $config->getUrl();
        try {
            $result = $this->doRequest($config->getUrl() . 'igrejas/all', $data, $config->getToken(), HTTPMethod::GET);
            return $this->decodeResponse($result, true);
        } catch (Exception $ex) {
            throw  $ex;
        }
    }
    
    public function requestMe(RequestConfig $config, $data): ?\SmartChurch\Igreja\Response\IgrejaResponse
    {
        $this->url = $config->getUrl();
        try {
            $result = $this->doRequest($config->getUrl() . 'igrejas/me', $data, $config->getToken(), HTTPMethod::GET);
            return $this->decodeResponse($result, false);
        } catch (Exception $ex) {
            throw  $ex;
        }
    }
    
    public function getAll(RequestConfig $config, $page = -1, $pageSize = 5): ?\SmartChurch\Igreja\Response\IgrejaResponse
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
    
    public function getMe(RequestConfig $config, $id): ?\SmartChurch\Igreja\Response\IgrejaResponse
    {
        $data = array(
            'id' => $id
        );
        return $this->requestMe($config, $data);
    }
}

