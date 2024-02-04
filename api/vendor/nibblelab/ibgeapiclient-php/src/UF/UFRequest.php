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

namespace IBGEApiClient\UF;

use \IBGEApiClient\Error\Errors;
use \IBGEApiClient\Request\HTTPMethod;
use \IBGEApiClient\Request\Request;
use \IBGEApiClient\Response\Response;
use \IBGEApiClient\UF\Response\UFResponseData;
use \IBGEApiClient\Regiao\RegiaoRequest;

/**
 * Requisição de lista das unidades federativas
 */
class UFRequest extends Request
{
    /**
     * Buffer de regiões já cadastradas para evitar mal uso de memória
     *
     * @var array
     */
    private $regioes;
    /**
     *
     * @var \IBGEApiClient\Regiao\RegiaoRequest 
     */
    private $regiao_req;
    
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
        $this->prepareAux();
        if(is_array($result_json)) {
            foreach($result_json as $data) {
                $result[] = $this->mountUF($data);
            }
        }
        else {
            $result[] = $this->mountUF($result_json);
        }
        
        $response->setData($result);
        
        return $response;
    }
    
    /**
     * Prepare os objetos auxiliares
     * 
     * @return void
     */
    public function prepareAux(): void
    {
        $this->regioes = array();
        $this->regiao_req = new RegiaoRequest();
    }
    
    /**
     * Gere os dados da UF a partir do objeto json
     * 
     * @param object $uf_obj
     * @return UFResponseData
     */
    public function mountUF($uf_obj): \IBGEApiClient\UF\Response\UFResponseData
    {
        $uf_data = new UFResponseData();
        $uf_data->setId($uf_obj->id);
        $uf_data->setNome($uf_obj->nome);
        $uf_data->setSigla($uf_obj->sigla);
        // veja se a região já foi obtida, para não criar múltiplos ojetos da mesma informação
        $regiao_id = $uf_obj->regiao->id;
        $regiao_data_prev = array_filter(
            $this->regioes,
            function ($r) use ($regiao_id) {
                return $r->getId() == $regiao_id;
            }
        ); 
        if(empty($regiao_data_prev)) {
            // não foi. Cadastre!
            $regiao_data = $this->regiao_req->mountRegiao($uf_obj->regiao);
            $this->regioes[] = $regiao_data;
            $regiao_data_prev[] = $regiao_data;
        }
        $uf_data->setRegiao(array_pop($regiao_data_prev));
        
        return $uf_data;
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
            $result = $this->doRequest($config['url'] . 'localidades/estados', HTTPMethod::GET);
            return $this->decodeResponse($result);
        } catch (Exception $ex) {
            throw  $ex;
        }
    }
    
    /**
     * requisição por região
     * 
     * @param array $config dados de configuração 
     * @param string $regiao id da microrregião
     * @return \IBGEApiClient\Response\Response|null
     * @throws \Exception
     */
    public function requestByRegiao(array $config, $regiao): ?\IBGEApiClient\Response\Response
    { 
        try {
            $result = $this->doRequest($config['url'] . 'localidades/regioes/' . $regiao . '/estados', HTTPMethod::GET);
            return $this->decodeResponse($result);
        } catch (Exception $ex) {
            throw  $ex;
        }
    }
    
    /**
     * requisição por regiões
     * 
     * @param array $config dados de configuração 
     * @param array $regioes array com os ids das regiões
     * @return \IBGEApiClient\Response\Response|null
     * @throws \Exception
     */
    public function requestByRegioes(array $config, $regioes): ?\IBGEApiClient\Response\Response
    { 
        $regiao = '';
        foreach($regioes as $_regiao) {
            if(!empty($regiao)) {
                $regiao .= '|';
            }
            $regiao .= $_regiao;
        }
        return $this->requestByRegiao($config, $regiao);
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
            $result = $this->doRequest($config['url'] . 'localidades/estados/' . $id, HTTPMethod::GET);
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
