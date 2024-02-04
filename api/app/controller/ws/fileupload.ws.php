<?php
require_once HLP_PATH . '/DocHelper.class.php'; 

class Fileupload
{
    /**
     * 
     * salva um upload
     * 
     * @httpmethod POST
     * @auth yes
     * @return array
     */
    public function save(): array
    {
        if(!empty($_FILES))
        {
            $helper = new DocHelper();
            $original_name = $_FILES['file']['name'];
            $ext_orig = pathinfo($original_name, PATHINFO_EXTENSION);
            $name_orig = basename($original_name, '.'.$ext_orig);
            $id_file = NblPHPUtil::makeNumericId();
            $arq_nome = 'scc_' . $id_file . '_'. $helper->normalizeFileName($original_name);
            $uploadfile = RSC_TMP_PATH .'/'. $arq_nome;
            if(move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile))
            {
                return array('ok' => true, 'file' => $arq_nome, 'original_name' => $name_orig);
            }
            else
            {
                return array('ok' => false, 'msg' => 'erro ao copiar o arquivo ' . $_FILES['file']['error']);
            }
        }
        else
        {
            return array('ok' => false, 'msg' => 'nenhum arquivo enviado');
        }
    }
}
