<?php

/**
 * Classe abstrata para enumerar os status possíveis
 */
abstract class Status
{
    /**
     * ativo
     */
    const ACTIVE = "ATV";
    /**
     * bloqueado
     */
    const BLOCKED = "BLK";
}

/**
 * Obtêm a lista de status
 * 
 * @return array
 */
function getStatusList(): array
{
    return array(
        array('value' => Status::ACTIVE, 'label' => 'Ativo'),
        array('value' => Status::BLOCKED, 'label' => 'Não Ativo')
    );
}

/**
 * Classe abstrata para enumerar os casos comuns de possuir ou não uma propriedade
 */
abstract class GenericHave
{
    const YES = "S";
    const NO = "N";
}

/**
 * Obtêm a lista de tem ou não tem
 * 
 * @return array
 */
function getGenericHaveList(): array
{
    return array(
        array('value' => GenericHave::YES, 'label' => 'Sim'),
        array('value' => GenericHave::NO, 'label' => 'Não')
    );
}

/**
 * Classe abstrata para enumerar os modos de operação do DTO
 */
abstract class DTOMode
{
    const ADD = 0;
    const EDIT = 1;
    const DELETE = 2;
}

/**
 * Classe abstrata para enumerar valores vazios/inválidos de determinados campos
 */
abstract class Voids
{
    const UF = "--";
    const TipoOficial = "---";
}

/**
 * Classe abstrata para enumerar os tipos de usuários
 */
abstract class UserTypes
{
    /**
     * equipe
     */
    const STAFF = "STF";
    /**
     * administrador
     */
    const ADMIN = "ADM";
    /**
     * usuário/membro
     */
    const USER = "USR";
}

/**
 * Classe abstrata para enumerar o nível do usuário
 */
abstract class UserLevel
{
    const MASTER = "S";
    const COMMON = "N";
}

/**
 * Classe abstrata para enumerar o SO do celular dos usuários
 */
abstract class MobileOS
{
    const ANDROID = "S";
    const IOS = "N";
    const DESCONHECIDO = "-";
}

/**
 * Classe abstrata para enumerar as referências
 */
abstract class References
{
    const NONE = "000";
    const IGREJA = "001";
    const PRESBITERIO = "002";
    const SINODO = "003";
    const SUPREMO = "004";
    const SOCIEDADE = "005";
    const FEDERACAO = "006";
    const SINODAL = "007";
    const NACIONAL = "011";
    const EVENTO = "024";
    const ATIVIDADE = "025";
    const ELEICAO = "026";
    const MINISTERIO = "027";
    const SECRETARIA = "028";
    const UCP = "030";
    const UPA = "031";
    const UMP = "032";
    const UPH = "033";
    const SAF = "034";
    const CONSELHO = "035";
    const JUNTA = "036";
    const MURAL = "037";
    const PASTOR = "038";
    const EVANGELISTA = "039";
    const EBD = "040";
}

/**
 * Obtêm a lista de referências/instâncias
 * 
 * @return array
 */
function getReferenceList(): array
{
    return array(
        array('value' => References::IGREJA, 'label' => 'Igreja/Congregação/Ponto'),
        array('value' => References::PRESBITERIO, 'label' => 'Presbitério'),
        array('value' => References::SINODO, 'label' => 'Sínodo'),
        array('value' => References::SUPREMO, 'label' => 'Supremo Concílio'),
        array('value' => References::SOCIEDADE, 'label' => 'Sociedade Local'),
        array('value' => References::FEDERACAO, 'label' => 'Federação'),
        array('value' => References::SINODAL, 'label' => 'Confederação Sinodal'),
        array('value' => References::NACIONAL, 'label' => 'Confederação Nacional'),
        array('value' => References::EVENTO, 'label' => 'Evento'),
        array('value' => References::ATIVIDADE, 'label' => 'Atividade'),
        array('value' => References::ELEICAO, 'label' => 'Eleição'),
        array('value' => References::MINISTERIO, 'label' => 'Ministério'),
        array('value' => References::SECRETARIA, 'label' => 'Secretaria'),
        array('value' => References::UCP, 'label' => 'UCP'),
        array('value' => References::UPA, 'label' => 'UPA'),
        array('value' => References::UMP, 'label' => 'UMP'),
        array('value' => References::UPH, 'label' => 'UPH'),
        array('value' => References::SAF, 'label' => 'SAF'),
        array('value' => References::CONSELHO, 'label' => 'Conselho'),
        array('value' => References::JUNTA, 'label' => 'Junta Diaconal'),
        array('value' => References::MURAL, 'label' => 'Mural'),
        array('value' => References::PASTOR, 'label' => 'Pastor'),
        array('value' => References::EVANGELISTA, 'label' => 'Evangelista'),
        array('value' => References::EBD, 'label' => 'Escola Dominical')
    );
}

