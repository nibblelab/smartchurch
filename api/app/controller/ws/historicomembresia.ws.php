<?php
require_once ADO_PATH . '/HistoricoMembresia.class.php'; 
require_once DAO_PATH . '/HistoricoMembresiaDAO.class.php'; 

/**
 * API REST de HistoricoMembresia
 */
class HistoricoMembresiaWS extends WSUtil
{
    /**
     * 
     * @var \HistoricoMembresiaWS singleton instance
     */
    private static $_Instance = null;
    
    /**
     * Get singleton instance
     * 
     * @return \HistoricoMembresiaWS
     */
    public static function getInstance(): \HistoricoMembresiaWS {
        if(self::$_Instance == null) {
            self::$_Instance = new self();
        }
        return self::$_Instance;
    }

    /**
     * Gera o objeto de dados (DTO) de histórico de membresia
     * 
     * @param object $data objeto com os dados vindos da interface
     * @param int $mode flag para indicar se é adição, edição ou remoção
     * @param string $requester quem está requisitando a criação do objeto. [opcional] Default: usuário logado
     * @return \HistoricoMembresiaDTO objeto de dados
     */
    public static function generateDTO($data, $mode, $requester = ''): \HistoricoMembresiaDTO
    {
        $dto = new HistoricoMembresiaDTO();

        if($mode == DTOMode::ADD) {
            $dto->add = true;
            $dto->id = NblPHPUtil::makeNumericId();
            $dto->pessoa = (is_object($data->pessoa)) ? $data->pessoa->id : $data->pessoa;
            $dto->time_cad = date('Y-m-d H:i:s');
            $dto->last_mod = $dto->time_cad;
        }
        else if($mode == DTOMode::EDIT) {
            $dto->edit = true;
            $dto->id = $data->id;
            $dto->last_mod = date('Y-m-d H:i:s');
        }
        else if($mode == DTOMode::DELETE) {
            $dto->delete = true;
            $dto->id = $data->id;
            return $dto;
        }
                
        $dto->igreja = $data->igreja;
        $dto->pastor = $data->pastor;
        $dto->telefone_pastor = $data->telefone_pastor;
        $dto->email_pastor = $data->email_pastor;
        $dto->membro = ($data->membro) ? GenericHave::YES : GenericHave::NO;
        $dto->frequentou_de = (empty($data->frequentou_de)) ? NULL : NblPHPUtil::HumanDate2DBDate($data->frequentou_de);
        $dto->frequentou_ate = (empty($data->frequentou_ate)) ? NULL : NblPHPUtil::HumanDate2DBDate($data->frequentou_ate);
        $dto->comungante = ($data->comungante) ? GenericHave::YES : GenericHave::NO;
        $dto->batizado = ($data->batizado) ? GenericHave::YES : GenericHave::NO;
        $dto->data_batismo = (empty($data->data_batismo)) ? NULL : NblPHPUtil::HumanDate2DBDate($data->data_batismo);
        $dto->last_amod = (empty($requester)) ? NblFram::$context->token['data']['nome'] : $requester;

        return $dto;
    }

    /**
     * 
     * Cria
     * 
     * @httpmethod POST
     * @auth yes
     * @return array
     */
    public function create(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'MembroIgrejaSave,MeSave')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }

        
        $obj = new HistoricoMembresiaADO();

        if(NblFram::$context->data->multiple)
        {
            foreach(NblFram::$context->data->historicos as $historico) 
            {
                $dto = HistoricoMembresiaWS::generateDTO($historico, DTOMode::ADD);
                $obj->add($dto);
            }
        }
        else 
        {
            $dto = HistoricoMembresiaWS::generateDTO(NblFram::$context->data, DTOMode::ADD);
            $obj->add($dto);
        }

        if($obj->sync())
        {
            return array('status' => 'ok', 'success' => true, 'id' => $dto->id);
        }
        else
        {
            return array('status' => 'no', 'success' => false, 'msg' => 'Erro na operação', 'errs' => $obj->getErrs());
        }
    }

    /**
     * 
     * Edita
     * 
     * @httpmethod PUT
     * @auth yes
     * @require id
     * @return array
     */
    public function edit(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'MembroIgrejaSave,MeSave')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }

        $obj = new HistoricoMembresiaADO();

        if(NblFram::$context->data->multiple)
        {
            foreach(NblFram::$context->data->historicos as $historico) 
            {
                $dto = HistoricoMembresiaWS::generateDTO($historico, DTOMode::EDIT);
                $obj->add($dto);
            }
        }
        else 
        {
            $dto = HistoricoMembresiaWS::generateDTO(NblFram::$context->data, DTOMode::EDIT);
            $obj->add($dto);
        }

        if($obj->sync())
        {
            return array('status' => 'ok', 'success' => true, 'id' => $dto->id);
        }
        else
        {
            return array('status' => 'no', 'success' => false, 'msg' => 'Erro na operação', 'errs' => $obj->getErrs());
        }
    }

    /**
     * 
     * Busca 
     * 
     * @httpmethod GET
     * @auth yes
     * @require pessoa
     * @return array
     */
    public function allbypessoa(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'MembroIgreja,Dados')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }

        $dto = new HistoricoMembresiaDTO();
        $obj = new HistoricoMembresiaADO();
        $obj_count = new HistoricoMembresiaADO();

        HistoricoMembresiaADO::addComparison($dto, 'pessoa', CMP_EQUAL, NblFram::$context->data->pessoa);

        $obj_count->countBy($dto);
        $ok = $obj->getAllbyParam($dto);
        $result = array();
        if($ok)
        {
            $obj->iterate();
            while($obj->hasNext())
            {
                $it = $obj->next();
                if(!is_null($it->id))
                {
                    
                    $result[] = array(
                        'id' => $it->id,
                        'pessoa' => $it->pessoa,
                        'igreja' => $it->igreja,
                        'pastor' => $it->pastor,
                        'telefone_pastor' => $it->telefone_pastor,
                        'email_pastor' => $it->email_pastor,
                        'membro' => ($it->membro == GenericHave::YES),
                        'frequentou_de' => $it->frequentou_de,
                        'frequentou_ate' => $it->frequentou_ate,
                        'comungante' => ($it->comungante == GenericHave::YES),
                        'batizado' => ($it->batizado == GenericHave::YES),
                        'data_batismo' => $it->data_batismo,
                        'time_cad' => $it->time_cad,
                        'last_mod' => $it->last_mod,
                        'last_amod' => $it->last_amod
                    );
                }
            }

            return array('status' => 'ok', 'success' => true, 'datas' => $result, 'total' => $obj_count->count());
        }
        else
        {
            return array('status' => 'no', 'success' => false, 'msg' => 'Erro na operação', 'errs' => $obj->getErrs());
        }

    }
}