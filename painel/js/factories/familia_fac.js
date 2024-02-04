angular.module('SmartChurchPanel').factory('FamiliaDTO', function () {
    
    /*********************** Filhos ****************************/
    var getDataFormFilho = function () {
        return {
            id: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            nome: { value: '', notEmpty: true, valid: true, StringfyFrom: '' },
            show_opts: { value: false, notEmpty: true, valid: true, StringfyFrom: '' },
            sexo: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            data_nascimento: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            crianca: { value: false, notEmpty: false, valid: true, StringfyFrom: '' },
            cannot_change: { value: false, notEmpty: false, valid: true, StringfyFrom: '' },
            cadastrado: { value: false, notEmpty: false, valid: true, StringfyFrom: '' },
            mesma_igreja: { value: false, notEmpty: false, valid: true, StringfyFrom: '' },
            associacao_id: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            remove: { value: false, notEmpty: false, valid: true, StringfyFrom: '' }
        };
    };
    
    var getDataSendFilho = function (pessoa, dataFrm) {
        var data = [];
        for (var d in dataFrm) {
            data.push({
                id: dataFrm[d].id.value,
                responsavel: pessoa,
                nome: dataFrm[d].nome.value,
                sexo: dataFrm[d].sexo.value,
                data_nascimento: dataFrm[d].data_nascimento.value,
                cadastrado: dataFrm[d].cadastrado.value,
                associacao_id: dataFrm[d].associacao_id.value,
                remove: dataFrm[d].remove.value
            });
        }
        return data;
    };
    
    /********************* Conjuge ****************************/
    var getDataFormConjuge = function() {
        return {
            id: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            nome: { value: '', notEmpty: true, valid: true, StringfyFrom: '' },
            id_conjuge: { value: '', notEmpty: true, valid: true, StringfyFrom: '' },
            cadastrado: { value: false, notEmpty: false, valid: true, StringfyFrom: '' },
            mesma_igreja: { value: false, notEmpty: false, valid: true, StringfyFrom: '' },
            remove: { value: false, notEmpty: false, valid: true, StringfyFrom: '' }
        };
    };
    
    var getDataSendConjuge = function (pessoa, dataFrm) {
        return {
            id: dataFrm.id.value,
            pessoa: pessoa,
            nome: dataFrm.nome.value,
            id_conjuge: dataFrm.id_conjuge.value,
            cadastrado: dataFrm.cadastrado.value,
            remove: dataFrm.remove.value
        };
    };
    
    return {
        getDataFormFilho: getDataFormFilho,
        getDataSendFilho: getDataSendFilho,
        getDataFormConjuge: getDataFormConjuge,
        getDataSendConjuge: getDataSendConjuge
    };
});