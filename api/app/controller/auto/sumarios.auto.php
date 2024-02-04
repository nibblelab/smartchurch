<?php
require_once dirname(dirname(dirname(__FILE__))) . '/config/conf.cfg.php';
require_once HLP_PATH . '/Auto.class.php';
require_once WS_PATH . '/salasebd.ws.php'; 
require_once WS_PATH . '/atualizacoessumarios.ws.php'; 
require_once WS_PATH . '/freqsumarios.ws.php'; 
require_once WS_PATH . '/frequencias.ws.php'; 

class SumarioSync extends Auto
{
    public function __construct()
    {
        $this->logfile = RSC_PATH . '/sumarios.auto.log';
    }
    
    public function sync() {
        /* busque as salas que tem a flag de atualização marcada */
        $salas = SalasEbdWS::getSalasToUpdateSumario();
        if(empty($salas)) {
            return;
        }
        /* busque os registros de atualização */
        $registros = AtualizacoesSumariosWS::getBySalas($salas);
        /* atualize/gere os sumários */
        foreach($registros as $r) {
            // busque as frequências
            $frequencias = FrequenciasWS::getBySalaAndDia($r['sala'], $r['dia']);
            $presentes = 0; 
            $ausentes = 0; 
            $visitantes = 0; 
            $total = 0; 
            foreach($frequencias as $freq) {
                $total++;
                if($freq['presente']) {
                    $presentes++;
                }
                else {
                    $visitantes++;
                }
            }
            // busque um possível id de sumário
            $sumario = FreqSumariosWS::getBySalaAndDia($r['sala'], $r['dia']);
            if(empty($sumario)) {
                // crie o registro
                FreqSumariosWS::create($r['sala'], $r['dia'], $presentes, $ausentes, $visitantes, $total);
            }
            else {
                // edite o registro
                FreqSumariosWS::edit($sumario, $r['sala'], $r['dia'], $presentes, $ausentes, $visitantes, $total);
            }
            // remova o registro de atualização
            AtualizacoesSumariosWS::remove($r['id']);
            // atualize os controles de atualização de sumário da sala
            SalasEbdWS::updateAtualizarSumario($r['sala'], GenericHave::NO, date('Y-m-d H:i:s'));
        }
    }
}


$run = new SumarioSync();
$run->sync();