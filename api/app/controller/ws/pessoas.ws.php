<?php
require_once ADO_PATH . '/Usuarios.class.php'; 
require_once ADO_PATH . '/Pessoas.class.php'; 
require_once DAO_PATH . '/PessoasDAO.class.php'; 
require_once ADO_PATH . '/Doadores.class.php'; 
require_once DAO_PATH . '/DoadoresDAO.class.php'; 
require_once ADO_PATH . '/NecessidadesDasPessoas.class.php'; 
require_once DAO_PATH . '/NecessidadesDasPessoasDAO.class.php'; 
require_once WS_PATH . '/igrejas.ws.php'; 
require_once WS_PATH . '/sysconfigs.ws.php'; 
require_once WS_PATH . '/familias.ws.php'; 

/**
 * API REST de Pessoas
 */
class PessoasWS extends WSUtil
{
    /**
     * 
     * @var PessoasWS singleton instance
     */
    private static $_Instance = null;
    
    /**
     * Get singleton instance
     * 
     * @return \PessoasWS
     */
    public static function getInstance() {
        if(self::$_Instance == null) {
            self::$_Instance = new self();
        }
        return self::$_Instance;
    }
    
    /**
     * Verifica se um e-mail já está sendo usado no sistema
     * 
     * @param string $email email a ser testado
     * @param string $id id que receberá o e-mail. [Opcional, caso de edição]
     * @return bool
     */
    private function isEmailUsed($email, $id = ''): bool
    {
        $dto = new PessoasDTO();
        $obj = new PessoasADO();
        
        PessoasADO::addComparison($dto, 'email', CMP_EQUAL, $email);
        
        if(empty($id)) {
            /* caso de adição: não tem id. Basta contar quantos registros já usam o e-mail */
            $obj->countBy($dto);
        
            return ($obj->count() > 0);
        }
        else {
            /* caso de edição: tem id. 
             * Se há registro e o id é diferente do id que receberá o e-mail, então
             * está tentando usar um e-mail já usado
             *  */
            if($obj->getAllbyParam($dto))
            {
                $finded = false;
                $obj->iterate();
                while($obj->hasNext())
                {
                    $it = $obj->next();
                    if(!is_null($it->id))
                    {
                        if($it->id != $id) {
                            $finded = true;
                        }
                    }
                }
                
                return $finded;
            }
        }
        
        return false;
    }
    
    /**
     * Notifica a nova pessoa de seu cadastro no sistema
     * 
     * @param string $email e-mail da pessoa
     * @param string $nome nome da pessoa
     * @param string $login login da pessoa
     * @param string $senha senha da pessoa
     * @param string $igreja nome da igreja
     * @param string $autor nome do autor do cadastro
     * @return void
     */
    private function notifyNewPessoa($email, $nome, $login, $senha, $igreja, $autor): void
    {
        // carregue o template
        $msg_tpl = file_get_contents(TPL_PATH . '/nova_pessoa.html');
        $msg = str_replace('[USER_NOME]', htmlentities($nome), $msg_tpl);
        $msg = str_replace('[IGREJA_NOME]', htmlentities($igreja), $msg);
        $msg = str_replace('[AUTOR_CADASTRO]', htmlentities($autor), $msg);
        $msg = str_replace('[USER_EMAIL]', $login, $msg);
        $msg = str_replace('[USER_SENHA]', $senha, $msg);
        // envie o e-mail
        $this->sendMail('Cadastro realizado no Smartchurch', $msg, $email);
    }
    
    /**
     * Gerencie as doações da pessoa
     * 
     * @param array $doacoes_da_pessoa doações da pessoa
     * @param string $pessoa id da pessoa
     * @return void
     */
    private function manageDoacoes($doacoes_da_pessoa, $pessoa): void
    {
        $obj = new DoadoresADO();
        
        $doacoes = $this->getDoacoesbyPessoa($pessoa);
        foreach($doacoes_da_pessoa as $doacao)
        {
            if($doacao->checked && !in_array($doacao->id, $doacoes)) 
            {
                // se doação foi marcada e não estava antes nos registros da pessoa, crie
                $dto = new DoadoresDTO();
                $dto->add = true;
                $dto->pessoa = $pessoa;
                $dto->doacao = $doacao->id;
                $obj->add($dto);
            }
            else if(!$doacao->checked && in_array($doacao->id, $doacoes))
            {
                // se doação não está marcada e estava nos registros da pessoa, remova
                $dto = new DoadoresDTO();
                $dto->delete = true;
                $dto->pessoa = $pessoa;
                $dto->doacao = $doacao->id;
                $obj->add($dto);
            }
        }
        
        $obj->sync();
    }
    
    /**
     * Gerencia as necessidades especiais da pessoa
     * 
     * @param array $necessidades_da_pessoa necessidades da pessoa
     * @param string $pessoa id da pessoa
     * @return void
     */
    private function manageNecessidadesEspeciais($necessidades_da_pessoa, $pessoa): void
    {
        $obj = new NecessidadesDasPessoasADO();
        
        $necessidades = $this->getNecessidadesEspeciaisbyPessoa($pessoa);
        foreach($necessidades_da_pessoa as $necessidade)
        {
            if($necessidade->checked && !in_array($necessidade->id, $necessidades)) 
            {
                // se necessidade foi marcada e não estava antes nos registros da pessoa, crie
                $dto = new NecessidadesDasPessoasDTO();
                $dto->add = true;
                $dto->pessoa = $pessoa;
                $dto->necessidade = $necessidade->id;
                $obj->add($dto);
            }
            else if(!$necessidade->checked && in_array($necessidade->id, $necessidades))
            {
                // se necessidade não está marcada e estava nos registros da pessoa, remova
                $dto = new NecessidadesDasPessoasDTO();
                $dto->delete = true;
                $dto->pessoa = $pessoa;
                $dto->necessidade = $necessidade->id;
                $obj->add($dto);
            }
        }
        
        $obj->sync();
    }
    
