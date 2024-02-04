<?php


/**
 * Classe auxiliar de processamento de documentos
 *
 * @author johnatas
 */
class DocHelper
{
    /**
     * Convert a base64 encoded string file to file
     * 
     * @param string $base_64_doc base64 enconded content
     * @param string $doc_name filename to received the decoded data
     * @return object
     */
    public function generateFromBase64($base_64_doc, $doc_name): object
    {
        $id_file = NblPHPUtil::makeNumericId();
        $file_name = 'doc_' . $id_file . '_'.$doc_name;
        $file_path = RSC_PATH . '/' . $file_name;
        $data = base64_decode(preg_replace('#^data:\w+/\w+;base64,#i', '', $base_64_doc));
        file_put_contents($file_path, $data);
        
        $ret = new stdClass();
        $ret->path = $file_path;
        $ret->name = $file_name;
        
        return $ret;
    }
    
    /**
     * Decode an base64 encoded csv file to a file
     * 
     * @param string $base_64_csv base64 enconded csv
     * @param string $csv_name csv filename to received the decoded data
     * @return object
     */
    public function getCSVFromBase64($base_64_csv, $csv_name): object
    {
        $id_file = NblPHPUtil::makeNumericId();
        $file_name = 'csv_' . $id_file . '_'.$csv_name;
        $file_path = RSC_PATH . '/' . $file_name;
        $data = base64_decode(preg_replace('#^data:\w+/\w+;base64,#i', '', $base_64_csv));
        file_put_contents($file_path, $data);
        
        $ret = new stdClass();
        $ret->path = $file_path;
        $ret->name = $file_name;
        
        return $ret;
    }
    
    /**
     * Generates a model (MDO) from base64 enconded csv
     * 
     * @param string $base_64_csv base64 enconded csv
     * @param string $csv_model MDO classname
     * @param int $start_index index/line on CSV from which data occur
     * @return object
     */
    public function getCSVModelFromBase64($base_64_csv, $csv_model, $start_index): object
    {
        $csv_file = RSC_PATH . '/file.csv';
        $data = base64_decode(preg_replace('#^data:\w+/\w+;base64,#i', '', $base_64_csv));
        file_put_contents($csv_file, $data);
        
        $csv = array();
        $f = fopen($csv_file, 'r');
        for ($i = 0; $row = fgetcsv($f); ++$i) {
            if($i > $start_index) {
                $inline_data = preg_split('/,|;/', $row[0]);
                if(!empty($inline_data)) {
                    $mdl = new $csv_model();
                    $mdl->generateFromArray($inline_data);
                    $csv[] = $mdl;
                }
            }
        }
        
        $ret = new stdClass();
        $ret->data = $csv;
        
        return $ret;
    }
    
    /**
     * Remove a set of files 
     * 
     * @param string $docs array with files (with paths) to be removed
     * @return void
     */
    public function delete($docs): void
    {
        foreach($docs as $doc) {
            if(file_exists($doc)) {
                @unlink($doc);
            }
        }
    }
    
    /**
     * Move a set of files
     * 
     * @param array $docs files to be moved from $d['old'] to $d['new']
     * @return void
     */
    public function move($docs): void
    {
        foreach($docs as $doc) {
            @rename($doc['old'], $doc['new']);
        }
    }
    
    /**
     * Normalize filename string removing accents
     * 
     * @param string $filename filename to be normalized
     * @return string
     */
    public function normalizeFileName($filename): string
    {
        $new_filename = $filename;
        $new_filename = str_replace(' ', '_', $new_filename);
        return strtr(utf8_decode($new_filename), utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'), 'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
    }
}
