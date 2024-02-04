<?php

/**
 * Enumerate validation results
 */
abstract class DAOConfigValidation 
{
    /**
     * Data is valid
     */
    const IS_VALID = 0;
    /**
     * Data is invalid
     */
    const IS_INVALID = 1;
    /**
     * Data is null
     */
    const IS_NULL = 2;
    /**
     * Data is empty
     */
    const IS_EMPTY = 3;
}

/**
 * Interface between database structure and system objects DAO, ADO and DTO
 * 
 */
class DAOConfig
{
    private $dao_config;
    
    private $dao_msg;

    /**
     * Validade a string as SQL date
     * 
     * @param string $date_str string to be validated
     * @return int
     */
    private function validateDBDate($date_str): \DAOConfigValidation {
        if(is_null($date_str)) {
            return DAOConfigValidation::IS_NULL;
        }
        
        if(empty($date_str)) {
            return DAOConfigValidation::IS_EMPTY;
        }
        
        $dt = DateTime::createFromFormat("Y-m-d", $date_str);
        if($dt !== false && !array_sum($dt->getLastErrors())) {
            return DAOConfigValidation::IS_VALID;
        }
        
        return DAOConfigValidation::IS_INVALID;
    }
        
    /**
     * Validade a string as SQL datetime
     * 
     * @param string $date_str string to be validated
     * @return int
     */
    private function validateDBDateTime($date_str): \DAOConfigValidation {
        if(is_null($date_str)) {
            return DAOConfigValidation::IS_NULL;
        }
        
        if(empty($date_str)) {
            return DAOConfigValidation::IS_EMPTY;
        }
        
        $dt = DateTime::createFromFormat("Y-m-d H:i:s", $date_str);
        if($dt !== false && !array_sum($dt->getLastErrors())) {
            return DAOConfigValidation::IS_VALID;
        }
        
        return DAOConfigValidation::IS_INVALID;
    }
    
    /**
     * Get DAO msg
     * 
     * @param string $msg
     * @return string
     */
    private function getMsg($msg): string
    {
        if(!isset($this->dao_config['strings'][$msg])) {
            return '';
        }
        
        return ' ' . $this->dao_config['strings'][$msg] . ' ';
    }

    /**
     * Load DAO configs
     * 
     * @return void
     */
    public function load(): void
    {
        $str = file_get_contents(DAO_PATH . '/dao_config.json');
        $this->dao_config = json_decode($str, true);
        
        $str_msg = file_get_contents(DAO_PATH . '/dao_config_'.LANG.'.json');
        $this->dao_msg = json_decode($str_msg, true);
    }

    /**
     * Get database tables for DTO object
     * 
     * @param string $dto_name DTO object name
     * @return array 
     */
    public function getDBTables($dto_name): array 
    {
        $result = array();
        foreach($this->dao_config['dtos'] as $dto) {
            if($dto['name'] == $dto_name) {
                foreach($dto['tables'] as $table) {
                    $result[] = $table['name'];
                }
            }
        }

        return $result;
    }

