<?php
ob_start();
require_once './app/headers.inc.php';
require_once './app/config/conf.cfg.php';
require_once APP_PATH . '/NblFram.class.php';


NblFram::exec();
