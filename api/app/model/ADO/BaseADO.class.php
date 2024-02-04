<?php

/**
 * Classe: BaseDTO
 * 
 * Classe base para as DaTa Object (DTO)
 * 
 */
class BaseDTO
{
    /* controles de operação */
    public $add = false;
    public $edit = false;
    public $change = false;
    public $delete = false;
    public $sync = false;
    
    /* ponteiros para lista duplamente encadeada */
    public $next;
    public $prev;

    /* ordenação e agrupamento */
    public $order_by;
    public $group_by;

    /* busca específica */
    public $transients;
    
    /* controle de isolamento (em loops) */
    public $ignore = array('add','edit','change','delete','next','prev','sync','order_by','group_by','transients');

    /* constutor para setar as variáveis como nulas */
    public function __construct()
    {
        $this->reset();
    }
    
    /* resete os campos de dados do DTO */
    public function reset()
    {
        $vars = get_object_vars($this);
        foreach($vars as $v => $val)
        {
            if($v != 'ignore')
            {
                if(!in_array($v, $this->ignore))
                {
                    $this->{$v} = VOID;
                }
            }
        }
    }
}

/**
 * Classe: BaseADO
 *
 * Classe base para as classes façade Application Data Object (ADO)
 *
 */
class BaseADO
{ 
    /**
     * ponteiro para o elemento atual da lista de DTO
     */
    protected $current;
    /**
     * ponteiro para o primeiro elemento da lista de DTO
     */
    protected $dto;
    /**
     * referência para o objeto DAO
     */
    protected $dao;
    /**
     * tamanho da lista de DTO
     */
    protected $size;
    /**
     * array com os erros do DAO
     */
    protected $errs;

    /**
     * Adiciona comparação no dto
     * 
     * @param object $dto objeto DTO em que será adicionado a comparação
     * @param string $var variável/propriedado de DTO que será comparada
     * @param int $cmp flag de comparação
     * @param string $value valor da comparação
     * @param string $cmp_op flag de operação de comparação (opcinal, padrão OP_AND)
     * @param boolean $multiple flag para indicar se a comparação aplica sobre a mesma variável várias vezes (opcional, padrão false)
     * @return void
     */
    public static function addComparison(&$dto, $var, $cmp, $value = '', $cmp_op = OP_AND, $multiple = false): void
    {
        if(!$multiple) {
            $dto->{$var} = new stdClass();
            $dto->{$var}->cmp = $cmp;
            $dto->{$var}->cmp_operation = $cmp_op;
            $dto->{$var}->value = $value;
        }
        else {
            if(is_null($dto->{$var}) || $dto->{$var} == VOID) {
                $dto->{$var} = array();
            }
            
            $cmp_data = new stdClass();
            $cmp_data->cmp = $cmp;
            $cmp_data->cmp_operation = $cmp_op;
            $cmp_data->value = $value;
            
            $dto->{$var}[] = $cmp_data;
        }
    }

    /**
     * Adiciona ordenação no dto
     * 
     * @param object $dto objeto DTO em que será adicionado a ordenação
     * @param string $var variável/propriedado de DTO que será ordenada
     * @param int $order flag de ordenação (ORDER_ASC|ORDER_DESC) (opcional, padrão ORDER_ASC)
     * @return void
     */
    public static function addOrdering(&$dto, $var, $order = ORDER_ASC): void
    {
        if(is_null($dto->order_by)) {
            $dto->order_by = new stdClass();
            $dto->order_by->fields = array();
            $dto->order_by->mode = $order;
        }
        
        $dto->order_by->fields[] = $var;
    }
    
    /**
     * Adiciona agrupamento no dto
     * 
     * @param object $dto objeto DTO em que será adicionado o agrupamento
     * @param string $var variável/propriedado de DTO que será agrupado
     * @return void
     */
    public static function addGrouping(&$dto, $var): void
    {
        if(is_null($dto->group_by)) {
            $dto->group_by = new stdClass();
            $dto->group_by->fields = array();
        }
        
        $dto->group_by->fields[] = $var;
    }

    /**
     * Obtem o array com os erros 
     * 
     * @return array
     */
    public function getErrs(): array
    {
        return $this->errs;
    }

    /**
     * Seta o array de erros
     * 
     * @param array $errs array com os erros
     * @return void
     */
    public function setErrs($errs) : void
    {
        $this->errs = $errs;
    }

    /**
     * Verifica se há mais resultados na lista
     * @return bool
     */
    public function hasNext(): bool
    {
        return !is_null($this->current);
    }

    /**
     * Verifica se este é o último resultado da lista
     * @return bool
     */
    public function isNotTheLast(): bool
    {
        return !is_null($this->current->next);
    }
    
    /**
     *  Pega o próximo da lista 
     * @return object
     */
    public function next(): ?object
    {
        $c = $this->current;
        $this->current = $this->current->next;
        return $c;
    }
    
    /**
     *  Prepara a lista para iterar 
     * @return void
     */
    public function iterate(): void
    {
        $this->current = $this->dto;
    }
	
