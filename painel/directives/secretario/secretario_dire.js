angular.module('SmartChurchPanel').controller('SecretarioDireCtrl', function() {
    
    var vm = this;
    
    vm.dataFrm = {};
    vm.listPerfis = [];
    
    vm.setModel = function(obj) {
        if(obj != undefined) {
            vm.dataFrm = obj;
        }
    };
    
    vm.setPerfis = function(obj) {
        if(obj != undefined) {
            vm.listPerfis = obj;
        }
    };
    
    $('.datepicker').datepicker({
        todayHighlight: true,
        language: 'pt-BR',
        format: 'dd/mm/yyyy',
        orientation: 'bottom'
    });
    
    
});