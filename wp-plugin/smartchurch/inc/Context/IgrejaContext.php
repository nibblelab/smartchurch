<?php

namespace SmartChurch\Context;

use SmartChurch\Config\SmartChurchConfig;
use SmartChurch\Context\Context;
use SmartChurch\Serie\SerieRequest;
use SmartChurch\Mensagem\MensagemRequest;
use SmartChurch\Transmissao\TransmissaoRequest;

/**
 * Façade para os métodos da API no contexto de Igreja
 *
 * @author johnatas
 */
class IgrejaContext extends Context {
    
    
    /**
     * Busque as séries de sermões da igreja
     * 
     * @param string $igreja id da igreja
     * @param int $page página. Opcional. Default = -1 (sem paginação)
     * @param int $pageSize tamanho da página. Ocional. Default = 5 (só funciona se $page > -1)
     * @return \SmartChurch\Serie\Response\SerieResponse|null
     * @throws \SmartChurch\Exception
     */
    public function getSeries($igreja, $page = -1, $pageSize = 5): ?\SmartChurch\Serie\Response\SerieResponse
    {
        try {
            $request = new SerieRequest();
            return $request->getAll(SmartChurchConfig::$config, $igreja, $page, $pageSize);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    /**
     * Busque a série pelo seu id
     * 
     * @param string $id id da série
     * @return \SmartChurch\Serie\Response\SerieResponse|null
     * @throws \SmartChurch\Exception
     */
    public function getSerie($id): ?\SmartChurch\Serie\Response\SerieResponse
    {
        try {
            $request = new SerieRequest();
            return $request->getMe(SmartChurchConfig::$config, $id);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    /**
     * Busque a série pela chave
     * 
     * @param string $chave chave da série
     * @return \SmartChurch\Serie\Response\SerieResponse|null
     * @throws \SmartChurch\Exception
     */
    public function getSerieByChave($chave): ?\SmartChurch\Serie\Response\SerieResponse
    {
        try {
            $request = new SerieRequest();
            return $request->getByChave(SmartChurchConfig::$config, $chave);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    /**
     * Busque as mensagens da igreja
     * 
     * @param string $igreja id da igreja
     * @param string $serie id a série. Opcional. Default = string vazia
     * @param int $page página. Opcional. Default = -1 (sem paginação)
     * @param int $pageSize tamanho da página. Ocional. Default = 5 (só funciona se $page > -1)
     * @return \SmartChurch\Mensagem\Response\MensagemResponse|null
     * @throws \SmartChurch\Exception
     */
    public function getMensagens($igreja, $serie = '', $page = -1, $pageSize = 6): ?\SmartChurch\Mensagem\Response\MensagemResponse
    {
        try {
            $request = new MensagemRequest();
            return $request->getAll(SmartChurchConfig::$config, $igreja, $serie, $page, $pageSize);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    /**
     * Busque as últimas mensagens da igreja
     * 
     * @param string $igreja id da igreja
     * @param int $size quantidade. Opcional. Default = 6
     * @return \SmartChurch\Mensagem\Response\MensagemResponse|null
     * @throws \SmartChurch\Exception
     */
    public function getLastMensagens($igreja, $size = 6): ?\SmartChurch\Mensagem\Response\MensagemResponse
    {
        try {
            $request = new MensagemRequest();
            return $request->getLasts(SmartChurchConfig::$config, $igreja, $size);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    /**
     * Busque uma mensagem pelo seu id
     * 
     * @param string $id id da mensagem
     * @return \SmartChurch\Mensagem\Response\MensagemResponse|null
     * @throws \SmartChurch\Exception
     */
    public function getMensagem($id): ?\SmartChurch\Mensagem\Response\MensagemResponse
    {
        try {
            $request = new MensagemRequest();
            return $request->getMe(SmartChurchConfig::$config, $id);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    /**
     * Busque a mensagem pela chave
     * 
     * @param string $chave chave da mensagem
     * @return \SmartChurch\Mensagem\Response\MensagemResponse|null
     * @throws \SmartChurch\Exception
     */
    public function getMensagemByChave($chave): ?\SmartChurch\Mensagem\Response\MensagemResponse
    {
        try {
            $request = new MensagemRequest();
            return $request->getByChave(SmartChurchConfig::$config, $chave);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    /**
     * Busque o download da mensagem
     * 
     * @param string $id id da mensagem
     * @return \SmartChurch\Mensagem\Response\MensagemResponse|null
     * @throws \SmartChurch\Exception
     */
    public function getMensagemDownload($id): ?\SmartChurch\Mensagem\Response\MensagemResponse
    {
        try {
            $request = new MensagemRequest();
            return $request->getDownload(SmartChurchConfig::$config, $id);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    /**
     * Busque as transmissões online da igreja
     * 
     * @param string $igreja id da igreja
     * @return \SmartChurch\Transmissao\Response\TransmissaoResponse|null
     * @throws \SmartChurch\Exception
     */
    public function getTransmissaoDaIgreja($igreja): ?\SmartChurch\Transmissao\Response\TransmissaoResponse 
    {
        try {
            $request = new TransmissaoRequest();
            return $request->getDaIgreja(SmartChurchConfig::$config, $igreja);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
}