    /** 
     * Adiciona informação à lista 
     * @return void
     */
    public function add($dto): void
    {
        if(is_null($this->dto))
        {
            $this->dto = $dto;
            $prev = null;
        }
        else 
        {
            $this->iterate();
            if($this->hasNext() && $this->isNotTheLast())
            {
                /* não está no final da lista. Itere até que esteja */
                while($this->isNotTheLast())
                {
                    $this->next();
                }
            }
            
            $this->current->next = $dto;
            $prev = $this->current;
        }
        
        $this->current = $dto;
        $this->current->prev = $prev;
    }
    
    /** 
     * Remove da lista, através de ponteiro já existente dentro dela 
     * @return void
     */
    public function remove($dto): void
    {
        if(is_null($dto->prev) && is_null($dto->next))
        {
            /* elemento único na lista. Só remova */
            unset($dto);
            $this->current = null;
            $this->dto = null;
        }
        else if(is_null($dto->prev) && !is_null($dto->next))
        {
            /* cabeça da lista */
            $this->current = $dto->next;
            $this->current->prev = null;
            unset($dto);
        }
        else if(!is_null($dto->prev) && is_null($dto->next)) 
        {
            /* cauda */
            $this->current = $dto->prev;
            $this->current->next = null;
            unset($dto);
        }
        else
        {
            /* caso comum. */
            $this->current = $dto->prev;
            $this->current->next = $dto->next;
            $this->current = $dto->next;
            $this->current->prev = $dto->prev;
            unset($dto);
        }
    }
    
    /** 
     * Sincroniza lista com o banco 
     * @return bool
     */
    public function sync(): bool
    {
        $has_err = false;
        $this->iterate();
        while($this->hasNext())
        {
            $it = $this->next();
            
            if($it->add && !$it->sync)
            {
                if($this->dao->add($it, $this))
                {
                    $it->sync = true;
                }
                else 
                {
                    $has_err = true;
                }
            }
            else if($it->edit && !$it->sync)
            {
                if($this->dao->edit($it, $this))
                {
                    $it->sync = true;
                }
                else 
                {
                    $has_err = true;
                }
            }
            else if($it->change && !$it->sync)
            {
                if($this->dao->change($it, $this))
                {
                    $it->sync = true;
                }
                else 
                {
                    $has_err = true;
                }
            }
            else if($it->delete && !$it->sync)
            {
                if($this->dao->delete($it, $this))
                {
                    $this->remove($it);
                }
                else 
                {
                    $has_err = true;
                }
            }
        }
        
        return !$has_err;
    }
    
    /** 
     * Procura informação na lista 
     * 
     * @param object $dto objeto DTO contendo os valores das chaves para a busca em banco
     * @return object null se nada é encontrado, ou objeto DTO com o resultado
     */
    public function get($dto): ?object
    {
        $this->iterate();
        while($this->hasNext())
        {
            $it = $this->next();
            
            if($this->dao->compareByKeys($it, $dto))
            {
                return $it;
            }
        }
        
        /* não encontrou. Busque no banco */
        if($this->dao->search($dto))
        {
            $this->add($dto);
            $this->size++;
            return $dto;
        }
        
        return null;
    }

    /**
     * Procura informação conforme parâmetro no DTO
     * 
     * @param object $dto objeto DTO contendo os valores para a busca em banco
     * @return object null se nada é encontrado, ou objeto DTO com o resultado
     */
    public function getBy($dto): ?object
    {   
        /* antes de resetar a lista, sincronize */
        if(!is_null($this->dto))
        {
            $this->sync();
        }
        
        $this->clear();

        if($this->dao->searchBy($dto))
        {
            $this->add($dto);
            $this->size++;
            return $dto;
        }
        
        return null;
    }
    
    /** 
     * Limpa a lista 
     * @return void
     */
    public function clear(): void
    {
        $this->iterate();
        while($this->hasNext())
        {
            $it = $this->next();
            
            $this->remove($it);
        }
    }
    
    /** 
     * Busca todos os resultados no banco de forma paginada gerando a lista de resultados 
     * 
     * @param string $page página a ser buscada (opcional)
     * @param string $len tamanho da página (opcional)
     * @return bool
     */
    public function getAll($page = null, $len = null): bool
    {
        /* antes de resetar a lista, sincronize */
        if(!is_null($this->dto))
        {
            $this->sync();
        }
        
        $this->clear();
        
        return $this->dao->searchAll($this, $page, $len);
    }
    
    /** 
     * Busca todos os resultados no banco conforme os parâmetros no DTO 
     * de forma paginada gerando a lista de resultados 
     * 
     * @param object $dto objeto DTO contendo os valores para a busca em banco
     * @param string $page página a ser buscada (opcional)
     * @param string $len tamanho da página (opcional)
     * @return bool
     */
    public function getAllbyParam($dto, $page = null, $len = null): bool
    {
        /* antes de resetar a lista, sincronize */
        if(!is_null($this->dto))
        {
            $this->sync();
        }
        
        $this->clear();
        
        return $this->dao->searchAllbyParam($this, $dto, $page, $len);
    }
    
    /** 
     * Incrementa o contador do tamanho da lista
     * 
     * @return void
    */
    public function incrementCount(): void
    {
        $this->size++;
    }

