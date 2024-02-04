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
    $response = $api->buscarMesoRegioes();
    foreach($response->getData() as $r) {
        echo ' nome = ' . $r->getNome() . "\n";
    }
    $response = $api->buscarMesoRegiaoById('1101');
    foreach($response->getData() as $r) {
        echo ' nome = ' . $r->getNome() . "\n";
    }
    $response = $api->buscarMesoRegioesByIds(array('1101','1102'));
    foreach($response->getData() as $r) {
        echo ' nome = ' . $r->getNome() . "\n";
    }
    $response = $api->buscarMesoRegioesByRegiao('1');
    foreach($response->getData() as $r) {
        echo ' nome = ' . $r->getNome() . "\n";
    }
    $response = $api->buscarMesoRegioesByRegioes(array('1','2'));
    foreach($response->getData() as $r) {
        echo ' nome = ' . $r->getNome() . "\n";
    }
    $response = $api->buscarMesoRegioesByUF('31');
    foreach($response->getData() as $r) {
        echo ' nome = ' . $r->getNome() . "\n";
    }
    $response = $api->buscarMesoRegioesByUFs(array('31','27'));
    foreach($response->getData() as $r) {
        echo ' nome = ' . $r->getNome() . "\n";
    }
} catch (Exception $ex) {
    echo $ex->getMessage();
}