    /**
     * Get primary keys for database table and DTO object
     * 
     * @param string $dto_name DTO object name
     * @param string $table_name nome da tabela
     * @return array 
     */
    public function getDBTablePKs($dto_name, $table_name): array
    {
        $result = array();
        foreach($this->dao_config['dtos'] as $dto) {
            if($dto['name'] == $dto_name) {
                foreach($dto['tables'] as $table) {
                    if($table['name'] == $table_name) {
                        foreach($table['primary_keys'] as $pk) {
                            $result[] = $pk;
                        }
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Get database table name for DTO object, filtered from possible tables and table field
     * 
     * @param string $dto_name  DTO object name
     * @param array $possible_tables possible tables 
     * @param string $field_name table field
     * @return array 
     */
    public function getTableOfField($dto_name, $possible_tables, $field_name): array
    {
        $tables = array();
        foreach($this->dao_config['dtos'] as $dto) {
            if($dto['name'] == $dto_name) {
                foreach($dto['tables'] as $table) {
                    if(in_array($table['name'], $possible_tables)) {
                        foreach($table['fields'] as $field) {
                            if($field['name'] == $field_name) {
                                $tables[] = $table['name'];
                            }
                        }
                    }
                    
                }
            }
        }

        return $tables;
    }

    /**
     * Get SQL 'from' for DTO object
     * 
     * @param string $dto_name DTO object name
     * @return string
     */
    public function getFromSQL($dto_name): string
    {
        foreach($this->dao_config['dtos'] as $dto) {
            if($dto['name'] == $dto_name) {
                return $dto['from_sql'];
            }
        }

        return '';
    }

    /**
     * Get primary keys from database table of DTO object
     * 
     * @param string $dto_name DTO object name
     * @return array 
     */
    public function getDBTablePKs4WhereSQL($dto_name): array
    {
        $result = array();
        foreach($this->dao_config['dtos'] as $dto) {
            if($dto['name'] == $dto_name) {
                foreach($dto['tables'] as $table) {
                    if($table['where_sql']) {
                        foreach($table['primary_keys'] as $pk) {
                            $key = $pk;
                            if(!empty($table['alias'])) {
                                $key = $table['alias'] . '.' . $pk;
                            }
                            $result[] = array(
                                'name' => $pk, 
                                'key' => $key
                            );
                        }
                    }
                }
            }
        }

        return $result;
    }
    
    /**
     * Get database table fields for DTO object
     * 
     * @param string $dto_name DTO object name
     * @return array
     */
    public function getFieldsOfDBTables($dto_name): array
    {
        $result = array();
        foreach($this->dao_config['dtos'] as $dto) {
            if($dto['name'] == $dto_name) {
                $has_join = (count($dto['tables']) > 1);
                foreach($dto['tables'] as $table) {
                    foreach($table['fields'] as $field) {
                        if(!in_array($field['name'], $result)) {
                            if($has_join) {
                                // table has SQL join
                                if($has_join &&
                                    !$table['where_sql'] && 
                                    in_array($field['name'], $table['primary_keys'])) {
                                    /* field is primary key of secundary table in SQL join. Ignore! */
                                    continue;
                                }
                                else  {
                                    // normal case in SQL join. Insert field aliases
                                    $result[] = $table['alias'] . '.' . $field['name'] . ' as ' . $field['name'];
                                }
                            }
                            else {
                                // not SQL join. Normal case
                                $result[] = $field['name'];
                            }
                        }
                    }
                }
            }
        }
        return $result;
    }

    /**
     * Check if field is not generated by DBMS
     * 
     * @param string $dto_name DTO object name
     * @param string $table_name table name
     * @param string $field field name
     * @return bool
     */
    public function checkIfFieldIsNotAuto($dto_name, $table_name, $field): bool
    {
        foreach($this->dao_config['dtos'] as $dto) {
            if($dto['name'] == $dto_name) {
                foreach($dto['tables'] as $table) {
                    if($table['name'] == $table_name) {
                        foreach($table['fields'] as $field) {
                            if($field['name'] == $field) {
                                return $field['is_auto'];
                            }
                        }
                    }
                }
            }
        }

        return false;
    }

    /**
     * Get the SQL value of field. 
     * If value is null, apply possible default value
     * If not, generate equivalent SQL value
     * 
     * @param string $dto_name DTO object name
     * @param string $table_name table name
     * @param string $field field name
     * @param string $value current value of field
     * @return string 
     */
    public function getFieldValueOrDefault($dto_name, $table_name, $field, $value): ?string 
    {
        foreach($this->dao_config['dtos'] as $dto) {
            if($dto['name'] == $dto_name) {
                foreach($dto['tables'] as $table) {
                    if($table['name'] == $table_name) {
                        foreach($table['fields'] as $field) {
                            if($field['name'] == $field) {
                                if(!empty($field['default'])) {
                                    // field has default value
                                    if(is_null($value)) {
                                        // field is null. Apply default
                                        if(strpos($field['default'], 'EMPTY') !== false) {
                                            // default value is empty string 
                                            return '';
                                        }
                                        else if(strpos($field['default'], 'NULL') !== false) {
                                            // default value is null
                                            return null;
                                        }
                                        else if(strpos($field['default'], 'STR') !== false) {
                                            // default value is a personalized string -- pattern: STR#default_value. Ex: STR#ATV puts ATV as value on field
                                            $default = explode('#', $field['default']);
                                            return $default[1];
                                        }
                                        else if(strpos($field['default'], 'INT') !== false) {
                                            // default value is an integer -- pattern: INT#default_value. Ex: INT#0 puts 0 as value on field
                                            $default = explode('#', $field['default']);
                                            return $default[1];
                                        }
                                        else if(strpos($field['default'], 'FLOAT') !== false) {
                                            // default value is a float -- pattern: FLOAT#default_value. Ex: FLOAT#0.00 puts 0.00 as value on field
                                            $default = explode('#', $field['default']);
                                            return $default[1];
                                        }
                                        else if(strpos($field['default'], 'DATE') !== false) {
                                            // default value is a date -- pattern: DATE#date_pattern. Ex: DATE#Y-m-d puts the result of date('Y-m-d') as value on field
                                            $default = explode('#', $field['default']);
                                            return date($default[1]);
                                        }
                                    }
                                    else {
                                        // field is not null. Just convert
                                        if(is_int($value) || is_float($value)) {
                                            // if is a number just cast to string
                                            return '' . $value;
                                        }
                                        
                                        return $value;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $value;
    }

    /**
     * Validates a field
     * 
     * @param string $dto_name DTO object name
     * @param string $table_name table name
     * @param string $field field name
     * @param string $value value of field
     * @return bool 
     */
    public function isFieldValid($dto_name, $table_name, $field, $value, &$errs): bool
    {
        $result = true;
        foreach($this->dao_config['dtos'] as $dto) {
            if($dto['name'] == $dto_name) {
                foreach($dto['tables'] as $table) {
                    if($table['name'] == $table_name) {
                        foreach($table['fields'] as $field) {
                            if($field['name'] == $field) {
                                /* run validations */
                                if(strpos($field['validation'], 'IS_NULL') !== false) {
                                    // field is null
                                    if(!is_null($value)) {
                                        $errs[] = $field . $this->getMsg('msg_is_null');
                                        $result = false;
                                    }
                                }
                                if(strpos($field['validation'], 'SIZE') !== false && !empty($field['size']) && 
                                        strpos($field['validation'], 'BE_NULL') === false) {
                                    // field has size validation and cannot be null
                                    $size = (int) $field['size'];
                                    $v_size = strlen($value);
                                    if(strpos($field['validation'], 'EQ_SIZE') !== false) {
                                        // size must be equal 
                                        if($v_size != $size) {
                                            $errs[] = $field . $this->getMsg('msg_eq_size') . $size;
                                            $result = false;
                                        }
                                    }
                                    else if(strpos($field['validation'], 'MX_SIZE') !== false) {
                                        // size must be equal or less 
                                        if($v_size > $size) {
                                            $errs[] = $field . $this->getMsg('msg_mx_size') . $size;
                                            $result = false;
                                        }
                                    }
                                }
                                else if(strpos($field['validation'], 'IS_INT') !== false) {
                                    // field is an int
                                    if (ctype_digit(ltrim($value, '-')) === false) {
                                        $errs[] = $field . $this->getMsg('msg_is_int');
                                        $result = false;
                                    }
                                }
                                else if(strpos($field['validation'], 'IS_FLOAT') !== false) {
                                    // field is a float
                                    if (!is_numeric($value)) {
                                        $errs[] = $field . $this->getMsg('msg_is_float');
                                        $result = false;
                                    }
                                }
                                else if(strpos($field['validation'], 'IS_DATE_DB') !== false) {
                                    // field is SQL date
                                    $validation = $this->validateDBDate($value);
                                    $can_be_null = (strpos($field['validation'], 'BE_NULL') !== false);
                                    if(($validation == DAOConfigValidation::IS_EMPTY) ||
                                        ($validation == DAOConfigValidation::IS_INVALID) ||
                                        ($validation == DAOConfigValidation::IS_NULL && !$can_be_null)) {
                                        $errs[] = $field . $this->getMsg('msg_is_date_db');
                                        $result = false;
                                    }
                                }
                                else if(strpos($field['validation'], 'IS_DATETIME_DB') !== false) {
                                    // field is SQL datetime
                                    $validation = $this->validateDBDateTime($value);
                                    $can_be_null = (strpos($field['validation'], 'BE_NULL') !== false);
                                    if(($validation == DAOConfigValidation::IS_EMPTY) ||
                                        ($validation == DAOConfigValidation::IS_INVALID) ||
                                        ($validation == DAOConfigValidation::IS_NULL && !$can_be_null)) {
                                        $errs[] = $field . $this->getMsg('msg_is_datetime_db');
                                        $result = false;
                                    }
                                }
                                else if(strpos($field['validation'], 'NOT_NULL') !== false) {
                                    // field cannot be null
                                    if(is_null($value)) {
                                        $errs[] = $field . $this->getMsg('msg_not_null');
                                        $result = false;
                                    }
                                }
                                else if(strpos($field['validation'], 'NOT_EMPTY') !== false) {
                                    // field cannot be empty
                                    if(empty($value)) {
                                        $errs[] = $field . $this->getMsg('msg_not_empty');
                                        $result = false;
                                    }
                                }
                            }
                        }
                    }
                    
                }
            }
        }

        return $result;
    }
}