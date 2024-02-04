<?php

/**
 * Classe para testar os dados de entrada dos métodos no service REST do Presbiterios
 */
class PresbiteriosFilter
{
    /**
     * 
     * Filtra os parâmetros da criação
     * 
     * @return array
     */
    public function create(): array
    {
        NblSEPHP::filterObject(NblFram::$context->data);
        
        return array('status' => 'ok', 'success' => true);
    }
    
    /**
     * 
     * Filtra os parâmetros da edição
     * 
     * @return array
     */
    public function edit(): array
    {
        NblSEPHP::filterObject(NblFram::$context->data);
        
        return array('status' => 'ok', 'success' => true);
    }
    
    /**
     * 
     * Filtra os parâmetros da busca por id
     * 
     * @return array
     */
    public function me(): array
    {
        NblSEPHP::filterObject(NblFram::$context->data);
        
        return array('status' => 'ok', 'success' => true);
    }
    
    /**
     * 
     * Filtra os parâmetros da busca
     * 
     * @return array
     */
    public function all(): array
    {
        NblSEPHP::filterObject(NblFram::$context->data);
        
        return array('status' => 'ok', 'success' => true);
    }
    
    /**
     * 
     * Filtra os parâmetros da remoção
     * 
     * @return array
     */
    public function remove(): array
    {
        NblSEPHP::filterObject(NblFram::$context->data);
        
        return array('status' => 'ok', 'success' => true);
    }
    
    /**
     * 
     * Filtra os parâmetros da remoção em lote
     * 
     * @return array
     */
    public function removeAll(): array
    {
        NblSEPHP::filterObject(NblFram::$context->data);
        
        return array('status' => 'ok', 'success' => true);
    }
}
