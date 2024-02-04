<?php


/**
 * REST Helper class
 *
 * @author johnatas
 */
class WSHelper
{
    /**
     * Checks if the given method is on method list
     * 
     * @param array $list method list
     * @param string $method method name
     * @return bool
     */
    private static function checkIfMethodIsInList($list, $method): bool
    {
        foreach($list as $l) {
            if($l->name == $method) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks if the given method exists on class
     * 
     * @param string $class class to be tested
     * @param string $method method name
     * @return bool
     */
    public static function checkIfMethodExists($class, $method): bool
    {
        $reflector = new ReflectionClass($class);
        $method_list = $reflector->getMethods();
        return (WSHelper::checkIfMethodIsInList($method_list, $method));
    }
    
    /**
     * Checks if the given method exists on class and extracts it's metadata
     * 
     * @param string $class class to be tested
     * @param string $method method name
     * @return object method metadata
     */
    public static function checkMethod($class, $method): object
    {
        $r = new stdClass();
        $reflector = new ReflectionClass($class);
        $method_list = $reflector->getMethods();
        if(WSHelper::checkIfMethodIsInList($method_list, $method))
        {
            /* get HTTP method */
            $methods = new ReflectionMethod($class, $method);
            $methods_doc = $methods->getDocComment();
            if(preg_match('/@httpmethod(\s+)POST/i',$methods_doc) === 1) 
            {
                $r->hasError = false;
                $r->HTTPMethod = 'POST';
            }
            else if(preg_match('/@httpmethod(\s+)GET/',$methods_doc) === 1) 
            {
                $r->hasError = false;
                $r->HTTPMethod = 'GET';
            }
            else if(preg_match('/@httpmethod(\s+)PUT/',$methods_doc) === 1) 
            {
                $r->hasError = false;
                $r->HTTPMethod = 'PUT';
            }
            else if(preg_match('/@httpmethod(\s+)DELETE/',$methods_doc) === 1) 
            {
                $r->hasError = false;
                $r->HTTPMethod = 'DELETE';
            }
            
            if(!$r->hasError) 
            {
                // check if method is authenticated
                $r->auth = (preg_match('/@auth(\s+)yes/i',$methods_doc) === 1);
                
                // get requirements list
                $matches = array();
                $r->requires = $matches;
                preg_match_all("/@require(\s+)([a-zA-Z0-9_]*)/im", $methods_doc, $matches);
                if(!empty($matches))
                {
                    if(!empty($matches[2])) {
                        $r->requires = $matches[2];
                    }
                }
                
                return $r;
            }
        }
        
        $r->hasError = true;
        $r->errorCode = 'METHOD_NOT_FOUND';
        $r->errorMsg = 'Method ' . $method . ' was not found in ' . $class;
        
        return $r;
    }
}

?>
