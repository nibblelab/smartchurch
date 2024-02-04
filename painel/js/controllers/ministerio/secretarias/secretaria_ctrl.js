angular.module('SmartChurchPanel').controller('SecretariaDoMinisterioCtrl', function ($scope, $state, $stateParams, $localstorage, $notifications, 
                    $rootScope, $dialogs, $timeout, $ibge, $cep, $context,
                    Data, Secretaria) { 
    
    /* config */
    $scope.opTitle = $stateParams.title;
    $scope.search = $stateParams.search;
    $scope.data = $stateParams.data;
    $scope.perms = $stateParams.perms;
    $scope.sref = {
        back: ($stateParams.back == '') ? 'SmartChurchPanel.ministeriosecretarias.buscar' : $stateParams.back,
        backParams: { },
        add: "SmartChurchPanel.ministerio.secretarias.adicionar",
        addParams: { back: 'SmartChurchPanel.ministerio.secretarias.buscar' },
        edit: "SmartChurchPanel.ministerio.secretarias.editar",
        editParams: { id: '', title: 'Editando', back: 'SmartChurchPanel.ministerio.secretarias.buscar' },
        substate: 'SmartChurchPanel.ministerio.secretarias.secretarios.buscar',
        substateParamName: 'secretaria'
    };
    $scope.id = $stateParams.id;
    $scope.objForm = 'Secretaria';
    $scope.objList = 'Secretarias';
    $scope.ministerio = $context.getMinisterioContext();
    $scope.ministerioData = $rootScope.USER.getContextByKeyAndId(Contexts.MINISTERIOS, $scope.ministerio);
    $scope.isCreate = false;

    /* verificação de permissão do state */
    if(!$scope.USER.doIHaveAccess($scope.user, $scope.perms)) {
        $state.go('SmartChurchPanel.sempermissao');
    }
    
    $scope.localPerms = {
        add: 'SecretariaMinisterioSave',
        edit: 'SecretariaMinisterioSave',
        changeStat: 'SecretariaMinisterioBlock',
        secretario: 'SecretariaMinisterioPessoa',
        remove: 'SecretariaMinisterioRemove'
    };
    
    /* common */
    $scope.listStatus = [];
    Data.getAll().then(function(r) {
        $scope.listStatus = r.status;
        
        $scope.$broadcast('preLoad');
    }, function(e) { console.log(e); $scope.testError(e); });
    
    /* search */
    $scope.storage_cache_name = 'search_secretariasdoministerio';
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
    $scope.filterUsed = false;
    $scope.isFilterUsed = function() {
        $scope.filterUsed = ($scope.stat != '');
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
                orderBy: $scope.orderBy
            });
        } else {
            var prev_search = $localstorage.getObject($scope.storage_cache_name, {});
            if (!$.isEmptyObject(prev_search)) {
                $scope.page = prev_search.page;
                $scope.pageSize = prev_search.pageSize;
                $scope.searchBy = prev_search.searchBy;
                $scope.stat = prev_search.stat;
                $scope.orderBy = prev_search.orderBy;
                var o_v = $scope.orderBy.split(',');
                $scope.orderField = o_v[0];
                $scope.orderOrientation = o_v[1];
            }
        }
        
        $scope.isFilterUsed();

        $scope.list = [];
        Secretaria.getAll($scope.page, $scope.pageSize, $scope.searchBy, $scope.orderBy, '', $scope.stat, 
                            '', '', '', '', $scope.ministerio).then(function(r) {
            if (r.datas.length > 0) {
                for (var k in r.datas) {
                    r.datas[k]['time_cad_str'] = moment(r.datas[k].time_cad).format('DD/MM/YYYY');
                    r.datas[k]['last_mod_str'] = moment(r.datas[k].last_mod).format('DD/MM/YYYY HH:mm');
                                     
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
    $scope.dataFrm = {
        isValid: false,
        data: {
            id: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            nome: { value: '', notEmpty: true, valid: true, StringfyFrom: '' },
            logo: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            email: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            telefone: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            ramal: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            site: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            facebook: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            instagram: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            youtube: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            vimeo: { value: '', notEmpty: false, valid: true, StringfyFrom: '' }
        },
        toSend: {
            id: '',
            nome: '',
            logo: '',
            email: '',
            telefone: '',
            ramal: '',
            site: '',
            facebook: '',
            instagram: '',
            youtube: '',
            vimeo: '',
            ministerio: $scope.ministerio
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
            Secretaria.getMe($scope.id).then(function(r) {
                $scope.data = r;
                $scope.load();
            }, function(e) { 
                $state.go($scope.back.parseState().state, $scope.back.parseState().params); 
            });
        });
    }
    
    $scope.load = function() {
        for (var k in $scope.data) {
            if ($scope.dataFrm.data.hasOwnProperty(k)) {
                $scope.dataFrm.data[k].value = $scope.data[k];
            }
        }
    };
    
    if(!$scope.data && !$scope.search && !$scope.id) {
        // adição
        $scope.isCreate = true;
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
                promise = Secretaria.createForMinisterio($scope.dataFrm.toSend);
            }
            else
            {
                promise = Secretaria.edit($scope.id, $scope.dataFrm.toSend);
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
            Secretaria.changeStat(d.id, d).then(function(r) {
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
            Secretaria.remove(d.id).then(function() {
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
            Secretaria.removeAll({
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


