angular.module('SmartChurchPanel').controller('OficialSociedadeCtrl', function ($scope, $state, $stateParams, $localstorage, $notifications, 
                    $rootScope, $dialogs, $timeout, $context,
                    Diretoria, Socio, Cargo, Oficial) { 
    
    /* config */
    $scope.diretoria = $stateParams.diretoria;
    $scope.opTitle = $stateParams.title;
    $scope.search = $stateParams.search;
    $scope.data = $stateParams.data;
    $scope.perms = $stateParams.perms;
    $scope.backDiretorias = 'SmartChurchPanel.sociedade.diretorias.buscar()';
    $scope.back = ($stateParams.back == '') ? 'SmartChurchPanel.sociedade.diretorias.oficiais.buscar({diretoria: "'+$scope.diretoria+'"})' : $stateParams.back + '({diretoria: "'+$scope.diretoria+'"})';
    $scope.id = $stateParams.id;
    $scope.objForm = 'Oficial';
    $scope.objList = 'Oficiais';
    $scope.sociedade = $context.getSociedadeContext();
    $scope.sociedadeData = $rootScope.USER.getContextByKeyAndId(Contexts.SOCIEDADES, $scope.sociedade);
    $scope.isCreate = false;
    
    /* verificação de permissão do state */
    if(!$scope.USER.doIHaveAccess($scope.user, $scope.perms)) {
        $state.go('SmartChurchPanel.sempermissao');
    }
    
    $scope.navStates = {
        add: 'SmartChurchPanel.sociedade.diretorias.oficiais.adicionar',
        edit: 'SmartChurchPanel.sociedade.diretorias.oficiais.editar',
        search: 'SmartChurchPanel.sociedade.diretorias.oficiais.buscar'
    };
    
    $scope.localPerms = {
        add: 'DiretoriaSociedadeOficiais',
        edit: 'DiretoriaSociedadeOficiais',
        changeStat: 'DiretoriaSociedadeOficiais',
        remove: 'DiretoriaSociedadeOficiais'
    };
    
    /* common */
    $scope.diretoriaData = { nome: '' };
    $scope.listSocios = [];
    $scope.listCargos = [];
    Diretoria.getMe($scope.diretoria).then(function(r) {
        $scope.diretoriaData = r;

        Socio.getAll('', '', '', '', '', $scope.sociedade, '', Status.ATIVO, '', '', '', true).then(function(r) {
            if(r.total > 0) {
                $scope.listSocios = r.datas;
            }

            Cargo.getAllForSociedade().then(function(r) {
                if(r.total > 0) {
                    $scope.listCargos = r.datas;
                }
                
                $scope.$broadcast('preLoad');
            }, function(e) { console.log(e); $scope.testError(e); });
        }, function(e) { console.log(e); $scope.testError(e); });
    }, function(e) { console.log(e); $state.go('SmartChurchPanel.sociedade.diretorias.buscar()'); });
    
    /* search */
    $scope.storage_cache_name = 'search_oficiaisdasociedade';
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
            cargo: '',
            orderBy: 'nome,desc'
        });
    };
    $scope.createSearchObject(true);
    $scope.clear = function() {
        $scope.searchBy = '';
        $scope.page = 1;
        $scope.createSearchObject();
        $scope.orderField = 'nome';
        $scope.orderOrientation = 'desc';
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
    $scope.cargo = '';
    $scope.filterUsed = false;
    $scope.isFilterUsed = function() {
        $scope.filterUsed = ($scope.stat != '' || $scope.cargo != '');
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
                cargo: $scope.cargo,
                orderBy: $scope.orderBy
            });
        } else {
            var prev_search = $localstorage.getObject($scope.storage_cache_name, {});
            if (!$.isEmptyObject(prev_search)) {
                $scope.page = prev_search.page;
                $scope.pageSize = prev_search.pageSize;
                $scope.searchBy = prev_search.searchBy;
                $scope.stat = prev_search.stat;
                $scope.cargo = prev_search.cargo;
                $scope.orderBy = prev_search.orderBy;
                var o_v = $scope.orderBy.split(',');
                $scope.orderField = o_v[0];
                $scope.orderOrientation = o_v[1];
            }
        }
        
        $scope.isFilterUsed();

        $scope.list = [];
        
        Oficial.getAll($scope.page, $scope.pageSize, $scope.searchBy, $scope.orderBy, '', 
                            $scope.stat, $scope.cargo, $scope.diretoria, '',
                            '', '', '', '', '', '', $scope.sociedade).then(function(r) {
            if (r.datas.length > 0) {
                for (var k in r.datas) {
                    r.datas[k]['time_cad_str'] = moment(r.datas[k].time_cad).format('DD/MM/YYYY');
                    r.datas[k]['last_mod_str'] = moment(r.datas[k].last_mod).format('DD/MM/YYYY HH:mm');
                    
                    r.datas[k]['cargo_str'] = '';
                    if(r.datas[k].cargo != null) {
                        var c = $scope.listCargos.find(x => x.id == r.datas[k].cargo);
                        if(c != undefined) {
                            r.datas[k]['cargo_str'] = c.nome;
                        }
                    }
                    
                    r.datas[k]['inicio_str'] = '';
                    if(r.datas[k].inicio != null) {
                        r.datas[k]['inicio_str'] = moment(r.datas[k].inicio).format('DD/MM/YYYY');
                    }
                    
                    r.datas[k]['fim_str'] = '';
                    if(r.datas[k].fim != null) {
                        r.datas[k]['fim_str'] = moment(r.datas[k].fim).format('DD/MM/YYYY');
                    }
                    
                    r.datas[k]['socio'] = {};
                    var sc = $scope.listSocios.find(x => x.pessoa == r.datas[k].pessoa);
                    if(sc != undefined) {
                        r.datas[k]['socio'] = sc;
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
        $scope.dataFrm.data.email.value = item.email;
        $scope.dataFrm.data.telefone.value = item.telefone;
        $scope.dataFrm.data.celular.value = item.celular_1;
        
        $scope.dataFrm.toSend.socio.id = item.id;
        $scope.dataFrm.toSend.socio.pessoa = item.pessoa;
        
        $scope.$apply();
    };
    
    $scope.prepareTypeAhead = function() {
        var options = {
            data: $scope.listSocios,
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
    
    $scope.onSelectCargo = function() {
        if($scope.dataFrm.data.cargo.value != '') {
            var cargo = $scope.listCargos.find(x => x.id == $scope.dataFrm.data.cargo.value);
            if(cargo != undefined) {
                $scope.dataFrm.toSend.socio.perfil = cargo.perfil;
            }
        }
    };
    
    $scope.dataFrm = {
        isValid: false,
        data: {
            id: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            pessoa: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            cargo: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            nome: { value: '', notEmpty: true, valid: true, StringfyFrom: '' },
            email: { value: '', notEmpty: true, valid: true, StringfyFrom: '' },
            telefone: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            celular: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            inicio: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            fim: { value: '', notEmpty: false, valid: true, StringfyFrom: '' }
        },
        toSend: {
            id: '',
            pessoa: '',
            cargo: '',
            diretoria: $scope.diretoria,
            sociedade: $scope.sociedade,
            nome: '',
            email: '',
            telefone: '',
            celular: '',
            inicio: '',
            fim: '',
            socio: {
                id: '',
                diretoria: true,
                sociedade: $scope.sociedade,
                pessoa: '',
                perfil: ''
            }
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
            Oficial.getMe($scope.id).then(function(r) {
                $scope.data = r;
                $scope.load();
            }, function(e) { 
                $state.go($scope.back.parseState().state, $scope.back.parseState().params); 
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
                promise = Oficial.createForSociedade($scope.dataFrm.toSend);
            }
            else
            {
                promise = Oficial.edit($scope.id, $scope.dataFrm.toSend);
            }

            promise.then(function(r) {
                Socio.changeDiretoria($scope.dataFrm.toSend.socio.id, $scope.dataFrm.toSend.socio).then(function(r) {
                    $dialogs.onSave().then(function() {
                        $state.reload();
                    }, function() {});
                }, function(e) {
                    $notifications.err(e);
                });
            }, function(e) {
                $notifications.err(e);
            });
        } else {
            $notifications.err("Há campos incorretos!");
        }
    };
    
    
    $scope.changeStat = function(d) {
        $dialogs.beforeChange().then(function() {
            Oficial.changeStat(d.id, d).then(function(r) {
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
            Oficial.remove(d.id).then(function() {
                d.socio.diretoria = false;
                Socio.changeDiretoria(d.socio.id, d.socio).then(function() {
                    $timeout(function() {
                        $dialogs.onRemove().then(function() {
                            $state.reload();
                        }, function() {});
                    }, 100);
                }, function(e) {
                    $notifications.err(e);
                });
            }, function(e) {
                $notifications.err(e);
            });
        }, function() {  });
    };

    $scope.removeSelected = function() {

        var ids = [];
        var socios = [];
        for (var k in $scope.markList) {
            ids.push($scope.markList[k].id);
            socios.push($scope.markList[k].socio);
        }

        $dialogs.beforeRemove().then(function() {
            Oficial.removeAll({
                ids: ids
            }).then(function() {
                Socio.cancelDiretorias({ socios: socios }).then(function() {
                    $timeout(function() {
                        $dialogs.onRemove().then(function() {
                            $state.reload();
                        }, function() {});
                    }, 100);
                }, function(e) {
                    $notifications.err(e);
                });
            }, function(e) {
                $notifications.err(e);
            });
        }, function() {});
    };
    
});


