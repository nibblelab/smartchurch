<?php

namespace SmartChurch\Response;


/**
 * Parte comum das respostas de requisições
 */
class Response
{
    /**
     * Status da resposta
     *
     * @var string 
     */
    protected $status;
    /**
     * Requisição teve ou não sucesso
     *
     * @var bool 
     */
    protected $success;
    /**
     * Dados retornados
     *
     * @var array 
     */
    protected $datas;
    /**
     * Quantidade de dados retornados
     *
     * @var int 
     */
    protected $total;
    /**
     * Mensagem em caso de errro
     *
     * @var string
     */
    protected $msg;
    /**
     * Array com os erros encontados
     *
     * @var array
     */
    protected $errs;
    
    public function getStatus(): string
    {
        return $this->status;
    }
  
    /**
     * Obtêm a flag de sucesso
     * 
     * @return bool
     */
    public function getSuccess(): bool
    {
        return $this->success;
    }
    
    public function getDatas(): array
    {
        return $this->datas;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function getMsg(): string
    {
        return $this->msg;
    }

    public function getErrs(): array
    {
        return $this->errs;
    }

    public function setStatus($status): void
    {
        $this->status = $status;
    }
    
    /**
     * Seta a flag de sucesso
     * 
     * @param type $success
     * @return void
     */
    public function setSuccess($success): void
    {
        $this->success = $success;
    }

    public function setDatas($datas): void
    {
        $this->datas = $datas;
    }

    public function setTotal($total): void
    {
        $this->total = $total;
    }

    public function setMsg($msg): void
    {
        $this->msg = $msg;
    }

    public function setErrs($errs): void
    {
        $this->errs = $errs;
    }


}

