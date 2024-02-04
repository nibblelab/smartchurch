angular.module('SmartChurchPanel').controller('PessoaDireCtrl', function($state, $dialogs, $rootScope, $generator, $timeout) {
    
    var vm = this;
    
    vm.dataFrm = {};
    
    vm.igreja = '';
    vm.listEscolaridade = [];
    vm.listProfissoes = [];
    vm.listSexos = [];
    vm.listEstadosCivis = [];
    vm.listRelacoes = [];
    vm.Pessoa = {};
    vm.MembroDaIgreja = {};
    vm.FamiliaFactory = {};
    vm.USER = {};
    vm.user = {};
    vm.readonly = false;
    vm.casado = false;
    
    vm.setModel = function(obj) {
        if(obj != undefined) {
            vm.dataFrm = obj;
        }
    };
    
    vm.setIgreja = function(obj) {
        if(obj != undefined) {
            vm.igreja = obj;
        }
    };
    
    vm.setEscolaridades = function(obj) {
        if(obj != undefined) {
            vm.listEscolaridade = obj;
        }
    };
    
    vm.setProfissoes = function(obj) {
        if(obj != undefined) {
            vm.listProfissoes = obj;
        }
    };
    
    vm.setSexos = function(obj) {
        if(obj != undefined) {
            vm.listSexos = obj;
        }
    };
    
    vm.setEstadosCivis = function(obj) {
        if(obj != undefined) {
            vm.listEstadosCivis = obj;
        }
    };
    
    vm.setRelacoes = function(obj) {
        if(obj != undefined) {
            vm.listRelacoes = obj;
        }
    };
    
    vm.setPessoa = function(obj) {
        if(obj != undefined) {
            vm.Pessoa = obj;
        }
    };
    
    vm.setMembroDaIgreja = function(obj) {
        if(obj != undefined) {
            vm.MembroDaIgreja = obj;
        }
    };
    
    vm.setFamiliaFactory = function(obj) {
        if(obj != undefined) {
            vm.FamiliaFactory = obj;
        }
    };
    
    vm.setUSER = function(obj) {
        if(obj != undefined) {
            vm.USER = obj;
        }
    };
    
    vm.setuser = function(obj) {
        if(obj != undefined) {
            vm.user = obj;
        }
    };
    
    vm.setreadonly = function(obj)  {
        if(obj != undefined) {
            vm.readonly = obj;
        }
    };
    
    vm.bootDatePicker = function() {
        $('.datepicker').each(function(){ 
            $(this).datepicker('destroy');
            $(this).datepicker({
                todayHighlight: true,
                language: 'pt-BR',
                format: 'dd/mm/yyyy',
                orientation: 'bottom'
            });
        });        
    };    
     
    vm.checkCasado = function() {
        vm.casado = (vm.dataFrm.data.estado_civil.value == EstadoCivil.CASADO);
        if(vm.casado) {
            if($.isEmptyObject(vm.dataFrm.data.conjuge.value)) {
                vm.dataFrm.data.conjuge.value = vm.FamiliaFactory.getDataFormConjuge();
            }
            else {
                var conjuge_from_server = vm.dataFrm.data.conjuge.value;
                vm.dataFrm.data.conjuge.value = vm.FamiliaFactory.getDataFormConjuge();
                vm.dataFrm.data.conjuge.value.id.value = conjuge_from_server.id;
                vm.dataFrm.data.conjuge.value.nome.value = conjuge_from_server.nome_externo;
                if(conjuge_from_server.parente != null) {
                    vm.Pessoa.getMe(conjuge_from_server.parente).then(function(r) {
                        vm.dataFrm.data.conjuge.value.id_conjuge.value = r.id;
                        vm.dataFrm.data.conjuge.value.nome.value = r.nome;
                        vm.dataFrm.data.conjuge.value.cadastrado.value = true;
                        vm.findConjugeMembresia(); 
                    }, function(e) {
                        console.log(e);
                    });
                }
            }
        }
        else {
            if(!$.isEmptyObject(vm.dataFrm.data.conjuge.value) && (vm.dataFrm.data.conjuge.value.id.value != "")) {
                vm.dataFrm.data.conjuge.value.remove.value = true;
            }            
        }
    };
    
    vm.findConjugeMembresia = function() {
        if(!$.isEmptyObject(vm.MembroDaIgreja)) {
            // pessoa foi encontrada. Verifique se ela é membro dessa igreja
            vm.MembroDaIgreja.check(vm.dataFrm.data.conjuge.value.id_conjuge.value, vm.igreja).then(function(r) {
                if(r.exists) {
                    vm.dataFrm.data.conjuge.value.mesma_igreja.value = true;
                }
            }, function() {});
        }
    };
    
    vm.tryFindConjuge = function() {
        vm.dataFrm.data.conjuge.value.id_conjuge.value = "";
        vm.dataFrm.data.conjuge.value.cadastrado.value = false;
        vm.dataFrm.data.conjuge.value.mesma_igreja.value = false;
        vm.Pessoa.getAllbyNome(vm.dataFrm.data.conjuge.value.nome.value).then(function(r) {
            if(r.total > 0) {
                var conjuge = r.datas[0];
                vm.dataFrm.data.conjuge.value.id_conjuge.value = conjuge.id;
                vm.dataFrm.data.conjuge.value.cadastrado.value = true;                
                vm.findConjugeMembresia();     
            }
        }, function(e) {
            console.log(e);
        });
    };
       
    vm.updateFilhoSexo = function(f) {
        if(f.sexo.value == Sexo.MASCULINO) {
            $('.filho[data-id="'+f.id.value+'"]').removeClass('menina');
            $('.filho[data-id="'+f.id.value+'"]').addClass('menino');
        }
        else if(f.sexo.value == Sexo.FEMININO) {
            $('.filho[data-id="'+f.id.value+'"]').removeClass('menino');
            $('.filho[data-id="'+f.id.value+'"]').addClass('menina');
        }
        else {
            $('.filho[data-id="'+f.id.value+'"]').removeClass('menino');
            $('.filho[data-id="'+f.id.value+'"]').removeClass('menina');
        }        
    };
    
    vm.addFilho = function() {
        
        if(vm.dataFrm.data.filhos.value.length > 1) {
            if(vm.dataFrm.data.filhos.value[vm.dataFrm.data.filhos.value.length-1].nome.value == '') {
                return;
            }
        }
        
        var filho = vm.FamiliaFactory.getDataFormFilho();
        filho.id.value = $generator.randomId(24);
        vm.dataFrm.data.filhos.value.push(filho);
        $timeout(function () { vm.bootDatePicker(); }, 500);
    };
    
    vm.handleFilhos = function() {
        if(vm.dataFrm.data.tem_filhos.value) {
            vm.addFilho();
        }
        else {
            for (var i in vm.dataFrm.data.filhos.value) {
                vm.dataFrm.data.filhos.value[i].remove.value = true;
            }         
        }
    };
    
    vm.loadFilhos = function() {
        if(vm.dataFrm.data.tem_filhos.value) {
            var temp_filhos = [];
            for (var i in vm.dataFrm.data.filhos.value) {
                var filho = vm.FamiliaFactory.getDataFormFilho();
                filho.id.value = vm.dataFrm.data.filhos.value[i].id;
                filho.associacao_id.value = vm.dataFrm.data.filhos.value[i].associacao_id;
                filho.nome.value = vm.dataFrm.data.filhos.value[i].nome;
                filho.sexo.value = vm.dataFrm.data.filhos.value[i].sexo;
                filho.data_nascimento.value = moment(vm.dataFrm.data.filhos.value[i].data_nascimento).format('DD/MM/YYYY');
                filho.crianca.value = vm.dataFrm.data.filhos.value[i].crianca;
                filho.cadastrado.value = vm.dataFrm.data.filhos.value[i].cadastrado;
                filho.show_opts.value = true;
                filho.cannot_change.value = !filho.crianca.value;
                temp_filhos.push(filho);
            }
            while (vm.dataFrm.data.filhos.value.length > 0) {
                vm.dataFrm.data.filhos.value.pop();
            }
            vm.dataFrm.data.filhos.value = temp_filhos;
            $timeout(function () { 
                for (var i in vm.dataFrm.data.filhos.value) {
                    vm.updateFilhoSexo(vm.dataFrm.data.filhos.value[i]);
                    if(!$.isEmptyObject(vm.MembroDaIgreja)) {
                        // pessoa foi encontrada. Verifique se ela é membro dessa igreja
                        vm.MembroDaIgreja.check(vm.dataFrm.data.filhos.value[i].id.value, vm.igreja).then(function(r) {
                            if(r.exists) {
                                vm.dataFrm.data.filhos.value[i].mesma_igreja.value = true;
                            }
                        }, function() {});
                    }
                } 
                vm.addFilho();
                vm.bootDatePicker();
            }, 500);  
        }        
    };
        
    vm.procFilhoNome = function(f) {
        if(f.nome.value != "" && !f.cadastrado.value) {
            /* procure a pessoa pelo nome */
            f.cadastrado.value = false;
            f.mesma_igreja.value = false;
            vm.Pessoa.getAllbyNome(f.nome.value).then(function(r) {
                if(r.total > 0) {
                    var pessoa = r.datas[0];
                    f.id.value = pessoa.id;
                    if(pessoa.data_nascimento != null) {
                        var data_nascimento_filho = moment(pessoa.data_nascimento);
                        var now = moment();
                        f.data_nascimento.value = data_nascimento_filho.format('DD/MM/YYYY');
                        if(now.diff(data_nascimento_filho, 'years', false) > LimitesDeIdades.CRIANCA) {
                            f.cannot_change.value = true;
                        }
                    }
                    f.sexo.value = pessoa.sexo;
                    f.cadastrado.value = true;
                    
                    $timeout(function () { vm.updateFilhoSexo(f); }, 100);                    
                                        
                    if(!$.isEmptyObject(vm.MembroDaIgreja)) {
                        // pessoa foi encontrada. Verifique se ela é membro dessa igreja
                        vm.MembroDaIgreja.check(f.id.value, vm.igreja).then(function(r) {
                            if(r.exists) {
                                f.mesma_igreja.value = true;
                            }
                        }, function() {});
                    }
                    f.show_opts.value = true;
                }
                else {
                    // pessoa não foi encontrada
                    f.show_opts.value = true;
                }
            }, function(e) {
                console.log(e);
            });
            vm.addFilho();
        }
        else if(f.nome.value != "" && f.cadastrado.value) {}
        else if(f.nome.value == "" && f.cadastrado.value) {
            for (var i = vm.dataFrm.data.filhos.value.length - 1; i >= 0; --i) {
                if (vm.dataFrm.data.filhos.value[i].id.value == f.id.value) {
                    vm.dataFrm.data.filhos.value[i].remove.value = true;
                }
            }
        }
        else {
            for (var i = vm.dataFrm.data.filhos.value.length - 1; i >= 0; --i) {
                if (vm.dataFrm.data.filhos.value[i].id.value == f.id.value) {
                    vm.dataFrm.data.filhos.value.splice(i,1);
                }
            }
        }
    };
    
    $timeout(function () { 
        vm.bootDatePicker();
        vm.checkCasado();
        vm.loadFilhos();
    }, 500);
    
    
});

