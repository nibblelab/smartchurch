angular.module('SmartChurchPanel').controller('AgendaDireCtrl', function($scope, $state, $dialogs, $ibge, $cep, $rootScope) {
    
    var vm = this;
    
    vm.dataFrm = {};
    vm.listTiposResponsaveis = [];
    vm.listResponsaveis = [];
    
    vm.setModel = function(obj) {
        if(obj != undefined) {
            vm.dataFrm = obj;
        }
    };
    
    vm.setTiposResponsaveis = function(obj) {
        if(obj != undefined) {
            vm.listTiposResponsaveis = obj;
        }
    };
    
    vm.setResponsaveis = function(obj) {
        if(obj != undefined) {
            vm.listResponsaveis = obj;
        }
    };
    
    vm.filterResponsavel = function(v, data_clear) {
        vm.listResponsaveis.forEach(function (element, index, array) {
            array[index].show = true;
        });
        if(v != '') {
            for(var k in vm.listResponsaveis) {
                if(vm.listResponsaveis[k].tipo != v) {
                    vm.listResponsaveis[k].show = false;
                }
            }
        }
        
        if(data_clear != undefined) {
            vm.dataFrm.data[data_clear].value = '';
        }
    };
    
    vm.addDiaHorario = function() {
        vm.dataFrm.data.dias_horarios.value.push({
            id: getRandomId(),
            domingo: false,
            segunda: false,
            terca: false,
            quarta: false,
            quinta: false,
            sexta: false,
            sabado: false,
            inicio: '',
            termino: ''
        });
    };
    
    $('.datepicker').datepicker({
        todayHighlight: true,
        language: 'pt-BR',
        format: 'dd/mm/yyyy',
        orientation: 'bottom'
    });
    
});