/**
 * Gera a lista de sociedades da IPB
 * 
 * @return array
 */
function getSociedades(): array
{
    return array(
        array('value' => References::UCP, 'label' => 'UCP'),
        array('value' => References::UPA, 'label' => 'UPA'),
        array('value' => References::UMP, 'label' => 'UMP'),
        array('value' => References::UPH, 'label' => 'UPH'),
        array('value' => References::SAF, 'label' => 'SAF')
    );
}

/**
 * Gera a lista de referências que podem receber cargos
 * 
 * @return array
 */
function getReferencesForCargos(): array
{
    return array(
        array('value' => References::IGREJA, 'label' => 'Igreja/Congregação/Ponto'),
        array('value' => References::PRESBITERIO, 'label' => 'Presbitério'),
        array('value' => References::SINODO, 'label' => 'Sínodo'),
        array('value' => References::SUPREMO, 'label' => 'Supremo Concílio'),
        array('value' => References::SOCIEDADE, 'label' => 'Sociedade Local'),
        array('value' => References::FEDERACAO, 'label' => 'Federação'),
        array('value' => References::SINODAL, 'label' => 'Confederação Sinodal'),
        array('value' => References::NACIONAL, 'label' => 'Confederação Nacional')
    );
}

/**
 * Classe abstrata para enumerar os contextos
 */
abstract class Contexts
{
    const IGREJAS = 'igrejas';
    const CONSELHOS = 'conselhos';
    const JUNTAS = 'juntas';
    const SOCIEDADES = 'sociedades';
    const FEDERACOES = 'federacoes';
    const SINODAIS = 'sinodais';
    const SECRETARIAS = 'secretarias';
    const MINISTERIOS = 'ministerios';
    const PASTORES = 'pastores';
    const EVANGELISTAS = 'evangelistas';
    const EVENTOS = 'eventos';
    const ELEICOES = 'eleicoes';
    const EBDS = 'ebds';
}

/**
 * Classe abstrata para enumerar os módulos
 */
abstract class Modules
{
    const BASE = 'MOD_BASE';
    const B_IGREJA = 'MOD_B_IGREJA';
    const IGREJA = 'MOD_IGREJA';
    const EBD = 'MOD_EBD';
    const AGENDA = 'MOD_AGENDA';
    const DOCUMENTOS = 'MOD_DOCUMENTOS';
    const EVENTOS = 'MOD_EVENTOS';
    const ELEICOES = 'MOD_ELEICOES';
    const FINANCAS = 'MOD_FINANCAS';
    const CONFEDERACAO = 'MOD_CONFEDERACAO';
}

/**
 * Classe abstrata para enumerar a escolaridade
 */
abstract class Escolaridade
{
    const VOID = "---";
    const FUNDAMENTAL_INCOMPLETO = "FNI";
    const FUNDAMENTAL_COMPLETO = "FNC";
    const MEDIO_INCOMPLETO = "MDI";
    const MEDIO_COMPLETO = "MDC";
    const TECNICO = "TEC";
    const SUPERIOR_INCOMPLETO = "SPI";
    const SUPERIOR_COMPLETO = "SPC";
    const POS_GRADUACAO = "PGD";
    const MESTRADO = "MET";
    const DOUTORADO = "DOC";
    const POS_DOUTORADO = "PHD";
}

/**
 * Obtêm a lista de escolaridade
 * 
 * @return array
 */
