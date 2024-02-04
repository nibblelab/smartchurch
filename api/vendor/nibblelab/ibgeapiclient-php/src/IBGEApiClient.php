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

namespace IBGEApiClient;

use \IBGEApiClient\Config\Config;
use \IBGEApiClient\Regiao\RegiaoRequest;
use \IBGEApiClient\UF\UFRequest;
use \IBGEApiClient\MesoRegiao\MesoRegiaoRequest;
use \IBGEApiClient\MicroRegiao\MicroRegiaoRequest;
use \IBGEApiClient\Municipio\MunicipioRequest;
use \IBGEApiClient\Response\Response;

/**
 * Façade para uso da API do IBGE
 */
class IBGEApiClient
{
    /**
     * Array de configuração
     *
     * @var array
     */
    private $config = array(
        'url' => ''
    );
    
    /**
     * 
     * @param string $token token do cliente
     * @param string $url_notificacao url que receberá as notificações do pagamento. Opcional
     * @param bool $sandbox usa o modo sandbox ou não. Padrão = false
     */
    public function __construct()
    {
        $this->config['url'] = Config::URL;
    }
    
    /***************************************************************************
     *                        REGIÃO
     ***************************************************************************/
    
    /**
     * Busca as regiões
     * 
     * @return Response|null
     * @throws \Exception
     */
    public function buscarRegioes(): ?\IBGEApiClient\Response\Response 
    {
        $request = new RegiaoRequest();
        return $request->request($this->config);
    }
    
    /**
     * Busca região por id
     * 
     * @param string $id 
     * @return Response|null
     * @throws \Exception
     */
    public function buscarRegiaoById($id): ?\IBGEApiClient\Response\Response 
    {
        $request = new RegiaoRequest();
        return $request->requestById($this->config, $id);
    }
    
    /**
     * Busca as regiões por ids
     * 
     * @param array $ids array com os ids  
     * @return Response|null
     * @throws \Exception
     */
    public function buscarRegioesByIds($ids): ?\IBGEApiClient\Response\Response 
    {
        $request = new RegiaoRequest();
        return $request->requestByIds($this->config, $ids);
    }
    
    /***************************************************************************
     *                        MESORREGIÃO
     ***************************************************************************/
    
    /**
     * Busca as mesorregiões
     * 
     * @return Response|null
     * @throws \Exception
     */
    public function buscarMesoRegioes(): ?\IBGEApiClient\Response\Response 
    {
        $request = new MesoRegiaoRequest();
        return $request->request($this->config);
    }
    
    /**
     * Busca mesorregião por id
     * 
     * @param string $id 
     * @return Response|null
     * @throws \Exception
     */
    public function buscarMesoRegiaoById($id): ?\IBGEApiClient\Response\Response
    {
        $request = new MesoRegiaoRequest();
        return $request->requestById($this->config, $id);
    }
    
    /**
     * Busca as mesorregiões por ids
     * 
     * @param array $ids array com os ids  
     * @return Response|null
     * @throws \Exception
     */
    public function buscarMesoRegioesByIds($ids): ?\IBGEApiClient\Response\Response
    {
        $request = new MesoRegiaoRequest();
        return $request->requestByIds($this->config, $ids);
    }
    
    /**
     * Busca mesorregiões por id da região
     * 
     * @param string $regiao 
     * @return Response|null
     * @throws \Exception
     */
    public function buscarMesoRegioesByRegiao($regiao): ?\IBGEApiClient\Response\Response 
    {
        $request = new MesoRegiaoRequest();
        return $request->requestByRegiao($this->config, $regiao);
    }
    
    /**
     * Busca as mesorregiões por ids de regiões
     * 
     * @param array $regioes array com os ids  
     * @return Response|null
     * @throws \Exception
     */
    public function buscarMesoRegioesByRegioes($regioes): ?\IBGEApiClient\Response\Response 
    {
        $request = new MesoRegiaoRequest();
        return $request->requestByRegioes($this->config, $regioes);
    }
    
    /**
     * Busca mesorregiões por id da uf
     * 
     * @param string $uf 
     * @return Response|null
     * @throws \Exception
     */
    public function buscarMesoRegioesByUF($uf): ?\IBGEApiClient\Response\Response
    {
        $request = new MesoRegiaoRequest();
        return $request->requestByUF($this->config, $uf);
    }
    
    /**
     * Busca as mesorregiões por ids de ufs
     * 
     * @param array $ufs array com os ids  
     * @return Response|null
     * @throws \Exception
     */
    public function buscarMesoRegioesByUFs($ufs): ?\IBGEApiClient\Response\Response
    {
        $request = new MesoRegiaoRequest();
        return $request->requestByUFs($this->config, $ufs);
    }
    
