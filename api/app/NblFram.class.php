<?php
require_once FCT_PATH . '/token.cfc.php'; 
require_once FCT_PATH . '/util.cfc.php';  
require_once LIB_PATH . '/adodb5/adodb.inc.php';
require_once DAO_PATH . '/DBO.class.php';
require_once ADO_PATH . '/BaseADO.class.php'; 
require_once DAO_PATH . '/BaseDAO.class.php';
require_once HLP_PATH . '/WSHelper.class.php';
require_once APP_PATH . '/i18n.php';


class NblFram
{
    public static $context;
    
    /**
     * 
     * Reads a PHP file and extracts a possible classname from it
     * 
     * @param string $file
     * @return string classname if finded. Empty string if not
     */
    private static function getClassNameFromFile($file): string
    {
        $content = file_get_contents($file);
        $tokens = token_get_all($content);
        $class_token = false;
        foreach ($tokens as $token) 
        {
            if (is_array($token)) 
            {
                if ($token[0] == T_CLASS) 
                {
                    $class_token = true;
                } 
                else if ($class_token && $token[0] == T_STRING) 
                {
                    return $token[1];
                }
            }       
        }
        
        return '';
    }
    
    /**
     * Checks if the controller requested exists
     * 
     * @param array $ret response array with classname and file
     * @return bool true if succeeds, false if not
     */
    private static function checkController(&$ret): bool
    {
        // tests if any controller was informed
        if(!isset($_GET['controller']) || empty($_GET['controller']))
        {
            $ret = array('status' => 'no', 'success' => false, 'msg' => self::$context->strings['resource_not_finded']);
            return false;
        }
        
        // checks if controller exists
        $cntl_name = $_GET['controller'];
        $cntl_file = WS_PATH . '/' . $cntl_name . '.ws.php';
        if(!file_exists($cntl_file))
        {
            $ret = array('status' => 'no', 'success' => false, 'msg' => self::$context->strings['resource_doesnt_exist']);
            return false;
        }
        
        // get class name
        $cntl_class = self::getClassNameFromFile($cntl_file);
        
        if(empty($cntl_class))
        {
            $ret = array('status' => 'no', 'success' => false, 'msg' => self::$context->strings['controller_not_finded']);
            return false;
        }
        
        // generates response
        $ret = array('status' => 'ok', 'success' => true, 'include' => $cntl_file, 'class' => $cntl_class);
        return true;
    }
    
    /**
     * 
     * Checks if HTTP request method is correct
     * 
     * @param string $req_expected
     * @return bool true if succeeds, false if not
     */
    private static function checkHTTPMethod($req_expected): bool
    {
        return (self::$context->srv['REQUEST_METHOD'] == $req_expected);
    }
    
    /**
     * extract all data from GET HTTP request
     * 
     * @return object object with the data
     */
    private static function getDataFromGET(): object
    {
        $data = new stdClass();
        foreach($_GET as $k => $v)
        {
            if($k != 'controller' && $k != 'method' && $k != 'object_id')
            {
                $data->{$k} = $v;
            }
        }
        
        return $data;
    }
    
    /**
     * Checks token
     * 
     * @param array $ret response array with possible errors
     * @return bool true if succeeds, false if not
     */
    private static function verifyToken(&$ret): bool
    {
        $token = getToken(self::$context->configs);
        $validToken = isValidToken($token);

        if(empty($token))
        {
            $ret = array('status' => 'no', 'success' => false, 'msg' => self::$context->strings['token_not_given']);
            return false;
        }
        else if(!$validToken)
        {
            $ret = array('status' => 'no', 'success' => false, 'msg' => self::$context->strings['token_invalid']);
            return false;
        }
        
        self::$context->token = $token;
        $ret = array('status' => 'ok', 'success' => true);
        return true;
    }
    
    /**
     * 
     * Checks if requested field was given in request
     * 
     * @param array $requires requested fields
     * @param array $ret response array with possible errors
     * @return bool true if succeeds, false if not
     */
    private static function verifyRequires($requires, &$ret): bool
    {
        if(empty($requires))
        {
            return true;
        }
        
        $missing = '';
        foreach($requires as $r)
        {
            if(!property_exists(self::$context->data, $r))
            {
                if(!empty($missing)) {
                    $missing .= ', ';
                }
                
                $missing .= $r;
            }
        }
        
        if(!empty($missing))
        {
            $ret = array('status' => 'no', 'success' => false, 'msg' => self::$context->strings['param_not_given'] . ': ' . $missing);
            return false;
        }
        
        return true;
    }
    