    /**
     * Seta o tamanho da lista
     * 
     * @param int $s tamanho da lista
     * @return void
     */
    public function setCount($s): void
    {
        $this->size = $s;
    }

    /**
     * Conta o tamanho da lista de resultado
     * 
     * @param bool $sync valor opcional para forçar a sincronização da lista no banco antes de verificar o tamanho da mesma
     * @return int
     */
    public function count($sync = false): int
    {
        if($sync) {
            $this->dao->count($this);
        }
        
        return $this->size;
    }

    /**
     * Conta o tamanho da lista de resultados conforme filtros no DTO
     * 
     * @param object $dto objeto DTO contendo os valores para a busca em banco
     * @return int
     */
    public function countBy($dto): int
    {
        $this->dao->countBy($this, $dto);
        
        return $this->size;
    }

    /**
     * Mapea os dados conforme parâmetro passado
     * 
     * @param string $map_by parâmetro para o mapeamento
     * @return array
     */
    public function mapAllBy($map_by): array
    {
        $map = array();
        $this->dao->mapAllBy($map, $map_by);
        return $map;
    }
    
    /**
     * Mapea os dados conforme parâmetro passado e filtro no DTO
     * 
     * @param string $map_by parâmetro para o mapeamento
     * @param object $dto objeto DTO contendo os valores para a busca em banco
     * @return array
     */
    public function mapAllByWithParam($map_by, $dto): array
    {
        $map = array();
        $this->dao->mapAllByWithParam($map, $map_by, $dto);
        return $map;
    }

    /**
     * Mapea multiplos dados conforme parâmetro passado
     * 
     * @param string $map_by parâmetro para o mapeamento
     * @return array
     */
    public function multiMapAllBy($map_by): array
    {
        $map = array();
        $this->dao->multiMapAllBy($map, $map_by);
        return $map;
    }

    /**
     * Mapea multiplos dados conforme parâmetro passado e filtro no DTO
     * 
     * @param string $map_by parâmetro para o mapeamento
     * @param object $dto objeto DTO contendo os valores para a busca em banco
     * @return array
     */
    public function multiMapAllByWithParam($map_by, $dto): array
    {
        $map = array();
        $this->dao->multiMapAllByWithParam($map, $map_by, $dto);
        return $map;
    }

    /**
     * Gera os resultados da lista em um array de strings
     * 
     * @param object $dto objeto DTO contido na lista
     * @return array
     */
    public function getDebugAsString($dto): array
    {
        $vars = get_object_vars($dto);
        
        $this->iterate();
        
        $list = array();
        while($this->hasNext())
        {
            $it = $this->next();
            
            $data = '';
            foreach($vars as $v => $val)
            {
                if(!in_array($v, $dto->ignore) && $v != 'ignore')
                {
                    $data .= $v . '=' . print_r($it->{$v},true) . '|';
                }
            }
            
            $list[] = $data;
        }
        
        return $list;
    }

    /**
     * Gera os resultados da lista em um array de arrays
     * 
     * @param object $dto objeto DTO contido na lista
     * @return array
     */
    public function getDebugAsArray($dto): array
    {
        $vars = get_object_vars($dto);
        
        $this->iterate();
        
        $list = array();
        while($this->hasNext())
        {
            $it = $this->next();
            
            $data = array();
            foreach($vars as $v => $val)
            {
                if(!in_array($v, $dto->ignore) && $v != 'ignore')
                {
                    if($it->{$v} == VOID) {
                        $data[$v] = '';
                    }
                    else {
                        $data[$v] = $it->{$v};
                    }
                }
            }
            
            $list[] = $data;
        }
        
        return $list;
    }

    /**
     * Gera o primeiro resultado da lista como um objeto
     * 
     * @param object $dto objeto DTO contido na lista
     * @return object
     */
    public function getDTOData($dto): object
    {
        $vars = get_object_vars($dto);
        
        $this->iterate();
        
        $data = new stdClass();
        while($this->hasNext())
        {
            $it = $this->next();
            
            foreach($vars as $v => $val)
            {
                if(!in_array($v, $dto->ignore) && $v != 'ignore')
                {
                    if($it->{$v} == VOID) {
                        $data->{$v} = '';
                    }
                    else {
                        $data->{$v} = $it->{$v};
                    }
                }
            }
            
            break;
        }
        
        return $data;
    }
    
    /**
     * Gera os resultados da lista em um array de objetos
     * 
     * @param object $dto objeto DTO contido na lista
     * @return array
     */
    public function getDTODataAsArray($dto): array
    {
        $vars = get_object_vars($dto);
        
        $this->iterate();
        
        $list = array();
        while($this->hasNext())
        {
            $it = $this->next();
            
            $data = new stdClass();
            foreach($vars as $v => $val)
            {
                if(!in_array($v, $dto->ignore) && $v != 'ignore')
                {
                    if($it->{$v} == VOID) {
                        $data->{$v} = '';
                    }
                    else {
                        $data->{$v} = $it->{$v};
                    }
                }
            }
            
            $list[] = $data;
        }
        
        return $list;
    }

} 

?>
