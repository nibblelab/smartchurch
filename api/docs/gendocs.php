<?php
require_once '../app/config/conf.cfg.php';
require_once DAO_PATH . '/DBO.class.php';
require_once ADO_PATH . '/BaseADO.class.php'; 
require_once DAO_PATH . '/BaseDAO.class.php';

class GenerateDocs
{
    private static $ws_doc_path = ROOT_PATH . '/docs/content/ws';
    private static $doc_js_path = ROOT_PATH . '/docs/js/doc.js';
    private static $nblfram_doc = ROOT_PATH . '/docs/content/nblfram-doc.json';
    
    /**
     * Get a possible classname on file
     * 
     * @param string $file file path
     * @return string
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
     * Get description comments from documentation code
     * 
     * @param string $doc documentation code
     * @return string
     */
    private static function getDesc($doc): string
    {
        $desc = $doc;
        $at_pos = strpos($desc, '@');
        if($at_pos !== false) {
            $desc = substr($desc, 0, strpos($desc, '@'));
        }
        $desc = str_replace('/**', '', $desc);
        $desc = str_replace('*/', '', $desc);
        $desc = str_replace('*', '', $desc);
        $desc = str_replace("\n      ", '', $desc);
        $desc = str_replace("\n  ", '', $desc);
        $desc = str_replace("\n ", '', $desc);
        return $desc;
    }
    
    /**
     * Get method used params from his code
     * 
     * @param string $method_code
     * @return array
     */
    private static function getUsedParams($method_code): array
    {
        $lines = preg_split('/\n/', $method_code, -1, PREG_SPLIT_OFFSET_CAPTURE);
        foreach($lines as $line) {
            if(strpos($line, 'NblFram::$context->data->') !== false) {
                
            }
        }
    }
    
    /**
     * Generates documentation for controller
     * 
     * @param object $controller
     * @return void
     */
    private static function generateControllerDoc($controller): void
    {
        require_once $controller->path;
        
        $controller_doc = array(
            'name' => trim($controller->controller),
            'desc' => '',
            'methods' => array()
        );
        
        $reflector = new ReflectionClass($controller->classname);
        $controller_doc['desc'] = self::getDesc($reflector->getDocComment());
        $method_list = $reflector->getMethods();
        foreach($method_list as $m)
        {
            $method_doc = array(
                'name' => trim($m->name),
                'desc' => '',
                'http_method' => '',
                'is_auth' => false,
                'params' => array()
            );
            $method = new ReflectionMethod($controller->classname, $m->name);
            $doc = $method->getDocComment();
            if(preg_match('/@httpmethod(\s+)POST/i', $doc) === 1) 
            {
                $method_doc['http_method'] = 'POST';
            }
            else if(preg_match('/@httpmethod(\s+)GET/', $doc) === 1) 
            {
                $method_doc['http_method'] = 'GET';
            }
            else if(preg_match('/@httpmethod(\s+)PUT/', $doc) === 1) 
            {
                $method_doc['http_method'] = 'PUT';
            }
            else if(preg_match('/@httpmethod(\s+)DELETE/', $doc) === 1) 
            {
                $method_doc['http_method'] = 'DELETE';
            }
            
            if(!empty($method_doc['http_method'])) {
                $method_doc['is_auth'] = (preg_match('/@auth(\s+)yes/i', $doc) === 1);
                
                $matches = array();
                preg_match_all("/@require(\s+)(.*)/im", $doc, $matches);
                if(!empty($matches))
                {
                    if(!empty($matches[2])) {
                        foreach($matches[2] as $param) {
                            $param_name = $param;
                            $param_desc = '';
                            $sep_pos = strpos($param, ' ');
                            if($sep_pos !== false) {
                                $param_name = trim(substr($param, 0, $sep_pos));
                                $param_desc = trim(substr($param, $sep_pos));
                            }
                            $method_doc['params'][] = array('param' => $param_name, 'desc'=> $param_desc, 'required' => true);
                        }
                    }
                }
                
                $method_doc['desc'] = self::getDesc($doc);
                
                $matches_param = array();
                preg_match_all("/@optional(\s+)(.*)/im", $doc, $matches_param);
                if(!empty($matches_param))
                {
                    if(!empty($matches_param[2])) {
                        foreach($matches_param[2] as $param) {
                            $param_name = $param;
                            $param_desc = '';
                            $sep_pos = strpos($param, ' ');
                            if($sep_pos !== false) {
                                $param_name = trim(substr($param, 0, $sep_pos));
                                $param_desc = trim(substr($param, $sep_pos));
                            }
                            $method_doc['params'][] = array('param' => $param_name, 'desc'=> $param_desc, 'required' => false);
                        }
                    }
                }
                
                
                $controller_doc['methods'][] = $method_doc;
            }
        }
        
        file_put_contents(self::$ws_doc_path . '/' . $controller->controller . '.json', json_encode($controller_doc));
    }
    
    /**
     * Get all controllers in the system
     * 
     * @return array
     */
    private static function getControllers(): array
    {
        $controllers = [];
        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(WS_PATH));
        foreach ($it as $cntl) {
            if ($cntl->isDir()){ 
                continue;
            }

            $controller_path = $cntl->getPathname();
            $cntl_obj = new stdClass();
            $cntl_obj->path = $controller_path;
            $cntl_obj->controller = str_replace('.ws.php', '', $cntl->getFilename());
            $cntl_obj->classname = self::getClassNameFromFile($controller_path);
            $controllers[] = $cntl_obj; 
        }
        
        return $controllers;
    }
    
    /**
     * Generate webservices list
     * 
     * @param array $controllers controllers list
     * @return void
     */
    private static function generateWSList($controllers): void
    {
        $doc_json = array('webservices' => array());
        foreach($controllers as $cntl) 
        {
            $doc_json['webservices'][] = $cntl->controller;
        }
        file_put_contents(self::$nblfram_doc, json_encode($doc_json));
    }
    
    /**
     * Generates documentation for all controllers on array
     * 
     * @param array $controllers
     * @return void
     */
    private static function generateControllersDoc($controllers): void
    {
        foreach($controllers as $cntl) 
        {
            self::generateControllerDoc($cntl);
        }
    }
    
    /**
     * Update version
     * 
     * @return void
     */
    private static function updateDocVersion(): void
    {
        $content = file_get_contents(self::$doc_js_path);
        $pattern = "/data: '([0-9]*)\.([0-9]*)\.([0-9]*)'/i";
        $matches = array();
        preg_match($pattern, $content, $matches);
        if(!empty($matches))
        {
            $v0 = (int) $matches[1];
            $v1 = (int) $matches[2];
            $v2 = (int) $matches[3];
            if($v2 < 10) {
                $v2++;
            }
            else {
                $v2 = 0;
                $v1++;
                
                if($v1 < 10) {
                    $v1 = 0;
                    $v0++;
                }
            }
            
            $new_version = "data: '{$v0}.{$v1}.{$v2}'";
            $new_content = preg_replace($pattern, $new_version, $content);
            file_put_contents(self::$doc_js_path, $new_content);
        }
    }

    /**
     * Generates system documentation
     */
    public static function start()
    {
        $controllers = self::getControllers();
        self::generateWSList($controllers);
        self::generateControllersDoc($controllers);
        self::updateDocVersion();
    }
}

GenerateDocs::start();