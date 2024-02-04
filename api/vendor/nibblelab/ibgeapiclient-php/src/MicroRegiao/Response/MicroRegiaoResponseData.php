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

namespace IBGEApiClient\MicroRegiao\Response;

use \IBGEApiClient\MesoRegiao\Response\MesoRegiaoResponseData;

/**
 * Resposta da busca de Microrregião
 */
class MicroRegiaoResponseData 
{
    /**
     * Id da mesorregião
     *
     * @var int 
     */
    private $id;
    /**
     * Nome da mesorregião
     *
     * @var string 
     */
    private $nome;
    /**
     * UF da mesorregião
     *
     * @var \IBGEApiClient\MesoRegiao\Response\MesoRegiaoResponseData
     */
    private $mesorregiao;
    
    /**
     * Obtêm o id da microregião
     * 
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Seta o id da microregião
     * 
     * @param int $id
     * @return void
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * Obtêm o nome da microregião
     * 
     * @return string|null
     */
    public function getNome(): ?string
    {
        return $this->nome;
    }

    /**
     * Seta o nome da microregião
     * 
     * @param string $nome
     * @return void
     */
    public function setNome($nome): void
    {
        $this->nome = $nome;
    }
    
    /**
     * Obtêm a mesorregião da microregião
     * 
     * @return \IBGEApiClient\MesoRegiao\Response\MesoRegiaoResponseData
     */
    public function getMesorregiao(): \IBGEApiClient\MesoRegiao\Response\MesoRegiaoResponseData
    {
        return $this->mesorregiao;
    }

    /**
     * Seta a mesorregião da microregião
     * 
     * @param \IBGEApiClient\MesoRegiao\Response\MesoRegiaoResponseData $mesorregiao
     */
    public function setMesorregiao($mesorregiao)
    {
        $this->mesorregiao = $mesorregiao;
    }
}

