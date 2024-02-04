<?php

/**
 * Check if a string starts with the given substring
 * 
 * @param string $haystack string to be verified upon
 * @param string $needle substring 
 * @return bool
 */
function startsWith($haystack, $needle): bool
{
    // search backwards starting from haystack length characters from the end
    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
}

/**
 * Check if a string ends with the given substring
 * 
 * @param string $haystack string to be verified upon
 * @param string $needle substring 
 * @return bool
 */
function endsWith($haystack, $needle): bool
{
    // search forward starting from end minus needle length characters
    return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== false);
}

/**
 * Check if a string contains a substring
 * 
 * @param string $haystack string to be verified upon
 * @param string $needle substring 
 * @return bool
 */
function contains($haystack, $needle): bool
{
    return (strpos($haystack, $needle) !== false);
}

/**
 * Checks if the assingment key=>value exists in an array
 * 
 * @param array $arr array to be verified upon
 * @param string $var key
 * @param string $value value
 * @return bool
 */
function checkIfExistsInArray($arr, $var, $value): bool
{
    foreach($arr as $a) {
        if(isset($a[$var])) {
            if($a[$var] == $value) {
                return true;
            }
        }
    }
    return false;
}

/**
 * Generates permissions array for token data
 * 
 * @param array $perms permission array
 * @return array
 */
function generatePermsForToken($perms): array
{
    $new_perms = array();
    foreach($perms as $p) {
        $new_perms[] = array(
            'nome' => $p['nome'],
            'modulo' => $p['modulo']
        );
    }
    
    return $new_perms;
}

/**
 * Checks if the requested permission exists on token data
 * 
 * @param object $token token data
 * @param string $perm_req requested permission
 * @return bool
 */
function doIHavePermission($token, $perm_req): bool
{
    $perms_req = array();
    if(strpos($perm_req, ',') !== false) {
        $perms_req = explode(',', $perm_req);
    }
    else {
        $perms_req[] = $perm_req;
    }
    
    foreach($token['data']['perms'] as $prm) {
        if(in_array($prm['nome'], $perms_req)) {
            // possui a permissão. Se não for staff, valide o módulo
            if($token['data']['tipo'] != 'STF') {
                $mod_find_str = $prm['modulo'] . ':';
                if(strpos($token['data']['modulos'], $mod_find_str) !== false) {
                    return true;
                }
            }
            else {
                return true;
            }
        }
    }
    
    return false;
}

/**
 * Checks if the module requested exists on token data
 * 
 * @param object $token token data
 * @param string $mod module requested
 * @return bool
 */
function doIHaveModule($token, $mod): bool
{
    return (strpos($token['data']['modulos'], $mod) !== false);
}

/**
 * Checks the use limits of module on toke data
 * 
 * @param object $token token data
 * @param string $mod module
 * @return int use limits if finded, pseudoinifinite if not
 */
function getMaxInMod($token, $mod): int
{
    $mods = explode('|',$token['data']['modulos']);
    foreach($mods as $m) {
        if(!empty($m)) {
            if(strpos($m, $mod) !== false) {
                $max_v = explode(':', $m);
                if(!empty($max_v[1])) {
                    $max = $max_v[1];
                    if($max == 'INF') {
                        return MOD_INFINITE;
                    }
                    else {
                        return (int) $max;
                    }
                }
            }
        }
    }
    
    return 0;
}

/**
 * Generates max and minimum dates for a given range of years
 * 
 * @param int $year_ini starting year
 * @param int $year_end ending year [optional] [default=-1]
 * @return object
 */
function getDateRangeFromYearRange($year_ini, $year_end = -1): ?object
{
    if($year_end != -1 && ($year_end < $year_ini)) {
        return null;
    }
    
    $now = new DateTime();
    $result = new stdClass();
    if($year_ini > 0)
    {
        $result->end = $now->modify('-'.$year_ini.' year');
    }
    else {
        $result->end = $now;
    }
    
    if($year_end > -1) {
        $now = new DateTime();
        $result->ini = $now->modify('-'.$year_end.' year');
    }
    else {
        $result->ini = null;
    }
    
    return $result;
}

/**
 * Tests if the given email address is valid
 * 
 * @param string $email email address to be tested
 * @param array $ignore_domais array with domais to be ingores [optional]
 * @return bool
 */
function validMailDestination($email, $ignore_domais = array()): bool
{
    if (filter_var($email, FILTER_VALIDATE_EMAIL) !== FALSE) {
        $domain = substr($email, strpos($email, '@') + 1);
        if(!in_array($domain, $ignore_domais)) {
            return(checkdnsrr($domain) !== FALSE);
        }
    }
    
    return false;
}