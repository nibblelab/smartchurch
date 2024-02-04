<?php

namespace SmartChurch\Request;

class RequestConfig
{
    private $url;
    private $token;
    
    public function getUrl()
    {
        return $this->url;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function setToken($token)
    {
        $this->token = $token;
    }


}