function getEscolaridadeList(): array
{
    return array(
        array('value' => Escolaridade::FUNDAMENTAL_INCOMPLETO, 'label' => 'Fundamental Incompleto'),
        array('value' => Escolaridade::FUNDAMENTAL_COMPLETO, 'label' => 'Fundamental Completo'),
        array('value' => Escolaridade::MEDIO_INCOMPLETO, 'label' => 'Médio Incompleto'),
        array('value' => Escolaridade::MEDIO_COMPLETO, 'label' => 'Médio Completo'),
        array('value' => Escolaridade::TECNICO, 'label' => 'Técnico'),
        array('value' => Escolaridade::SUPERIOR_INCOMPLETO, 'label' => 'Superior Incompleto'),
        array('value' => Escolaridade::SUPERIOR_COMPLETO, 'label' => 'Superior Completo'),
        array('value' => Escolaridade::POS_GRADUACAO, 'label' => 'Pós Graduação/MBA'),
        array('value' => Escolaridade::MESTRADO, 'label' => 'Mestrado'),
        array('value' => Escolaridade::DOUTORADO, 'label' => 'Doutorado'),
        array('value' => Escolaridade::POS_DOUTORADO, 'label' => 'Pós Doutorado')
    );
}

/**
 * Obtêm a lista de escolaridade com o valor nulo
 * 
 * @return array
 */
function getEscolaridadeListWithVoid(): array
{
    return array(
        array('value' => Escolaridade::VOID, 'label' => ''),
        array('value' => Escolaridade::FUNDAMENTAL_INCOMPLETO, 'label' => 'Fundamental Incompleto'),
        array('value' => Escolaridade::FUNDAMENTAL_COMPLETO, 'label' => 'Fundamental Completo'),
        array('value' => Escolaridade::MEDIO_INCOMPLETO, 'label' => 'Médio Incompleto'),
        array('value' => Escolaridade::MEDIO_COMPLETO, 'label' => 'Médio Completo'),
        array('value' => Escolaridade::TECNICO, 'label' => 'Técnico'),
        array('value' => Escolaridade::SUPERIOR_INCOMPLETO, 'label' => 'Superior Incompleto'),
        array('value' => Escolaridade::SUPERIOR_COMPLETO, 'label' => 'Superior Completo'),
        array('value' => Escolaridade::POS_GRADUACAO, 'label' => 'Pós Graduação/MBA'),
        array('value' => Escolaridade::MESTRADO, 'label' => 'Mestrado'),
        array('value' => Escolaridade::DOUTORADO, 'label' => 'Doutorado'),
        array('value' => Escolaridade::POS_DOUTORADO, 'label' => 'Pós Doutorado')
    );
}

/**
 * Obtêm a lista de escolaridade de forma ordenada
 * 
 * @return array
 */
function getEscolaridadeOrderedList(): array
{
    return array(
        Escolaridade::VOID,
        Escolaridade::FUNDAMENTAL_INCOMPLETO,
        Escolaridade::FUNDAMENTAL_COMPLETO,
        Escolaridade::MEDIO_INCOMPLETO,
        Escolaridade::MEDIO_COMPLETO,
        Escolaridade::TECNICO,
        Escolaridade::SUPERIOR_INCOMPLETO,
        Escolaridade::SUPERIOR_COMPLETO,
        Escolaridade::POS_GRADUACAO,
        Escolaridade::MESTRADO,
        Escolaridade::DOUTORADO,
        Escolaridade::POS_DOUTORADO
    );
}

/**
 * Classe abstrata para enumerar o estado civil
 */
abstract class EstadoCivil
{
    const VOID = "---";
    const SOLTEIRO = "SOL";
    const CASADO = "CAS";
    const VIUVO = "VIU";
    const DIVORCIADO = "DIV";
}

/**
 * Obtêm a lista de estado civil
 * 
 * @return array
 */
function getEstadoCivilList(): array
{
    return array(
        array('value' => EstadoCivil::SOLTEIRO, 'label' => 'Solteiro(a)'),
        array('value' => EstadoCivil::CASADO, 'label' => 'Casado(a)'),
        array('value' => EstadoCivil::VIUVO, 'label' => 'Viúvo(a)'),
        array('value' => EstadoCivil::DIVORCIADO, 'label' => 'Divorciado(a)')
    );
}

/**
 * Obtêm a lista de estado civil com o valor nulo
 * 
 * @return array
 */