    /***************************************************************************
     *                        MICRORREGIÃO
     ***************************************************************************/
    
    /**
     * Busca as microrregiões
     * 
     * @return Response|null
     * @throws \Exception
     */
    public function buscarMicroRegioes(): ?\IBGEApiClient\Response\Response 
    {
        $request = new MicroRegiaoRequest();
        return $request->request($this->config);
    }
    
    /**
     * Busca microrregião por id
     * 
     * @param string $id 
     * @return Response|null
     * @throws \Exception
     */
    public function buscarMicroRegiaoById($id): ?\IBGEApiClient\Response\Response 
    {
        $request = new MicroRegiaoRequest();
        return $request->requestById($this->config, $id);
    }
    
    /**
     * Busca as microrregiões por ids
     * 
     * @param array $ids array com os ids  
     * @return Response|null
     * @throws \Exception
     */
    public function buscarMicroRegioesByIds($ids): ?\IBGEApiClient\Response\Response 
    {
        $request = new MicroRegiaoRequest();
        return $request->requestByIds($this->config, $ids);
    }
    
    /**
     * Busca microrregiões por id da região
     * 
     * @param string $regiao 
     * @return Response|null
     * @throws \Exception
     */
    public function buscarMicroRegioesByRegiao($regiao): ?\IBGEApiClient\Response\Response 
    {
        $request = new MicroRegiaoRequest();
        return $request->requestByRegiao($this->config, $regiao);
    }
    
    /**
     * Busca as microrregiões por ids de regiões
     * 
     * @param array $regioes array com os ids  
     * @return Response|null
     * @throws \Exception
     */
    public function buscarMicroRegioesByRegioes($regioes): ?\IBGEApiClient\Response\Response 
    {
        $request = new MicroRegiaoRequest();
        return $request->requestByRegioes($this->config, $regioes);
    }
    
    /**
     * Busca microrregiões por id da uf
     * 
     * @param string $uf 
     * @return Response|null
     * @throws \Exception
     */
    public function buscarMicroRegioesByUF($uf): ?\IBGEApiClient\Response\Response 
    {
        $request = new MicroRegiaoRequest();
        return $request->requestByUF($this->config, $uf);
    }
    
    /**
     * Busca as microrregiões por ids de ufs
     * 
     * @param array $ufs array com os ids  
     * @return Response|null
     * @throws \Exception
     */
    public function buscarMicroRegioesByUFs($ufs): ?\IBGEApiClient\Response\Response 
    {
        $request = new MicroRegiaoRequest();
        return $request->requestByUFs($this->config, $ufs);
    }
    
    /**
     * Busca microrregiões por id da mesorregião
     * 
     * @param string $mesorregiao 
     * @return Response|null
     * @throws \Exception
     */
    public function buscarMicroRegioesByMesoRegiao($mesorregiao): ?\IBGEApiClient\Response\Response 
    {
        $request = new MicroRegiaoRequest();
        return $request->requestByMesoRegiao($this->config, $mesorregiao);
    }
    
    /**
     * Busca as microrregiões por ids de mesorregiões
     * 
     * @param array $mesorregioes array com os ids  
     * @return Response|null
     * @throws \Exception
     */
    public function buscarMicroRegioesByMesoRegioes($mesorregioes): ?\IBGEApiClient\Response\Response 
    {
        $request = new MicroRegiaoRequest();
        return $request->requestByMesoRegioes($this->config, $mesorregioes);
    }
    
    /***************************************************************************
     *                        UF
     ***************************************************************************/
    
    /**
     * Busca as unidades federativas
     * 
     * @return Response|null
     * @throws \Exception
     */
    public function buscarUFs(): ?\IBGEApiClient\Response\Response 
    {
        $request = new UFRequest();
        return $request->request($this->config);
    }
    
    /**
     * Busca uf por id
     * 
     * @param string $id 
     * @return Response|null
     * @throws \Exception
     */
    public function buscarUFById($id): ?\IBGEApiClient\Response\Response 
    {
        $request = new UFRequest();
        return $request->requestById($this->config, $id);
    }
    
    /**
     * Busca as ufs por ids
     * 
     * @param array $ids array com os ids  
     * @return Response|null
     * @throws \Exception
     */
    public function buscarUFsByIds($ids): ?\IBGEApiClient\Response\Response 
    {
        $request = new UFRequest();
        return $request->requestByIds($this->config, $ids);
    }
    
