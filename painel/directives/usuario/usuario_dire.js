angular.module('SmartChurchPanel').controller('UsuarioDireCtrl', function($state, $dialogs, $rootScope, $generator) {
    
    var vm = this;
    
    vm.dataFrm = {};
    
    vm.igreja = '';
    vm.listPerfis = [];
    vm.Pessoa = {};
    vm.MembroDaIgreja = {};
    vm.checkNome = false;
    vm.mailValidated = false;
    vm.perfilDefault = false;
    vm.readonly = false;
    
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
    
    vm.setPerfis = function(obj) {
        if(obj != undefined) {
            vm.listPerfis = obj;
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
    
    vm.setCheckNome = function(obj) {
        if(obj != undefined) {
            vm.checkNome = obj;
        }
    };
    
    vm.setPerfilDefault = function(obj) {
        if(obj != undefined) {
            vm.perfilDefault = obj;
        }
    };
    
    vm.setreadonly = function(obj)  {
        if(obj != undefined) {
            vm.readonly = obj;
        }
    };
    
    vm.clearPessoaData = function() {
        for (var k in vm.dataFrm.data) {
            vm.dataFrm.data[k].value = '';
        }
        vm.dataFrm.toSend.pessoa.id = '';
        vm.dataFrm.data.tem_filhos.value = false;
        vm.dataFrm.data.comungante.value = false;
        vm.dataFrm.data.perfil.value = vm.perfilDefault;
        vm.mailValidated = false;
    };
    vm.checkUsuario = function() {
        if(vm.dataFrm.data.nome.value != '' && vm.checkNome) {
            /* procure a pessoa pelo nome */
            vm.Pessoa.getAllbyNome(vm.dataFrm.data.nome.value).then(function(r) {
                if(r.total > 0) {
                    if(r.datas[0].id != vm.dataFrm.data.pessoa_id.value) {
                        for (var k in r.datas[0]) {
                            if (vm.dataFrm.data.hasOwnProperty(k) && k != 'id') {
                                vm.dataFrm.data[k].value = r.datas[0][k];
                            }
                        }

                        if(vm.dataFrm.data.hasOwnProperty('data_nascimento') && vm.dataFrm.data.data_nascimento.value != null) {
                            vm.dataFrm.data.data_nascimento.value = moment(vm.dataFrm.data.data_nascimento.value).format('DD/MM/YYYY');
                        }
                        else {
                            vm.dataFrm.data.data_nascimento.value = '';
                        }

                        vm.dataFrm.data.pessoa_id.value = r.datas[0].id;
                        vm.mailValidated = true;

                        if(!$.isEmptyObject(vm.MembroDaIgreja)) {
                            // pessoa foi encontrada. Verifique se ela já não é membro dessa igreja
                            vm.MembroDaIgreja.check(vm.dataFrm.data.pessoa_id.value, vm.igreja).then(function(r) {
                                if(r.exists) {
                                    $dialogs.beforeNotify('Membro já cadastrado!', 
                                                    vm.dataFrm.data.nome.value + " já é um membro da igreja")
                                            .then(function() {
                                                $state.reload();
                                            });
                                }
                            }, function() {});
                        }
                    }
                }
                else {
                    // pessoa não foi encontrada
                    if(vm.dataFrm.toSend.pessoa.id != '') {
                        vm.clearPessoaData();
                    }
                }
            }, function() {});
        }
    };
    
    vm.checkEmail = function() {
        if(vm.dataFrm.data.email.value != '' && !vm.mailValidated) {
            /* procure a pessoa pelo email */
            vm.Pessoa.getAllbyEmail(vm.dataFrm.data.email.value).then(function(r) {
                if(r.total > 0) {
                    for(var k in r.datas) {
                        if(r.datas[k].id != vm.dataFrm.data.pessoa_id.value) {
                            $dialogs.beforeNotify('E-mail já cadastrado!', 
                                    vm.dataFrm.data.email.value + " já está cadastrado para outro usuário.")
                            .then(function() {
                                vm.dataFrm.data.email.value = '';
                            });
                            break;
                        }
                    }
                }
                else {
                }
            }, function() {});
        }
    };
        
    vm.generatePassword = function() {
        var pwd = $generator.randomPwd(10);
        vm.dataFrm.data.senha.value = pwd;
        vm.dataFrm.data.r_senha.value = pwd;
        $dialogs.beforeNotify('Senha Gerada', pwd).then(function() {}, function() {});
    };
    
    vm.generateEmail = function() {
        var email = $generator.randomEmail(10) + System.AT;
        vm.dataFrm.data.email.value = email;
    };
    
});