function getEstadoCivilListWithVoid(): array
{
    return array(
        array('value' => EstadoCivil::VOID, 'label' => ''),
        array('value' => EstadoCivil::SOLTEIRO, 'label' => 'Solteiro(a)'),
        array('value' => EstadoCivil::CASADO, 'label' => 'Casado(a)'),
        array('value' => EstadoCivil::VIUVO, 'label' => 'Viúvo(a)'),
        array('value' => EstadoCivil::DIVORCIADO, 'label' => 'Divorciado(a)')
    );
}

/**
 * Classe abstrata para enumerar o sexo
 */
abstract class Sexo
{
    const VOID = "-";
    const MASCULINO = "M";
    const FEMININO = "F";
}

/**
 * Obtêm a lista de sexos
 * 
 * @return array
 */
function getSexoList(): array
{
    return array(
        array('value' => Sexo::MASCULINO, 'label' => 'Masculino'),
        array('value' => Sexo::FEMININO, 'label' => 'Feminino')
    );
}

/**
 * Obtêm a lista de sexos com o valor nulo
 * 
 * @return array
 */
function getSexoListWithVoid(): array
{
    return array(
        array('value' => Sexo::VOID, 'label' => ''),
        array('value' => Sexo::MASCULINO, 'label' => 'Masculino'),
        array('value' => Sexo::FEMININO, 'label' => 'Feminino')
    );
}

/**
 * Classe abstrata para enumerar a relação familiar
 */
abstract class RelacaoFamiliar
{
    const VOID = "---";
    const PAI = "PAI";
    const MAE = "MAE";
    const ESPOSO = "ESO";
    const ESPOSA = "ESA";
    const FILHO = "FLO";
    const FILHA = "FLA";
    const IRMAO = "IMO";
    const IRMA = "IMA";
    const TIO = "TIO";
    const TIA = "TIA";
    const AVO = "AVO";
    const AVOO = "VOO";
    const NETO = "NTO";
    const NETA = "NTA";
    const PRIMO = "PRO";
    const PRIMA = "PRA";
}

/**
 * Obtêm a lista de relação familiar
 * 
 * @return array
 */
function getRelacaoFamiliarList(): array
{
    return array(
        array('value' => RelacaoFamiliar::VOID, 'label' => ''),
        array('value' => RelacaoFamiliar::PAI, 'label' => 'Pai'),
        array('value' => RelacaoFamiliar::MAE, 'label' => 'Mãe'),
        array('value' => RelacaoFamiliar::ESPOSO, 'label' => 'Esposo'),
        array('value' => RelacaoFamiliar::ESPOSA, 'label' => 'Esposa'),
        array('value' => RelacaoFamiliar::FILHO, 'label' => 'Filho'),
        array('value' => RelacaoFamiliar::FILHA, 'label' => 'Filha'),
        array('value' => RelacaoFamiliar::IRMAO, 'label' => 'Irmão'),
        array('value' => RelacaoFamiliar::IRMA, 'label' => 'Irmã'),
        array('value' => RelacaoFamiliar::TIO, 'label' => 'Tio'),
        array('value' => RelacaoFamiliar::TIA, 'label' => 'Tia'),
        array('value' => RelacaoFamiliar::AVO, 'label' => 'Avô'),
        array('value' => RelacaoFamiliar::AVOO, 'label' => 'Avó'),
        array('value' => RelacaoFamiliar::NETO, 'label' => 'Neto'),
        array('value' => RelacaoFamiliar::NETA, 'label' => 'Neta'),
        array('value' => RelacaoFamiliar::PRIMO, 'label' => 'Primo'),
        array('value' => RelacaoFamiliar::PRIMA, 'label' => 'Prima')
    );
}

/**
 * Classe abstrata para enumerar a frequência
 */
abstract class Frequencia
{
    const PRESENTE = "P";
    const AUSENTE = "A";
}

/**
 * Obtêm a lista de frequência
 * 
 * @return array
 */
function getFrequenciaList(): array
{
    return array(
        array('value' => Frequencia::PRESENTE, 'label' => 'Presente'),
        array('value' => Frequencia::AUSENTE, 'label' => 'Ausente')
    );
}

/**
 * Classe abstrata para enumerar os status de aprovação de ata
 */
