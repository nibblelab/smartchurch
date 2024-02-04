angular.module('SmartChurchPanel').controller('FrequenciaDaSalaEBDCtrl', function ($scope, $state, $stateParams, $localstorage, $notifications, 
                    $rootScope, $dialogs, $timeout, $context,
                    Data, SalaEBD, AlunoEBD, FrequenciaEBD) { 
    
    /* config */
    
    $scope.sala = $stateParams.sala;
    $scope.opTitle = $stateParams.title;
    $scope.search = $stateParams.search;
    $scope.data = $stateParams.data;
    $scope.perms = $stateParams.perms;
    $scope.sref = {
        back_level2: 'SmartChurchPanel.ebd.salas.buscar()',
        back: ($stateParams.back == '') ? 'SmartChurchPanel.ebd.salas.frequencias.buscar' : $stateParams.back,
        backParams: { sala: $scope.sala },
        add: "SmartChurchPanel.ebd.salas.frequencias.adicionar",
        addParams: { back: 'SmartChurchPanel.ebd.salas.frequencias.buscar', sala: $scope.sala },
        edit: "SmartChurchPanel.ebd.salas.frequencias.editar",
        editParams: { dia: '', title: 'Editando', back: 'SmartChurchPanel.ebd.salas.frequencias.buscar', sala: $scope.sala }
    };
    $scope.dia = $stateParams.dia;
    $scope.objForm = 'Frequência';
    $scope.objList = 'Frequências';
    $scope.context = $context.getActiveContext();
    $scope.igreja = $scope.context.id;
    $scope.igrejaData = $rootScope.USER.getContextByKeyAndId($scope.context.key, $scope.context.id);
    $scope.isCreate = false;
    
    /* verificação de permissão do state */
    if(!$scope.USER.doIHaveAccess($scope.user, $scope.perms)) {
        $state.go('SmartChurchPanel.sempermissao');
    }
    
    $scope.localPerms = {
        add: 'FrequenciaEBDSave',
        edit: 'FrequenciaEBDSave',
        changeStat: 'FrequenciaEBDSave',
        remove: 'FrequenciaEBDRemove'
    };
    
    /* common */
    $scope.listFrequencias = [];
    $scope.salaData = { nome: '' };
    $scope.listAlunos = [];
    Data.getAll().then(function(r) {
        $scope.listFrequencias = r.frequencia;
        
        SalaEBD.getMe($scope.sala).then(function(r) {
            $scope.salaData = r;
            
            AlunoEBD.getAll('', '', '', 'nome,asc', '', $scope.sala, '', Status.ATIVO).then(function(r) {
                if(r.total > 0) {
                    $scope.listAlunos = r.datas;
                }

                $scope.$broadcast('preLoad');
            }, function(e) { console.log(e); $state.go('SmartChurchPanel.ebd.salas.buscar()'); });
        }, function(e) { console.log(e); $state.go('SmartChurchPanel.ebd.salas.buscar()'); });
    }, function(e) { console.log(e); $scope.testError(e); });
    
    /* search */
    $scope.storage_cache_name = 'search_frequenciasdaebd';
    $scope.searchBy = '';
    $scope.page = 1;
    $scope.pageSize = '10';
    $scope.hasPrev = false;
    $scope.hasNext = false;
    $scope.toPrev = function() {
        $scope.page--;
        var prev_search = $localstorage.getObject($scope.storage_cache_name, {});
        if (!$.isEmptyObject(prev_search)) {
            prev_search.page = $scope.page;
            $localstorage.setObject($scope.storage_cache_name, prev_search);
        }
        $scope.doSearch();
    };
    $scope.toNext = function() {
        $scope.page++;
        var prev_search = $localstorage.getObject($scope.storage_cache_name, {});
        if (!$.isEmptyObject(prev_search)) {
            prev_search.page = $scope.page;
            $localstorage.setObject($scope.storage_cache_name, prev_search);
        }
        $scope.doSearch();
    };
    $scope.list = [];
    $scope.createSearchObject = function(only_non_existent) {
        if(only_non_existent != undefined && only_non_existent == true) {
            var prev_search = $localstorage.getObject($scope.storage_cache_name, {});
            if (!$.isEmptyObject(prev_search)) {
                return;
            }
        }
        
        $localstorage.setObject($scope.storage_cache_name, {
            page: $scope.page,
            pageSize: $scope.pageSize,
            searchBy: '',
            frequencia: '',
            dia: '',
            sexo: '',
            escolaridade: '',
            estado_civil: '',
            com_filhos: false,
            sem_filhos: false,
            orderBy: 'nome,asc'
        });
    };
    $scope.createSearchObject(true);
    $scope.clear = function() {
        $scope.searchBy = '';
        $scope.page = 1;
        $scope.createSearchObject();
        $scope.orderField = 'nome';
        $scope.orderOrientation = 'asc';
        $scope.doSearch();
        $scope.clearMarkList();
    };
    $scope.orderField = 'nome';
    $scope.orderOrientation = 'asc';
    $scope.doSort = function() {
        if($scope.orderField != '') {
            $scope.orderBy = $scope.orderField + ',' + $scope.orderOrientation;
            var prev_search = $localstorage.getObject($scope.storage_cache_name, {});
            if (!$.isEmptyObject(prev_search)) {
                prev_search.orderBy = $scope.orderBy;
                $localstorage.setObject($scope.storage_cache_name, prev_search);
            }
        }

        $scope.doSearch();
    };
    $scope.orderBy = 'nome,asc';
    $scope.total = 0;
    $scope.getPreviousOrdering = function() {
        var prev_search = $localstorage.getObject($scope.storage_cache_name, {});
        if (!$.isEmptyObject(prev_search)) {
            $scope.orderBy = prev_search.orderBy;
            var o_v = $scope.orderBy.split(',');
            $scope.orderField = o_v[0];
            $scope.orderOrientation = o_v[1];
        }
    };
    $scope.getPreviousOrdering();
    $scope.filterEnabled = false;
    $scope.enableFilters = function() {
        $scope.filterEnabled = !$scope.filterEnabled;
    };
    $scope.frequencia = '';
    $scope.sexo = '';
    $scope.escolaridade = '';
    $scope.estado_civil = '';
    $scope.com_filhos = false;
    $scope.sem_filhos = false;
    $scope.filterUsed = false;
    $scope.isFilterUsed = function() {
        $scope.filterUsed = ($scope.frequencia != '' || $scope.sexo != '' || $scope.escolaridade != '' || 
                                $scope.estado_civil != '' || $scope.com_filhos || $scope.sem_filhos);
    };
    $scope.listRange = [];
    $scope.generateRange = function(range) {
        $scope.listRange = [];
        for(var k in range) {
            var dt = moment(range[k]);
            $scope.listRange.push({
                'dia': range[k],
                'label': dt.format('DD/MM/YYYY')
            });
        }
    };
    $scope.checkPresenca = function(obj, dia) {
        if (!obj.frequencias.hasOwnProperty(dia)) {
            return '';
        }
        if(obj.frequencias[dia] instanceof Array) { 
            return '';
        }
        return (obj.frequencias[dia].presente) ? 'P' : 'A';
    };
    $scope.doSearch = function(is_new) {

        if (is_new != undefined && is_new == true) {
            
            var prev_search = $localstorage.getObject($scope.storage_cache_name, {});
            if (!$.isEmptyObject(prev_search)) {
                if(prev_search.searchBy != $scope.searchBy) {
                    $scope.page = 1; 
                    $scope.pageSize = '10';
                }
            }
            
            $localstorage.setObject($scope.storage_cache_name, {
                page: $scope.page,
                pageSize: $scope.pageSize,
                searchBy: $scope.searchBy,
                stat: $scope.stat,
                frequencia: $scope.frequencia,
                sexo: $scope.sexo,
                escolaridade: $scope.escolaridade,
                estado_civil: $scope.estado_civil,
                com_filhos: $scope.com_filhos,
                sem_filhos: $scope.sem_filhos,
                orderBy: $scope.orderBy
            });
        } else {
            var prev_search = $localstorage.getObject($scope.storage_cache_name, {});
            if (!$.isEmptyObject(prev_search)) {
                $scope.page = prev_search.page;
                $scope.pageSize = prev_search.pageSize;
                $scope.searchBy = prev_search.searchBy;
                $scope.stat = prev_search.stat;
                $scope.frequencia = prev_search.frequencia;
                $scope.sexo = prev_search.sexo;
                $scope.escolaridade = prev_search.escolaridade;
                $scope.estado_civil = prev_search.estado_civil;
                $scope.com_filhos = prev_search.com_filhos;
                $scope.sem_filhos = prev_search.sem_filhos;
                $scope.orderBy = prev_search.orderBy;
                var o_v = $scope.orderBy.split(',');
                $scope.orderField = o_v[0];
                $scope.orderOrientation = o_v[1];
            }
        }
        
        $scope.isFilterUsed();

        $scope.list = [];
        FrequenciaEBD.getAll('', '', $scope.searchBy, $scope.orderBy, '', $scope.sala, '', $scope.frequencia, '', 
                            $scope.sexo, $scope.estado_civil, $scope.escolaridade, $scope.com_filhos, $scope.sem_filhos).then(function(r) {
            if (r.datas.length > 0) {
                $scope.generateRange(r.range);                
                $scope.list = r.datas;
            } else {
                $notifications.info(':(', 'Sem dados para exibir');
            }
        }, function(e) {
            $notifications.err(e);
        });
    };
    if ($scope.search) {
        if ($scope.markList) {
            $scope.clearMarkList();
        }
        
        $scope.$on('preLoad', function() {
            $scope.doSearch();
        });
    }
    
    
    /* form */
    $('.datepicker').datepicker({
        todayHighlight: true,
        language: 'pt-BR',
        format: 'dd/mm/yyyy',
        orientation: 'bottom'
    });
    
    $scope.dataFrm = {
        isValid: false,
        data: {
            id: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            dia: { value: '', notEmpty: true, valid: true, StringfyFrom: '' },
            frequencias: { value: [], notEmpty: false, valid: true, StringfyFrom: '' }
        },
        toSend: {
            id: '',
            sala: $scope.sala,
            dia: '',
            frequencias: []
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
    $scope.generateFrequenciasList = function(old) {
        for(var k in $scope.listAlunos) {
            var id = getRandomId();
            var presente = false;
            if(old != undefined) {
                var f = old.find(x => x.pessoa == $scope.listAlunos[k].pessoa);
                if(f != undefined) {
                    id = f.id;
                    presente = f.presente;
                }
            }
            
            $scope.dataFrm.data.frequencias.value.push({
                id: id,
                pessoa: $scope.listAlunos[k].pessoa,
                nome: $scope.listAlunos[k].nome,
                presente: presente
            });
        }
    };
    
    if ($state.current.name.includes('editar') && $scope.dia) {
        // edição - via id 
        $scope.$on('preLoad', function() {
            FrequenciaEBD.getByDiaAndSala($scope.dia, $scope.sala).then(function(r) {
                $scope.data = r;
                $scope.load();
            }, function(e) { 
                $state.go($scope.sref.back.parseState().state, $scope.sref.backParams); 
            });
        });
    }
    
    
    $scope.load = function() {
        if($scope.data[0].dia != null) {
            $scope.dataFrm.data.dia.value = moment($scope.data[0].dia).format('DD/MM/YYYY');
        }
        
        $scope.generateFrequenciasList($scope.data);
    };
    
    if(!$scope.data && !$scope.search && !$scope.dia) {
        // adição
        $scope.isCreate = true;
        $scope.$on('preLoad', function() {
            $scope.generateFrequenciasList();
        });
    }
    
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
                promise = FrequenciaEBD.create($scope.dataFrm.toSend);
            }
            else
            {
                promise = FrequenciaEBD.edit($scope.dataFrm.toSend);
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
      
    /* remove */
    $scope.remove = function(dia) {
        
        $dialogs.beforeRemove().then(function() {
            FrequenciaEBD.removeByDia({ dia: dia, sala: $scope.sala }).then(function() {
                $timeout(function() {
                    $dialogs.onRemove().then(function() {
                        $state.reload();
                    }, function() {});
                }, 100);
            }, function(e) {
                $notifications.err(e);
            });
        }, function() {  });
    };

    
});


