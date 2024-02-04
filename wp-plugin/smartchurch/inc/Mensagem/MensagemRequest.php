<?php

namespace SmartChurch\Mensagem;

use SmartChurch\Config\Config;
use SmartChurch\Request\HTTPMethod;
use \SmartChurch\Error\Errors;
use SmartChurch\Request\RequestConfig;
use SmartChurch\Request\Request;
use SmartChurch\Mensagem\Response\MensagemResponseData;
use SmartChurch\Mensagem\Response\MensagemDownloadData;
use SmartChurch\Mensagem\Response\MensagemResponse;


class MensagemRequest extends Request
{
    private $url;
    
    private function generateMensagemData($data): \SmartChurch\Mensagem\Response\MensagemResponseData
    {
        $response_data = new MensagemResponseData();
        $response_data->setId($data->id);
        $response_data->setIgreja($data->igreja);
        $response_data->setSerie($data->serie);
        $response_data->setAutor($data->autor);
        $response_data->setTitulo($data->titulo);
        $response_data->setChave($data->chave);
        if(!empty($data->logo)) {
            $response_data->setHaslogo(true);
            $response_data->setLogo($this->url . Config::RESOURCE_PATH . $data->logo);
        }
        else {
            $response_data->setHaslogo(false);
        }
        $response_data->setConteudo($data->conteudo);
        $response_data->setAnexo($data->anexo);
        $data_sermao = (!empty($data->data_sermao) && !is_null($data->data_sermao)) ? new \DateTime($data->data_sermao) : null;
        $response_data->setDataSermao($data_sermao);
        $response_data->setVideo($data->video);
        $response_data->setAudio($data->audio);
        $response_data->setStat($data->stat);
        $response_data->setTimeCad(new \DateTime($data->time_cad));
        $response_data->setLastMod(new \DateTime($data->last_mod));
        $response_data->setLastAmod($data->last_amod);
        
        return $response_data;
    }
    
    private function generateMensagemDownloadData($data): \SmartChurch\Mensagem\Response\MensagemDownloadData
    {
        $response_data = new MensagemDownloadData();
        $response_data->setName($data->name);
        $response_data->setType($data->type);
        $response_data->setContent($data->content);
        $response_data->setSize($data->size);
        
        return $response_data;
    }
    
    /**
     * Processa a resposta da requisição
     * 
     * @param string $json_str string com o json de resposta
     * @return \SmartChurch\Mensagem\Response\MensagemResponse
     * @throws \Exception
     */
    private function decodeResponse($json_str, $multiple = true): \SmartChurch\Mensagem\Response\MensagemResponse
    {
        $result_json = json_decode($json_str);
        if ($result_json === null && json_last_error() !== JSON_ERROR_NONE) {
            // erro ao decodificar 
            $json_except = new \Exception(json_last_error_msg(),json_last_error());
            throw new \Exception("O resultado da requisição não poderam ser interpretados",Errors::REQUEST_ERROR,$json_except);
        }
        
        // processe o retorno e gere os devidos objetos 
        $response = new MensagemResponse();
        $response->setSuccess($result_json->success);
        
        if($result_json->success) {
            // deu certo. Processe o retorno
            if($multiple) {
                foreach($result_json->datas as $data)
                {
                    $response->add($this->generateMensagemData($data));
                }
            }
            else {
                $response->add($this->generateMensagemData($result_json->datas));
            }
        }
        else {
            // deu errado
            $response->setMsg($result_json->msg);
        }
        
        return $response;
    }
    
    private function decodeDownloadResponse($json_str): \SmartChurch\Mensagem\Response\MensagemResponse
    {
        $result_json = json_decode($json_str);
        if ($result_json === null && json_last_error() !== JSON_ERROR_NONE) {
            // erro ao decodificar 
            $json_except = new \Exception(json_last_error_msg(),json_last_error());
            throw new \Exception("O resultado da requisição não poderam ser interpretados",Errors::REQUEST_ERROR,$json_except);
        }
        
        // processe o retorno e gere os devidos objetos 
        $response = new MensagemResponse();
        $response->setSuccess($result_json->success);
        
        if($result_json->success) {
            // deu certo. Processe o retorno
            $response->addDownload($this->generateMensagemDownloadData($result_json));
        }
        else {
            // deu errado
            $response->setMsg($result_json->msg);
        }
        
        return $response;
    }
    
    public function requestAll(RequestConfig $config, $data): ?\SmartChurch\Mensagem\Response\MensagemResponse
    {
        $this->url = $config->getUrl();
        try {
            $result = $this->doRequest($config->getUrl() . 'sermoes/all', $data, $config->getToken(), HTTPMethod::GET);
            return $this->decodeResponse($result);
        } catch (Exception $ex) {
            throw  $ex;
        }
    }
    
    public function requestMe(RequestConfig $config, $data): ?\SmartChurch\Mensagem\Response\MensagemResponse
    {
        $this->url = $config->getUrl();
        try {
            $result = $this->doRequest($config->getUrl() . 'sermoes/me', $data, $config->getToken(), HTTPMethod::GET);
            return $this->decodeResponse($result, false);
        } catch (Exception $ex) {
            throw  $ex;
        }
    }
    
    public function requestByChave(RequestConfig $config, $data): ?\SmartChurch\Mensagem\Response\MensagemResponse
    {
        $this->url = $config->getUrl();
        try {
            $result = $this->doRequest($config->getUrl() . 'sermoes/bychave', $data, $config->getToken(), HTTPMethod::GET);
            return $this->decodeResponse($result, false);
        } catch (Exception $ex) {
            throw  $ex;
        }
    }
    
    public function requestDownload(RequestConfig $config, $data): ?\SmartChurch\Mensagem\Response\MensagemResponse
    {
        $this->url = $config->getUrl();
        try {
            $result = $this->doRequest($config->getUrl() . 'sermoes/download', $data, $config->getToken(), HTTPMethod::GET);
            return $this->decodeDownloadResponse($result);
        } catch (Exception $ex) {
            throw  $ex;
        }
    }
    
    public function getAll(RequestConfig $config, $igreja, $serie = '', $page = -1, $pageSize = 5): ?\SmartChurch\Mensagem\Response\MensagemResponse
    {
        $data = array(
            'igreja' => $igreja,
            'serie' => $serie,
            'stat' => 'ATV',
            'orderBy' => 'time_cad,desc'
        );
        
        if($page != -1) {
            $data['page'] = $page;
            $data['pageSize'] = $pageSize;
        }
        
        return $this->requestAll($config, $data);
    }
    
    public function getLasts(RequestConfig $config, $igreja, $size = 6): ?\SmartChurch\Mensagem\Response\MensagemResponse 
    {
        return $this->getAll($config, $igreja,'', 1, $size);
    }
    
    public function getMe(RequestConfig $config, $id): ?\SmartChurch\Mensagem\Response\MensagemResponse
    {
        $data = array(
            'id' => $id
        );
        return $this->requestMe($config, $data);
    }
    
    public function getByChave(RequestConfig $config, $chave): ?\SmartChurch\Mensagem\Response\MensagemResponse
    {
        $data = array(
            'chave' => $chave
        );
        return $this->requestByChave($config, $data);
    }
    
    public function getDownload(RequestConfig $config, $id): ?\SmartChurch\Mensagem\Response\MensagemResponse
    {
        $data = array(
            'id' => $id
        );
        return $this->requestDownload($config, $data);
    }
}

