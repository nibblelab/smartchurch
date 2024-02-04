angular.module('SmartChurchPanel').controller('MembresiaCtrl', function ($scope, $state, $stateParams, $localstorage, $notifications, $rootScope, $dialogs, $timeout, 
                    $ibge, 
                    Sinodo, Presbiterio, Templo, MembroDaIgreja) { 
    
    /* config */
    $scope.perms = $stateParams.perms;
    $scope.data = {};
    $scope.isCreate = false;

    /* verificação de permissão do state */
    if(!$scope.USER.doIHaveAccess($scope.user, $scope.perms)) {
        $state.go('SmartChurchPanel.sempermissao');
    }
    
    /* common */
    $scope.listSinodos = [];
    $scope.listPresbiterios = [];
    $scope.listIgrejas = [];
    $scope.membresiaData = $rootScope.USER.getMembresiaData();
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

            Templo.getAll('', '', '', 'nome,asc').then(function(r) {
                if(r.total > 0) {
                    for(var k in r.datas) {
                        r.datas[k]['show'] = true;
                    }
                    $scope.listIgrejas = r.datas;
                }

                $scope.$broadcast('preLoad');
            }, function(e) { console.log(e); $scope.testError(e); });
        }, function(e) { console.log(e); $scope.testError(e); });
    }, function(e) { console.log(e); $scope.testError(e); });
    $scope.filterPresbiterios = function() {
        if($scope.dataFrm.data.sinodo.value != '') {
            $scope.listPresbiterios.forEach(function (element, index, array) {
                array[index].show = true;
            });
            
            for(var k in $scope.listPresbiterios) {
                if($scope.listPresbiterios[k].sinodo != $scope.dataFrm.data.sinodo.value) {
                    $scope.listPresbiterios[k].show = false;
                }
            }
        }
    };
    $scope.filterIgrejas = function() {
        $scope.listIgrejas.forEach(function (element, index, array) {
            array[index].show = true;
        });
        if($scope.dataFrm.data.presbiterio.value != '') {
            for(var k in $scope.listIgrejas) {
                if($scope.listIgrejas[k].presbiterio != $scope.dataFrm.data.presbiterio.value) {
                    $scope.listIgrejas[k].show = false;
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
            igreja: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            arrolado: { value: false, notEmpty: false, valid: true, StringfyFrom: '' }
        },
        toSend: {
            id: '',
            pessoa: $scope.user.id,
            igreja: '',
            comungante: '',
            especial: '',
            arrolado: '',
            data_admissao: '',
            data_demissao: ''
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
    
    
    if($scope.membresiaData.igreja == '') {
        $scope.isCreate = true;
    }
    else {
        $scope.$on('preLoad', function() {
            MembroDaIgreja.getMe($scope.membresiaData.id).then(function(r) {
                $scope.data = r;
                $scope.load();
            }, function(e) { });
        });
    }
    
    $scope.load = function() {
        for (var k in $scope.data) {
            if ($scope.dataFrm.data.hasOwnProperty(k)) {
                $scope.dataFrm.data[k].value = $scope.data[k];
            }
        }
        
        if(!$scope.isCreate) {
            $scope.dataFrm.data.sinodo.value = $scope.membresiaData.sinodo;
            $scope.dataFrm.data.presbiterio.value = $scope.membresiaData.presbiterio;
            
            $scope.filterPresbiterios($scope.dataFrm.data.sinodo.value);
            $scope.filterIgrejas($scope.dataFrm.data.presbiterio.value);
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
                promise = MembroDaIgreja.create($scope.dataFrm.toSend);
            }
            else
            {
                promise = MembroDaIgreja.edit($scope.id, $scope.dataFrm.toSend);
            }
            promise.then(function(r) {
                $dialogs.onRequested('Sucesso', 'Membresia salva com sucesso! É necessário refazer o login para atualizar os dados.').then(function() {
                    $scope.logout();
                }, function() {});
            }, function(e) {
                $notifications.err(e);
            });
        } else {
            $notifications.err("Há campos incorretos!");
        }
    };
    
    
});


