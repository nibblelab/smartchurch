angular.module('SmartChurchPanel').controller('DadosIgrejaCtrl', function ($scope, $state, $stateParams, $localstorage, $notifications, 
                    $rootScope, $dialogs, $context, $cache,
                    Data, Sinodo, Presbiterio, Igreja) { 
    
    /* config */
    $scope.perms = $stateParams.perms;
    $scope.data = {};
    $scope.isCreate = false;
    $scope.igreja = $context.getIgrejaContext();
    $scope.igrejaData = $rootScope.USER.getContextByKeyAndId(Contexts.IGREJAS, $scope.igreja);

    /* verificação de permissão do state */
    if(!$scope.USER.doIHaveAccess($scope.user, $scope.perms)) {
        $state.go('SmartChurchPanel.sempermissao');
    }
    
    /* common */
    $scope.listSinodos = [];
    $scope.listPresbiterios = [];
    $scope.listUFs = $cache.get().ufs;
    $scope.listCidades = $cache.get().cidades;
    Sinodo.getAll('', '', '', 'nome,asc').then(function(r) {
        if(r.total > 0) {
            $scope.listSinodos = r.datas;
        }

        Presbiterio.getAll('', '', '', 'nome,asc').then(function(r) {
            if(r.total > 0) {
                for(var k in r.datas) {
                    r.datas[k]['show'] = true;
                }
                $scope.listPresbiterios = r.datas;
            }

            $scope.$broadcast('preLoad');
        }, function(e) { console.log(e); $scope.testError(e); });
    }, function(e) { console.log(e); $scope.testError(e); });
    $scope.filterPresbiterios = function(v) {
        $scope.listPresbiterios.forEach(function (element, index, array) {
            array[index].show = true;
        });
        if(v != '') {
            for(var k in $scope.listPresbiterios) {
                if($scope.listPresbiterios[k].sinodo != v) {
                    $scope.listPresbiterios[k].show = false;
                }
            }
        }
    };
    
    /* form */
    $scope.dataFrm = {
        isValid: false,
        data: {
            id: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            sinodo: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            presbiterio: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            nome: { value: '', notEmpty: true, valid: true, StringfyFrom: '' },
            fundacao: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            telefone: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            email: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
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
            id: $scope.igreja,
            sinodo: '',
            presbiterio: '',
            nome: '',
            fundacao: '',
            telefone: '',
            email: '',
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
        }
    };
        
    $scope.$on('preLoad', function() {
        Igreja.getMe($scope.igreja).then(function(r) {
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
        
        $scope.filterPresbiterios($scope.dataFrm.data.sinodo.value);
        
        
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
            Igreja.edit($scope.igreja, $scope.dataFrm.toSend).then(function(r) {
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
