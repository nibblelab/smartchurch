<?php
require_once FCT_PATH . '/util.cfc.php';  
require_once LIB_PATH . '/adodb5/adodb.inc.php';
require_once DAO_PATH . '/DBO.class.php';
require_once ADO_PATH . '/BaseADO.class.php'; 
require_once DAO_PATH . '/BaseDAO.class.php';

class Auto
{
    /**
     * Nome do arquivo de log
     *
     * @var string 
     */
    protected $log;
    
    /**
     * Gere um log da informação
     * 
     * @param type $info informação para o log
     * @return void 
     */
    protected function logThis($info): void
    {
        NblLogUtil::log($this->log, $info);
    }
}

