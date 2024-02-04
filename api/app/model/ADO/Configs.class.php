<?php 
 
/**
 * Model class for XML config file
 */  
class Configs 
{ 
    public $id; 
    public $crypt_key; 
    public $crypt_iv; 
    public $timezone; 
    public $site_title; 
    public $site_desc; 
    public $site_abstract; 
    public $site_keywords; 
    public $site_author; 
    public $site_contact; 
    public $robots_time_visit; 
    public $ga_code; 
    public $js_mode; 
    public $last_mod; 
 
    private $dao; 
 
    public $page; 
    public $len; 
    public $size; 
 
    public $next; 
 
    public function __construct()
    { 
        $this->dao = new ConfigsDAO(); 
    } 
 
    public function __clone() 
    { 
        $this->next = NULL; 
    } 
 
    /**
     * edit config xml
     * 
     * @return bool
     */
    public function edit(): bool
    { 
        return $this->dao->edit($this); 
    } 
 
    /**
     * load config xml
     * 
     * @return bool
     */
    public function load(): bool
    { 
        return $this->dao->load($this); 
    } 
 
    /**
     * load timezones xml
     * 
     * @return bool
     */
    public function searchAllTimeZones(): bool
    { 
        return $this->dao->searchAllTimeZones($this); 
    } 
 
} 
 
?> 
