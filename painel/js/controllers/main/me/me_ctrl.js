angular.module('SmartChurchPanel').controller('MeCtrl', function ($scope, $state, $stateParams, $localstorage, $notifications, $rootScope, $dialogs, $timeout, 
                    $ibge, $cache,
                    FamiliaDTO,
                    Perfil, Profissao, Doacao, Necessidade, SysConfig, Pessoa) { 
    
    /* config */
    $scope.perms = $stateParams.perms;
    $scope.data = {};
    $scope.igreja = '';

    /* verificação de permissão do state */
    if(!$scope.USER.doIHaveAccess($scope.user, $scope.perms)) {
        $state.go('SmartChurchPanel.sempermissao');
    }
    
    /* common */
    $scope.listStatus = $cache.get().status;
    $scope.listSexos = $cache.get().sexo;
    $scope.listEstadosCivis = $cache.get().estado_civil;
    $scope.listEscolaridade = $cache.get().escolaridade;
    $scope.listUFs = $cache.get().ufs;
    $scope.listCidades = $cache.get().cidades;
    $scope.listRelacoes = $cache.get().relacao_familiar;
    $scope.listProfissoes = [];
    $scope.listDoacoes = [];
    $scope.listNecessidadesEspeciais = [];
    $scope.listPerfis = [];
    $scope.listConfigs = [];
    $scope.familiaFactory = FamiliaDTO;
    Profissao.getAll('', '', '', 'nome,asc').then(function(r) {
        if(r.total > 0) {
            $scope.listProfissoes = r.datas;
        }

        Doacao.getAll('', '', '', 'nome,asc').then(function(r) {
            if(r.total > 0) {
                $scope.listDoacoes = r.datas;
            }

            Necessidade.getAll('', '', '', 'nome,asc').then(function(r) {
                if(r.total > 0) {
                    $scope.listNecessidadesEspeciais = r.datas;
                }

                Perfil.getAllNoStaff().then(function(r) {
                    if(r.total > 0) {
                        $scope.listPerfis = r.datas;
                    }

                    SysConfig.getAll().then(function(r) {
                        if(r.total > 0) {
                            $scope.listConfigs = r.datas;
                        }

                        $scope.$broadcast('preLoad');
                    }, function(e) { console.log(e); $scope.testError(e); });
                }, function(e) { console.log(e); $scope.testError(e); });
            }, function(e) { console.log(e); $scope.testError(e); });
        }, function(e) { console.log(e); $scope.testError(e); });
    }, function(e) { console.log(e); $scope.testError(e); });
    
    
    
    /* form */
    $scope.dataFrm = {
        isValid: false,
        data: {
            id: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            pessoa_id: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            nome: { value: '', notEmpty: true, valid: true, StringfyFrom: '' },
            perfil: { value: '', notEmpty: true, valid: true, StringfyFrom: '', show: false },
            email: { value: '', notEmpty: true, valid: true, StringfyFrom: '' },
            senha: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            r_senha: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            sexo: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            data_nascimento: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            crianca: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            responsavel: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            estado_civil: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            especial: { value: false, notEmpty: false, valid: true, StringfyFrom: '' },
            escolaridade: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            profissao: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            necessidades_especiais: { value: [], notEmpty: false, valid: true, StringfyFrom: '' },
            telefone: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            celular_1: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            celular_2: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            pai: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            mae: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            conjuge: { value: {}, notEmpty: false, valid: true, StringfyFrom: '' },
            tem_filhos: { value: false, notEmpty: false, valid: true, StringfyFrom: '' },
            filhos: { value: [], notEmpty: false, valid: true, StringfyFrom: '' },
            naturalidade: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            nacionalidade: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            doacoes: { value: [], notEmpty: false, valid: true, StringfyFrom: '' },
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
        },
        toSend: {
            id: '',
            perfil: '',
            nome: '',
            email: '',
            senha: '',
            sexo: '',
            data_nascimento: '',
            crianca: '',
            responsavel: '',
            estado_civil: '',
            escolaridade: '',
            profissao: '',
            telefone: '',
            celular_1: '',
            celular_2: '',
            pai: '',
            mae: '',
            conjuge: {},
            tem_filhos: false,
            filhos: [],
            naturalidade: '',
            nacionalidade: '',
            doacoes: [],
            necessidades_especiais: [],
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
            vimeo: ''
        },
        validate: function() {
            var valid = true;
            for (var k in $scope.dataFrm.data) {
                if ($scope.dataFrm.data[k].notEmpty) {
                    if ($scope.dataFrm.data[k].value == '') {
                        valid = false;
                        $scope.dataFrm.data[k].valid = false;
                    }
                }
            }
            
            $scope.dataFrm.isValid = valid;
            
            $scope.$apply();
        },
        prepare: function() {
            for (var k in $scope.dataFrm.data) {
                if ($scope.dataFrm.toSend.hasOwnProperty(k)) {
                    $scope.dataFrm.toSend[k] = $scope.dataFrm.data[k].value;
                }
            }
                        
            if(($scope.dataFrm.toSend.estado_civil == EstadoCivil.CASADO) || 
                    (!$.isEmptyObject($scope.dataFrm.data.conjuge.value) && $scope.dataFrm.data.conjuge.value.remove.value)) {
                $scope.dataFrm.toSend.conjuge = $scope.familiaFactory.getDataSendConjuge($scope.dataFrm.toSend.id, $scope.dataFrm.data.conjuge.value);
            }
                        
            if($scope.dataFrm.toSend.tem_filhos) {
                $scope.dataFrm.toSend.filhos = $scope.familiaFactory.getDataSendFilho($scope.dataFrm.toSend.id, $scope.dataFrm.data.filhos.value);
            }
            
        }
    };
    
    // services e dados padrão para as diretivas
    $scope.Pessoa = Pessoa;
    $scope.MembroDaIgreja = {};
    $scope.perfilDefault = "";
    
    // monte os arrays de seleção de necessidades e doações
    $scope.mountNecessidadesEspeciais = function(old) {
        $scope.dataFrm.data.necessidades_especiais.value = [];
        for(var k in $scope.listNecessidadesEspeciais) {
            var checked = (old != undefined) ? old.includes($scope.listNecessidadesEspeciais[k].id) : false;
            $scope.dataFrm.data.necessidades_especiais.value.push({
                checked: checked,
                id: $scope.listNecessidadesEspeciais[k].id,
                label: $scope.listNecessidadesEspeciais[k].nome
            });
        }
    };
    $scope.mountDoacoes = function(old) {
        $scope.dataFrm.data.doacoes.value = [];
        for(var k in $scope.listDoacoes) {
            var checked = (old != undefined) ? old.includes($scope.listDoacoes[k].id) : false;
            $scope.dataFrm.data.doacoes.value.push({
                checked: checked,
                id: $scope.listDoacoes[k].id,
                label: $scope.listDoacoes[k].nome
            });
        }
    };
    
    // edição - via id 
    $scope.$on('preLoad', function() {
        Pessoa.getMe($scope.user.id).then(function(r) {
            $scope.data = r;
            $scope.load();
        }, function(e) { });
    });
    
    $scope.load = function() {
                        
        for (var k in $scope.data) {
            if ($scope.dataFrm.data.hasOwnProperty(k)) {
                $scope.dataFrm.data[k].value = $scope.data[k];
            }
        }
        
        $scope.mountNecessidadesEspeciais($scope.data.necessidades_especiais);
        $scope.mountDoacoes($scope.data.doacoes);
        
        $scope.dataFrm.data.pessoa_id.value = $scope.user.id;
        
        if($scope.dataFrm.data.data_nascimento.value != null) {
            $scope.dataFrm.data.data_nascimento.value = moment($scope.dataFrm.data.data_nascimento.value).format('DD/MM/YYYY');
        }
        else {
            $scope.dataFrm.data.data_nascimento.value = '';
        }
    };
        
    /* save */
    $('#dataFrm').validate({
        submit: {
            settings: {
                inputContainer: '.form-group',
                errorListClass: 'form-control-error',
                errorClass: 'has-danger'
            },
            callback: {
                onSubmit: function(node, formData) {
                    $scope.doSave();
                },
                onError: function (node, globalError) {
                    $notifications.err("Há campos incorretos!");
                }
            }
        }
    });
    
    $scope.doSave = function() {
        $scope.dataFrm.validate();
        if ($scope.dataFrm.isValid) {
            $scope.dataFrm.prepare();
            Pessoa.edit($scope.user.id, $scope.dataFrm.toSend).then(function(r) {
                $dialogs.onSave().then(function() {
                    $state.reload();
                }, function() {});
            }, function(e) {
                $notifications.err(e);
            });
        } else {
            $notifications.err("Há campos incorretos!");
        }
    };
    
    
});


