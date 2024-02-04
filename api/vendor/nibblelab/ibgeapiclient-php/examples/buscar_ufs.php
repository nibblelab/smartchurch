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

include '../vendor/autoload.php';

use \IBGEApiClient\IBGEApiClient;

try
{
    $api = new IBGEApiClient();
    $ufs = $api->buscarUFs();
    foreach($ufs->getData() as $uf) {
        echo ' nome = ' . $uf->getNome() . "\n";
    }
    $ufs = $api->buscarUFById('31'); 
    foreach($ufs->getData() as $uf) {
        echo ' nome = ' . $uf->getNome() . "\n"; # printe o nome
    }
    $ufs = $api->buscarUFsByIds(array('31','32')); 
    foreach($ufs->getData() as $uf) {
        echo ' nome = ' . $uf->getNome() . "\n"; # printe o nome
    }
    $ufs = $api->buscarUFsByRegiao('3'); 
    foreach($ufs->getData() as $uf) {
        echo ' nome = ' . $uf->getNome() . "\n"; # printe o nome
    }
    $ufs = $api->buscarUFsByRegioes(array('2','3')); 
    foreach($ufs->getData() as $uf) {
        echo ' nome = ' . $uf->getNome() . "\n"; # printe o nome
    }
} catch (Exception $ex) {
    echo $ex->getMessage();
}


