<?php
require_once COMPOSER_PATH . '/autoload.php';

use PHPMailer\PHPMailer\PHPMailer as PhPMailer;
use PHPMailer\PHPMailer\Exception as PhPMailerException;

/**
 * Class with common useful methods for webservices
 *
 * @author johnatas
 */
class WSUtil
{
    /**
     * Generates pagination object
     * 
     * @param int $page page number
     * @param int $pagesize pagesize 
     * @return object
     */
    protected function calcPagination($page, $pagesize): object
    {
        $pagination = new stdClass();
        $pagination->page = ($page == -1) ? 0 : ($page - 1) * $pagesize;
        $pagination->pagesize = $pagesize;

        return $pagination;
    }
    
    /**
     * Ordering operation
     * 
     * @param array $pre_result array to be ordered
     * @return array
     */
    protected function orderBy($pre_result): array
    {
        usort($pre_result, function($a, $b) {
            $order_by_v = explode(',',NblFram::$context->data->orderBy);
            $order_var = $order_by_v[0];
            $v1 = $a[$order_var];
            $v2 = $b[$order_var];
            if($order_var == 'data_nascimento' && 
                    $order_var == 'time_cad' && 
                    $order_var == 'last_mod') {
                $v1 = (is_null($a[$order_var])) ? null : new DateTime($a[$order_var]);
                $v2 = (is_null($b[$order_var])) ? null : new DateTime($b[$order_var]);
                
                if($order_by_v[1] == 'asc') {
                    return $v1 > $v2;
                }
                else {
                    return $v1 < $v2;
                }
            }
            
            if($order_by_v[1] == 'asc') {
                return strtolower($v1) > strtolower($v2);
            }
            else {
                return strtolower($v1) < strtolower($v2);
            }
        });
        
        return $pre_result;
    }
    
    /**
     * Test for input parameter as a not empty, not null string
     * 
     * @param string $var parameter name
     * @param object $obj input object. Optional. Default to NblFram input object
     * @return bool
     */
    protected function testInputString($var, $obj = null): bool
    {
        $obj_test = (is_null($obj)) ? NblFram::$context->data : $obj;
        if(property_exists($obj_test, $var)) {
            return (isset($obj_test->{$var}) && !empty($obj_test->{$var}));
        }
        
        return false;
    }
    
    /**
     * Test for input parameter as a valid boolean value
     * 
     * @param string $var parameter name
     * @param object $obj input object. Optional. Default to NblFram input object
     * @return bool
     */
    protected function testInputBool($var, $obj = null): bool 
    {
        $obj_test = (is_null($obj)) ? NblFram::$context->data : $obj;
        if(property_exists($obj_test, $var)) {
            return (isset($obj_test->{$var}) && !empty($obj_test->{$var}) && 
                        (
                            ((gettype($obj_test->{$var}) == 'boolean') && $obj_test->{$var}) ||
                            ((gettype($obj_test->{$var}) == 'string') && ($obj_test->{$var} === 'true'))
                        )
                    );
        }
        
        return false;
    }
    
    /**
     * Check if the given string is formatted as date
     * 
     * @param string $str string to be tested
     * @param regex $regex regex with the date format [default: DD/MM/YYYY]
     * @return bool
     */
    protected function isDateStr($str, $regex = '/[0-9]{2}\/[0-9]{2}\/[0-9]{4}/'): bool
    {
        return (preg_match($regex, $str) === 1);
    }
    
    /**
     * Check if the given string is formatted as date range
     * 
     * @param string $str string to be tested
     * @param regex $regex regex with the date range format [default: DD/MM/YYYY - DD/MM/YYYY]
     * @return bool
     */
    protected function isDateRangeStr($str, $regex = '/[0-9]{2}\/[0-9]{2}\/[0-9]{4}\s*\-\s*[0-9]{2}\/[0-9]{2}\/[0-9]{4}/'): bool
    {
        return (preg_match($regex, $str) === 1);
    }
    
    /**
     * Get starting and ending dates from a string formatted as date range
     * 
     * @param string $str formatted string
     * @return array array with results: ['start' => '', 'end' => '']
     */
    protected function getDateRangeFromStr($str): array
    {
        $split = explode('-', $str);
        if(is_array($split) && count($split) == 2) {
            return array('start' => trim($split[0]), 'end' => trim($split[1]));
        }
        return [];
    }
    
    /**
     * Checks if the given string is formatted as SQL date (YYYY-MM-DD)
     * 
     * @param string $str string to be tested
     * @return bool
     */
    protected function isDateSqlStr($str): bool
    {
        return (preg_match('/[0-9]{4}\-[0-9]{2}\-[0-9]{2}/', $str) === 1);
    }
    
    /**
     * Check if the given string is formatted as money
     * 
     * @param string $str string to be tested
     * @param regex $regex regex with the pattern [default: 999,999.99]
     * @return bool
     */
    protected function isMoneyStr($str, $regex = '/\b\d{1,3}(?:\.?\d{3})*(?:,\d{2})+\b/'): bool
    {
        return (preg_match($regex, $str) === 1);
    }
    
    /**
     * Check if the given variable is empty or equal to a value
     * 
     * @param mixed $var variable to be tested
     * @param mixed $value value to be compared
     * @return bool
     */
    protected function isEmptyOrEqualTo($var, $value): bool
    {
        return (empty($var) || ($var == $value));
    }
    
