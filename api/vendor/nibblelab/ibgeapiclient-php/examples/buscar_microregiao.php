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
    $response = $api->buscarMicroRegioes();
    foreach($response->getData() as $r) {
        echo ' nome = ' . $r->getNome() . "\n";
    }
    $response = $api->buscarMicroRegiaoById('11001');
    foreach($response->getData() as $r) {
        echo ' nome = ' . $r->getNome() . "\n";
    }
    $response = $api->buscarMicroRegioesByIds(array('11001','11002'));
    foreach($response->getData() as $r) {
        echo ' nome = ' . $r->getNome() . "\n";
    }
    $response = $api->buscarMicroRegioesByRegiao('1');
    foreach($response->getData() as $r) {
        echo ' nome = ' . $r->getNome() . "\n";
    }
    $response = $api->buscarMicroRegioesByRegioes(array('1','2'));
    foreach($response->getData() as $r) {
        echo ' nome = ' . $r->getNome() . "\n";
    }
    $response = $api->buscarMicroRegioesByUF('31');
    foreach($response->getData() as $r) {
        echo ' nome = ' . $r->getNome() . "\n";
    }
    $response = $api->buscarMicroRegioesByUFs(array('31','27'));
    foreach($response->getData() as $r) {
        echo ' nome = ' . $r->getNome() . "\n";
    }
    $response = $api->buscarMicroRegioesByMesoRegiao('1102');
    foreach($response->getData() as $r) {
        echo ' nome = ' . $r->getNome() . "\n";
    }
    $response = $api->buscarMicroRegioesByMesoRegioes(array('1101','1102'));
    foreach($response->getData() as $r) {
        echo ' nome = ' . $r->getNome() . "\n";
    }
} catch (Exception $ex) {
    echo $ex->getMessage();
}


