<?php

namespace SmartChurch\Error;

/**
 * Enumeração de erros possíveis
 */
abstract class Errors
{
    /**
     * lib curl não está presente
     */
    const CURL_NOT_FOUND = -1;
    /**
     * Requisição vazia
     */
    const EMPTY_REQUEST = -2;
    /**
     * Erro no curl
     */
    const CURL_ERROR = -3;
    /**
     * Erro de validação dos dados
     */
    const VALIDATION_ERROR = -4;
    /**
     * Erro ao processar a requisição
     */
    const REQUEST_ERROR = -5;
    /**
     * Requisição incorreta
     */
    const INCORRECT_REQUEST = -6;
    /**
     * Erro na busca
     */
    const FETCH_ERROR = -7;
}


