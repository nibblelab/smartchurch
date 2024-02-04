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
    $response = $api->buscarMunicipios();
    foreach($response->getData() as $r) {
        echo ' nome = ' . $r->getNome() . "\n";
    }
    $response = $api->buscarMunicipioById('3170206');
    foreach($response->getData() as $r) {
        echo ' nome = ' . $r->getNome() . "\n";
    }
    $response = $api->buscarMunicipiosByIds(array('3170206','5108352'));
    foreach($response->getData() as $r) {
        echo ' nome = ' . $r->getNome() . "\n";
    }
    $response = $api->buscarMunicipiosByRegiao('1');
    foreach($response->getData() as $r) {
        echo ' nome = ' . $r->getNome() . "\n";
    }
    $response = $api->buscarMunicipiosByRegioes(array('1','2'));
    foreach($response->getData() as $r) {
        echo ' nome = ' . $r->getNome() . "\n";
    }
    $response = $api->buscarMunicipiosByUF('31');
    foreach($response->getData() as $r) {
        echo ' nome = ' . $r->getNome() . "\n";
    }
    $response = $api->buscarMunicipiosByUFs(array('31','27'));
    foreach($response->getData() as $r) {
        echo ' nome = ' . $r->getNome() . "\n";
    }
    $response = $api->buscarMunicipiosByMesoRegiao('1102');
    foreach($response->getData() as $r) {
        echo ' nome = ' . $r->getNome() . "\n";
    }
    $response = $api->buscarMunicipiosByMesoRegioes(array('1101','1102'));
    foreach($response->getData() as $r) {
        echo ' nome = ' . $r->getNome() . "\n";
    }
    $response = $api->buscarMunicipiosByMicroRegiao('11001');
    foreach($response->getData() as $r) {
        echo ' nome = ' . $r->getNome() . "\n";
    }
    $response = $api->buscarMunicipiosByMicroRegioes(array('11001','11002'));
    foreach($response->getData() as $r) {
        echo ' nome = ' . $r->getNome() . "\n";
    }
} catch (Exception $ex) {
    echo $ex->getMessage();
}


