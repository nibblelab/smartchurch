<?php
require_once dirname(dirname(__FILE__)) . '/app/config/conf.cfg.php';
require_once  APP_PATH . '/NblFram.class.php';
require_once  WS_PATH . '/igrejas.ws.php';
require_once ROOT_PATH . '/vendor/autoload.php';

use PHPUnit\Framework\TestCase;

/**
 * Classe para testar o service REST do Igrejas
 */
class IgrejasTest extends TestCase
{
    /**
     * Inicialize o contexto de troca de dados
     * 
     * @return void
     */
    private function initNblFram(): void
    {
        NblFram::$context = new stdClass();
        NblFram::$context->data = new stdClass();
    }
    
    /**
     * Execute o teste
     * 
     * @return void
     */
    public function testRun(): void
    {
        $this->initNblFram();
        $obj = new IgrejasWS();
        
        // teste create
        $ret = $obj->create();
        $this->assertTrue($ret['success']);
        NblFram::$context->data->id = $ret['id'];
        
        // teste edit
        $ret = $obj->edit();
        $this->assertTrue($ret['success']);
        
        // teste busca por id
        $ret = $obj->me();
        $this->assertTrue($ret['success']);
        
        // teste busca todos
        $ret = $obj->all();
        $this->assertTrue($ret['success']);
        
        // teste remover por id
        $ret = $obj->remove();
        $this->assertTrue($ret['success']);
        
        // teste remover vários
        $ret = $obj->removeAll();
        $this->assertTrue($ret['success']);
    }
}

