<?php
/**
 * 2019
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @author    Nibblelab Tecnologia LTDA
 * @copyright 2019 Nibblelab Tecnologia LTDA
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 *
 */

namespace IBGEApiClient\Regiao;

use \IBGEApiClient\Error\Errors;
use \IBGEApiClient\Request\HTTPMethod;
use \IBGEApiClient\Request\Request;
use \IBGEApiClient\Response\Response;
use \IBGEApiClient\Regiao\Response\RegiaoResponseData;

/**
 * Requisição de lista das regiões
 */
class RegiaoRequest extends Request
{
    /**
     * Processa a resposta da requisição
     * 
     * @param string $json_str string com o json de resposta
     * @return \IBGEApiClient\Response\Response
     * @throws \Exception
     */
    private function decodeResponse($json_str): \IBGEApiClient\Response\Response
    {
        $result_json = json_decode($json_str);
        if ($result_json === null && json_last_error() !== JSON_ERROR_NONE) {
            // erro ao decodificar 
            $json_except = new \Exception(json_last_error_msg(),json_last_error());
            throw new \Exception("O resultado da requisição não poderam ser interpretados",Errors::REQUEST_ERROR,$json_except);
        }
        
        // processe o retorno e gere os devidos objetos 
        $response = new Response();
        
        $result = array();
        if(is_array($result_json)) {
            foreach($result_json as $data) {
                $result[] = $this->mountRegiao($data);
            }
        }
        else {
            $result[] = $this->mountRegiao($result_json);
        }
        
        $response->setData($result);
        
        return $response;
    }
    
    /**
     * Gera os dados de região a partir de um objeto json
     * 
     * @param object $regiao_obj
     * @return RegiaoResponseData
     */
    public function mountRegiao($regiao_obj): \IBGEApiClient\Regiao\Response\RegiaoResponseData
    {
        $regiao_data = new RegiaoResponseData();
        $regiao_data->setId($regiao_obj->id);
        $regiao_data->setNome($regiao_obj->nome);
        $regiao_data->setSigla($regiao_obj->sigla);
        return $regiao_data;
    } 
    
    /**
     * requisição
     * 
     * @param array $config dados de configuração 
     * @return \IBGEApiClient\Response\Response|null
     * @throws \Exception
     */
    public function request(array $config): ?\IBGEApiClient\Response\Response
    {
        try {
            $result = $this->doRequest($config['url'] . 'localidades/regioes', HTTPMethod::GET);
            return $this->decodeResponse($result);
        } catch (Exception $ex) {
            throw  $ex;
        }
    }
    
    /**
     * requisição por id
     * 
     * @param array $config dados de configuração 
     * @param string $id 
     * @return \IBGEApiClient\Response\Response|null
     * @throws \Exception
     */
    public function requestById(array $config, $id): ?\IBGEApiClient\Response\Response
    {
        try {
            $result = $this->doRequest($config['url'] . 'localidades/regioes/' . $id, HTTPMethod::GET);
            return $this->decodeResponse($result);
        } catch (Exception $ex) {
            throw  $ex;
        }
    }
    
    /**
     * requisição por ids
     * 
     * @param array $config dados de configuração 
     * @param array $ids array com os ids 
     * @return \IBGEApiClient\Response\Response|null
     * @throws \Exception
     */
    public function requestByIds(array $config, $ids): ?\IBGEApiClient\Response\Response
    { 
        $id = '';
        foreach($ids as $_id) {
            if(!empty($id)) {
                $id .= '|';
            }
            $id .= $_id;
        }
        return $this->requestById($config, $id);
    }
}
