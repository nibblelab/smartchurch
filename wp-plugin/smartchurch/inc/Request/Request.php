<?php

namespace SmartChurch\Request;

use \SmartChurch\Error\Errors;
use \SmartChurch\Request\HTTPMethod;
use \SmartChurch\Config\SmartChurchConfig;
use \SmartChurch\Log\SmartChurchLog;

/**
 * Processamento de requisições na API
 */
class Request
{
    /**
     * Realiza uma requisição na API
     * 
     * @param string $url url destino da requisição
     * @param mixed $data array ou string com os dados.
     * @param string $token token de autenticação
     * @param int $http_method flag indicando o tipo de requisição HTTP a se realizada
     * @return string
     * @throws \Exception
     */
    protected function doRequest($url, $data, $token, $http_method = HTTPMethod::POST): string
    {
        // verifique se a lib curl existe
        if(!function_exists('curl_init')) {
            throw new \Exception("A biblioteca CURL não está disponível em seu ambiente",Errors::CURL_NOT_FOUND);
        }
        
        // se é requisição POST é obrigatório passar dados
        if($http_method == HTTPMethod::POST && empty($data)) {
            throw new \Exception("Uma requisição POST não pode ser vazia",Errors::EMPTY_REQUEST);
        }
        
        // se está requisitando via get coloque os dada na url
        if($http_method == HTTPMethod::GET && !empty($data)) {
            if(is_array($data)) {
                // é array. Converta para http query
                $url .= '?' . http_build_query($data);
            }
            else {
                // converta para string e adicione a url
                $url .= '?' . $data;
            }
        }
              
        // abra o recurso
        $curl = curl_init($url);
        // obtenha o retorno
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        // obrigue o uso de UTF-8
        curl_setopt($curl, CURLOPT_ENCODING, 'UTF-8');
        // sete a agent
        curl_setopt($curl, CURLOPT_USERAGENT, 'smartchurch-wp');
        // defina o timeout da requisição
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        // configure os headers
        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
            'Accept-Encoding: gzip, deflate',
            'Accept-Language: en-US,en;q=0.5',
            'Cache-Control: no-cache',
            'Authorization: token=' . $token
        ];
        
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        // verifique se tem ou não suporte a HTTP2
//        if(defined("CURL_VERSION_HTTP2") && (curl_version()["features"] & CURL_VERSION_HTTP2) !== 0) {
//            curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2_0);
//        }
        
        if(SmartChurchConfig::isDebugActive()) {
            SmartChurchLog::start();
            curl_setopt($curl, CURLOPT_VERBOSE, 1);
            curl_setopt($curl, CURLOPT_STDERR, SmartChurchLog::$log);
        }
        
                        
        if($http_method == HTTPMethod::POST) {
            // se é post, configure o curl e envie os dados
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        }
        
        // execute a requisição
        $result = curl_exec($curl);
        
        if(SmartChurchConfig::isDebugActive()) {
            SmartChurchLog::end();
        }
        
        // analise o retorno
        if($result !== false) {
            // feche o recurso
            curl_close($curl);
            // deu certo. Retorne
            return $result;
        }
        else {
            // deu erro. Reporte
            $curl_except = new \Exception(curl_error($curl),curl_errno($curl));
            throw new \Exception("A requisição não pode ser atendida",Errors::CURL_ERROR, $curl_except);
        }
    }
}