    /**
     * Busca ufs por id da região
     * 
     * @param string $regiao 
     * @return Response|null
     * @throws \Exception
     */
    public function buscarUFsByRegiao($regiao): ?\IBGEApiClient\Response\Response 
    {
        $request = new UFRequest();
        return $request->requestByRegiao($this->config, $regiao);
    }
    
    /**
     * Busca as ufs por ids de regiões
     * 
     * @param array $regioes array com os ids  
     * @return Response|null
     * @throws \Exception
     */
    public function buscarUFsByRegioes($regioes): ?\IBGEApiClient\Response\Response 
    {
        $request = new UFRequest();
        return $request->requestByRegioes($this->config, $regioes);
    }
    
    /***************************************************************************
     *                        MUNICÍPIO
     ***************************************************************************/
    
    /**
     * Busca os municípios
     * 
     * @return Response|null
     * @throws \Exception
     */
    public function buscarMunicipios(): ?\IBGEApiClient\Response\Response 
    {
        $request = new MunicipioRequest();
        return $request->request($this->config);
    }
    
    /**
     * Busca município por id
     * 
     * @param string $id 
     * @return Response|null
     * @throws \Exception
     */
    public function buscarMunicipioById($id): ?\IBGEApiClient\Response\Response 
    {
        $request = new MunicipioRequest();
        return $request->requestById($this->config, $id);
    }
    
    /**
     * Busca os municípios por ids
     * 
     * @param array $ids array com os ids  
     * @return Response|null
     * @throws \Exception
     */
    public function buscarMunicipiosByIds($ids): ?\IBGEApiClient\Response\Response 
    {
        $request = new MunicipioRequest();
        return $request->requestByIds($this->config, $ids);
    }
    
    /**
     * Busca municípios por id da região
     * 
     * @param string $regiao 
     * @return Response|null
     * @throws \Exception
     */
    public function buscarMunicipiosByRegiao($regiao): ?\IBGEApiClient\Response\Response 
    {
        $request = new MunicipioRequest();
        return $request->requestByRegiao($this->config, $regiao);
    }
    
    /**
     * Busca os municípios por ids de regiões
     * 
     * @param array $regioes array com os ids  
     * @return Response|null
     * @throws \Exception
     */
    public function buscarMunicipiosByRegioes($regioes): ?\IBGEApiClient\Response\Response 
    {
        $request = new MunicipioRequest();
        return $request->requestByRegioes($this->config, $regioes);
    }
    
    /**
     * Busca municípios por id da uf
     * 
     * @param string $uf 
     * @return Response|null
     * @throws \Exception
     */
    public function buscarMunicipiosByUF($uf): ?\IBGEApiClient\Response\Response 
    {
        $request = new MunicipioRequest();
        return $request->requestByUF($this->config, $uf);
    }
    
    /**
     * Busca municípios por ids de ufs
     * 
     * @param array $ufs array com os ids  
     * @return Response|null
     * @throws \Exception
     */
    public function buscarMunicipiosByUFs($ufs): ?\IBGEApiClient\Response\Response 
    {
        $request = new MunicipioRequest();
        return $request->requestByUFs($this->config, $ufs);
    }
    
    /**
     * Busca municípios por id da mesorregião
     * 
     * @param string $mesorregiao 
     * @return Response|null
     * @throws \Exception
     */
    public function buscarMunicipiosByMesoRegiao($mesorregiao): ?\IBGEApiClient\Response\Response 
    {
        $request = new MunicipioRequest();
        return $request->requestByMesoRegiao($this->config, $mesorregiao);
    }
    
    /**
     * Busca as municípios por ids de mesorregiões
     * 
     * @param array $mesorregioes array com os ids  
     * @return Response|null
     * @throws \Exception
     */
    public function buscarMunicipiosByMesoRegioes($mesorregioes): ?\IBGEApiClient\Response\Response 
    {
        $request = new MunicipioRequest();
        return $request->requestByMesoRegioes($this->config, $mesorregioes);
    }
    
    /**
     * Busca municípios por id da microrregião
     * 
     * @param string $microrregiao 
     * @return Response|null
     * @throws \Exception
     */
    public function buscarMunicipiosByMicroRegiao($microrregiao): ?\IBGEApiClient\Response\Response 
    {
        $request = new MunicipioRequest();
        return $request->requestByMicroRegiao($this->config, $microrregiao);
    }
    
    /**
     * Busca as municípios por ids de microrregiões
     * 
     * @param array $microrregioes array com os ids  
     * @return Response|null
     * @throws \Exception
     */
    public function buscarMunicipiosByMicroRegioes($microrregioes): ?\IBGEApiClient\Response\Response 
    {
        $request = new MunicipioRequest();
        return $request->requestByMicroRegioes($this->config, $microrregioes);
    }
}