    private function manageConjuge($conjuge, $pessoa_id, $pessoa_sexo): void
    {
        $errs = [];  
        
        if($conjuge->remove && !empty($conjuge->id)) {
            FamiliasWS::desassociate($conjuge->id, $errs);
            return;
        }
        
        $tipo = ($pessoa_sexo == Sexo::MASCULINO) ? RelacaoFamiliar::ESPOSA : RelacaoFamiliar::ESPOSO;
        
        if($conjuge->cadastrado) {
            // conjuge existe na plataforma
            $id_conjuge = $conjuge->id_conjuge;
        }
        else {
            // conjuge não existe na plataforma. É externo
            $id_conjuge = NULL;            
        }
                
        if(empty($conjuge->id)) {
            // crie
            $id = "";               
            FamiliasWS::associate($id_conjuge, $pessoa_id, $tipo, $conjuge->nome, $id, $errs);
        }
        else {
            // atualize
            FamiliasWS::updateAssociation($conjuge->id, $id_conjuge, $tipo, $conjuge->nome, $errs);
        }
    }
    
    private function manageFilhos($filhos, $pessoa_id, $pessoa_nome): void
    {
        $errs = [];
        $associacao_id = "";
        
        foreach($filhos as $filho) {
            if($filho->remove && $filho->cadastrado && !empty($filho->associacao_id)) {
                // remover
                FamiliasWS::desassociate($filho->associacao_id, $errs);
            }
            else {
                if(empty($filho->nome) || empty($filho->data_nascimento)) {
                    continue;
                }
                
                $tipo = ($filho->sexo == Sexo::MASCULINO) ? RelacaoFamiliar::FILHO : RelacaoFamiliar::FILHA;
                
                $data_filho = new DateTime(NblPHPUtil::HumanDate2DBDate($filho->data_nascimento));
                $now = new DateTime();
                $interval = $now->diff($data_filho);
                $is_crianca = ($interval->y <= LimitesDeIdades::CRIANCA);
                                
                if(!$filho->cadastrado) {
                    // filho não existe no sistema. Crie um usuário externo pra ele
                    if(PessoasWS::createExternal($filho->nome, $filho->sexo, $filho->data_nascimento, $is_crianca, $pessoa_id, $pessoa_nome, $id, $errs)) {
                        FamiliasWS::associate($id, $pessoa_id, $tipo, '', $associacao_id, $errs);
                    }
                }
                else {
                    // filho cadastrado no sistema. Se criança, permita a edição dos dados, e atualize a associação
                    if($is_crianca) {
                        PessoasWS::editExternal($filho->id, $filho->nome, $filho->sexo, $filho->data_nascimento, $pessoa_nome, $errs);
                    }
                    
                    if(empty($filho->associacao_id)) {
                        FamiliasWS::associate($filho->id, $pessoa_id, $tipo, '', $associacao_id, $errs);
                    }
                    else {
                        FamiliasWS::updateAssociation($filho->associacao_id, $filho->id, $tipo, '', $errs);
                    }
                    
                }
            }
        }
    }
    
    /**
     * Busque as doações da pessoa
     * 
     * @param string $pessoa id da pessoa
     * @return array
     */
    public static function getDoacoesbyPessoa($pessoa): array
    {
        $dto = new DoadoresDTO();
        $obj = new DoadoresADO();
        
        DoadoresADO::addComparison($dto, 'pessoa', CMP_EQUAL, $pessoa);
        
        $result = array();
        if($obj->getAllbyParam($dto))
        {
            $obj->iterate();
            while($obj->hasNext())
            {
                $it = $obj->next();
                if(!is_null($it->doacao))
                {
                    $result[] = $it->doacao;
                }
            }
        }
        
        return $result;
    }
    
    /**
     * Mapeie as doações por pessoa
     * 
     * @return array
     */
    public static function mapDoacoesbyPessoa(): array
    {
        $dto = new DoadoresDTO();
        $obj = new DoadoresADO();
        
        $result = array();
        if($obj->getAllbyParam($dto))
        {
            $obj->iterate();
            while($obj->hasNext())
            {
                $it = $obj->next();
                if(!is_null($it->pessoa))
                {
                    if(!isset($result[$it->pessoa])) {
                        $result[$it->pessoa] = array();
                    }
                    $result[$it->pessoa][] = $it->doacao;
                }
            }
        }
        
        return $result;
    }
    
    /**
     * Busque as necessidades especiais da pessoa
     * 
     * @param string $pessoa id da pessoa
     * @return array
     */
    public static function getNecessidadesEspeciaisbyPessoa($pessoa): array
    {
        $dto = new NecessidadesDasPessoasDTO();
        $obj = new NecessidadesDasPessoasADO();
        
        NecessidadesDasPessoasADO::addComparison($dto, 'pessoa', CMP_EQUAL, $pessoa);
        
        $result = array();
        if($obj->getAllbyParam($dto))
        {
            $obj->iterate();
            while($obj->hasNext())
            {
                $it = $obj->next();
                if(!is_null($it->necessidade))
                {
                    $result[] = $it->necessidade;
                }
            }
        }
        
        return $result;
    }
    
    /**
     * Mapeie as necessidades especiais por pessoa
     * 
     * @return array
     */
    public static function mapNecessidadesEspeciaisbyPessoa(): array
    {
        $dto = new NecessidadesDasPessoasDTO();
        $obj = new NecessidadesDasPessoasADO();
        
        $result = array();
        if($obj->getAllbyParam($dto))
        {
            $obj->iterate();
            while($obj->hasNext())
            {
                $it = $obj->next();
                if(!is_null($it->pessoa))
                {
                    if(!isset($result[$it->pessoa])) {
                        $result[$it->pessoa] = array();
                    }
                    $result[$it->pessoa][] = $it->necessidade;
                }
            }
        }
        
        return $result;
    }
    
    /**
     * Obtêm a pessoa pelo seu id
     * 
     * @param string $id id da pessoa
     * @return object|null
     */
    public static function getById($id): ?object
    {
        $dto = new PessoasDTO();
        $obj = new PessoasADO();

        $dto->id = $id;
        return $obj->get($dto);
    }
    
    /**
     * Mapeie nome, email, sexo, data de nascimento, estado_civil, escolaridade, 
     * se tem ou não filhos, telefone e celular (1 e 2) das pessoas pelo id
     * 
     * @return array
     */
    public static function mapBasicDataById(): array
    {
        $obj = new PessoasADO();
        
        return $obj->mapBasicDataById();
    }
    
