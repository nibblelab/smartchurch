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

namespace IBGEApiClient\Response;


/**
 * Parte comum das respostas de requisições
 */
class Response
{
    /**
     * Status da resposta
     *
     * @var array 
     */
    protected $data;
    
    /**
     * Obtêm os dados
     * 
     * @return bool
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Seta os dados
     * 
     * @param array $data
     * @return void
     */
    public function setData($data): void
    {
        $this->data = $data;
    }

    
}

