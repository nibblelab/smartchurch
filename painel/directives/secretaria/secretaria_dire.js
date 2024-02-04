angular.module('SmartChurchPanel').controller('SecretariaDireCtrl', function() {
    
    var vm = this;
    
    vm.dataFrm = {};
    
    vm.setModel = function(obj) {
        if(obj != undefined) {
            vm.dataFrm = obj;
        }
    };
    
    
    
});