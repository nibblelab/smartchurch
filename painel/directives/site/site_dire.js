angular.module('SmartChurchPanel').controller('SiteDireCtrl', function() {
    
    var vm = this;
    
    vm.dataFrm = {};
    vm.readonly = false;
    
    vm.setModel = function(obj) {
        if(obj != undefined) {
            vm.dataFrm = obj;
        }
    };
    
    vm.setreadonly = function(obj)  {
        if(obj != undefined) {
            vm.readonly = obj;
        }
    };
    
});

