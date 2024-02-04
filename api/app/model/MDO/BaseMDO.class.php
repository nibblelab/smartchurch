<?php

/**
 * Classe: BaseMDO
 * 
 * Classe base para as classes MoDel Object (MDO)
 *
 *
 */
class BaseMDO
{
    /* metadados de configuração para geração do modelo */
    public $metadata;
    
    /**
     * Gera o MDO com base no array
     * 
     * @param array $arr array de onde será gerado o MDO
     * @return void
     */
    public function generateFromArray($arr): void
    {
        foreach($this->metadata as $index => $field)
        {
            if(isset($arr[$index])) {
                $this->{$field} = $arr[$index];
            }
        }
    }
}



