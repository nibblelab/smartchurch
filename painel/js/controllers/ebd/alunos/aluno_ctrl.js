angular.module('SmartChurchPanel').controller('AlunoDaEBDCtrl', function ($scope, $state, $stateParams, $localstorage, $notifications, 
                    $rootScope, $dialogs, $timeout, $context,
                    Data, MembroDaIgreja, SalaEBD, AlunoEBD) { 
    
    /* config */
    
    $scope.sala = $stateParams.sala;
    $scope.opTitle = $stateParams.title;
    $scope.search = $stateParams.search;
    $scope.data = $stateParams.data;
    $scope.perms = $stateParams.perms;
    $scope.multiple = $stateParams.multiple;
    $scope.sref = {
        back_level2: 'SmartChurchPanel.ebd.salas.buscar()',
        back: ($stateParams.back == '') ? 'SmartChurchPanel.ebd.salas.alunos.buscar' : $stateParams.back,
        backParams: { sala: $scope.sala },
        add: "SmartChurchPanel.ebd.salas.alunos.adicionar",
        addParams: { back: 'SmartChurchPanel.ebd.salas.alunos.buscar', sala: $scope.sala },
        addMultipleParams: { back: 'SmartChurchPanel.ebd.salas.alunos.buscar', sala: $scope.sala, multiple: true },
        edit: "SmartChurchPanel.ebd.salas.alunos.editar",
        editParams: { id: '', title: 'Editando', back: 'SmartChurchPanel.ebd.salas.alunos.buscar', sala: $scope.sala },
        editMultipleParams: { id: '', title: 'Editando', back: 'SmartChurchPanel.ebd.salas.alunos.buscar', sala: $scope.sala, multiple: true }
    };
    $scope.id = $stateParams.id;
    $scope.objForm = 'Aluno';
    $scope.objList = 'Alunos';
    $scope.context = $context.getActiveContext();
    $scope.igreja = $scope.context.id;
    $scope.igrejaData = $rootScope.USER.getContextByKeyAndId($scope.context.key, $scope.context.id);
    $scope.isCreate = false;
    
    /* verificação de permissão do state */
    if(!$scope.USER.doIHaveAccess($scope.user, $scope.perms)) {
        $state.go('SmartChurchPanel.sempermissao');
    }
    
    $scope.localPerms = {
        add: 'SalaEBDAlunos',
        edit: 'SalaEBDAlunos',
        changeStat: 'SalaEBDAlunos',
        remove: 'SalaEBDAlunos'
    };
    
    /* common */
    $scope.listStatus = [];
    $scope.salaData = { nome: '' };
    $scope.listMembros = [];
    Data.getAll().then(function(r) {
        $scope.listStatus = r.status;
        
        SalaEBD.getMe($scope.sala).then(function(r) {
            $scope.salaData = r;
            
            MembroDaIgreja.getAll('', '', '', 'nome,asc', '', '', $scope.igreja).then(function(r) {
                if(r.total > 0) {
                    $scope.listMembros = r.datas;
                }

                $scope.$broadcast('preLoad');
            }, function(e) { console.log(e); $state.go('SmartChurchPanel.ebd.salas.buscar()'); });
        }, function(e) { console.log(e); $state.go('SmartChurchPanel.ebd.salas.buscar()'); });
    }, function(e) { console.log(e); $scope.testError(e); });
    
    /* search */
    $scope.storage_cache_name = 'search_professoresdaebd';
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
            stat: '',
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
    $scope.orderOrientation = 'desc';
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
    $scope.orderBy = 'nome,desc';
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
    $scope.stat = '';
    $scope.sexo = '';
    $scope.escolaridade = '';
    $scope.estado_civil = '';
    $scope.com_filhos = false;
    $scope.sem_filhos = false;
    $scope.filterUsed = false;
    $scope.isFilterUsed = function() {
        $scope.filterUsed = ($scope.stat != '' || $scope.sexo != '' || $scope.escolaridade != '' || 
                                $scope.estado_civil != '' || $scope.com_filhos || $scope.sem_filhos);
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
        AlunoEBD.getAll($scope.page, $scope.pageSize, $scope.searchBy, $scope.orderBy, '', $scope.sala, '', $scope.stat, '', 
                            $scope.sexo, $scope.estado_civil, $scope.escolaridade, $scope.com_filhos, $scope.sem_filhos).then(function(r) {
            if (r.datas.length > 0) {
                for (var k in r.datas) {
                    r.datas[k]['inicio_str'] = '';
                    if(r.datas[k].inicio != null) {
                        r.datas[k]['inicio_str'] = moment(r.datas[k].inicio).format('DD/MM/YYYY');
                    }
                    
                    r.datas[k]['fim_str'] = '';
                    if(r.datas[k].fim != null) {
                        r.datas[k]['fim_str'] = moment(r.datas[k].fim).format('DD/MM/YYYY');
                    }
                    
                    r.datas[k]['selected'] = false;
                }
                
                $scope.list = r.datas;
                $scope.hasNext = (r.total > ((($scope.page - 1) * parseInt($scope.pageSize)) + r.datas.length));
                $scope.hasPrev = ($scope.page > 1);
                $scope.total = r.total;
                
                $rootScope.list = $scope.list;
            } else {
                $scope.hasNext = false;
                $scope.hasPrev = false;
                $scope.total = r.total;
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
    
    
    $scope.onSelectNome = function(item) {
        $scope.dataFrm.data.pessoa.value = item.pessoa;
        $scope.dataFrm.data.nome.value = item.nome;
        $scope.$apply();
    };
    
    $scope.prepareTypeAhead = function() {
        var options = {
            data: $scope.listMembros,
            getValue: "nome",
            list: {
                match: {
                    enabled: true
                },
                onChooseEvent: function() {
                    $scope.onSelectNome($("#nome").getSelectedItemData());
                }
            }
        };
        $('#nome').easyAutocomplete(options);
    };
    
    $scope.preparePessoas = function(old, remove_old) {
        for(var k in $scope.listMembros) {
            var checked = false;
            if(old != undefined) {
                var f = old.find(x => x.pessoa == $scope.listMembros[k].pessoa);
                checked = (f != undefined);
                if(remove_old != undefined && remove_old && checked) {
                    continue;
                }
            }
            $scope.dataFrm.data.pessoas.value.push({
                id: $scope.listMembros[k].pessoa,
                nome: $scope.listMembros[k].nome,
                checked: checked
            });
        }
    };
    
    
    $scope.dataFrm = {
        isValid: false,
        data: {
            id: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            nome: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            pessoa: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            pessoas: { value: [], notEmpty: false, valid: true, StringfyFrom: '' },
            inicio: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            fim: { value: '', notEmpty: false, valid: true, StringfyFrom: '' }
        },
        toSend: {
            id: '',
            sala: $scope.sala,
            pessoa: '',
            pessoas: [],
            inicio: '',
            fim: '',
            multiple: $scope.multiple
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
            
            if($scope.multiple) {
                var has_one = false;
                for(var k in $scope.dataFrm.data.pessoas.value) {
                    if($scope.dataFrm.data.pessoas.value[k].checked) {
                        has_one = true;
                        break;
                    }
                }
                
                valid = has_one;
            }
            else {
                if ($scope.dataFrm.data.nome.value == '') {
                    valid = false;
                    $scope.dataFrm.data.nome.valid = false;
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
    
    if ($state.current.name.includes('editar') && $scope.id) {
        // edição - via id 
        $scope.$on('preLoad', function() {
            AlunoEBD.getMe($scope.id).then(function(r) {
                $scope.data = r;
                $scope.load();
            }, function(e) { 
                $state.go($scope.sref.back.parseState().state, $scope.sref.backParams); 
            });
        });
    }
    
    $scope.load = function() {
        if($scope.multiple) {
            AlunoEBD.getAlunos($scope.sala).then(function(r) {
                $scope.preparePessoas(r.alunos);
            }, function(e) { console.log(e); });
        }
        else {
            $scope.prepareTypeAhead();
        }
        
        for (var k in $scope.data) {
            if ($scope.dataFrm.data.hasOwnProperty(k)) {
                $scope.dataFrm.data[k].value = $scope.data[k];
            }
        }
        
        if($scope.dataFrm.data.inicio.value != null) {
            $scope.dataFrm.data.inicio.value = moment($scope.dataFrm.data.inicio.value).format('DD/MM/YYYY');
        }
        else {
            $scope.dataFrm.data.inicio.value = '';
        }
        
        if($scope.dataFrm.data.fim.value != null) {
            $scope.dataFrm.data.fim.value = moment($scope.dataFrm.data.fim.value).format('DD/MM/YYYY');
        }
        else {
            $scope.dataFrm.data.fim.value = '';
        }
    };
    
    if(!$scope.data && !$scope.search && !$scope.id) {
        // adição
        $scope.isCreate = true;
        $scope.$on('preLoad', function() {
            if($scope.multiple) {
                AlunoEBD.getAlunos($scope.sala).then(function(r) {
                    $scope.preparePessoas(r.alunos, true);
                }, function(e) { console.log(e); });
            }
            else {
                $scope.prepareTypeAhead();
            }
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
                promise = AlunoEBD.create($scope.dataFrm.toSend);
            }
            else
            {
                promise = AlunoEBD.edit($scope.id, $scope.dataFrm.toSend);
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
    
    $scope.changeStat = function(d) {
        $dialogs.beforeChange().then(function() {
            AlunoEBD.changeStat(d.id, d).then(function(r) {
                $dialogs.onChange().then(function() {
                    for (var k in $scope.list) {
                        if ($scope.list[k].id == d.id) {
                            $scope.list[k].stat = r.stat;
                        }
                    }
                }, function() {});
            }, function(e) {
                $notifications.err(e);
            });
        }, function() {});
    };
        
    /* remove */
    $scope.remove = function(d) {
        
        $dialogs.beforeRemove().then(function() {
            AlunoEBD.remove(d.id).then(function() {
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

    $scope.removeSelected = function() {

        var ids = [];
        for (var k in $scope.markList) {
            ids.push($scope.markList[k].id);
        }

        $dialogs.beforeRemove().then(function() {
            AlunoEBD.removeAll({
                ids: ids
            }).then(function() {
                $timeout(function() {
                    $dialogs.onRemove().then(function() {
                        $state.reload();
                    }, function() {});
                }, 100);
            }, function(e) {
                $notifications.err(e);
            });
        }, function() {});
    };
    
});


