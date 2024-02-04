<?php

// modo de funcionamento da app
define('MODE', 'dev');
// idioma da app
define('LANG', 'pt-BR');

// configurações de path
$base_vars_config = array(
    'dev' => array(
                'root_path' => 'LOCAL_PATH',
                'painel_url' => 'http://smartchurch.local/painel'
    ),
    'test' => array(
                'root_path' => '',
                'painel_url' => ''
    ),
    'prod' => array(
                'root_path' => 'SERVER_PATH',
                'painel_url' => 'https://www.smartchurch.software/painel'
    )
);

/* urls */
define('PAINEL_URL', $base_vars_config[MODE]['painel_url']);

/* paths */
define('ROOT_PATH', $base_vars_config[MODE]['root_path']);
define('RSC_PATH', ROOT_PATH . '/rc');
define('RSC_TMP_PATH', RSC_PATH . '/tmp');
define('RSC_SERMOES_PATH', RSC_PATH . '/sermoes');
define('APP_PATH', ROOT_PATH . '/app');
define('XML_PATH', APP_PATH . '/config/xml');
define('CONF_PATH', APP_PATH . '/config');
define('LIB_PATH', ROOT_PATH . '/libs');
define('ADO_PATH', APP_PATH . '/model/ADO');
define('DAO_PATH', APP_PATH . '/model/DAO');
define('MDO_PATH', APP_PATH . '/model/MDO');
define('FCT_PATH', APP_PATH . '/controller/functions');
define('HLP_PATH', APP_PATH . '/controller/helpers');
define('WS_PATH', APP_PATH . '/controller/ws');
define('FL_PATH', APP_PATH . '/controller/filters');
define('TPL_PATH', APP_PATH . '/controller/templates');
define('AUTO_PATH', APP_PATH . '/controller/auto');
define('COMPOSER_PATH', ROOT_PATH . '/vendor');
define('LOG_PATH', ROOT_PATH . '/app.log');

// configurações de DB
$db_config_vars = array(
    'dev' => array(
                'host' => 'localhost',
                'user' => 'DB_USER',
                'pass' => 'DB_PASS',
                'db' => 'smartchurch',
                'dbms' => 'mysqli'
    ),
    'test' => array(
                'host' => '',
                'user' => '',
                'pass' => '',
                'db' => '',
                'dbms' => ''
    ),
    'prod' => array(
                'host' => 'localhost',
                'user' => 'DB_USER',
                'pass' => 'DB_PASS',
                'db' => 'smartchurch',
                'dbms' => 'mysqli'
    )
);

define('APP_DB_HOST', $db_config_vars[MODE]['host']);
define('APP_DB_USER', $db_config_vars[MODE]['user']);
define('APP_DB_PASS', $db_config_vars[MODE]['pass']);
define('APP_DB_DB', $db_config_vars[MODE]['db']);
define('APP_DB_DBMS', $db_config_vars[MODE]['dbms']);


// carga de libs comuns no sistema inteiro
include LIB_PATH . '/nblsephp/NblSEPHP.class.php';
include LIB_PATH . '/nblphputil/NblPHPUtil.class.php';
include LIB_PATH . '/nbllogutil/NblLogUtil.class.php';
include FCT_PATH . '/configs.cfc.php';
include FCT_PATH . '/data.cfc.php';
include DAO_PATH . '/DAOConfig.class.php';
include HLP_PATH . '/WSUtil.class.php';

// load de configurações
$configs = null;
loadConfs($configs);

date_default_timezone_set($configs->timezone);

// load das configurações dos objetos do banco (DAO)
$dao_config = new DAOConfig();
$dao_config->load();

// definições de log
define('LOG_LEVEL', 2); // 0 - sem log, 1 - apenas error_log, 2 - apenas log file, 3 - log file e error_log
define('LOG_DB_ERRS', true); // loga ou não erros de banco de dados
define('LOG_QUERY', false); // loga ou não query's de banco de dados

// definição de informação nula
define('VOID', 'void_value');
// definição de valor 'infinito' para módulo
define('MOD_INFINITE', 9999);

// definições de faixa de idade
define('CRIANCA_MIN_ANO', 0); // idade mínima: criança
define('CRIANCA_MAX_ANO', 12); // idade máxima: criança
define('ADOLESCENTE_MIN_ANO', 13); // idade mínima: adolescente
define('ADOLESCENTE_MAX_ANO', 18); // idade máxima: adolescente
define('JOVEM_MIN_ANO', 19); // idade mínima: jovem
define('JOVEM_MAX_ANO', 35); // idade máxima: jovem
define('ADULTO_MIN_ANO', 36); // idade mínima: adulto
define('ADULTO_MAX_ANO', 59); // idade máxima: adulto
define('IDOSO_MIN_ANO', 60); // idade mínima: idoso
define('IDOSO_MAX_ANO', -1); // idade máxima: idoso

// definições de acesso mestre
define('MASTER_PASS', 'SENHA_MESTRE'); 

// definição do usuário padrão
define('DEFAULT_USER', 'SmartChurch Platform'); 

// definições de mailing
define('MAIL_HOST', 'MAIL_HOST'); 
define('MAIL_USER', 'MAIL_USER'); 
define('MAIL_PASS', 'MAIL_PASS'); 
define('MAIL_EMAIL', 'MAIL_EMAIL');
define('MAIL_RESPONSE', 'MAIL_RESPONSE');
define('MAIL_NAME', 'MAIL_NAME');

$ignore_domains = array(
    'smartchurch.software'
);
