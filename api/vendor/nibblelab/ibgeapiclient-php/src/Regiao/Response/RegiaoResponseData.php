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

namespace IBGEApiClient\Regiao\Response;

/**
 * Resposta da busca de região
 */
class RegiaoResponseData 
{
    /**
     * Id da região
     *
     * @var int 
     */
    private $id;
    /**
     * Nome da região
     *
     * @var string 
     */
    private $nome;
    /**
     * Sigla da região
     *
     * @var string 
     */
    private $sigla;
    
    /**
     * Obtêm o id da região
     * 
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Seta o id da região
     * 
     * @param int $id
     * @return void
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * Obtêm o nome da região
     * 
     * @return string|null
     */
    public function getNome(): ?string
    {
        return $this->nome;
    }

    /**
     * Seta o nome da região
     * 
     * @param string $nome
     * @return void
     */
    public function setNome($nome): void
    {
        $this->nome = $nome;
    }

    /**
     * Obtêm a sigla da região
     * 
     * @return string|null
     */
    public function getSigla(): ?string
    {
        return $this->sigla;
    }

    /**
     * Seta a sigla da região
     * 
     * @param string $sigla
     * @return void
     */
    public function setSigla($sigla): void
    {
        $this->sigla = $sigla;
    }


}

