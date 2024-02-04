angular.module('SmartChurchPanel').controller('ClienteCtrl', function ($scope, $state, $stateParams, $localstorage, $notifications, $rootScope, $dialogs, $timeout,
                    $modulos, $generator, $cache, 
                    Data, Perfil, Sinodo, Presbiterio, Templo, SysConfig, Cliente) { 
    
    /* config */
    $scope.opTitle = $stateParams.title;
    $scope.search = $stateParams.search;
    $scope.data = $stateParams.data;
    $scope.perms = $stateParams.perms;
    $scope.back = ($stateParams.back == '') ? 'SmartChurchPanel.staff.clientes.buscar()' : $stateParams.back + '()';
    $scope.id = $stateParams.id;
    $scope.objForm = 'Cliente';
    $scope.objList = 'Clientes';
    $scope.isCreate = false;

    /* verificação de permissão do state */
    if(!$scope.USER.doIHaveAccess($scope.user, $scope.perms)) {
        $state.go('SmartChurchPanel.sempermissao');
    }
    
    $scope.localPerms = {
        add: 'ClienteSave',
        edit: 'ClienteSave',
        changeStat: 'ClienteBlock',
        remove: 'ClienteRemove'
    };
    
    /* common */
    $scope.listStatus = $cache.get().status;
    $scope.listPerfis = [];
    $scope.listSinodos = [];
    $scope.listPresbiterios = [];
    $scope.listTemplos = [];
    $scope.listConfigs = [];
    Perfil.getAllNoStaff().then(function(r) {
        if(r.total > 0) {
            $scope.listPerfis = r.datas;
        }

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
                        $scope.listTemplos = r.datas;
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
    $scope.filterTemplos = function(v) {
        $scope.listTemplos.forEach(function (element, index, array) {
            array[index].show = true;
        });
        if(v != '') {
            for(var k in $scope.listTemplos) {
                if($scope.listTemplos[k].presbiterio != v) {
                    $scope.listTemplos[k].show = false;
                }
            }
        }
    };
    
    /* search */
    $scope.storage_cache_name = 'search_clientes';
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
            sinodo: '',
            presbiterio: '',
            igreja: '',
            orderBy: 'time_cad,desc'
        });
    };
    $scope.createSearchObject(true);
    $scope.clear = function() {
        $scope.searchBy = '';
        $scope.page = 1;
        $scope.createSearchObject();
        $scope.orderField = 'time_cad';
        $scope.orderOrientation = 'desc';
        $scope.doSearch();
        $scope.clearMarkList();
    };
    $scope.orderField = 'time_cad';
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
    $scope.orderBy = 'time_cad,desc';
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
    $scope.sinodo = '';
    $scope.presbiterio = '';
    $scope.igreja = '';
    $scope.filterUsed = false;
    $scope.isFilterUsed = function() {
        $scope.filterUsed = ($scope.stat != '' || $scope.sinodo != '' || $scope.presbiterio != '' || $scope.igreja != '');
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
                sinodo: $scope.sinodo,
                presbiterio: $scope.presbiterio,
                igreja: $scope.igreja,
                orderBy: $scope.orderBy
            });
        } else {
            var prev_search = $localstorage.getObject($scope.storage_cache_name, {});
            if (!$.isEmptyObject(prev_search)) {
                $scope.page = prev_search.page;
                $scope.pageSize = prev_search.pageSize;
                $scope.searchBy = prev_search.searchBy;
                $scope.stat = prev_search.stat;
                $scope.sinodo = prev_search.sinodo;
                $scope.presbiterio = prev_search.presbiterio;
                $scope.igreja = prev_search.igreja;
                $scope.orderBy = prev_search.orderBy;
                var o_v = $scope.orderBy.split(',');
                $scope.orderField = o_v[0];
                $scope.orderOrientation = o_v[1];
            }
        }
        
        $scope.isFilterUsed();
        $scope.filterPresbiterios($scope.sinodo);
        $scope.filterTemplos($scope.presbiterio);

        $scope.list = [];
        Cliente.getAll($scope.page, $scope.pageSize, $scope.searchBy, $scope.orderBy, '', $scope.stat, 
                            $scope.sinodo, $scope.presbiterio, $scope.igreja).then(function(r) {
            if (r.datas.length > 0) {
                for (var k in r.datas) {
                    r.datas[k].mestre['time_cad_str'] = moment(r.datas[k].mestre.time_cad).format('DD/MM/YYYY');
                    r.datas[k].mestre['last_mod_str'] = moment(r.datas[k].mestre.last_mod).format('DD/MM/YYYY HH:mm');
                    
                    r.datas[k].igreja['sinodo_str'] = '';
                    if(r.datas[k].igreja.sinodo != null) {
                        var s = $scope.listSinodos.filter(x => x.id == r.datas[k].igreja.sinodo);
                        if(s.length > 0) {
                            r.datas[k].igreja['sinodo_str'] = s[0].sigla;
                        }
                    }
                    
                    r.datas[k].igreja['presbiterio_str'] = '';
                    if(r.datas[k].igreja.presbiterio != null) {
                        var p = $scope.listPresbiterios.filter(x => x.id == r.datas[k].igreja.presbiterio);
                        if(p.length > 0) {
                            r.datas[k].igreja['presbiterio_str'] = p[0].sigla;
                        }
                    }
                    
                    r.datas[k].igreja['igreja_str'] = '';
                    if(r.datas[k].igreja.id != null) {
                        var i = $scope.listTemplos.filter(x => x.id == r.datas[k].igreja.id);
                        if(i.length > 0) {
                            r.datas[k].igreja['igreja_str'] = i[0].nome;
                        }
                    }
                    
                    r.datas[k]['id'] = r.datas[k].mestre.id;
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
    
    $scope.generatePassword = function() {
        var pwd = $generator.randomPwd(10);
        $scope.dataFrm.data.senha.value = pwd;
        $scope.dataFrm.data.r_senha.value = pwd;
        $dialogs.beforeNotify('Senha Gerada', pwd).then(function() {}, function() {});
    };
    
    $scope.checkIsAlreadyAdmin = function() {
        if($scope.dataFrm.data.email.value != '') {
            Cliente.checkIsCliente($scope.dataFrm.data.email.value).then(function(r) {
                if(r.exists) {
                    $notifications.err("Esse usuário já é cliente!");
                    $scope.dataFrm.data.email.value = '';
                }
            }, function() {});
        }
    };
    
    $scope.checkTemploHasAdmin = function() {
        if($scope.dataFrm.data.igreja.value != '') {
            Cliente.checkClienteForTemplo($scope.dataFrm.data.igreja.value).then(function(r) {
                if(r.exists) {
                    $notifications.err("Esse templo já é cliente!");
                    $scope.dataFrm.data.igreja.value = '';
                }
            }, function() {});
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
            sinodo: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            presbiterio: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            igreja: { value: '', notEmpty: true, valid: true, StringfyFrom: '' },
            modulos: { value: $modulos.getForm(), notEmpty: false, valid: true, StringfyFrom: '' }
        },
        toSend: {
            id: '',
            perfil: '',
            nome: '',
            email: '',
            senha: '',
            sinodo: '',
            presbiterio: '',
            igreja: '',
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
    
    if ($state.current.name.includes('editar') && $scope.id) {
        // edição - via id 
        $scope.$on('preLoad', function() {
            Cliente.getMe($scope.id).then(function(r) {
                $scope.data = r;
                $scope.load();
            }, function(e) { 
                $state.go($scope.back.parseState().state, $scope.back.parseState().params); 
            });
        });
    }
    
    $scope.load = function() {
        
        $scope.dataFrm.data.id.value = $scope.id;
        $scope.dataFrm.data.perfil.value = $scope.data.mestre.perfil;
        $scope.dataFrm.data.nome.value = $scope.data.mestre.nome;
        $scope.dataFrm.data.email.value = $scope.data.mestre.email;
        $scope.dataFrm.data.sinodo.value = $scope.data.igreja.sinodo;
        $scope.dataFrm.data.presbiterio.value = $scope.data.igreja.presbiterio;
        $scope.dataFrm.data.igreja.value = $scope.data.igreja.id;
        
        $scope.filterPresbiterios($scope.dataFrm.data.sinodo.value);
        $scope.filterTemplos($scope.dataFrm.data.presbiterio.value);
        
        for(var k in $scope.dataFrm.data.modulos.value) {
            $scope.dataFrm.data.modulos.value[k].checked = ($scope.data.mestre.modulos.indexOf($scope.dataFrm.data.modulos.value[k].id) > -1);
        }
    };
    
    if(!$scope.data && !$scope.search && !$scope.id) {
        // adição
        $scope.isCreate = true;
        $scope.$on('preLoad', function() {
            $scope.dataFrm.data.perfil.value = $scope.listConfigs[0].perfil_cliente;
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
                promise = Cliente.create($scope.dataFrm.toSend);
            }
            else
            {
                promise = Cliente.edit($scope.id, $scope.dataFrm.toSend);
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
            Cliente.changeStat(d.mestre.id, d.mestre).then(function(r) {
                $dialogs.onChange().then(function() {
                    for (var k in $scope.list) {
                        if ($scope.list[k].mestre.id == d.mestre.id) {
                            $scope.list[k].mestre.stat = r.stat;
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
            Cliente.remove(d.mestre.id).then(function() {
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
            ids.push($scope.markList[k].mestre.id);
        }

        $dialogs.beforeRemove().then(function() {
            Cliente.removeAll({
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


