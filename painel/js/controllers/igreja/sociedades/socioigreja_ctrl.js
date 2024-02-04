angular.module('SmartChurchPanel').controller('SocioIgrejaCtrl', function ($scope, $state, $stateParams, $localstorage, $notifications, 
                    $rootScope, $dialogs, $timeout, $context,
                    Sociedade, MembroDaIgreja, Perfil, SysConfig, Socio) { 
    
    /* config */
    $scope.sociedade = $stateParams.sociedade;
    $scope.opTitle = $stateParams.title;
    $scope.search = $stateParams.search;
    $scope.data = $stateParams.data;
    $scope.perms = $stateParams.perms;
    $scope.back = ($stateParams.back == '') ? 'SmartChurchPanel.igreja.sociedades.socios.buscar({sociedade: "'+$scope.sociedade+'"})' : $stateParams.back + '({sociedade: "'+$scope.sociedade+'"})';
    $scope.id = $stateParams.id;
    $scope.objForm = 'Sócio';
    $scope.objList = 'Sócios';
    $scope.igreja = $context.getIgrejaContext();
    $scope.igrejaData = $rootScope.USER.getContextByKeyAndId(Contexts.IGREJAS, $scope.igreja);
    $scope.isCreate = false;
    
    /* verificação de permissão do state */
    if(!$scope.USER.doIHaveAccess($scope.user, $scope.perms)) {
        $state.go('SmartChurchPanel.sempermissao');
    }
    
    $scope.localPerms = {
        add: 'SociedadeIgrejaSocio',
        edit: 'SociedadeIgrejaSocio',
        changeStat: 'SociedadeIgrejaSocio',
        remove: 'SociedadeIgrejaSocio'
    };
    
    /* common */
    $scope.sociedadeData = { nome: '' };
    $scope.listMembros = [];
    $scope.listPerfis = [];
    $scope.listConfigs = [];
    Sociedade.getMe($scope.sociedade).then(function(r) {
        $scope.sociedadeData = r;

        MembroDaIgreja.getAll('', '', '', '', '', '', $scope.igreja).then(function(r) {
            if(r.total > 0) {
                $scope.listMembros = r.datas;
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
    }, function(e) { console.log(e); $state.go('SmartChurchPanel.igreja.sociedadesbuscar()'); });
    
    /* search */
    $scope.storage_cache_name = 'search_sociosdaigreja';
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
            admin: false,
            diretoria: false,
            cooperador: false,
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
    $scope.stat = '';
    $scope.admin = false;
    $scope.diretoria = false;
    $scope.cooperador = false;
    $scope.filterUsed = false;
    $scope.isFilterUsed = function() {
        $scope.filterUsed = ($scope.stat != '' || $scope.admin || $scope.diretoria || $scope.cooperador);
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
                admin: $scope.admin,
                diretoria: $scope.diretoria,
                cooperador: $scope.cooperador,
                orderBy: $scope.orderBy
            });
        } else {
            var prev_search = $localstorage.getObject($scope.storage_cache_name, {});
            if (!$.isEmptyObject(prev_search)) {
                $scope.page = prev_search.page;
                $scope.pageSize = prev_search.pageSize;
                $scope.searchBy = prev_search.searchBy;
                $scope.stat = prev_search.stat;
                $scope.admin = prev_search.admin;
                $scope.diretoria = prev_search.diretoria;
                $scope.cooperador = prev_search.cooperador;
                $scope.orderBy = prev_search.orderBy;
                var o_v = $scope.orderBy.split(',');
                $scope.orderField = o_v[0];
                $scope.orderOrientation = o_v[1];
            }
        }
        
        $scope.isFilterUsed();

        $scope.list = [];
        Socio.getAll($scope.page, $scope.pageSize, $scope.searchBy, $scope.orderBy, '', $scope.sociedade, '', 
                        $scope.stat, $scope.admin, $scope.diretoria, $scope.cooperador).then(function(r) {
            if (r.datas.length > 0) {
                for (var k in r.datas) {
                    r.datas[k]['data_admissao_str'] = '';
                    if(r.datas[k].data_admissao != null) {
                        r.datas[k]['data_admissao_str'] = moment(r.datas[k].data_admissao).format('DD/MM/YYYY');
                    }
                    
                    r.datas[k]['data_demissao_str'] = '';
                    if(r.datas[k].data_demissao != null) {
                        r.datas[k]['data_demissao_str'] = moment(r.datas[k].data_demissao).format('DD/MM/YYYY');
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
    
    $scope.selectDefaultPerfil = function() {
        $scope.dataFrm.data.perfil.value = ($scope.dataFrm.data.diretoria.value) ? $scope.listConfigs[0].perfil_diretoria_sociedade : '';
    };
    
    $scope.dataFrm = {
        isValid: false,
        data: {
            id: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            nome: { value: '', notEmpty: true, valid: true, StringfyFrom: '' },
            pessoa: { value: '', notEmpty: true, valid: true, StringfyFrom: '' },
            admin: { value: false, notEmpty: false, valid: true, StringfyFrom: '' },
            diretoria: { value: false, notEmpty: false, valid: true, StringfyFrom: '' },
            cooperador: { value: false, notEmpty: false, valid: true, StringfyFrom: '' },
            perfil: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            data_admissao: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            data_demissao: { value: '', notEmpty: false, valid: true, StringfyFrom: '' }
        },
        toSend: {
            id: '',
            sociedade: $scope.sociedade,
            pessoa: '',
            admin: false,
            diretoria: false,
            cooperador: false,
            perfil: '',
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
    
    if ($state.current.name.includes('editar') && $scope.id) {
        // edição - via id 
        $scope.$on('preLoad', function() {
            Socio.getMe($scope.id).then(function(r) {
                $scope.data = r;
                $scope.load();
            }, function(e) { 
                console.log(e);
                $state.go($stateParams.back, { sociedade: $scope.sociedade }); 
            });
        });
    }
    
    $scope.load = function() {
        $scope.prepareTypeAhead();
        
        for (var k in $scope.data) {
            if ($scope.dataFrm.data.hasOwnProperty(k)) {
                $scope.dataFrm.data[k].value = $scope.data[k];
            }
        }
        
        if($scope.dataFrm.data.data_admissao.value != null) {
            $scope.dataFrm.data.data_admissao.value = moment($scope.dataFrm.data.data_admissao.value).format('DD/MM/YYYY');
        }
        else {
            $scope.dataFrm.data.data_admissao.value = '';
        }
        
        if($scope.dataFrm.data.data_demissao.value != null) {
            $scope.dataFrm.data.data_demissao.value = moment($scope.dataFrm.data.data_demissao.value).format('DD/MM/YYYY');
        }
        else {
            $scope.dataFrm.data.data_demissao.value = '';
        }
    };
    
    if(!$scope.data && !$scope.search && !$scope.id) {
        // adição
        $scope.isCreate = true;
        $scope.$on('preLoad', function() {
            $scope.prepareTypeAhead();
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
                promise = Socio.create($scope.dataFrm.toSend);
            }
            else
            {
                promise = Socio.edit($scope.id, $scope.dataFrm.toSend);
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
            Socio.changeStat(d.id, d).then(function(r) {
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
            Socio.remove(d.id).then(function() {
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
            Socio.removeAll({
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