    /** 
     * Mapeie nome, email, sexo, data de nascimento, estado_civil, escolaridade, 
     * se tem ou não filhos, telefone e celular (1 e 2) das pessoas pelo id conforme o parâmetro passado
     * 
     * @param string $param parâmetro de busca textual
     * @return array
     */
    public static function mapBasicDataByIdWithParam($param): array
    {
        $dto = new PessoasDTO();
        $obj = new PessoasADO();
        
        PessoasADO::addComparison($dto, 'nome', CMP_INCLUDE_INSIDE, $param, OP_OR);
        PessoasADO::addComparison($dto, 'email', CMP_INCLUDE_INSIDE, $param, OP_OR);
        PessoasADO::addComparison($dto, 'telefone', CMP_INCLUDE_INSIDE, $param, OP_OR);
        PessoasADO::addComparison($dto, 'celular_1', CMP_INCLUDE_INSIDE, $param, OP_OR);
        PessoasADO::addComparison($dto, 'celular_2', CMP_INCLUDE_INSIDE, $param, OP_OR);
            
        return $obj->mapBasicDataByIdWithParam($dto);
    }
    
    /**
     * Mapeie nome, email, sexo, data de nascimento, estado_civil, escolaridade, 
     * se tem ou não filhos, telefone e celular (1 e 2) das pessoas pelo id 
     * conforme os filtros opcionais passados
     * 
     * @param string $param parâmetro de busca textual
     * @param string $sexo busca por sexo [opcional]
     * @param string $estado_civil busca por estado civil [opcional]
     * @param string $escolaridade busca por escolaridade [opcional]
     * @param bool $com_filhos apenas quem tem filhos (exclusivo com $sem_filhos) [opcional]
     * @param bool $sem_filhos apenas quem não tem filhos (exclusivo com $com_filhos) [opcional]
     * @param bool $aniversariantes apenas aniversariantes [opcional]
     * @param array $faixas array com as faixas etárias [opcional]
     * @return array
     */
    public static function mapBasicDataByIdWithFilters($param, $sexo = '', $estado_civil = '', 
                                                        $escolaridade = '', $com_filhos = false, 
                                                        $sem_filhos = false, $aniversariantes = false, 
                                                        $faixas = []): array
    {
        $dto = new PessoasDTO();
        $obj = new PessoasADO();
        
        if(!empty($param)) 
        {
            PessoasADO::addComparison($dto, 'nome', CMP_INCLUDE_INSIDE, $param, OP_OR);
            PessoasADO::addComparison($dto, 'email', CMP_INCLUDE_INSIDE, $param, OP_OR);
            PessoasADO::addComparison($dto, 'telefone', CMP_INCLUDE_INSIDE, $param, OP_OR);
            PessoasADO::addComparison($dto, 'celular_1', CMP_INCLUDE_INSIDE, $param, OP_OR);
            PessoasADO::addComparison($dto, 'celular_2', CMP_INCLUDE_INSIDE, $param, OP_OR);
        }
        
        if(!empty($sexo)) 
        {
            PessoasADO::addComparison($dto, 'sexo', CMP_EQUAL, $sexo);
        }
        
        if(!empty($estado_civil)) 
        {
            PessoasADO::addComparison($dto, 'estado_civil', CMP_EQUAL, $estado_civil);
        }
        
        if(!empty($escolaridade)) 
        {
            PessoasADO::addComparison($dto, 'escolaridade', CMP_EQUAL, $escolaridade);
        }
        
        if($com_filhos) 
        {
            PessoasADO::addComparison($dto, 'tem_filhos', CMP_EQUAL, GenericHave::YES);
        }
        else if($sem_filhos) 
        {
            PessoasADO::addComparison($dto, 'tem_filhos', CMP_EQUAL, GenericHave::NO);
        }
        
        if($aniversariantes)
        {   
            PessoasADO::addComparison($dto, 'data_nascimento', CMP_EQUAL_MONTH, date('m'));
        }
        
        if(!empty($faixas)) 
        {
            PessoasADO::addComparison($dto, 'faixas_idade', CMP_EQUAL, $faixas);
        }
           
        return $obj->mapBasicDataByIdWithParam($dto);
    }
    
    /**
     * Busca um possível usuário já previamente existente para o email
     * 
     * @param string $email email do usuário
     * @return \PessoasDTO|null
     */
    public static function getUserbyEmail($email):?\PessoasDTO
    {
        $dto = new PessoasDTO();
        $obj = new PessoasADO();
        
        PessoasADO::addComparison($dto, 'email', CMP_EQUAL, $email);
        if($obj->getAllbyParam($dto))
        {
            $obj->iterate();
            while($obj->hasNext())
            {
                $it = $obj->next();
                if(!is_null($it->id))
                {
                    return $it;
                }
            }
        }
        
        return null;
    }
        
    /**
     * Verifica se o um usuário possui poderes de admin
     * 
     * @param string $email email do usuário
     * @return bool
     */
    public static function isAdminByEmail($email): bool
    {
        $dto = new PessoasDTO();
        $obj = new PessoasADO();
        
        PessoasADO::addComparison($dto, 'email', CMP_EQUAL, $email);
        PessoasADO::addComparison($dto, 'is_master', CMP_EQUAL, UserLevel::MASTER);
        if($obj->getAllbyParam($dto))
        {
            $obj->iterate();
            while($obj->hasNext())
            {
                $it = $obj->next();
                if(!is_null($it->id))
                {
                    return ($it->is_master == UserLevel::MASTER);
                }
            }
        }
        
        return false;        
    }
    
    /**
     * Busca todos os ids usuários com poder de admin
     * 
     * @return array
     */
    public static function getAllAdminIds(): array
    {
        $dto = new PessoasDTO();
        $obj = new PessoasADO();
        
        $result = [];
        PessoasADO::addComparison($dto, 'is_master', CMP_EQUAL, UserLevel::MASTER);
        if($obj->getAllbyParam($dto))
        {
            $obj->iterate();
            while($obj->hasNext())
            {
                $it = $obj->next();
                if(!is_null($it->id))
                {
                    $result[] = $it->id;
                }
            }
        }
        return $result;
    }
    
