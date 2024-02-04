<?php 

define('CMP_EQUAL', 0);
define('CMP_INCLUDE_LEFT', 1);
define('CMP_INCLUDE_RIGHT', 2);
define('CMP_INCLUDE_INSIDE', 3);
define('CMP_LESSER', 4);
define('CMP_LESSER_THEN', 5);
define('CMP_GREATER', 6);
define('CMP_GREATER_THEN', 7);
define('CMP_EQUAL_DATE', 8);
define('CMP_LESSER_DATE', 9);
define('CMP_LESSER_THEN_DATE', 10);
define('CMP_GREATER_DATE', 11);
define('CMP_GREATER_THEN_DATE', 12);
define('CMP_EQUAL_TIME', 13);
define('CMP_LESSER_TIME', 14);
define('CMP_LESSER_THEN_TIME', 15);
define('CMP_GREATER_TIME', 16);
define('CMP_GREATER_THEN_TIME', 17);
define('CMP_EQUAL_MONTH', 18);
define('CMP_LESSER_MONTH', 19);
define('CMP_LESSER_THEN_MONTH', 20);
define('CMP_GREATER_MONTH', 21);
define('CMP_GREATER_THEN_MONTH', 22);
define('CMP_EQUAL_YEAR', 23);
define('CMP_LESSER_YEAR', 24);
define('CMP_LESSER_THEN_YEAR', 25);
define('CMP_GREATER_YEAR', 26);
define('CMP_GREATER_THEN_YEAR', 27);
define('CMP_EQUAL_DAY', 28);
define('CMP_LESSER_DAY', 29);
define('CMP_LESSER_THEN_DAY', 30);
define('CMP_GREATER_DAY', 31);
define('CMP_GREATER_THEN_DAY', 32);
define('CMP_IN_LIST', 33);
define('CMP_NOT_IN_LIST', 34);
define('CMP_DIFF', 35);
define('CMP_IS_NULL', 36);
define('CMP_NOT_NULL', 37);
define('CMP_IS_EMPTY', 38);
define('CMP_NOT_EMPTY', 39);
define('ORDER_NONE', '');
define('ORDER_ASC', 'ASC');
define('ORDER_DESC', 'DESC');
define('GROUP_NO', false);
define('GROUP_YES', true);
define('OP_AND', 'AND');
define('OP_OR', 'OR');

/**
 * Base class for database handling
 */
class DBO 
{ 
    /* database connection resorce */ 
    public $con; 
    /* query execution error message */ 
    public $err_msg;
     
    public function connect()  
    {  
        $this->con = NewADOConnection(APP_DB_DBMS);  
        $this->con->Connect(APP_DB_HOST, APP_DB_USER, APP_DB_PASS, APP_DB_DB);
        $this->con->Execute('SET NAMES "UTF8"'); 
        $this->con->debug = false; 
    } 
 
} 
 
 