    /**
     * 
     * Checks requested method from controller
     * 
     * @param array $ret response array with possible errors or success
     * @return bool true if succeeds, false if not
     */
    private static function checkMethod(&$ret): bool
    {
        // test if method was given
        if(!isset($_GET['method']) || empty($_GET['method']))
        {
            $ret = array('status' => 'no', 'success' => false, 'msg' => self::$context->strings['method_not_given']);
            return false;
        }
        
        $method = $_GET['method'];
        
        // test method em controller
        include_once self::$context->include;
        $result = WSHelper::checkMethod(self::$context->classname, $method);
        if($result->hasError) 
        {
            // method not found
            if($result->errorCode == 'METHOD_NOT_FOUND')
            {
                $ret = array('status' => 'no', 'success' => false, 'msg' => self::$context->strings['method_invalid']);
                return false;
            }
            else 
            {
                $ret = array('status' => 'no', 'success' => false, 'msg' => self::$context->strings['param_invalid']);
                return false;
            }
        }
        else
        {
            // test if request is correct
            if(!self::checkHTTPMethod($result->HTTPMethod))
            {
                $ret = array('status' => 'no', 'success' => false, 'msg' => self::$context->strings['wrong_http_req']);
                return false;
            }
            else
            {
                // get the data!
                if($result->HTTPMethod == 'POST')
                {
                    self::$context->data = json_decode(file_get_contents("php://input"));
                }
                else if($result->HTTPMethod == 'GET')
                {
                    self::$context->data = self::getDataFromGET();
                }
                else if($result->HTTPMethod == 'PUT')
                {
                    self::$context->data = json_decode(file_get_contents("php://input"));
                    self::$context->data->id = ltrim($_GET['object_id'], '/');
                }
                else if($result->HTTPMethod == 'DELETE')
                {
                    self::$context->data = new stdClass();
                    self::$context->data->id = ltrim($_GET['object_id'], '/');
                }
                
                // test for authentication
                if($result->auth)
                {
                    // tests authentication token
                    if(!self::verifyToken($ret))
                    {
                        return false;
                    }
                }
                
                // test if requested fields was given
                if(!self::verifyRequires($result->requires, $ret))
                {
                    return false;
                }
                
                $ret = array('status' => 'ok', 'success' => true, 'method' => $method);
                return true;
            }
        }
    }
    
    /**
     * Checks if any filter was defined
     * 
     * @return bool true if succeeds, false if not
     */
    private static function useFilter(&$filter_path,&$filter_class): bool
    {
        $fl_name = $_GET['controller'];
        $filter_path = FL_PATH . '/' . $fl_name . '.fl.php';
        if(!file_exists($filter_path))
        {
            return false;
        }
        
        $filter_class = self::getClassNameFromFile($filter_path);

        if(empty($filter_class)) {
            return false;
        }
        
        include $filter_path;
        
        return (WSHelper::checkIfMethodExists($filter_class, self::$context->method));
    }
    
    /**
     * Checks if HTTP request is valid
     * 
     * @return bool true if succeeds, false if not
     */
    private static function validRequest(&$ret): bool
    {
        self::$context->srv = $_SERVER;
        if(isset(self::$context->srv['REQUEST_METHOD']))
        {
            if(self::$context->srv['REQUEST_METHOD'] == 'GET' || 
                    self::$context->srv['REQUEST_METHOD'] == 'POST' || 
                    self::$context->srv['REQUEST_METHOD'] == 'PUT' || 
                    self::$context->srv['REQUEST_METHOD'] == 'DELETE')
            {
                $ret = array('status' => 'ok', 'success' => true);
                return true;
            }
        }

        $ret = array('status' => 'no', 'success' => false, 'msg' => self::$context->strings['invalid_http_req']);
        return false;
    }

    /**
     * bootup framework
     * 
     * @return void
     */
    public static function init(): void
    {
        global $configs;
        
        self::$context = new stdClass();
        self::$context->configs = $configs;
    }
    
    /**
     * Run!
     * 
     * @param string $lang
     * @return void
     */
    public static function exec($lang = 'pt-BR'): void
    {
        global $nbl_fram_strings;
        
        self::init();
        self::$context->strings = $nbl_fram_strings[$lang];
        $ret = array();
        
        // test request 
        if(!self::validRequest($ret)) 
        {
            echo json_encode($ret);
            return;
        }
        
        // test controller
        if(!self::checkController($ret))
        {
            echo json_encode($ret);
            return;
        }
        
        self::$context->include = $ret['include'];
        self::$context->classname = $ret['class'];
        
        // test method
        if(!self::checkMethod($ret))
        {
            echo json_encode($ret);
            return;
        }
        
        self::$context->method = $ret['method'];
        
        // test filters
        if(self::useFilter($filter_path, $filter_class))
        {
            // apply filters
            self::$context->filter = new $filter_class();
            $ret = self::$context->filter->{self::$context->method}();
            if(!$ret['success'])
            {
                echo json_encode($ret);
                return;
            }
        }
        
        
        // Everything looks in order, sir. You may proceed
        include_once self::$context->include;
        self::$context->ws = new self::$context->classname();
        $ret = self::$context->ws->{self::$context->method}();
        echo json_encode($ret);
    }
}

