<?php

namespace SmartChurch\Shortcode;

use SmartChurch\Shortcode\SmartCodes as ShortCodes;

class EventoCode extends ShortCodes
{
    public function enable() {
        add_shortcode('smartchurh-inscricao', array($this, 'getEventoInscricao'));
    }
    
    /**
     * Processa a URL do evento
     * 
     * @param string $url
     * @return \stdClass
     */
    private function parseEventUrl($url) {
        $url_parts = explode('/', $url);
        $url_info = explode('|', base64_decode($url_parts[count($url_parts) -1]));
        $data = new \stdClass();
        $data->chave = $url_info[0];
        $data->ref_tp = $url_info[1];
        $data->ref = $url_info[2];
        return $data;
    }
    
    /**
     * Busca o evento pela URL
     * 
     * @param string $url
     * @return \SmartChurch\Evento\Response\EventoResponseData|null
     */
    public function getEventoByUrl($url) {
        $evento_data = $this->parseEventUrl($url);
        $evento_api = $this->api->getEventoByChave($evento_data->chave);
        if(!is_null($evento_api)) {
            return $evento_api->get();
        }
        
        return null;
    }
    
    /**
     * Gera o campo
     * 
     * @param string $label
     * @param string $field
     * @param string $type
     * @param bool $needed
     */
    public function generateField($label, $field, $type, $needed) {}
    
    /**
     * Gera o botão de submissão 
     */
    public function generateSubmitButton() {}
    
    /**
     * Obtem a URL de envio do formulário
     */
    public function getSubmitURL() {}
    
    /**
     * Obtenha o CSS do form
     */
    public function getFormCSS() {}
    
    public function onInscricaoEncerrada() {}
    
    /**
     * Busque o formulário de inscrição do evento
     * 
     * @param array $atts
     * @return string
     */
    public function getEventoInscricao($atts) {
        if(!isset($atts) || empty($atts['url'])) {
            return '';
        }
        
        $evento = $this->getEventoByUrl($atts['url']);
        if(is_null($evento)) {
            return '';
        }
        
        if(!is_null($evento->getFimInscricao()) && !empty($evento->getFimInscricao())) {
            $now = new \DateTime();
            $now->setTimezone(new \DateTimeZone('America/Sao_Paulo'));
            $fim = new \DateTime($evento->getFimInscricao(), new \DateTimeZone('America/Sao_Paulo'));
            if($now > $fim) {
                return $this->onInscricaoEncerrada();
            }
            
        }
        
        $html = '<form id="smartchurchEventoInscricaoForm" method="POST" action="'.$this->getSubmitURL().'" class="'.$this->getFormCSS().'">';
        $evento_form = json_decode($evento->getFormularioInscricao());
        foreach($evento_form as $field) {
            if($field->checked) {
                $html .= $this->generateField($field->label, $field->field, $field->type, $field->needed);
            }
        }
        $html .= '<input type="hidden" id="cargo_ref" name="cargo_ref" value="" /> ' .
                '<input type="hidden" id="evento" name="evento" value="'.$evento->getId().'" /> ' .
                '<input type="hidden" id="nome_evento" name="nome_evento" value="'.$evento->getNome().'" /> ' .
                $this->generateSubmitButton() . 
                '<div style="display: none;" id="smartchurchData">'.$this->getEncodedData().'</div>'.
            '</form>';
        
        return $html;
    }
}

