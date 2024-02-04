<?php

/**
 * A set of useful helper functions
 *
 * @author johnatas
 */
class NblPHPUtil
{
    /**
     * Convert a string in format day/month/year to format year-month-day
     * 
     * @param string $data string to be converted
     * @return string 
     */
    public static function HumanDate2DBDate($data): string
    {
        if(empty($data))
        {
            return '0000-00-00';
        }

        if(preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2}/', $data) === 1) {
            return $data;
        }

        $d_v = explode('/', $data);

        return $d_v[2] . '-' . $d_v[1] . '-' . $d_v[0];
    }

    /**
     * Convert a string in format day/month/year hour:minute:second to format year-month-day hour:minute:second
     * 
     * @param string $str string to be converted
     * @return string 
     */
    public static function HumanTime2DBTime($str): string
    {
        if(empty($str))
        {
            return '0000-00-00 00:00:00';
        }


        if(preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/', $str) === 1) {
            return $str;
        }

        $t_v = explode(' ', $str);
        $d_v = explode('/', $t_v[0]);
        $h_v = explode(':', $t_v[1]);

        return $d_v[2] . '-' . $d_v[1] . '-' . $d_v[0] . ' ' . $h_v[0] . ':' . $h_v[1] . ':00';
    }

    /**
     * Convert a string in format thousands.hundreds,cents to float
     * 
     * @param string $str string to be converted
     * @return float
     */
    public static function Money2Float($str): float
    {
        if(empty($str))
        {
            return 0.00;
        }
        $str = str_replace('.', '', $str);
        $str = str_replace(',', '.', $str);

        return (float) $str;
    }

    /**
     * Convert a string in format thousands.hundreds,cents to a string in format thousandshundreds.cents
     * 
     * @param string $str string to be converted
     * @return string
     */
    public static function Money2FloatStr($str): string
    {
        if(empty($str))
        {
            return '0.00';
        }
        
        $str = str_replace('.', '', $str);
        $str = str_replace(',', '.', $str);

        return $str;
    }
    
    /**
     * Convert an array to a csv string
     * 
     * @param array $data array to be converted
     * @param bool $end_comma use end comma or not. Default = true
     * @param string $comma comma used. Default = ,
     * @return string
     */
    public static function Array2CSV($data, $end_comma = true, $comma = ','): string
    {
        $str = '';
        foreach($data as $d) {
            if(!empty($str)) {
                $str .= $comma;
            }
            $str .= $d;
        }
        if($end_comma) {
            $str .= $comma;
        }
        return $str;
    }

    /**
     * Get month's name by it's number (01-12)
     * 
     * @param string $num month number
     * @param string $lang [optinal] language:pt-br (default), en
     * @return string
     */
    public static function num2Month($num, $lang = 'pt-br'): string
    {
        $months = array(
            'pt-br' => array('Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 
                                'Junho', 'Julho', 'Agosto', 'Setembro', 
                                'Outubro', 'Novembro', 'Dezembro', 'Erro'),
            'en' => array('January', 'February', 'March', 'April', 'May', 
                                'June', 'July', 'August', 'September', 
                                'October', 'November', 'December', 'Error')
        );
        
        switch($num)
        {
            case '1':
                return $months[$lang][0];
            case '01':
                return $months[$lang][0];
            case '2':
                return $months[$lang][1];
            case '02':
                return $months[$lang][1];
            case '3':
                return $months[$lang][2];
            case '03':
                return $months[$lang][2];
            case '4':
                return $months[$lang][3];
            case '04':
                return $months[$lang][3];
            case '5':
                return $months[$lang][4];
            case '05':
                return $months[$lang][4];
            case '6':
                return $months[$lang][5];
            case '06':
                return $months[$lang][5];
            case '7':
                return $months[$lang][6];
            case '07':
                return $months[$lang][6];
            case '8':
                return $months[$lang][7];
            case '08':
                return $months[$lang][7];
            case '9':
                return $months[$lang][8];
            case '09':
                return $months[$lang][0];
            case '10':
                return $months[$lang][9];
            case '11':
                return $months[$lang][10];
            case '12':
                return $months[$lang][11];
            default:
                return $months[$lang][12];
        }
    }

    /**
     * Get last day of a month
     * 
     * @param string $month month number (with ou without leading 0)
     * @return string
     */
    public static function getLastDayOfMonth($month): string
    {
        $map = array(
            '1' => '31',
            '01' => '31',
            '2' => '28',
            '02' => '28',
            '3' => '31',
            '03' => '31',
            '4' => '30',
            '04' => '30',
            '5' => '31',
            '05' => '31',
            '6' => '30',
            '06' => '30',
            '7' => '31',
            '07' => '31',
            '8' => '31',
            '08' => '31',
            '9' => '30',
            '09' => '30',
            '10' => '31',
            '11' => '30',
            '12' => '31'
        );

        return $map[$month];
    }
    
    /**
     * Get an array with the brazilian provinces
     * 
     * @return array
     */
    public static function makeBrazilianProvinces(): array
    {
        $provinces = array(
            array('v_short' => 'AC', 'v_long' => 'Acre', 'html' => 'Acre', 'selected' => false),
            array('v_short' => 'AL', 'v_long' => 'Alagoas', 'html' => 'Alagoas', 'selected' => false),
            array('v_short' => 'AP', 'v_long' => 'Amapá', 'html' => 'Amapá', 'selected' => false),
            array('v_short' => 'AM', 'v_long' => 'Amazonas', 'html' => 'Amazonas', 'selected' => false),
            array('v_short' => 'BA', 'v_long' => 'Bahia', 'html' => 'Bahia', 'selected' => false),
            array('v_short' => 'CE', 'v_long' => 'Ceará', 'html' => 'Ceará', 'selected' => false),
            array('v_short' => 'DF', 'v_long' => 'Distrito Federal', 'html' => 'Distrito Federal', 'selected' => false),
            array('v_short' => 'ES', 'v_long' => 'Espírito Santo', 'html' => 'Espírito Santo', 'selected' => false),
            array('v_short' => 'GO', 'v_long' => 'Goiás', 'html' => 'Goiás', 'selected' => false),
            array('v_short' => 'MA', 'v_long' => 'Maranhão', 'html' => 'Maranhão', 'selected' => false),
            array('v_short' => 'MT', 'v_long' => 'Mato Grosso', 'html' => 'Mato Grosso', 'selected' => false),
            array('v_short' => 'MS', 'v_long' => 'Mato Grosso do Sul', 'html' => 'Mato Grosso do Sul', 'selected' => false),
            array('v_short' => 'MG', 'v_long' => 'Minas Gerais', 'html' => 'Minas Gerais', 'selected' => false),
            array('v_short' => 'PA', 'v_long' => 'Pará', 'html' => 'Pará', 'selected' => false),
            array('v_short' => 'PB', 'v_long' => 'Paraíba', 'html' => 'Paraíba', 'selected' => false),
            array('v_short' => 'PR', 'v_long' => 'Paraná', 'html' => 'Paraná', 'selected' => false),
            array('v_short' => 'PE', 'v_long' => 'Pernambuco', 'html' => 'Pernambuco', 'selected' => false),
            array('v_short' => 'PI', 'v_long' => 'Piauí', 'html' => 'Piauí', 'selected' => false),
            array('v_short' => 'RJ', 'v_long' => 'Rio de Janeiro', 'html' => 'Rio de Janeiro', 'selected' => false),
            array('v_short' => 'RN', 'v_long' => 'Rio Grande do Norte', 'html' => 'Rio Grande do Norte', 'selected' => false),
            array('v_short' => 'RS', 'v_long' => 'Rio Grande do Sul', 'html' => 'Rio Grande do Sul', 'selected' => false),
            array('v_short' => 'RO', 'v_long' => 'Rondônia', 'html' => 'Rondônia', 'selected' => false),
            array('v_short' => 'RR', 'v_long' => 'Roraima', 'html' => 'Roraima', 'selected' => false),
            array('v_short' => 'SC', 'v_long' => 'Santa Catarina', 'html' => 'Santa Catarina', 'selected' => false),
            array('v_short' => 'SP', 'v_long' => 'São Paulo', 'html' => 'São Paulo', 'selected' => false),
            array('v_short' => 'SE', 'v_long' => 'Sergipe', 'html' => 'Sergipe', 'selected' => false),
            array('v_short' => 'TO', 'v_long' => 'Tocantins', 'html' => 'Tocantins', 'selected' => false)
        );

        return $provinces;
    }

    /**
     * Get an array with the country names (portugue only)
     * 
     * @return array
     */
    public static function makeContries($lang = 'pt-br'): array
    {
        $contries = array(
            'pt-br' => array(
                array('value' => 'Afeganistão', 'html' => 'Afeganistão', 'selected' => false),
                array('value' => 'África do Sul', 'html' => 'África do Sul', 'selected' => false),
                array('value' => 'Albânia', 'html' => 'Albânia', 'selected' => false),
                array('value' => 'Alemanha', 'html' => 'Alemanha', 'selected' => false),
                array('value' => 'Andorra', 'html' => 'Andorra', 'selected' => false),
                array('value' => 'Angola', 'html' => 'Angola', 'selected' => false),
                array('value' => 'Antígua e Barbuda', 'html' => 'Antígua e Barbuda', 'selected' => false),
                array('value' => 'Arábia Saudita', 'html' => 'Arábia Saudita', 'selected' => false),
                array('value' => 'Argélia', 'html' => 'Argélia', 'selected' => false),
                array('value' => 'Argentina', 'html' => 'Argentina', 'selected' => false),
                array('value' => 'Armênia', 'html' => 'Armênia', 'selected' => false),
                array('value' => 'Austrália', 'html' => 'Austrália', 'selected' => false),
                array('value' => 'Áustria', 'html' => 'Áustria', 'selected' => false),
                array('value' => 'Azerbaijão', 'html' => 'Azerbaijão', 'selected' => false),
                array('value' => 'Bahamas', 'html' => 'Bahamas', 'selected' => false),
                array('value' => 'Bangladesh', 'html' => 'Bangladesh', 'selected' => false),
                array('value' => 'Barbados', 'html' => 'Barbados', 'selected' => false),
                array('value' => 'Bahrein', 'html' => 'Bahrein', 'selected' => false),
                array('value' => 'Bélgica', 'html' => 'Bélgica', 'selected' => false),
                array('value' => 'Belize', 'html' => 'Belize', 'selected' => false),
                array('value' => 'Benin', 'html' => 'Benin', 'selected' => false),
                array('value' => 'Bielorrússia', 'html' => 'Bielorrússia', 'selected' => false),
                array('value' => 'Bolívia', 'html' => 'Bolívia', 'selected' => false),
                array('value' => 'Bósnia e Herzegóvina', 'html' => 'Bósnia e Herzegóvina', 'selected' => false),
                array('value' => 'Botsuana', 'html' => 'Botsuana', 'selected' => false),
                array('value' => 'Brasil', 'html' => 'Brasil', 'selected' => true),
                array('value' => 'Brunei"', 'html' => 'Brunei"', 'selected' => false),
                array('value' => 'Bulgária', 'html' => 'Bulgária', 'selected' => false),
                array('value' => 'Burquina Fasso', 'html' => 'Burquina Fasso', 'selected' => false),
                array('value' => 'Burundi', 'html' => 'Burundi', 'selected' => false),
                array('value' => 'Butão', 'html' => 'Butão', 'selected' => false),
                array('value' => 'Cabo Verde', 'html' => 'Cabo Verde', 'selected' => false),
                array('value' => 'Camarões', 'html' => 'Camarões', 'selected' => false),
                array('value' => 'Camboja', 'html' => 'Camboja', 'selected' => false),
                array('value' => 'Canadá', 'html' => 'Canadá', 'selected' => false),
                array('value' => 'Catar', 'html' => 'Catar', 'selected' => false),
                array('value' => 'Cazaquistão', 'html' => 'Cazaquistão', 'selected' => false),
                array('value' => 'Chade', 'html' => 'Chade', 'selected' => false),
                array('value' => 'Chile', 'html' => 'Chile', 'selected' => false),
                array('value' => 'China', 'html' => 'China', 'selected' => false),
                array('value' => 'Chipre', 'html' => 'Chipre', 'selected' => false),
                array('value' => 'Chipre do Norte', 'html' => 'Chipre do Norte', 'selected' => false),
                array('value' => 'Cidade do Vaticano', 'html' => 'Cidade do Vaticano', 'selected' => false),
                array('value' => 'Cingapura', 'html' => 'Cingapura', 'selected' => false),
                array('value' => 'Colômbia', 'html' => 'Colômbia', 'selected' => false),
                array('value' => 'Comores', 'html' => 'Comores', 'selected' => false),
                array('value' => 'Coreia do Norte', 'html' => 'Coreia do Norte', 'selected' => false),
                array('value' => 'Coreia do Sul', 'html' => 'Coreia do Sul', 'selected' => false),
                array('value' => 'Costa do Marfim', 'html' => 'Costa do Marfim', 'selected' => false),
                array('value' => 'Costa Rica', 'html' => 'Costa Rica', 'selected' => false),
                array('value' => 'Croácia', 'html' => 'Croácia', 'selected' => false),
                array('value' => 'Cuba', 'html' => 'Cuba', 'selected' => false),
                array('value' => 'Dinamarca', 'html' => 'Dinamarca', 'selected' => false),
                array('value' => 'Djibuti', 'html' => 'Djibuti', 'selected' => false),
                array('value' => 'Dominica', 'html' => 'Dominica', 'selected' => false),
                array('value' => 'Egito', 'html' => 'Egito', 'selected' => false),
                array('value' => 'El Salvador', 'html' => 'El Salvador', 'selected' => false),
                array('value' => 'Emirados Árabes Unidos', 'html' => 'Emirados Árabes Unidos', 'selected' => false),
                array('value' => 'Equador', 'html' => 'Equador', 'selected' => false),
                array('value' => 'Eritreia', 'html' => 'Eritreia', 'selected' => false),
                array('value' => 'Eslováquia', 'html' => 'Eslováquia', 'selected' => false),
                array('value' => 'Eslovênia', 'html' => 'Eslovênia', 'selected' => false),
                array('value' => 'Espanha', 'html' => 'Espanha', 'selected' => false),
                array('value' => 'Estados Federados da Micronésia', 'html' => 'Estados Federados da Micronésia', 'selected' => false),
                array('value' => 'Estados Unidos', 'html' => 'Estados Unidos', 'selected' => false),
                array('value' => 'Estônia', 'html' => 'Estônia', 'selected' => false),
                array('value' => 'Etiópia', 'html' => 'Etiópia', 'selected' => false),
                array('value' => 'Fiji', 'html' => 'Fiji', 'selected' => false),
                array('value' => 'Filipinas', 'html' => 'Filipinas', 'selected' => false),
                array('value' => 'Finlândia', 'html' => 'Finlândia', 'selected' => false),
                array('value' => 'França', 'html' => 'França', 'selected' => false),
                array('value' => 'Gabão', 'html' => 'Gabão', 'selected' => false),
                array('value' => 'Gâmbia', 'html' => 'Gâmbia', 'selected' => false),
                array('value' => 'Gana', 'html' => 'Gana', 'selected' => false),
                array('value' => 'Geórgia', 'html' => 'Geórgia', 'selected' => false),
                array('value' => 'Granada', 'html' => 'Granada', 'selected' => false),
                array('value' => 'Grécia', 'html' => 'Grécia', 'selected' => false),
                array('value' => 'Guatemala', 'html' => 'Guatemala', 'selected' => false),
                array('value' => 'Guiana', 'html' => 'Guiana', 'selected' => false),
                array('value' => 'Guiné', 'html' => 'Guiné', 'selected' => false),
                array('value' => 'Guiné-Bissau', 'html' => 'Guiné-Bissau', 'selected' => false),
                array('value' => 'Guiné Equatorial', 'html' => 'Guiné Equatorial', 'selected' => false),
                array('value' => 'Haiti', 'html' => 'Haiti', 'selected' => false),
                array('value' => 'Holanda', 'html' => 'Holanda', 'selected' => false),
                array('value' => 'Honduras', 'html' => 'Honduras', 'selected' => false),
                array('value' => 'Hungria', 'html' => 'Hungria', 'selected' => false),
                array('value' => 'Iêmen', 'html' => 'Iêmen', 'selected' => false),
                array('value' => 'Ilhas Cook', 'html' => 'Ilhas Cook', 'selected' => false),
                array('value' => 'Ilhas Fiji', 'html' => 'Ilhas Fiji', 'selected' => false),
                array('value' => 'Ilhas Marshall', 'html' => 'Ilhas Marshall', 'selected' => false),
                array('value' => 'Ilhas Salomão', 'html' => 'Ilhas Salomão', 'selected' => false),
                array('value' => 'Índia', 'html' => 'Índia', 'selected' => false),
                array('value' => 'Indonésia', 'html' => 'Indonésia', 'selected' => false),
                array('value' => 'Irã', 'html' => 'Irã', 'selected' => false),
                array('value' => 'Iraque', 'html' => 'Iraque', 'selected' => false),
                array('value' => 'Irlanda', 'html' => 'Irlanda', 'selected' => false),
                array('value' => 'Islândia', 'html' => 'Islândia', 'selected' => false),
                array('value' => 'Israel', 'html' => 'Israel', 'selected' => false),
                array('value' => 'Itália', 'html' => 'Itália', 'selected' => false),
                array('value' => 'Jamaica', 'html' => 'Jamaica', 'selected' => false),
                array('value' => 'Japão', 'html' => 'Japão', 'selected' => false),
                array('value' => 'Jordânia', 'html' => 'Jordânia', 'selected' => false),
                array('value' => 'Karabaque Montanhoso', 'html' => 'Karabaque Montanhoso', 'selected' => false),
                array('value' => 'Kiribati', 'html' => 'Kiribati', 'selected' => false),
                array('value' => 'Kosovo', 'html' => 'Kosovo', 'selected' => false),
                array('value' => 'Kuwait', 'html' => 'Kuwait', 'selected' => false),
                array('value' => 'Laos', 'html' => 'Laos', 'selected' => false),
                array('value' => 'Lesoto', 'html' => 'Lesoto', 'selected' => false),
                array('value' => 'Letônia', 'html' => 'Letônia', 'selected' => false),
                array('value' => 'Líbano', 'html' => 'Líbano', 'selected' => false),
                array('value' => 'Libéria', 'html' => 'Libéria', 'selected' => false),
                array('value' => 'Líbia', 'html' => 'Líbia', 'selected' => false),
                array('value' => 'Liechtenstein', 'html' => 'Liechtenstein', 'selected' => false),
                array('value' => 'Lituânia', 'html' => 'Lituânia', 'selected' => false),
                array('value' => 'Luxemburgo', 'html' => 'Luxemburgo', 'selected' => false),
                array('value' => 'Macedônia', 'html' => 'Macedônia', 'selected' => false),
                array('value' => 'Madagascar', 'html' => 'Madagascar', 'selected' => false),
                array('value' => 'Malásia', 'html' => 'Malásia', 'selected' => false),
                array('value' => 'Malawi', 'html' => 'Malawi', 'selected' => false),
                array('value' => 'Maldivas', 'html' => 'Maldivas', 'selected' => false),
                array('value' => 'Mali', 'html' => 'Mali', 'selected' => false),
                array('value' => 'Malta', 'html' => 'Malta', 'selected' => false),
                array('value' => 'Marrocos', 'html' => 'Marrocos', 'selected' => false),
                array('value' => 'Maurício', 'html' => 'Maurício', 'selected' => false),
                array('value' => 'Mauritânia', 'html' => 'Mauritânia', 'selected' => false),
                array('value' => 'México', 'html' => 'México', 'selected' => false),
                array('value' => 'Mianmar', 'html' => 'Mianmar', 'selected' => false),
                array('value' => 'Moçambique', 'html' => 'Moçambique', 'selected' => false),
                array('value' => 'Moldávia', 'html' => 'Moldávia', 'selected' => false),
                array('value' => 'Mônaco', 'html' => 'Mônaco', 'selected' => false),
                array('value' => 'Mongólia', 'html' => 'Mongólia', 'selected' => false),
                array('value' => 'Montenegro', 'html' => 'Montenegro', 'selected' => false),
                array('value' => 'Namíbia', 'html' => 'Namíbia', 'selected' => false),
                array('value' => 'Nauru', 'html' => 'Nauru', 'selected' => false),
                array('value' => 'Nepal', 'html' => 'Nepal', 'selected' => false),
                array('value' => 'Nicarágua', 'html' => 'Nicarágua', 'selected' => false),
                array('value' => 'Níger', 'html' => 'Níger', 'selected' => false),
                array('value' => 'Nigéria', 'html' => 'Nigéria', 'selected' => false),
                array('value' => 'Niue', 'html' => 'Niue', 'selected' => false),
                array('value' => 'Noruega', 'html' => 'Noruega', 'selected' => false),
                array('value' => 'Nova Zelândia', 'html' => 'Nova Zelândia', 'selected' => false),
                array('value' => 'Omã', 'html' => 'Omã', 'selected' => false),
                array('value' => 'Palau', 'html' => 'Palau', 'selected' => false),
                array('value' => 'Panamá', 'html' => 'Panamá', 'selected' => false),
                array('value' => 'Papua Nova Guiné', 'html' => 'Papua Nova Guiné', 'selected' => false),
                array('value' => 'Paquistão', 'html' => 'Paquistão', 'selected' => false),
                array('value' => 'Paraguai', 'html' => 'Paraguai', 'selected' => false),
                array('value' => 'Peru', 'html' => 'Peru', 'selected' => false),
                array('value' => 'Polônia', 'html' => 'Polônia', 'selected' => false),
                array('value' => 'Portugal', 'html' => 'Portugal', 'selected' => false),
                array('value' => 'Quênia', 'html' => 'Quênia', 'selected' => false),
                array('value' => 'Quirguistão', 'html' => 'Quirguistão', 'selected' => false),
                array('value' => 'Quiribáti', 'html' => 'Quiribáti', 'selected' => false),
                array('value' => 'Reino Unido', 'html' => 'Reino Unido', 'selected' => false),
                array('value' => 'República Árabe Saariana Democrática', 'html' => 'República Árabe Saariana Democrática', 'selected' => false),
                array('value' => 'República Centro-Africana', 'html' => 'República Centro-Africana', 'selected' => false),
                array('value' => 'República Democrática do Congo', 'html' => 'República Democrática do Congo', 'selected' => false),
                array('value' => 'República do Congo', 'html' => 'República do Congo', 'selected' => false),
                array('value' => 'República Dominicana', 'html' => 'República Dominicana', 'selected' => false),
                array('value' => 'República Tcheca', 'html' => 'República Tcheca', 'selected' => false),
                array('value' => 'Romênia', 'html' => 'Romênia', 'selected' => false),
                array('value' => 'Ruanda', 'html' => 'Ruanda', 'selected' => false),
                array('value' => 'Rússia', 'html' => 'Rússia', 'selected' => false),
                array('value' => 'Samoa', 'html' => 'Samoa', 'selected' => false),
                array('value' => 'Santa Lúcia', 'html' => 'Santa Lúcia', 'selected' => false),
                array('value' => 'São Cristóvão e Neves', 'html' => 'São Cristóvão e Neves', 'selected' => false),
                array('value' => 'San Marino', 'html' => 'San Marino', 'selected' => false),
                array('value' => 'São Tomé e Príncipe', 'html' => 'São Tomé e Príncipe', 'selected' => false),
                array('value' => 'São Vicente e Granadinas', 'html' => 'São Vicente e Granadinas', 'selected' => false),
                array('value' => 'Seicheles', 'html' => 'Seicheles', 'selected' => false),
                array('value' => 'Senegal', 'html' => 'Senegal', 'selected' => false),
                array('value' => 'Serra Leoa', 'html' => 'Serra Leoa', 'selected' => false),
                array('value' => 'Sérvia', 'html' => 'Sérvia', 'selected' => false),
                array('value' => 'Síria', 'html' => 'Síria', 'selected' => false),
                array('value' => 'Somália', 'html' => 'Somália', 'selected' => false),
                array('value' => 'Somalilândia', 'html' => 'Somalilândia', 'selected' => false),
                array('value' => 'Sri Lanca', 'html' => 'Sri Lanca', 'selected' => false),
                array('value' => 'Suazilândia', 'html' => 'Suazilândia', 'selected' => false),
                array('value' => 'Sudão', 'html' => 'Sudão', 'selected' => false),
                array('value' => 'Sudão do Sul', 'html' => 'Sudão do Sul', 'selected' => false),
                array('value' => 'Suécia', 'html' => 'Suécia', 'selected' => false),
                array('value' => 'Suíça', 'html' => 'Suíça', 'selected' => false),
                array('value' => 'Suriname', 'html' => 'Suriname', 'selected' => false),
                array('value' => 'Tailândia', 'html' => 'Tailândia', 'selected' => false),
                array('value' => 'Taiwan', 'html' => 'Taiwan', 'selected' => false),
                array('value' => 'Tadjiquistão', 'html' => 'Tadjiquistão', 'selected' => false),
                array('value' => 'Tanzânia', 'html' => 'Tanzânia', 'selected' => false),
                array('value' => 'Timor-Leste', 'html' => 'Timor-Leste', 'selected' => false),
                array('value' => 'Togo', 'html' => 'Togo', 'selected' => false),
                array('value' => 'Tonga', 'html' => 'Tonga', 'selected' => false),
                array('value' => 'Trinidad e Tobago', 'html' => 'Trinidad e Tobago', 'selected' => false),
                array('value' => 'Tunísia', 'html' => 'Tunísia', 'selected' => false),
                array('value' => 'Turcomenistão', 'html' => 'Turcomenistão', 'selected' => false),
                array('value' => 'Turquia', 'html' => 'Turquia', 'selected' => false),
                array('value' => 'Tuvalu', 'html' => 'Tuvalu', 'selected' => false),
                array('value' => 'Ucrânia', 'html' => 'Ucrânia', 'selected' => false),
                array('value' => 'Uganda', 'html' => 'Uganda', 'selected' => false),
                array('value' => 'Uruguai', 'html' => 'Uruguai', 'selected' => false),
                array('value' => 'Uzbequistão', 'html' => 'Uzbequistão', 'selected' => false),
                array('value' => 'Vanuatu', 'html' => 'Vanuatu', 'selected' => false),
                array('value' => 'Venezuela', 'html' => 'Venezuela', 'selected' => false),
                array('value' => 'Vietnã', 'html' => 'Vietnã', 'selected' => false),
                array('value' => 'Zâmbia', 'html' => 'Zâmbia', 'selected' => false),
                array('value' => 'Zimbábue', 'html' => 'Zimbábue', 'selected' => false)
            ),
            
        );

        return $contries[$lang];
    }
    
    /**
     * Generate a random numeric sequence
     * 
     * @param int $len size of sequence
     * @return string
     */
    public static function makeRandomNumericCode($len = 10): string
    {
        $code = "";
        $chars = array('0','1','2','3','4','5','6','7','8','9');

        for($i = 0; $i < $len; $i++)
        {
            $c = $chars[rand(0,9)];
            $code .= $c;
        }

        return $code;
    }
    
    /**
     * Generate a random alphanumeric sequence
     * 
     * @param int $len size of sequence
     * @return string
     */
    public static function makeRandomAlphaNumericCode($len = 10): string
    {
        $chave = "";
        $chars = array( "a","b","c","d","e","f","g","h","i","j",
                        "k","l","m","n","o","p","q","r","s","t",
                        "u","v","x","z","w","y",
                        "A","B","C","D","E","F","G","H","I","J",
                        "K","L","M","N","O","P","Q","R","S","T",
                        "U","V","X","Z","W","Y",
                        "0","1","2","3","4","5","6","7","8","9");

        for($i = 0; $i < $len; $i++)
        {
            $c = $chars[rand(0,61)];
            $chave .= $c;
        }

        return $chave;
    }

    /**
     * Generate a random hexadecimal sequence
     * 
     * @param int $len size of sequence
     * @return string
     */
    public static function makeRandomHexCode($len = 10): string
    {
        $code = "";
        $chars = array('0','1','2','3','4','5','6','7','8','9',
                        'A','B','C','D','E','F');

        for($i = 0; $i < $len; $i++)
        {
            $c = $chars[rand(0,15)];
            $code .= $c;
        }

        return $code;
    }

    /**
     * Generate a unique numeric id whith timestamp
     * 
     * @return string
     */
    public static function makeNumericId(): string
    {
        return date('YmdHis') . NblPHPUtil::makeRandomNumericCode();
    }
    
    /**
     * Generate a unique alphanumeric id whith timestamp
     * 
     * @return string
     */
    public static function makeAlphaNumericId(): string
    {
        return date('YmdHis') . NblPHPUtil::makeRandomAlphaNumericCode();
    }

    /**
     * Generate a unique hexadecimal id whith timestamp
     * 
     * @return string
     */
    public static function makeHexId(): string
    {
        return date('YmdHis') . NblPHPUtil::makeRandomHexCode();
    }
    
    /**
     * Generate a unique numeric id whith date only
     * 
     * @return string
     */
    public static function makeNumericSimpleCode(): string
    {
        return date('ymd') . NblPHPUtil::makeRandomNumericCode(14);
    }
    
    /**
     * Generate a unique alphanumeric id whith date only
     * 
     * @return string
     */
    public static function makeAlphaNumericSimpleCode(): string
    {
        return date('ymd') . NblPHPUtil::makeRandomAlphaNumericCode(14);
    }

    /**
     * Generate a unique hexadecimal id whith date only
     * 
     * @return string
     */
    public static function makeHexSimpleCode(): string
    {
        return date('ymd') . NblPHPUtil::makeRandomHexCode(14);
    }

    /**
     * Generate a unique filename
     * 
     * @param string $filename original filename
     * @return string
     */
    public static function makeArchName($filename): string
    {
        $mix = explode(".", $filename);
        $mix_s = count($mix) - 1;

        $new_filename = '';
        for($i = 0; $i < $mix_s; $i++)
        {
            $new_filename .= $mix[$i];
        }

        $new_filename .= "_" . NblPHPUtil::makeRandomAlphaNumericCode() . "." . strtolower($mix[$mix_s]);

        return $new_filename;
    }

}

?>
