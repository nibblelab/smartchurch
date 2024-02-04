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

namespace IBGEApiClient\UF\Response;

use \IBGEApiClient\Regiao\Response\RegiaoResponseData;

/**
 * Resposta da busca de UF
 */
class UFResponseData 
{
    /**
     * Id da UF
     *
     * @var int 
     */
    private $id;
    /**
     * Nome da UF
     *
     * @var string 
     */
    private $nome;
    /**
     * Sigla da UF
     *
     * @var string 
     */
    private $sigla;
    /**
     * Região da UF
     *
     * @var \IBGEApiClient\Regiao\Response\RegiaoResponseData
     */
    private $regiao;
    
    /**
     * Obtêm o id da UF
     * 
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Seta o id da UF
     * 
     * @param int $id
     * @return void
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * Obtêm o nome da UF
     * 
     * @return string|null
     */
    public function getNome(): ?string
    {
        return $this->nome;
    }

    /**
     * Seta o nome da UF
     * 
     * @param string $nome
     * @return void
     */
    public function setNome($nome): void
    {
        $this->nome = $nome;
    }

    /**
     * Obtêm a sigla da UF
     * 
     * @return string|null
     */
    public function getSigla(): ?string
    {
        return $this->sigla;
    }

    /**
     * Seta a sigla da UF
     * 
     * @param string $sigla
     * @return void
     */
    public function setSigla($sigla): void
    {
        $this->sigla = $sigla;
    }
    
    /**
     * Obtêm a região da UF
     * 
     * @return \IBGEApiClient\Regiao\Response\RegiaoResponseData
     */
    public function getRegiao(): \IBGEApiClient\Regiao\Response\RegiaoResponseData
    {
        return $this->regiao;
    }

    /**
     * Seta a região da UF
     * 
     * @param \IBGEApiClient\Regiao\Response\RegiaoResponseData $regiao
     */
    public function setRegiao($regiao)
    {
        $this->regiao = $regiao;
    }
}