    /**
     * Sends an email
     * 
     * @param string $title title
     * @param string $msg content
     * @param string $email recipient address
     * @return void
     */
    protected function sendMail($title, $msg, $email): void
    {
        global $ignore_domains;
        if(validMailDestination($email, $ignore_domains)) {
            $mail = new PhPMailer(true);                             
            try {
                $mail->SMTPDebug = 0;                                 
                $mail->isSMTP();                                      
                $mail->Host = MAIL_HOST;  
                $mail->SMTPAuth = true;                               
                $mail->Username = MAIL_USER;                
                $mail->Password = MAIL_PASS;    
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;         
                $mail->CharSet = 'UTF-8';

                $mail->setFrom(MAIL_EMAIL, MAIL_NAME);
                $mail->addAddress($email);
                $mail->addReplyTo(MAIL_RESPONSE, MAIL_NAME);

                $mail->isHTML(true);                                 
                $mail->Subject = $title;
                $mail->Body    = $msg;
                $mail->AltBody = $msg;

                $mail->send();
            } catch (Exception $e) {
                NblLogUtil::log(LOG_PATH, print_r($e, true));
            }
        }
    }
    
    /**
     * Stringify an array
     * 
     * @param array $array array to be stringified
     * @param string $sep field separator [optional] [default: ,]
     * @param bool $sql_mode flag to indicate use o SQL mode (escape data) [optional] [default: true]
     * @param bool $key flag to indicate use of key instead of value of array on string [optional] [default: false]
     * @return string
     */
    protected function stringifyArray($array, $sep = ',', $sql_mode = true, $key = false): string
    {
        $str = '';
        foreach($array as $k => $a)
        {
            if(!empty($str)) {
                $str .= $sep;
            }
            
            if($sql_mode) {
                $str .= ($key) ? "'$k'" : "'$a'";
            }
            else {
                $str .= ($key) ? $k : $a;
            }
        }
        
        return $str;
    }
    
    /**
     * Prepare a file for download
     * 
     * @param string $path file path
     * @param string $f_name filename
     * @return array
     */
    protected function prepareDownload($path, $f_name): array
    {
        if(!empty($path)) {
            if(file_exists($path)) {
                $ext = pathinfo($path, PATHINFO_EXTENSION);
                $filename = $f_name . '.' . $ext;

                $f_content = base64_encode(file_get_contents($path));

                $result = array(
                    'status' => 'ok',
                    'success' => true,
                    'name' => $filename,
                    'type' => mime_content_type($path),
                    'content' => $f_content,
                    'size' => filesize($path)
                );
            }
            else {
                $result = array(
                    'status' => 'no',
                    'success' => false,
                    'msg' => 'Arquivo não existe'
                );
            }
        }
        else {
            $result = array(
                'status' => 'no',
                'success' => false,
                'msg' => 'Arquivo não existe'
            );
        }
        
        return $result;
    }
    
    /**
     * Check if the given content is a base64 encoded image
     * 
     * @param string $content content to be tested
     * @return bool
     */
    protected function isBase64Img($content): bool
    {
        return (!empty($content) && (preg_match('/data:image\/(bmp|gif|jpeg|png|svg\+xml)/', $content) === 1));
    }
    
    /**
     * Generates a filter string from a input list
     * 
     * @param string $input input list
     * @param string $separador separator used on list [default: ,]
     * @param bool $sql_mode flag to indicate use o SQL mode (escape data) [default: true]
     * @return string
     */
    protected function generateFilterFromInputList($input, $separador = ',', $sql_mode = true): string
    {
        $input_list = '';
            
        if(strpos($input, $separador) !== false) {
            $input_v = explode($separador, $input);
            foreach($input_v as $i) {
                if(!empty($input_list)) {
                    $input_list .= ',';
                }

                if($sql_mode) {
                    $input_list .= "'{$i}'";
                }
                else {
                    $input_list .= $i;
                }
            }
        }
        else {
            if($sql_mode) {
                $input_list .= "'{$input}'";
            }
            else {
                $input_list .= $input;
            }
        }
        
        return $input_list;
    }
    
    /**
     * Generates a filter jsonn array from a input list
     * 
     * @param string $input input list
     * @param string $separador separator used on list [default: ,]
     * @param callable $json_closure callback function to generates json code
     * @return array
     */
    protected function generateFilterJsonFromInputList($input, $separador = ',', $json_closure): array
    {
        $input_list = array();
        
        if(strpos($input, $separador) !== false) {
            $input_v = explode($separador, $input);
            foreach($input_v as $i) {
                $input_list[] = $json_closure($i);
            }
        }
        else {
            $input_list[] = $json_closure($input);
        }
        return $input_list;
    }
    
    /**
     * Generates a log
     * 
     * @param type $info information to be logged
     * @return void 
     */
    protected function logThisInfo($info): void
    {
        if(LOG_LEVEL == 0) { return; }
        if(LOG_LEVEL == 1 || LOG_LEVEL == 3) {
            error_log($info);
        }
        if(LOG_LEVEL == 2 || LOG_LEVEL == 3) {
            NblLogUtil::log(LOG_PATH, $info);
        }
    }
    
}
