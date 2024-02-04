angular.module('SmartChurchPanel').factory('EventoDTO', function () {
    
    var getDataForm = function () {
        return {
            id: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            nome: { value: '', notEmpty: true, valid: true, StringfyFrom: '' },
            descricao: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            data_ini: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            hora_ini: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            data_end: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            hora_end: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            inscricoes_ativas: { value: false, notEmpty: false, valid: true, StringfyFrom: '' },
            data_fim_inscricao: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            hora_fim_inscricao: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            formulario_inscricao: { value: [], notEmpty: false, valid: true, StringfyFrom: '' },
            valor: { value: 0.00, notEmpty: false, valid: true, StringfyFrom: '' },
            opcoes_pagto: { value: [], notEmpty: false, valid: true, StringfyFrom: '' },
            lotes: { value: [], notEmpty: false, valid: true, StringfyFrom: '' },
            tem_eleicoes: { value: false, notEmpty: false, valid: true, StringfyFrom: '' },
            credencial_digital: { value: false, notEmpty: false, valid: true, StringfyFrom: '' },
            data_max_delegados: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            hora_max_delegados: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            agenda: {
                data: {
                    endereco: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
                    numero: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
                    complemento: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
                    bairro: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
                    cidade: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
                    uf: { value: '--', notEmpty: false, valid: true, StringfyFrom: '' },
                    cep: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
                    site: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
                    facebook: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
                    instagram: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
                    youtube: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
                    vimeo: { value: '', notEmpty: false, valid: true, StringfyFrom: '' }
                }
            }
        };
    };
    
    var getDataSendForSinodal = function (sinodal) {
        return {
            id: '',
            sinodal: sinodal,
            nome: '',
            logo: '',
            descricao: '',
            time_ini: '',
            time_end: '',
            inscricoes_ativas: false,
            fim_inscricao: '',
            formulario_inscricao: '',
            valor: '',
            opcoes_pagto: '',
            lotes: '',
            tem_eleicoes: false,
            credencial_digital: false,
            data_delegados: '',
            agenda: {
                id: '',
                sinodal: sinodal,
                nome: '',
                logo: '',
                responsavel: sinodal,
                recorrente: false,
                dias_horarios: '',
                time_ini: '',
                time_end: '',
                endereco: '',
                numero: '',
                complemento: '',
                bairro: '',
                cidade: '',
                uf: '',
                cep: '',
                site: '',
                facebook: '',
                instagram: '',
                youtube: '',
                vimeo: '',
                observacoes: '',
                tags: []
            }
        };
    };
    
    return {
        getDataForm: getDataForm,
        getDataSendForSinodal: getDataSendForSinodal
    };
});