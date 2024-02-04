<?php
namespace SmartChurch\Context;

use SmartChurch\Config\SmartChurchConfig;
use SmartChurch\Context\Context;
use SmartChurch\Evento\EventoRequest;
use SmartChurch\Cargo\CargoRequest;
use SmartChurch\Data\DataRequest;
use SmartChurch\Sinodo\SinodoRequest;
use SmartChurch\Presbiterio\PresbiterioRequest;
use SmartChurch\Igreja\IgrejaRequest;
use SmartChurch\Templo\TemploRequest;
use SmartChurch\Sinodal\SinodalRequest;
use SmartChurch\Federacao\FederacaoRequest;
use SmartChurch\Sociedade\SociedadeRequest;
use SmartChurch\Inscricao\InscricaoRequest;

/**
 * Façade para os métodos da API no contexto de Evento
 *
 * @author johnatas
 */
class EventoContext extends Context {  
    
    /**
     * Busque um evento por sua chave
     * 
     * @param string $chave chave do evento
     * @return \SmartChurch\Evento\Response\EventoResponse|null
     * @throws \SmartChurch\Exception
     */
    public function getEventoByChave($chave): ?\SmartChurch\Evento\Response\EventoResponse
    {
        try {
            $request = new EventoRequest();
            return $request->getByChave(SmartChurchConfig::$config, $chave);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    /**
     * Busca os dados comuns
     * 
     * @return \SmartChurch\Data\Response\DataResponse|null
     * @throws \SmartChurch\Exception
     */
    public function getDatas(): ?\SmartChurch\Data\Response\DataResponse
    {
        try {
            $request = new DataRequest();
            return $request->getAll(SmartChurchConfig::$config);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    /**
     * Busca os cargos
     * 
     * @return \SmartChurch\Cargo\Response\CargoResponse|null
     * @throws \SmartChurch\Exception
     */
    public function getCargos(): ?\SmartChurch\Cargo\Response\CargoResponse
    {
        try {
            $request = new CargoRequest();
            return $request->getAll(SmartChurchConfig::$config);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    /**
     * Busca os sínodos
     * 
     * @return \SmartChurch\Sinodo\Response\SinodoResponse|null
     * @throws \SmartChurch\Exception
     */
    public function getSinodos(): ?\SmartChurch\Sinodo\Response\SinodoResponse
    {
        try {
            $request = new SinodoRequest();
            return $request->getAll(SmartChurchConfig::$config);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    /**
     * Busca os presbitérios
     * 
     * @return \SmartChurch\Presbiterio\Response\PresbiterioResponse|null
     * @throws \SmartChurch\Exception
     */
    public function getPresbiterios(): ?\SmartChurch\Presbiterio\Response\PresbiterioResponse
    {
        try {
            $request = new PresbiterioRequest();
            return $request->getAll(SmartChurchConfig::$config);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    /**
     * Busca as igrejas
     * 
     * @return \SmartChurch\Igreja\Response\IgrejaResponse|null
     * @throws \SmartChurch\Exception
     */
    public function getIgrejas(): ?\SmartChurch\Igreja\Response\IgrejaResponse
    {
        try {
            $request = new IgrejaRequest();
            return $request->getAll(SmartChurchConfig::$config);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    /**
     * Busca os templos
     * 
     * @return \SmartChurch\Templo\Response\TemploResponse|null
     * @throws \SmartChurch\Exception
     */
    public function getTemplos(): ?\SmartChurch\Templo\Response\TemploResponse
    {
        try {
            $request = new TemploRequest();
            return $request->getAll(SmartChurchConfig::$config);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    /**
     * Busca as sinodais
     * 
     * @return \SmartChurch\Sinodal\Response\SinodalResponse|null
     * @throws \SmartChurch\Exception
     */
    public function getSinodais(): ?\SmartChurch\Sinodal\Response\SinodalResponse
    {
        try {
            $request = new SinodalRequest();
            return $request->getAll(SmartChurchConfig::$config);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    /**
     * Busca as federações
     * 
     * @return \SmartChurch\Federacao\Response\FederacaoResponse|null
     * @throws \SmartChurch\Exception
     */
    public function getFederacoes(): ?\SmartChurch\Federacao\Response\FederacaoResponse
    {
        try {
            $request = new FederacaoRequest();
            return $request->getAll(SmartChurchConfig::$config);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    /**
     * Busca as sociedades
     * 
     * @return \SmartChurch\Sociedade\Response\SociedadeResponse|null
     * @throws \SmartChurch\Exception
     */
    public function getSociedades(): ?\SmartChurch\Sociedade\Response\SociedadeResponse 
    {
        try {
            $request = new SociedadeRequest();
            return $request->getAll(SmartChurchConfig::$config);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    /**
     * 
     * Realiza a inscrição em um evento
     * 
     * @param string $nome Nome do inscrito
     * @param string $email E-mail do Inscrito
     * @param string $sexo Sexo do inscrito [opcional]
     * @param string $data_nascimento Data de nascimento [opcional]
     * @param string $estado_civil Estado Civil [opcional]
     * @param string $telefone Telefone [opcional]
     * @param string $celular_1 Celular (1) [opcional]
     * @param string $celular_2 Celular (2) [opcional]
     * @param string $nome_evento Nome do evento
     * @param string $evento id do evento
     * @param string $igreja id da igreja do inscrito [opcional]
     * @param string $presbiterio id do presbitério do inscrito [opcional]
     * @param string $sinodo id do sínodo do inscrito [opcional]
     * @param string $sociedade id da sociedade interna do inscrito [opcional]
     * @param string $federacao id da federação do inscrito [opcional]
     * @param string $sinodal id da sinodal do inscrito [opcional]
     * @param string $delegado Flag pra indicar se é ou não delegado [opcional]
     * @param string $cargo_ref Referência para o cargo [opcional]
     * @param string $cargo Id do cargo do inscrito [opcional]
     * @param string $has_pagto Se tem ou não pagamento na inscrição 
     * @param string $forma_pagto Forma de pagamento [opcional]
     * @param string $stat_pagto Status do pagamento [opcional] 
     * @param string $valor_pago Valor do Pagamento [opcional] 
     * @param string $data_pagto Data do Pagamento [opcional] 
     * @return \SmartChurch\Inscricao\Response\InscricaoResponse|null
     * @throws \SmartChurch\Exception
     */
    public function inscricao($nome, $email, $sexo, $data_nascimento, $estado_civil, $telefone, 
                                $celular_1, $celular_2, $nome_evento, $evento, $igreja, $presbiterio, $sinodo,
                                $sociedade, $federacao, $sinodal, $delegado, $cargo_ref, $cargo, $has_pagto = false, 
                                $forma_pagto = '', $stat_pagto = '', $valor_pago = '', $data_pagto = ''): ?\SmartChurch\Inscricao\Response\InscricaoResponse 
    {
        try {
            $request = new InscricaoRequest();
            return $request->doSubscribe(SmartChurchConfig::$config, $nome, $email, $sexo, $data_nascimento, $estado_civil, $telefone, 
                                $celular_1, $celular_2, $nome_evento, $evento, $igreja, $presbiterio, $sinodo,
                                $sociedade, $federacao, $sinodal, $delegado, $cargo_ref, $cargo, $has_pagto, $forma_pagto,
                                $stat_pagto, $valor_pago, $data_pagto);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
}
