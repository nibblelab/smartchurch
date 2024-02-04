<?php

/**
 * Load configs from xml
 * 
 * @param Configs $obj
 * @return type
 */
function loadConfs(&$obj)
{
    require_once ADO_PATH . '/Configs.class.php';
    require_once DAO_PATH . '/ConfigsDAO.class.php';
    
    $obj = new Configs();
    return $obj->load();
}

/**
 * Get all timezones from xml
 * 
 * @param Configs $obj
 * @return type
 */
function searchAllTimeZones(&$obj)
{
    require_once ADO_PATH . '/Configs.class.php';
    require_once DAO_PATH . '/ConfigsDAO.class.php';
    
    $obj = new Configs();
    return $obj->searchAllTimeZones();
}

?>