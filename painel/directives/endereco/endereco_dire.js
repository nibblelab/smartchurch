angular.module('SmartChurchPanel').controller('EnderecoDireCtrl', function($scope, $state, $dialogs, $ibge, $cep, $rootScope) {
    
    var vm = this;
    
    vm.dataFrm = {};
    
    vm.listUFs = [];
    vm.listCidades = [];
    vm.readonly = false;
    
    vm.setModel = function(obj) {
        if(obj != undefined) {
            vm.dataFrm = obj;
        }
    };
    
    vm.setUFs = function(obj) {
        if(obj != undefined) {
            vm.listUFs = obj;
        }
    };
    
    vm.setCidades = function(obj) {
        if(obj != undefined) {
            vm.listCidades = obj;
        }
    };
    
    vm.setreadonly = function(obj)  {
        if(obj != undefined) {
            vm.readonly = obj;
        }
    };
    
    vm.loadCidades = function(uf_id) {
        var r = vm.listCidades.filter(x => x.uf == uf_id);
        var nomes = [];
        for(var k in r) {
            nomes.push({
                id: r[k].id,
                label: r[k].nome,
                value: r[k].nome
            });
        }

        $('#cidade').autocomplete({
            source: nomes,
            select: function(event, ui) {
                event.preventDefault();
                vm.dataFrm.data.cidade.value = ui.item.value;
                $scope.$apply();
            }
        });
    };
    vm.selectUF = function() {
        if(vm.dataFrm.data.uf.value != '') {
            vm.dataFrm.data.cidade.value = '';
            vm.dataFrm.data.endereco.value = '';
            vm.dataFrm.data.bairro.value = '';
            var uf = vm.listUFs.filter(x => x.sigla == vm.dataFrm.data.uf.value);
            if(uf.length > 0) {
                vm.loadCidades(uf[0].id);
            }
        }
    };
    vm.getEnderecobyCEP = function() {
        if(vm.dataFrm.data.cep.value != '') {
            $cep.getAddress(vm.dataFrm.data.cep.value).then(function(r) {
                vm.dataFrm.data.uf.value = r.uf;
                vm.selectUF();
                vm.dataFrm.data.cidade.value = r.localidade;
                vm.dataFrm.data.endereco.value = r.logradouro;
                vm.dataFrm.data.bairro.value = r.bairro;
            }, function() {});
        }
    };
    
    
});

