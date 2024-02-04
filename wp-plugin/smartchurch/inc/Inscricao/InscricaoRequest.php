<?php

namespace SmartChurch\Inscricao;

use SmartChurch\Request\HTTPMethod;
use \SmartChurch\Error\Errors;
use SmartChurch\Request\RequestConfig;
use SmartChurch\Request\Request;
use SmartChurch\Inscricao\Response\InscricaoResponse;

class InscricaoRequest extends Request
{
    private $url;
     
    /**
     * Processa a resposta da requisição
     * 
     * @param string $json_str string com o json de resposta
     * @return \SmartChurch\Inscricao\Response\InscricaoResponse
     * @throws \Exception
     */
    private function decodeResponse($json_str): \SmartChurch\Inscricao\Response\InscricaoResponse
    {
        $result_json = json_decode($json_str);
        if ($result_json === null && json_last_error() !== JSON_ERROR_NONE) {
            // erro ao decodificar 
            $json_except = new \Exception(json_last_error_msg(),json_last_error());
            throw new \Exception("O resultado da requisição não poderam ser interpretados",Errors::REQUEST_ERROR, $json_except);
        }
        
        // processe o retorno e gere os devidos objetos 
        $response = new InscricaoResponse();
        $response->setSuccess($result_json->success);
        
        if(!$result_json->success) {
            $response->setMsg($result_json->msg);
        }
        
        return $response;
    }
    
    public function subscribe(RequestConfig $config, $data): ?\SmartChurch\Inscricao\Response\InscricaoResponse
    {
        $this->url = $config->getUrl();
        try {
            $result = $this->doRequest($config->getUrl() . 'inscricoes/inscrever', $data, $config->getToken(), HTTPMethod::POST);
            return $this->decodeResponse($result);
        } catch (Exception $ex) {
            throw  $ex;
        }
    }
    
    public function doSubscribe(RequestConfig $config, $nome, $email, $sexo, $data_nascimento, $estado_civil, $telefone, 
                                $celular_1, $celular_2, $nome_evento, $evento, $igreja, $presbiterio, $sinodo,
                                $sociedade, $federacao, $sinodal, $delegado, $cargo_ref, $cargo, $has_pagto, 
                                $forma_pagto, $stat_pagto, $valor_pago, $data_pagto): ?\SmartChurch\Inscricao\Response\InscricaoResponse
    {
        
        $data = array(
            'inscrito_data' => array(
                'nome' => $nome,
                'email' => $email,
                'sexo' => $sexo,
                'data_nascimento' => $data_nascimento,
                'estado_civil' => $estado_civil,
                'telefone' => $telefone,
                'celular_1' => $celular_1,
                'celular_2' => $celular_2
            ),
            'nome' => $nome,
            'email' => $email,
            'nome_evento' => $nome_evento,
            'evento' => $evento,
            'igreja' => $igreja,
            'presbiterio' => $presbiterio,
            'sinodo' => $sinodo,
            'sociedade' => $sociedade,
            'federacao' => $federacao,
            'sinodal' => $sinodal,
            'delegado' => $delegado,
            'cargo_ref' => $cargo_ref,
            'cargo' => $cargo,
            'has_pagto' => $has_pagto,
            'forma_pagto' => $forma_pagto,
            'stat_pagto' => $stat_pagto,
            'valor_pago' => $valor_pago,
            'data_pagto' => $data_pagto
        );
        return $this->subscribe($config, $data);
    }
}

