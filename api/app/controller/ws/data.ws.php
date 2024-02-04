<?php
require_once ADO_PATH . '/UFs.class.php'; 
require_once DAO_PATH . '/UFsDAO.class.php'; 
require_once ADO_PATH . '/Cidades.class.php'; 
require_once DAO_PATH . '/CidadesDAO.class.php'; 

/**
 * API REST de Dados
 */
class DataWS
{
    /**
     * Busque os ufs
     * 
     * @return array
     */
    private function getUFs(): array
    {
        $ufs = array();
        $obj = new UFsADO();
        $dto = new UFsDTO();
        
        UFsADO::addOrdering($dto, 'nome');
        if($obj->getAllbyParam($dto)) 
        {
            $obj->iterate();
            while($obj->hasNext())
            {
                $it = $obj->next();
                if(!is_null($it->id))
                {
                    $ufs[] = array(
                        'id' => $it->id,
                        'nome' => $it->nome,
                        'sigla' => $it->sigla
                    );
                }
            }
        }
        
        return $ufs;
    }
    
    
    /**
     * Busque as cidades
     * 
     * @return array
     */
    private function getCidades(): array
    {
        $cidades = array();
        $obj = new CidadesADO();
        $dto = new CidadesDTO();
        
        CidadesADO::addOrdering($dto, 'nome');
        if($obj->getAllbyParam($dto)) 
        {
            $obj->iterate();
            while($obj->hasNext())
            {
                $it = $obj->next();
                if(!is_null($it->id))
                {
                    $cidades[] = array(
                        'id' => $it->id,
                        'uf' => $it->uf,
                        'nome' => $it->nome
                    );
                }
            }
        }
        
        return $cidades;
    }
    
    /**
     * 
     * Busca 
     * 
     * @httpmethod GET
     * @auth yes
     * @return array
     */
    public function all(): array
    {
        $result = array(
            'status' => getStatusList(),
            'referencias' => getReferenceList(),
            'referencias_cargos' => getReferencesForCargos(),
            'sociedades' => getSociedades(),
            'escolaridade' => getEscolaridadeList(),
            'escolaridade_void' => getEscolaridadeListWithVoid(),
            'estado_civil' => getEstadoCivilList(),
            'estado_civil_void' => getEstadoCivilListWithVoid(),
            'sexo' => getSexoList(),
            'relacao_familiar' => getRelacaoFamiliarList(),
            'frequencia' => getFrequenciaList(),
            'pagamento_status' => getPagamentoStatusList(),
            'aprovacao_ata' => getAprovacaoAtaList(),
            'status_inscricao' => getStatusInscricaoList(),
            'profissao_fe' => getProfissaoFeList(),
            'registro_financeiro' => getRegistroFinanceiroList(),
            'tipo_oficiais' => getTipoOficiaisList(),
            'disponibilidade_oficiais' => getDisponibilidadeOficiaisList(),
            'responsaveis_virtuais' => getResponsaveisVirtuaisList(),
            'formas_pagto' => getFormasPagtoList(),
            'formulario_inscricao' => getFormularioInscricao(),
            'opcao_pagto' => getOpcoesPagtoStruct(),
            'lote_pagto' => getLotePagtoStruct(),
            'tipos_secretario' => getTiposSecretarioList(),
            'ufs' => $this->getUFs(),
            'cidades' => $this->getCidades()
        );
        
        return array('status' => 'ok', 'success' => true, 'datas' => $result);
    }

}

