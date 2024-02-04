<?php 
 
 
class ConfigsDAO 
{ 
 
    /**
     * edit config xml
     * 
     * @param Configs $ado config ADO object
     * @return boolean
     */
    public function edit(&$ado): bool
    { 
        $xml = simplexml_load_file(XML_PATH . '/configs.cfg.xml');  
        $xml->crypt_key = $ado->crypt_key;  
        $xml->crypt_iv = $ado->crypt_iv;  
        $xml->timezone = $ado->timezone;  
        $xml->site_title = $ado->site_title;  
        $xml->site_desc = $ado->site_desc;  
        $xml->site_abstract = $ado->site_abstract;  
        $xml->site_keywords = $ado->site_keywords;  
        $xml->site_author = $ado->site_author;  
        $xml->site_contact = $ado->site_contact;  
        $xml->robots_time_visit = $ado->robots_time_visit;  
        $xml->ga_code = $ado->ga_code; 
        $xml->js_mode = $ado->js_mode;  
        $xml->last_mod = $ado->last_mod;  
         
        $content = $xml->asXML(); 
        $arq = fopen(XML_PATH . '/configs.cfg.xml', 'w'); 
        fwrite($arq, $content); 
        fclose($arq); 
         
        return true;  
    } 
 
    /**
     * load config xml
     * 
     * @param Configs $ado config ADO object
     * @return bool
     */
    public function load(&$ado): bool
    { 
        $xml = simplexml_load_file(XML_PATH . '/configs.cfg.xml'); 
        $ado->crypt_key = (string) $xml->crypt_key[0]; 
        $ado->crypt_iv = (string) $xml->crypt_iv[0]; 
        $ado->timezone = (string) $xml->timezone[0]; 
        $ado->site_title = (string) $xml->site_title[0]; 
        $ado->site_desc = (string) $xml->site_desc[0];  
        $ado->site_abstract = (string) $xml->site_abstract[0] ;  
        $ado->site_keywords = (string) $xml->site_keywords[0];  
        $ado->site_author = (string) $xml->site_author[0];  
        $ado->site_contact = (string) $xml->site_contact[0];  
        $ado->robots_time_visit = (string) $xml->robots_time_visit[0];  
        $ado->ga_code = (string) $xml->ga_code[0];  
        $ado->js_mode = (string) $xml->js_mode[0]; 
        $ado->last_mod = (string) $xml->last_mod[0]; 
        return true; 
    } 
 
    /**
     * load timezones xml
     * 
     * @param Configs $ado config ADO object
     * @return bool
     */
    public function searchAllTimeZones(&$ado): bool
    { 
        $xml = simplexml_load_file(XML_PATH . '/timezones.cfg.xml'); 
 
        $size = 0; 
        $cur = $ado; 
        foreach($xml->timezone as $timezone) 
        { 
            $cur->timezone = (string) $timezone[0]; 
 
            $cur->next = clone($cur); 
            $cur = $cur->next; 
            $size++; 
        } 
        $ado->size = $size; 
        return true; 
    } 
 
} 
 
?> 