abstract class AprovacaoAta
{
    const APROVADA = "S";
    const NAO_APROVADA = "N";
}

/**
 * Obtêm a lista de status de aprovação de ata
 * 
 * @return array
 */
function getAprovacaoAtaList(): array
{
    return array(
        array('value' => AprovacaoAta::APROVADA, 'label' => 'Aprovada'),
        array('value' => AprovacaoAta::NAO_APROVADA, 'label' => 'Não aprovada')
    );
}

/**
 * Classe abstrata para enumerar os status de inscrição
 */
abstract class StatusInscricao
{
    const APROVADA = "APR";
    const AGUARDANDO = "WAT";
    const RECUSADA = "DEN";
}

/**
 * Obtêm a lista de status de inscrição
 * 
 * @return array
 */
function getStatusInscricaoList(): array
{
    return array(
        array('value' => StatusInscricao::APROVADA, 'label' => 'Aprovada'),
        array('value' => StatusInscricao::AGUARDANDO, 'label' => 'Aguardando'),
        array('value' => StatusInscricao::RECUSADA, 'label' => 'Recusada')
    );
}

/**
 * Classe abstrata para enumerar o status de organização de igreja
 */
abstract class IgrejaOrganizacao
{
    const ORGANIZADA = "O";
    const CONGREGACAO = "C";
    const PONTO = "P";
}

/**
 * Classe abstrata para enumerar o status de profissão de fé
 */
abstract class ProfissaoFe
{
    const PROFESSO = "P";
    const BATIZADO = "B";
    const PARTICIPANTE = "A";
}

/**
 * Obtêm a lista de status de profissão de fé
 * 
 * @return array
 */
function getProfissaoFeList(): array
{
    return array(
        array('value' => ProfissaoFe::PROFESSO, 'label' => 'Professo(a)'),
        array('value' => ProfissaoFe::BATIZADO, 'label' => 'Batizado(a)'),
        array('value' => ProfissaoFe::PARTICIPANTE, 'label' => 'Participante/Visitante')
    );
}

/**
 * Classe abstrata para enumerar o status de registros financeiros (entrada/saída)
 */
abstract class RegistroFinanceiro
{
    const REALIZADO = "EXE";
    const PLANEJADO = "PLN";
    const ATRASADO = "DLY";
}

/**
 * Obtêm a lista de status de registro financeiro
 * 
 * @return array
 */
function getRegistroFinanceiroList(): array
{
    return array(
        array('value' => RegistroFinanceiro::REALIZADO, 'label' => 'Realizado'),
        array('value' => RegistroFinanceiro::PLANEJADO, 'label' => 'Planejado'),
        array('value' => RegistroFinanceiro::ATRASADO, 'label' => 'Atrasado')
    );
}

/**
 * Classe abstrata para enumerar os tipos de oficiais
 */
abstract class TipoOficiais
{
    const PASTOR = "REV";
    const EVANGELISTA = "EVG";
    const SEMINARISTA = "SEM";
    const PRESBITERO = "PRB";
    const DIACONO = "DIC";
    const MISSIONARIO = "MIS";
    const SUPERINTENDENTE = "SIT";
    const PROFESSOR = "PRF";
    const OUTRO = "OTR";
}

/**
 * Obtêm a lista de tipos de oficiais
 * 
 * @return array
 */
function getTipoOficiaisList(): array
{
    return array(
        array('value' => TipoOficiais::PASTOR, 'label' => 'Pastor'),
        array('value' => TipoOficiais::EVANGELISTA, 'label' => 'Evangelista'),
        array('value' => TipoOficiais::SEMINARISTA, 'label' => 'Seminarista'),
        array('value' => TipoOficiais::PRESBITERO, 'label' => 'Presbítero'),
        array('value' => TipoOficiais::DIACONO, 'label' => 'Diácono'),
        array('value' => TipoOficiais::MISSIONARIO, 'label' => 'Missionário'),
        array('value' => TipoOficiais::SUPERINTENDENTE, 'label' => 'Superintendente'),
        array('value' => TipoOficiais::PROFESSOR, 'label' => 'Professor'),
        array('value' => TipoOficiais::OUTRO, 'label' => 'Outro')
    );
}

/**
 * Classe abstrata para enumerar os status de disponibilidade de oficiais
 */
