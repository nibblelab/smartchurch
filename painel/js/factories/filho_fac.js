angular.module('SmartChurchPanel').factory('FilhoDTO', function () {
    
    var getDataForm = function () {
        return {
            id: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            nome: { value: '', notEmpty: true, valid: true, StringfyFrom: '' },
            show_opts: { value: false, notEmpty: true, valid: true, StringfyFrom: '' },
            sexo: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            data_nascimento: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            crianca: { value: false, notEmpty: false, valid: true, StringfyFrom: '' },
            cadastrado: { value: false, notEmpty: false, valid: true, StringfyFrom: '' },
            mesma_igreja: { value: false, notEmpty: false, valid: true, StringfyFrom: '' }
        };
    };
    
    var getDataSend = function (pessoa) {
        return {
            id: '',
            responsavel: pessoa,
            nome: '',
            sexo: '',
            data_nascimento: ''
        };
    };
    
    return {
        getDataForm: getDataForm,
        getDataSend: getDataSend
    };
});