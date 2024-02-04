angular.module('SmartChurchPanel').controller('SysConfigCtrl', function($scope, $state, $stateParams, $localstorage, $dialogs, $rootScope, $timeout, $notifications, 
                    Perfil, TagAgenda, SysConfig) { 
    
    /* config */
    $scope.opTitle = $stateParams.title;
    $scope.search = $stateParams.search;
    $scope.data = $stateParams.data;
    $scope.perms = $stateParams.perms;
    $scope.id = $stateParams.id;
    $scope.objForm = 'Configurações do Sistema';
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
    $scope.listPerfis = [];
    $scope.listTags = [];
    Perfil.getAll('', '', '', 'nome,asc').then(function(r) {
        if(r.total > 0) {
            $scope.listPerfis = r.datas;
        }
        
        TagAgenda.getAll('', '', '', 'tag,asc').then(function(r) {
            if(r.total > 0) {
                $scope.listTags = r.datas;
            }
        
            $scope.$broadcast('preLoad');
        }, function(e) { console.log(e); $scope.testError(e); });
    }, function(e) { console.log(e); $scope.testError(e); });
    
    
    /* form */
    $scope.dataFrm = {
        isValid: false,
        data: {
            id: { value: '201410281411411185581553', notEmpty: false, valid: true, StringfyFrom: '' },
            perfil_membro: { value: '', notEmpty: true, valid: true, StringfyFrom: '' },
            perfil_cliente: { value: '', notEmpty: true, valid: true, StringfyFrom: '' },
            perfil_diretoria_sociedade: { value: '', notEmpty: true, valid: true, StringfyFrom: '' },
            perfil_federacao: { value: '', notEmpty: true, valid: true, StringfyFrom: '' },
            perfil_sinodal: { value: '', notEmpty: true, valid: true, StringfyFrom: '' },
            perfil_superintendente: { value: '', notEmpty: true, valid: true, StringfyFrom: '' },
            perfil_secretario: { value: '', notEmpty: true, valid: true, StringfyFrom: '' },
            perfil_ministerio: { value: '', notEmpty: true, valid: true, StringfyFrom: '' },
            perfil_pastor: { value: '', notEmpty: true, valid: true, StringfyFrom: '' },
            perfil_evangelista: { value: '', notEmpty: true, valid: true, StringfyFrom: '' },
            perfil_presbitero: { value: '', notEmpty: true, valid: true, StringfyFrom: '' },
            perfil_diacono: { value: '', notEmpty: true, valid: true, StringfyFrom: '' },
            perfil_referencia: { value: '', notEmpty: true, valid: true, StringfyFrom: '' },
            perfil_professor: { value: '', notEmpty: true, valid: true, StringfyFrom: '' },
            tag_evento: { value: '', notEmpty: true, valid: true, StringfyFrom: '' },
            tag_eleicao: { value: '', notEmpty: true, valid: true, StringfyFrom: '' }
        },
        toSend: {
            id: '201410281411411185581553',
            perfil_membro: '',
            perfil_cliente: '',
            perfil_diretoria_sociedade: '',
            perfil_federacao: '',
            perfil_sinodal: '',
            perfil_superintendente: '',
            perfil_secretario: '',
            perfil_ministerio: '',
            perfil_pastor: '',
            perfil_evangelista: '',
            perfil_presbitero: '',
            perfil_diacono: '',
            perfil_referencia: '',
            perfil_professor: '',
            tag_evento: '',
            tag_eleicao: ''
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
        }
    };
    
    if ($state.current.name.includes('editar') && $scope.id) {
        // edição - via id 
        $scope.$on('preLoad', function() {
            SysConfig.getMe($scope.id).then(function(r) {
                $scope.data = r;
                $scope.load();
            }, function(e) { console.log(e); $notifications.err("Não encontrei as configurações do sistema!"); });
        });
    }
    
    $scope.load = function() {
        for (var k in $scope.data) {
            if ($scope.dataFrm.data.hasOwnProperty(k)) {
                $scope.dataFrm.data[k].value = $scope.data[k];
            }
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
            SysConfig.edit($scope.id, $scope.dataFrm.toSend).then(function(r) {
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