abstract class DisponibilidadeOficiais
{
    const ATIVO = "ATV";
    const EM_DISPONIBILIDADE = "DIS";
    const JUBILADO = "JUB";
}

/**
 * Obtêm a lista de status de disponibilidade de oficiais
 * 
 * @return array
 */
function getDisponibilidadeOficiaisList(): array
{
    return array(
        array('value' => DisponibilidadeOficiais::ATIVO, 'label' => 'Ativo'),
        array('value' => DisponibilidadeOficiais::EM_DISPONIBILIDADE, 'label' => 'Em disponibilidade'),
        array('value' => DisponibilidadeOficiais::JUBILADO, 'label' => 'Jubilado')
    );
}

/**
 * Classe abstrata para enumerar os tipos de secretário
 */
abstract class TiposSecretario
{
    const NONE = "-";
    const OFICIAL = "O";
    const AUXILIAR = "A";
}

/**
 * Obtêm a lista de tipos de secretário
 * 
 * @return array
 */
function getTiposSecretarioList(): array
{
    return array(
        array('value' => TiposSecretario::OFICIAL, 'label' => 'Oficial'),
        array('value' => TiposSecretario::AUXILIAR, 'label' => 'Auxiliar')
    );
}

/**
 * Classe abstrata para enumerar os status de votação
 */
abstract class VotacaoStat
{
    const AGUARDANDO = "WAT";
    const EM_VOTACAO = "EXE";
    const EM_APURACAO = "RES";
    const ENCERRADA = "END";
}

/**
 * Obtêm a lista de status de votação
 * 
 * @return array
 */
function getVotacaoStatusList(): array
{
    return array(
        array('value' => VotacaoStat::AGUARDANDO, 'label' => 'Aguardando'),
        array('value' => VotacaoStat::EM_VOTACAO, 'label' => 'Em votação'),
        array('value' => VotacaoStat::EM_APURACAO, 'label' => 'Em apuração'),
        array('value' => VotacaoStat::ENCERRADA, 'label' => 'Encerrada')
    );
}

/**
 * Classe abstrata para enumerar os responsáveis virtuais por atividades na agenda
 */
abstract class ResponsavelVirtual
{
    const CONSELHO = "000000000000000000000001";
    const JUNTA = "000000000000000000000002";
    const IGREJA = "000000000000000000000003";
}

function getResponsaveisVirtuaisList(): array
{
    return array(
        array('value' => ResponsavelVirtual::CONSELHO, 'label' => 'Conselho'),
        array('value' => ResponsavelVirtual::JUNTA, 'label' => 'Junta'),
        array('value' => ResponsavelVirtual::IGREJA, 'label' => 'Igreja')
    );
}

/**
 * Classe abstrata para enumerar as formas de pagamento suportadas pelo sistema
 */
abstract class FormasPagto
{
    const NONE = "---";
    const PAGSEGURO = "001";
    const DEPOSITO = "002";
    const BOLETOFACIL = "003";
}

function getFormasPagtoList(): array
{
    return array(
        array('value' => FormasPagto::PAGSEGURO, 'label' => 'Pagseguro'),
        array('value' => FormasPagto::DEPOSITO, 'label' => 'Depósito'),
        array('value' => FormasPagto::BOLETOFACIL, 'label' => 'Boleto Fácil')
    );
}

/**
 * Classe abstrata para enumerar os status de pagamento
 */
abstract class PagamentoStatus
{
    const NONE = "---";
    const AGUARDANDO = "WAT";
    const PAGO = "PAG";
    const DEVOLVIDO = "BCK";
    const RECUSADO = "DEN";
    const CANCELADO = "CAN";
    const PROBLEMA = "PRB";
}

/**
 * Obtêm a lista de pagamento
 * 
 * @return array
 */
function getPagamentoStatusList(): array
{
    return array(
        array('value' => PagamentoStatus::AGUARDANDO, 'label' => 'Aguardando'),
        array('value' => PagamentoStatus::PAGO, 'label' => 'Pago'),
        array('value' => PagamentoStatus::DEVOLVIDO, 'label' => 'Devolvido'),
        array('value' => PagamentoStatus::RECUSADO, 'label' => 'Recusado'),
        array('value' => PagamentoStatus::CANCELADO, 'label' => 'Cancelado'),
        array('value' => PagamentoStatus::PROBLEMA, 'label' => 'Com problemas')
    );
}

