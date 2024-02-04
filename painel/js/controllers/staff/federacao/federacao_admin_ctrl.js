angular.module('SmartChurchPanel').controller('FederacaoAdminCtrl', function ($scope, $state, $stateParams, $localstorage, $notifications, $rootScope, $dialogs, $timeout,
                    $modulos, $generator,
                    Perfil, SysConfig, Federacao, Admin) { 
    
    /* config */
    $scope.opTitle = $stateParams.title;
    $scope.search = $stateParams.search;
    $scope.data = $stateParams.data;
    $scope.perms = $stateParams.perms;
    $scope.back = ($stateParams.back == '') ? 'dashboard.federacoes.buscar()' : $stateParams.back + '()';
    $scope.id = $stateParams.id;
    $scope.objForm = 'Admin';
    $scope.isCreate = false;

    /* verificação de permissão do state */
    if(!$scope.USER.doIHaveAccess($scope.user, $scope.perms)) {
        $state.go('SmartChurchPanel.sempermissao');
    }
    
    $scope.localPerms = {
        add: '',
        edit: '',
        changeStat: '',
        remove: ''
    };
    
    /* common */
    $scope.federacaoData = { nome: '' };
    $scope.listPerfis = [];
    $scope.listConfigs = [];
    Federacao.getMe($scope.id).then(function(r) {
        $scope.federacaoData = r;
        Perfil.getAllNoStaff().then(function(r) {
            if(r.total > 0) {
                $scope.listPerfis = r.datas;
            }
            
            SysConfig.getAll().then(function(r) {
                if(r.total > 0) {
                    $scope.listConfigs = r.datas;
                    $scope.dataFrm.data.perfil.value = $scope.listConfigs[0].perfil_federacao;
                }

                Admin.getAll('', '', '', '', '', $scope.id).then(function(r) {
                    if(r.total > 0) {
                        $scope.data = r.datas[0];
                        $scope.load();
                        
                    }
                    else {
                        $scope.isCreate = true;
                    }

                    $scope.$broadcast('preLoad');
                }, function(e) { console.log(e); $scope.testError(e); });
            }, function(e) { console.log(e); $scope.testError(e); });
        }, function(e) { console.log(e); $scope.testError(e); });
    }, function(e) {
        $state.go($scope.back.parseState().state, $scope.back.parseState().params); 
    });
    
    
    /* form */
    
    $scope.generatePassword = function() {
        var pwd = $generator.randomPwd(10);
        $scope.dataFrm.data.senha.value = pwd;
        $scope.dataFrm.data.r_senha.value = pwd;
        $dialogs.beforeNotify('Senha Gerada', pwd).then(function() {}, function() {});
    };
    
    $scope.generateEmail = function() {
        if($scope.dataFrm.data.id.value == '') {
            var email = $generator.randomEmail(10) + System.AT;
            $scope.dataFrm.data.email.value = email;
        }
    };
    
    $scope.dataFrm = {
        isValid: false,
        data: {
            id: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            perfil: { value: '', notEmpty: true, valid: true, StringfyFrom: '' },
            nome: { value: '', notEmpty: true, valid: true, StringfyFrom: '' },
            email: { value: '', notEmpty: true, valid: true, StringfyFrom: '' },
            senha: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            r_senha: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            modulos: { value: $modulos.getForm(), notEmpty: false, valid: true, StringfyFrom: '' }
        },
        toSend: {
            id: '',
            perfil: '',
            nome: '',
            email: '',
            senha: '',
            federacao: $scope.id,
            modulos: ''
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
                if ($scope.dataFrm.data[k].StringfyFrom != '') {
                    $scope.dataFrm.data[k].value = JSON.stringify($scope.dataFrm.data[$scope.dataFrm.data[k].StringfyFrom].value);
                }
                if ($scope.dataFrm.toSend.hasOwnProperty(k)) {
                    $scope.dataFrm.toSend[k] = $scope.dataFrm.data[k].value;
                }
            }
            
            var mods = '|';
            for(var k in $scope.dataFrm.data.modulos.value) {
                if($scope.dataFrm.data.modulos.value[k].checked) {
                    mods += $scope.dataFrm.data.modulos.value[k].id + ':|';
                }
            }
            
            $scope.dataFrm.toSend.modulos = mods;
        }
    };
    
    $scope.load = function() {
        
        $scope.dataFrm.data.id.value = $scope.data.id;
        $scope.dataFrm.data.perfil.value = $scope.data.perfil;
        $scope.dataFrm.data.nome.value = $scope.data.nome;
        $scope.dataFrm.data.email.value = $scope.data.email;
        
        for(var k in $scope.dataFrm.data.modulos.value) {
            $scope.dataFrm.data.modulos.value[k].checked = ($scope.data.modulos.indexOf($scope.dataFrm.data.modulos.value[k].id) > -1);
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
            var promise = {};
            if($scope.isCreate)
            {
                promise = Admin.createForFederacao($scope.dataFrm.toSend);
            }
            else
            {
                promise = Admin.edit($scope.dataFrm.toSend.id, $scope.dataFrm.toSend);
            }
            
            promise.then(function(r) {
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


