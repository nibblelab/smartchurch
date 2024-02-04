angular.module('SmartChurchPanel').controller('DadosSinodalCtrl', function ($scope, $state, $stateParams, $localstorage, $notifications, 
                    $rootScope, $dialogs, $context, $cache,
                    Sinodo, Nacional, Sinodal) { 
    
    /* config */
    $scope.perms = $stateParams.perms;
    $scope.data = {};
    $scope.isCreate = false;
    $scope.sinodal = $context.getSinodalContext();
    $scope.sinodalData = $rootScope.USER.getContextByKeyAndId(Contexts.SINODAIS, $scope.sinodal);

    /* verificação de permissão do state */
    if(!$scope.USER.doIHaveAccess($scope.user, $scope.perms)) {
        $state.go('SmartChurchPanel.sempermissao');
    }
    
    /* common */
    $scope.listReferencias = $cache.get().sociedades;
    $scope.listSinodos = [];
    $scope.listNacionais = [];
    Sinodo.getAll().then(function(r) {
        if(r.total > 0) {
            $scope.listSinodos = r.datas;
        }

        Nacional.getAll().then(function(r) {
            if(r.total > 0) {
                $scope.listNacionais = r.datas;
            }

            $scope.$broadcast('preLoad');
        }, function(e) { console.log(e); $scope.testError(e); });
    }, function(e) { console.log(e); $scope.testError(e); });
    
    /* form */
    $scope.dataFrm = {
        isValid: false,
        data: {
            id: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            sinodo: { value: '', notEmpty: true, valid: true, StringfyFrom: '' },
            nacional: { value: '', notEmpty: true, valid: true, StringfyFrom: '' },
            reference: { value: '', notEmpty: true, valid: true, StringfyFrom: '' },
            nome: { value: '', notEmpty: true, valid: true, StringfyFrom: '' },
            email: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            sigla: { value: '', notEmpty: true, valid: true, StringfyFrom: '' },
            fundacao: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            site: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            facebook: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            instagram: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            youtube: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            vimeo: { value: '', notEmpty: false, valid: true, StringfyFrom: '' }
        },
        toSend: {
            id: $scope.sinodal,
            sinodo: '',
            nacional: '',
            reference: '',
            nome: '',
            email: '',
            sigla: '',
            fundacao: '',
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
        }
    };
        
    $scope.$on('preLoad', function() {
        Sinodal.getMe($scope.sinodal).then(function(r) {
            $scope.data = r;
            $scope.load();
        }, function(e) {  });
    });
    
    $scope.load = function() {
        for (var k in $scope.data) {
            if ($scope.dataFrm.data.hasOwnProperty(k)) {
                $scope.dataFrm.data[k].value = $scope.data[k];
            }
        }
        
        if($scope.dataFrm.data.fundacao.value != null) {
            $scope.dataFrm.data.fundacao.value = moment($scope.dataFrm.data.fundacao.value).format('DD/MM/YYYY');
        }
        else {
            $scope.dataFrm.data.fundacao.value = '';
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
            Sinodal.edit($scope.sinodal, $scope.dataFrm.toSend).then(function(r) {
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