/**
 * Obtêm os campos possíveis ao formulário de inscrição em eventos
 * 
 * @return array
 */
function getFormularioInscricao(): array
{
    return array(
        array('field' => 'nome', 'label' => 'Nome', 'type' => 'text', 'checked' => true, 'needed' => true, 'scope' => 'common'),
        array('field' => 'email', 'label' => 'E-mail', 'type' => 'email', 'checked' => true, 'needed' => true, 'scope' => 'common'),
        array('field' => 'sexo', 'label' => 'Sexo', 'type' => 'sex', 'checked' => false, 'needed' => false, 'scope' => 'people'),
        array('field' => 'data_nascimento', 'label' => 'Data Nascimento', 'type' => 'date', 'checked' => false, 'needed' => false, 'scope' => 'people'),
        array('field' => 'estado_civil', 'label' => 'Estado Civil', 'type' => 'marital', 'checked' => false, 'needed' => false, 'scope' => 'people'),
        array('field' => 'telefone', 'label' => 'Telefone', 'type' => 'phone', 'checked' => false, 'needed' => false, 'scope' => 'people'),
        array('field' => 'celular_1', 'label' => 'Celular', 'type' => 'cellphone', 'checked' => false, 'needed' => false, 'scope' => 'people'),
        array('field' => 'celular_2', 'label' => 'Celular (1)', 'type' => 'cellphone', 'checked' => false, 'needed' => false, 'scope' => 'people'),
        array('field' => 'sinodo', 'label' => 'Sínodo', 'type' => 'sinod', 'checked' => false, 'needed' => false, 'scope' => 'church'),
        array('field' => 'presbiterio', 'label' => 'Presbitério', 'type' => 'presbytery', 'checked' => false, 'needed' => false, 'scope' => 'church'),
        array('field' => 'igreja', 'label' => 'Igreja', 'type' => 'church', 'checked' => false, 'needed' => false, 'scope' => 'church'),
        array('field' => 'sinodal', 'label' => 'Sinodal', 'type' => 's_sinod', 'checked' => false, 'needed' => false, 'scope' => 'society'),
        array('field' => 'federacao', 'label' => 'Federação', 'type' => 's_presbytery', 'checked' => false, 'needed' => false, 'scope' => 'society'),
        array('field' => 'sociedade', 'label' => 'Sociedade Local', 'type' => 's_church', 'checked' => false, 'needed' => false, 'scope' => 'society'),
        array('field' => 'delegado', 'label' => 'Delegado', 'type' => 'check', 'checked' => false, 'needed' => false, 'scope' => 'subscribe'),
        array('field' => 'cargo', 'label' => 'Cargo', 'type' => 'office', 'checked' => false, 'needed' => false, 'scope' => 'subscribe'),
        array('field' => 'credencial', 'label' => 'Credencial', 'type' => 'credential', 'checked' => false, 'needed' => false, 'scope' => 'subscribe')
    );
}

/**
 * Obtêm a estrutura de dados de opção de pagamento
 * 
 * @return array
 */
function getOpcoesPagtoStruct(): array
{
    return array(
        'id' => '',
        'forma' => '',
        'desconto' => 0.00,
        'taxa' => 0.00,
        'deposito' => array(
            'selected' => false,
            'banco' => '',
            'agencia' => '',
            'conta' => '',
            'favorecido' => '',
            'documento' => ''
        ),
        'pagseguro' => array(
            'selected' => false,
            'email' => '',
            'tokenl' => ''
        ),
        'boleto' => array(
            'selected' => false,
            'tokenl' => ''
        )
    );
}

/**
 * Obtêm a estrutura de dados de lote de pagamento
 * 
 * @return array
 */
function getLotePagtoStruct(): array
{
    return array(
        'id' => '',
        'nome' => '',
        'data_maxima' => '',
        'valor' => 0.00
    );
}

/**
 * 
 */
abstract class LimitesDeIdades
{
    const CRIANCA = 13;
}

/**
 * Valor inteiro default para campos inteiros que devem ser ignorados no processamento
 */
define('IGNORE_INT', -1);
