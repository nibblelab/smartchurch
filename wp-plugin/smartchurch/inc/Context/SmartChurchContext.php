<?php

namespace SmartChurch\Context;

use SmartChurch\Context\Contexts;
use SmartChurch\Context\Context;
use SmartChurch\Context\IgrejaContext;

/**
 * Description of SmartChurchContext
 *
 * @author johnatas
 */
class SmartChurchContext {
    public static $context = null;
    
    public static function create($ctx) {
        if(!is_null(self::$context)) {
            unset(self::$context);
        }
        if($ctx == Contexts::IGREJA) {
            self::$context = new IgrejaContext();
        }
        else if($ctx == Contexts::FEDERACAO) {
            //self::$context = new IgrejaContext();
        }
        else if($ctx == Contexts::SINODAL) {
            //self::$context = new IgrejaContext();
        }
        else if($ctx == Contexts::EVENTO) {
            //self::$context = new IgrejaContext();
        }
    }
}