    /**
     * Busca um possível usuário já previamente existente para o código de reset
     * 
     * @param string $reset_code código de requisição
     * @return \PessoasDTO|null
     */
    public static function getUserbyResetCode($reset_code):?\PessoasDTO
    {
        $dto = new PessoasDTO();
        $obj = new PessoasADO();
        
        PessoasADO::addComparison($dto, 'reset_code', CMP_EQUAL, $reset_code);
        if($obj->getAllbyParam($dto))
        {
            $obj->iterate();
            while($obj->hasNext())
            {
                $it = $obj->next();
                if(!is_null($it->id))
                {
                    return $it;
                }
            }
        }
        
        return null;
    }
    
    /**
     * Valida um e-mail
     * 
     * @param string $email email a ser validado
     * @return bool
     */
    public static function validateEmail($email): bool
    {
        return (preg_match('/^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/iD', $email) === 1);
    }
    
    /**
     * Valida uma senha
     * 
     * @param string $password senha a ser validada
     * @return bool
     */
    public static function validatePasswd($password): bool
    {
        return (preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$@$!%*?&])[A-Za-z\d$@$!%*?&]{6,}/', $password) === 1);
    }
        
    /**
     * Gera o objeto de dados (DTO) de pessoa
     * 
     * @param object $data objeto com os dados
     * @param int $mode flag para indicar se é adição, edição ou remoção
     * @param string $requester quem está requisitando a criação do objeto. [opcional] Default: usuário logado
     * @return \PessoasDTO
     */
    public static function generateDTO($data, $mode, $requester = ''): \PessoasDTO
    {
        $dto = new PessoasDTO();
        $instance = self::getInstance();

        if($mode == DTOMode::ADD) {
            $dto->add = true;
            $dto->id = NblPHPUtil::makeNumericId();
            
            $dto->crianca = (!$instance->testInputBool('crianca', $data)) ? GenericHave::NO : GenericHave::YES;
            $dto->tem_filhos = (!$instance->testInputBool('tem_filhos', $data)) ? GenericHave::NO : GenericHave::YES;
            
            $dto->perfil = $data->perfil;
            $dto->senha = sha1($data->senha);
            $dto->is_android = MobileOS::DESCONHECIDO;
            $dto->avatar = '';
            $dto->fcm_token = '';
            $dto->reset_code = '';
            $dto->tp = (!$instance->testInputString('tp', $data)) ? UserTypes::USER : $data->tp;
            $dto->is_master = (!$instance->testInputString('is_master', $data)) ? UserLevel::COMMON : $data->is_master;
            $dto->stat = Status::ACTIVE;
            
            $dto->time_cad = date('Y-m-d H:i:s');
            $dto->last_mod = $dto->time_cad;
            $dto->last_sync = NULL;
        }
        else if($mode == DTOMode::EDIT) {
            $dto->edit = true;
            $dto->id = $data->id;            
            $dto->perfil = (!$instance->testInputString('perfil', $data)) ? VOID : $data->perfil;
            $dto->senha =  (!$instance->testInputString('senha', $data)) ? VOID : sha1($data->senha); 
            
            if(property_exists($data, 'stat')) {
                $dto->stat = (!$instance->testInputString('stat', $data)) ? Status::ACTIVE : $data->stat;
            }
            if(property_exists($data, 'crianca')) {
                $dto->crianca = (!$instance->testInputBool('crianca', $data)) ? GenericHave::NO : GenericHave::YES;
            }
            if(property_exists($data, 'tem_filhos')) {
                $dto->tem_filhos = (!$instance->testInputBool('tem_filhos', $data)) ? GenericHave::NO : GenericHave::YES;
            }
            
            $dto->last_mod = date('Y-m-d H:i:s');
        }
        else if($mode == DTOMode::DELETE) {
            $dto->delete = true;
            $dto->id = $data->id;
            return $dto;
        }        
        
        $dto->nome = $data->nome;
        $dto->email = (!$instance->testInputString('email', $data)) ? VOID : $data->email;
        $dto->profissao = (!$instance->testInputString('profissao', $data)) ? NULL : $data->profissao;
        $dto->sexo = (!$instance->testInputString('sexo', $data)) ? Sexo::VOID : $data->sexo;
        $dto->data_nascimento = (!$instance->testInputString('data_nascimento', $data)) ? NULL : NblPHPUtil::HumanDate2DBDate($data->data_nascimento);
        $dto->responsavel = (!$instance->testInputString('responsavel', $data)) ? NULL : $data->responsavel;
        $dto->estado_civil = (!$instance->testInputString('estado_civil', $data)) ? EstadoCivil::VOID : $data->estado_civil;
        $dto->escolaridade = (!$instance->testInputString('escolaridade', $data)) ? Escolaridade::VOID : $data->escolaridade;
        $dto->telefone = (!$instance->testInputString('telefone', $data)) ? '' : $data->telefone;
        $dto->celular_1 = (!$instance->testInputString('celular_1', $data)) ? '' : $data->celular_1;
        $dto->celular_2 = (!$instance->testInputString('celular_2', $data)) ? '' : $data->celular_2;
        $dto->pai = (!$instance->testInputString('pai', $data)) ? '' : $data->pai;
        $dto->mae = (!$instance->testInputString('mae', $data)) ? '' : $data->mae;
        $dto->naturalidade = (!$instance->testInputString('naturalidade', $data)) ? '' : $data->naturalidade;
        $dto->nacionalidade = (!$instance->testInputString('nacionalidade', $data)) ? '' : $data->nacionalidade;
        $dto->endereco = (!$instance->testInputString('endereco', $data)) ? '' : $data->endereco;
        $dto->numero = (!$instance->testInputString('numero', $data)) ? '' : $data->numero;
        $dto->complemento = (!$instance->testInputString('complemento', $data)) ? '' : $data->complemento;
        $dto->bairro = (!$instance->testInputString('bairro', $data)) ? '' : $data->bairro;
        $dto->cidade = (!$instance->testInputString('cidade', $data)) ? '' : $data->cidade;
        $dto->uf = (!$instance->testInputString('uf', $data)) ? Voids::UF : $data->uf;
        $dto->cep = (!$instance->testInputString('cep', $data)) ? '' : $data->cep;
        $dto->site = (!$instance->testInputString('site', $data)) ? '' : $data->site;
        $dto->facebook = (!$instance->testInputString('facebook', $data)) ? '' : $data->facebook;
        $dto->instagram = (!$instance->testInputString('instagram', $data)) ? '' : $data->instagram;
        $dto->youtube = (!$instance->testInputString('youtube', $data)) ? '' : $data->youtube;
        $dto->vimeo = (!$instance->testInputString('vimeo', $data)) ? '' : $data->vimeo;
        $dto->avatar = (!$instance->testInputString('avatar', $data)) ? '' : $data->avatar;
        
        $dto->last_amod = (empty($requester)) ? NblFram::$context->token['data']['nome'] : $requester;
        return $dto;
    }
    
    public static function createExternal($nome, $sexo, $data_nascimento, $crianca, $responsavel, $author, &$id, &$errs): bool
    {
        $obj = new PessoasADO();
        
        $data = new stdClass();
        $data->perfil = SysconfigsWS::getPerfilMembro();
        $data->nome = $nome;
        $data->email = NblPHPUtil::makeRandomAlphaNumericCode(15) . "@smartchurch.software"; // email aleatório
        $data->senha = rand(); // senha aleatória
        $data->sexo = $sexo;
        $data->data_nascimento = $data_nascimento;
        $data->responsavel = $responsavel;
        $data->crianca = $crianca;
        
        $dto = PessoasWS::generateDTO($data, DTOMode::ADD, $author);
        
        $obj->add($dto);
        if($obj->sync())
        {
            $id = $dto->id;
            return true;
        }
        else
        {
            $id = '';
            $errs = $obj->getErrs();
            return false;
        }
    }
    
    public static function editExternal($id, $nome, $sexo, $data_nascimento, $author, &$errs): bool
    {
        $obj = new PessoasADO();
        
        $data = new stdClass();
        $data->id = $id;
        $data->nome = $nome;
        $data->sexo = $sexo;
        $data->data_nascimento = $data_nascimento;
        
        $dto = PessoasWS::generateDTO($data, DTOMode::EDIT, $author);
        
        $obj->add($dto);
        if($obj->sync())
        {
            return true;
        }
        else
        {
            $errs = $obj->getErrs();
            return false;
        }
    }
    
    /**
     * Cria uma pessoa com dados mínimos
     * 
     * @param string $perfil perfil da pessoa
     * @param string $nome nome
     * @param string $email e-mail
     * @param string $senha senha
     * @param \UserTypes $tp tipo
     * @param \UserLevel $master é ou não mestre
     * @param string $author usuário que está realizando o cadastro
     * @param string $id id da pessoa criada [referência]
     * @param array $errs erros, se ocorrerem [referência]
     * @return bool
     */
    public static function createMinimal($perfil, $nome, $email, $senha, $tp, $master, $author, &$id, &$errs): bool
    {
        
        $obj = new PessoasADO();
        
        $data = new stdClass();
        $data->perfil = $perfil;
        $data->nome = $nome;
        $data->email = $email;
        $data->senha = $senha;
        $data->tp = $tp;
        $data->is_master = $master;
        
        $dto = PessoasWS::generateDTO($data, DTOMode::ADD, $author);
                
        $obj->add($dto);
        if($obj->sync())
        {
            $id = $dto->id;
            return true;
        }
        else
        {
            $id = '';
            $errs = $obj->getErrs();
            return false;
        }
    }
    
    /**
     * Edita os dados mínimos de uma pessoa
     * 
     * @param string $id id da pessoa
     * @param string $perfil perfil da pessoa
     * @param string $nome nome
     * @param string $senha senha
     * @param \UserTypes $tp tipo
     * @param \UserLevel $master é ou não mestre
     * @param string $author usuário que está realizando a edição
     * @param array $errs erros, se ocorrerem
     * @return bool
     */
    public static function editMininal($id, $perfil, $nome, $senha, $tp, $master, $author, &$errs): bool
    {
        $obj = new PessoasADO();
                
        $data = new stdClass();
        $data->id = $id;
        $data->perfil = $perfil;
        $data->nome = $nome;
        $data->senha = $senha;
        $data->tp = $tp;
        $data->is_master = $master;
        $data->edit_stat = true;
        $data->stat = Status::ACTIVE;
        
        $dto = PessoasWS::generateDTO($data, DTOMode::EDIT, $author);
        
        $obj->add($dto);
        if($obj->sync())
        {
            return true;
        }
        else
        {
            $errs = $obj->getErrs();
            return false;
        }
    }
    
    /**
     * Cria uma pessoa para inscrição em evento
     * 
     * @param string $nome nome
     * @param string $email email
     * @param string $sexo sexo 
     * @param string $data_nascimento data de nascimento no padrão DD/MM/YYYY [opcional]
     * @param string $estado_civil estado civil [opcional]
     * @param string $telefone telefone [opcional]
     * @param string $celular_1 celular (1) [opcional]
     * @param string $celular_2 celular (2) [opcional]
     * @param string $id id da pessoa criada [referência]
     * @param array $errs erros, se ocorrerem [referência]
     * @return bool
     */
    public static function createForInscricao($nome, $email, $sexo, $data_nascimento, $estado_civil, 
                                                $telefone, $celular_1, $celular_2, &$id, &$errs): bool
    {
        $obj = new PessoasADO();
        
        $data = new stdClass();
        $data->perfil = SysconfigsWS::getPerfilMembro();
        $data->nome = $nome;
        $data->email = $email;
        $data->senha = rand();
        $data->sexo = $sexo;
        $data->data_nascimento = $data_nascimento;
        $data->estado_civil = $estado_civil;
        $data->telefone = $telefone;
        $data->celular_1 = $celular_1;
        $data->celular_2 = $celular_2;
        
        $dto = PessoasWS::generateDTO($data, DTOMode::ADD, $nome);
        
        $obj->add($dto);
        if($obj->sync())
        {
            $id = $dto->id;
            return true;
        }
        else
        {
            $id = '';
            $errs = $obj->getErrs();
            return false;
        }
    }
    
    /**
     * Atualize os dados de uma pessoa com base nos fornecidos em uma inscrição
     * 
     * @param string $id id da pessoa
     * @param string $nome nome
     * @param string $email email
     * @param string $sexo sexo 
     * @param string $data_nascimento data de nascimento no padrão DD/MM/YYYY [opcional]
     * @param string $estado_civil estado civil [opcional]
     * @param string $telefone telefone [opcional]
     * @param string $celular_1 celular (1) [opcional]
     * @param string $celular_2 celular (2) [opcional]
     * @param array $errs erros, se ocorrerem [referência]
     * @return bool
     */
    public static function updatePessoaFromInscricao($id, $nome, $email, $sexo, $data_nascimento, $estado_civil, 
                                                $telefone, $celular_1, $celular_2, &$errs): bool
    {
        $obj = new PessoasADO();
        
        $data = new stdClass();
        $data->id = $id;
        $data->nome = $nome;
        $data->email = $email;
        $data->sexo = $sexo;
        $data->data_nascimento = $data_nascimento;
        $data->estado_civil = $estado_civil;
        $data->telefone = $telefone;
        $data->celular_1 = $celular_1;
        $data->celular_2 = $celular_2;
        
        $dto = PessoasWS::generateDTO($data, DTOMode::EDIT, $nome);
        
        $obj->add($dto);
        if($obj->sync())
        {
            return true;
        }
        else
        {
            $errs = $obj->getErrs();
            return false;
        }
    }
    
    /**
     * Atualize os dados da pessoa com base nos dados fornecidos em credencial
     * 
     * @param string $id id da pessoa
     * @param string $nome nome
     * @param string $email email
     * @param string $celular celular
     * @param string $requester requester
     * @param array $errs erros, se ocorrerem [referência]
     * @return bool
     */
    public static function updatePessoaFromCredencial($id, $nome, $email, $celular, $requester, &$errs): bool
    {
        $obj = new PessoasADO();
        
        $data = new stdClass();
        $data->id = $id;
        $data->nome = $nome;
        $data->email = $email;
        $data->celular_1 = $celular;
        
        $dto = PessoasWS::generateDTO($data, DTOMode::EDIT, 'Smartchurch: atualizado via credencial por '. substr($requester, 0, 100));       
        
        $obj->add($dto);
        if($obj->sync())
        {
            return true;
        }
        else
        {
            $errs = $obj->getErrs();
            return false;
        }
    }
    
    /**
     * Requisite o código de autenticação de mudança de senha
     * 
     * @param string $id id da pessoa
     * @param string $author usuário que está realizando a edição
     * @param array $errs erros, se ocorrerem
     * @return string código de autenticação de mundança de senha
     */
    public static function requestPwdChangeAuthCode($id, $author, &$errs): string
    {
        $dto = new PessoasDTO();
        $obj = new PessoasADO();
        
        $dto->edit = true;
        $dto->id = $id;
        $dto->reset_code = NblPHPUtil::makeRandomHexCode(7);
        $dto->last_mod = date('Y-m-d H:i:s');
        $dto->last_amod = $author;
        
        $obj->add($dto);
        if($obj->sync())
        {
            return $dto->reset_code;
        }
        else
        {
            $errs = $obj->getErrs();
            return '';
        }
    }
    
    /**
     * Mude a senha
     * 
     * @param string $id id da pessoa
     * @param string $senha nova senha
     * @param array $errs erros, se ocorrerem
     * @return bool
     */
    public static function resetPwd($id, $senha, &$errs): bool
    {
        $dto = new PessoasDTO();
        $obj = new PessoasADO();
        
        $dto->edit = true;
        $dto->id = $id;
        $dto->senha = sha1($senha);
        $dto->reset_code = '';
        $dto->last_mod = date('Y-m-d H:i:s');
        
        $obj->add($dto);
        if($obj->sync())
        {
            return true;
        }
        else
        {
            $errs = $obj->getErrs();
            return false;
        }
    }
    
    /**
     * Remova a pessoa pelo seu id
     * 
     * @param string $id id da pessoa
     * @param array $errs erros, se ocorrerem
     * @return bool
     */
    public static function deleteById($id, &$errs): bool
    {
        $dto = new PessoasDTO();
        $obj = new PessoasADO();
        
        $dto->delete = true;
        $dto->id = $id;
        
        $obj->add($dto);
        if($obj->sync())
        {
            return true;
        }
        else
        {
            $errs = $obj->getErrs();
            return false;
        }
    }
    
    /**
     * Busca as pessoas pelos seus ids
     * 
     * @param array $ids array com os ids
     * @return array
     */
    public static function getAllByIds($ids): array
    {
        $dto = new PessoasDTO();
        $obj = new PessoasADO();
        
        $ids_list = parent::stringifyArray($ids);
        
        if(empty($ids_list)) {
            return array();
        }
        
        PessoasADO::addComparison($dto, 'p.id', CMP_IN_LIST, $ids_list);
        
        return $obj->mapAllByWithParam('id', $dto);
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
        if(!doIHavePermission(NblFram::$context->token, 'PessoasSave')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        if($this->isEmailUsed(NblFram::$context->data->email)) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Esse e-mail já está em uso');
        }
        
        $obj = new PessoasADO();
        
        $dto = PessoasWS::generateDTO(NblFram::$context->data, DTOMode::ADD);
        
        $obj->add($dto);
        if($obj->sync())
        {
            // gerencie as doações e necessidades especiais
            $this->manageDoacoes(NblFram::$context->data->doacoes, $dto->id);
            $this->manageNecessidadesEspeciais(NblFram::$context->data->necessidades_especiais, $dto->id);
            
            // notifique a pessoa do novo usuário criado, caso o e-mail seja válido
            global $ignore_domains;
            if(validMailDestination($dto->email, $ignore_domains)) {
                $igreja = IgrejasWS::getById(NblFram::$context->data->igreja);
                if(!is_null($igreja)) {
                    // envie o e-mail de notificação
                    $this->notifyNewPessoa($dto->email, 
                                                $dto->nome, 
                                                $dto->email,
                                                NblFram::$context->data->senha,
                                                $igreja->nome,
                                                NblFram::$context->token['data']['nome']);
                }

            }
            
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
        if(!doIHavePermission(NblFram::$context->token, 'PessoasSave,MeSave')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        if($this->isEmailUsed(NblFram::$context->data->email, NblFram::$context->data->id)) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Esse e-mail já está em uso');
        }
        
        $obj = new PessoasADO();
        
        $dto = PessoasWS::generateDTO(NblFram::$context->data, DTOMode::EDIT);
        
        $obj->add($dto);
        if($obj->sync())
        {
            // gerencie as doações e necessidades especiais
            $this->manageDoacoes(NblFram::$context->data->doacoes, $dto->id);
            $this->manageNecessidadesEspeciais(NblFram::$context->data->necessidades_especiais, $dto->id);            
            if($dto->estado_civil == EstadoCivil::CASADO || 
                        (property_exists(NblFram::$context->data->conjuge, 'remove') && NblFram::$context->data->conjuge->remove)) {
                // gerencie informação de conjuge
                $this->manageConjuge(NblFram::$context->data->conjuge, $dto->id, $dto->sexo);
            }
            if($dto->tem_filhos == GenericHave::YES) {
                // gerencie informação de filhos
                $this->manageFilhos(NblFram::$context->data->filhos, $dto->id, $dto->nome);
            }
                        
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
     * @require id
     * @return array
     */
    public function me(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'Pessoas,Me')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new PessoasDTO();
        $obj = new PessoasADO();
        
        $dto->id = NblFram::$context->data->id;
        if(!is_null($obj->get($dto)))
        {
            $d = $obj->getDTODataObject();
            $d->senha = '';
            $d->tem_filhos = ($d->tem_filhos == GenericHave::YES);
            $d->sexo = ($d->sexo == Sexo::VOID) ? '' : $d->sexo;
            $d->estado_civil = ($d->estado_civil ==  EstadoCivil::VOID) ? '' : $d->estado_civil;
            $d->tem_filhos = ($d->tem_filhos == GenericHave::YES);
            $d->escolaridade = ($d->escolaridade == Escolaridade::VOID) ? '' : $d->escolaridade;
            $d->doacoes = $this->getDoacoesbyPessoa(NblFram::$context->data->id);
            $d->necessidades_especiais = $this->getNecessidadesEspeciaisbyPessoa(NblFram::$context->data->id);
            $d->crianca = ($d->crianca == GenericHave::YES);
            
            if($d->estado_civil == EstadoCivil::CASADO) {
                $d->conjuge = FamiliasWS::getConjugeByPessoa($d->id);                
            }
            
            if($d->tem_filhos) {
                $filhos = FamiliasWS::getFilhosByPessoa($d->id);
                $d->filhos = [];
                foreach($filhos as $filho) {
                    $f_data = PessoasWS::getById($filho['parente']);
                    if(!is_null($f_data)) {
                        $f = new stdClass();
                        $f->id = $filho['parente'];
                        $f->associacao_id = $filho['id'];
                        $f->nome = $f_data->nome;
                        $f->data_nascimento = $f_data->data_nascimento;
                        $f->sexo = $f_data->sexo;
                        $f->cadastrado = true;
                        $f->crianca = ($f_data->crianca == GenericHave::YES);
                        $d->filhos[] = $f;
                    }
                    
                }
            }
            
            return array('status' => 'ok', 'success' => true, 'datas' => $d);
        }
        else 
        {
            return array('status' => 'no', 'success' => false, 'msg' => 'Erro na operação', 'errs' => $obj->getErrs());
        }
    }

    /**
     * 
     * Relatório: percentagem de perfil preenchido 
     * 
     * @httpmethod GET
     * @auth yes
     * @require id
     * @return array
     */
    public function relPerfilPreenchido(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'Pessoas,Me,RelPerfilPreenchido')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new PessoasDTO();
        $obj = new PessoasADO();
        
        $dto->id = NblFram::$context->data->id;
        if(!is_null($obj->get($dto)))
        {
            $d = $obj->getDTODataObject();
            
            $preenchidos = 0;
            $total = 0;
            if(!empty($d->nome)) { $preenchidos++; } $total++;
            if(!empty($d->email)) { $preenchidos++; } $total++;
            if(!empty($d->sexo)) { $preenchidos++; } $total++;
            if(!empty($d->endereco)) { $preenchidos++; } $total++;
            if(!empty($d->bairro)) { $preenchidos++; } $total++;
            if(!empty($d->uf) && ($d->uf != Voids::UF)) { $preenchidos++; } $total++;
            if(!empty($d->data_nascimento)) { $preenchidos++; } $total++;
            if(!empty($d->estado_civil) && ($d->estado_civil != EstadoCivil::VOID)) { $preenchidos++; } $total++;
            if(!empty($d->escolaridade) && ($d->escolaridade != Escolaridade::VOID)) { $preenchidos++; } $total++;
            if(!empty($d->celular_1)) { $preenchidos++; } $total++;
            
            return array('status' => 'ok', 'success' => true, 'data' => ($preenchidos/$total));
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
     * @return array
     */
    public function all(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'Pessoas,Me,Dados')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }

        $dto = new PessoasDTO();
        $obj = new PessoasADO();
        $obj_count = new PessoasADO();
        
        $doacoes = $this->mapDoacoesbyPessoa();
        $necessidades = $this->mapNecessidadesEspeciaisbyPessoa();

        $page = ($this->testInputString('page')) ? (int) NblFram::$context->data->page : -1;
        $pageSize = ($this->testInputString('pageSize')) ? (int) NblFram::$context->data->pageSize : -1;
        $pagination = $this->calcPagination($page, $pageSize);

        $searchBy = ($this->testInputString('searchBy')) ? NblFram::$context->data->searchBy : '';
        $orderBy = ($this->testInputString('orderBy')) ? NblFram::$context->data->orderBy : '';
        $groupBy = ($this->testInputString('groupBy')) ? NblFram::$context->data->groupBy : '';
        
        $nome = ($this->testInputString('nome')) ? NblFram::$context->data->nome : '';
        $email = ($this->testInputString('email')) ? NblFram::$context->data->email : '';
        
        $fields = ($this->testInputString('fields')) ? NblFram::$context->data->fields : '';

        $has_at_least_one_filter = false;

        if(!empty($searchBy))
        {   
            $has_at_least_one_filter = true;
            PessoasADO::addComparison($dto, 'nome', CMP_INCLUDE_INSIDE, $searchBy, OP_OR);
            PessoasADO::addComparison($dto, 'email', CMP_INCLUDE_INSIDE, $searchBy, OP_OR);
        }

        if(!empty($nome))
        {   
            $has_at_least_one_filter = true;
            PessoasADO::addComparison($dto, 'nome', CMP_EQUAL, $nome);
        }

        if(!empty($email))
        {   
            $has_at_least_one_filter = true;
            PessoasADO::addComparison($dto, 'email', CMP_EQUAL, $email);
        }
        
        if(!empty($orderBy))
        {
            $has_at_least_one_filter = true;
            $order_by_v = explode(',',$orderBy);
            PessoasADO::addOrdering($dto, $order_by_v[0], ($order_by_v[1] == 'asc') ? ORDER_ASC : ORDER_DESC);
        }

        if(!empty($groupBy))
        {
            $has_at_least_one_filter = true;
            $groups = explode(',',$groupBy);
            foreach($groups as $g) {
                PessoasADO::addGrouping($dto, $g);
            }
        }

        if (!$has_at_least_one_filter) {
            $obj_count->count(true);
        }
        else {
            $obj_count->countBy($dto);
        }
        
        if($page == -1)
        {
            $ok = (!$has_at_least_one_filter) ? $obj->getAll() : $obj->getAllbyParam($dto);
        }
        else
        {
            $ok = (!$has_at_least_one_filter) ? $obj->getAll($pagination->page, $pagination->pagesize) : $obj->getAllbyParam($dto, $pagination->page, $pagination->pagesize);
        }
        
        $result = array();
        if($ok)
        {
            $obj->iterate();
            while($obj->hasNext())
            {
                $it = $obj->next();
                if(!is_null($it->id))
                {
                    $doacoes_da_pessoa = (isset($doacoes[$it->id]))? $doacoes[$it->id] : array();
                    $necessidaes_da_pessoa = (isset($necessidades[$it->id])) ? $necessidades[$it->id] : array();
                    
                    $pre_result = array(
                        'id' => $it->id,
                        'perfil' => $it->perfil,
                        'nome' => $it->nome,
                        'email' => $it->email,
                        'senha' => '',
                        'profissao' => $it->profissao,
                        'sexo' => $it->sexo,
                        'data_nascimento' => $it->data_nascimento,
                        'crianca' => ($it->crianca == GenericHave::YES),
                        'responsavel' => $it->responsavel,
                        'estado_civil' => $it->estado_civil,
                        'tem_filhos' => ($it->tem_filhos == GenericHave::YES),
                        'escolaridade' => $it->escolaridade,
                        'telefone' => $it->telefone,
                        'celular_1' => $it->celular_1,
                        'celular_2' => $it->celular_2,
                        'doacoes' => $doacoes_da_pessoa,
                        'necessidades_especiais' => $necessidaes_da_pessoa,
                        'endereco' => $it->endereco,
                        'numero' => $it->numero,
                        'complemento' => $it->complemento,
                        'bairro' => $it->bairro,
                        'cidade' => $it->cidade,
                        'uf' => $it->uf,
                        'cep' => $it->cep,
                        'stat' => $it->stat,
                        'tp' => $it->tp,
                        'avatar' => $it->avatar,
                        'is_master' => $it->is_master,
                        'is_android' => $it->is_android,
                        'fcm_token' => $it->fcm_token,
                        'reset_code' => $it->reset_code,
                        'time_cad' => $it->time_cad,
                        'last_mod' => $it->last_mod,
                        'last_sync' => $it->last_sync,
                        'last_amod' => $it->last_amod
                    );
                    
                    // aplique alguma possível restrição de campo na resposta 
                    $filtered_pre_result = array();
                    if(!empty($fields)) {
                        foreach($pre_result as $field => $value) {
                            if(strpos($fields, $field) !== false) {
                                $filtered_pre_result[$field] = $value;
                            }
                        }
                    }
                    else {
                        $filtered_pre_result = $pre_result;
                    }
                    
                    $result[] = $filtered_pre_result;
                }
            }

            return array('status' => 'ok', 'success' => true, 'datas' => $result, 'total' => $obj_count->count());
        }
        else
        {
            return array('status' => 'no', 'success' => false, 'msg' => 'Erro na operação', 'errs' => $obj->getErrs());
        }
    }

    /**
     * 
     * Busca por nome
     * 
     * @httpmethod GET
     * @auth yes
     * @require nome
     * @return array
     */
    public function bynome(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'Pessoas,Me')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        return $this->all();
    }

    /**
     * 
     * Busca por email
     * 
     * @httpmethod GET
     * @auth yes
     * @require email
     * @return array
     */
    public function byemail(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'Pessoas,Me')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        return $this->all();
    }

    /**
     * 
     * Busca os nomes e ids
     * 
     * @httpmethod GET
     * @auth yes
     * @return array
     */
    public function allnomes(): array
    {
        NblFram::$context->data->fields = 'id,nome';
        return $this->all();
    }

    /**
     * 
     * Remove
     * 
     * @httpmethod DELETE
     * @auth yes
     * @require id
     * @return array
     */
    public function remove(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'PessoasRemove')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $dto = new PessoasDTO();
        $obj = new PessoasADO();
        
        $dto->id = NblFram::$context->data->id;
        $r = $obj->get($dto);
        if(!is_null($r))
        {
            $r->delete = true;
            if($obj->sync())
            {
                return array('status' => 'ok', 'success' => true);
            }
            else
            {
                return array('status' => 'no', 'success' => false, 'msg' => 'Erro na operação', 'errs' => $obj->getErrs());
            }
        }
        else 
        {
            return array('status' => 'no', 'success' => false, 'msg' => 'Recurso não encontrado');
        }
    }
    
    /**
     * 
     * Remove todos
     * 
     * @httpmethod POST
     * @auth yes
     * @require ids
     * @return array
     */
    public function removeAll(): array
    {
        if(!doIHavePermission(NblFram::$context->token, 'PessoasRemove')) {
            return array('status' => 'no', 'success' => false, 'msg' => 'Você não tem permissão para executar essa operação');
        }
        
        $obj = new PessoasADO();
        
        foreach(NblFram::$context->data->ids as $id) {
            $dto = new PessoasDTO();
            $dto->delete = true;
            $dto->id = $id;
            
            $obj->add($dto);
        }
        
        if($obj->sync())
        {
            return array('status' => 'ok', 'success' => true);
        }
        else 
        {
            return array('status' => 'no', 'success' => false, 'msg' => 'Recursos não encontrados');
        }
    }
}

