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

namespace IBGEApiClient\Municipio\Response;

use \IBGEApiClient\MicroRegiao\Response\MicroRegiaoResponseData;

/**
 * Resposta da busca de Município
 */
class MunicipioResponseData 
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
     * @var \IBGEApiClient\MicroRegiao\Response\MicroRegiaoResponseData
     */
    private $microrregiao;
    
    /**
     * Obtêm o id do município
     * 
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Seta o id do município
     * 
     * @param int $id
     * @return void
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * Obtêm o nome do município
     * 
     * @return string|null
     */
    public function getNome(): ?string
    {
        return $this->nome;
    }

    /**
     * Seta o nome do município
     * 
     * @param string $nome
     * @return void
     */
    public function setNome($nome): void
    {
        $this->nome = $nome;
    }
    
    /**
     * Obtêm a microrregião do município
     * 
     * @return \IBGEApiClient\MicroRegiao\Response\MicroRegiaoResponseData
     */
    public function getMesorregiao(): \IBGEApiClient\MicroRegiao\Response\MicroRegiaoResponseData
    {
        return $this->microrregiao;
    }

    /**
     * Seta a microrregião do município
     * 
     * @param \IBGEApiClient\MicroRegiao\Response\MicroRegiaoResponseData $microrregiao
     */
    public function setMesorregiao($microrregiao)
    {
        $this->microrregiao = $microrregiao;
    }
}

