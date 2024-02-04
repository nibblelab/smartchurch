angular.module('SmartChurchPanel').controller('FederacaoCtrl', function ($scope, $state, $stateParams, $localstorage, 
                    $notifications, $rootScope, $dialogs, $timeout, $cache,
                    Data, Sinodo, Presbiterio, Sinodal, Federacao) { 
    
    /* config */
    $scope.opTitle = $stateParams.title;
    $scope.search = $stateParams.search;
    $scope.data = $stateParams.data;
    $scope.perms = $stateParams.perms;
    $scope.back = ($stateParams.back == '') ? 'dashboard.federacoes.buscar()' : $stateParams.back + '()';
    $scope.id = $stateParams.id;
    $scope.objForm = 'Federação';
    $scope.objList = 'Federações';
    $scope.isCreate = false;

    /* verificação de permissão do state */
    if(!$scope.USER.doIHaveAccess($scope.user, $scope.perms)) {
        $state.go('SmartChurchPanel.sempermissao');
    }
    
    $scope.localPerms = {
        add: 'FederacaoSave',
        edit: 'FederacaoSave',
        changeStat: 'FederacaoBlock',
        remove: 'FederacaoRemove'
    };
    
    /* common */
    $scope.listStatus = $cache.get().status;
    $scope.listReferencias = $cache.get().sociedades;
    $scope.listSinodos = [];
    $scope.listPresbiterios = [];
    $scope.listSinodais = [];
    Sinodo.getAll().then(function(r) {
        if(r.total > 0) {
            $scope.listSinodos = r.datas;
        }

        Presbiterio.getAll().then(function(r) {
            if(r.total > 0) {
                for(var k in r.datas) {
                    r.datas[k]['show'] = true;
                }
                $scope.listPresbiterios = r.datas;
            }

            Sinodal.getAll().then(function(r) {
                if(r.total > 0) {
                    for(var k in r.datas) {
                        r.datas[k]['show'] = true;
                    }
                    $scope.listSinodais = r.datas;
                }

                $scope.$broadcast('preLoad');
            }, function(e) { console.log(e); $scope.testError(e); });
        }, function(e) { console.log(e); $scope.testError(e); });
    }, function(e) { console.log(e); $scope.testError(e); });
    $scope.filterPresbiterios = function(v) {
        $scope.listPresbiterios.forEach(function (element, index, array) {
            array[index].show = true;
        });
        $scope.listSinodais.forEach(function (element, index, array) {
            array[index].show = true;
        });
        if(v != '') {
            for(var k in $scope.listPresbiterios) {
                if($scope.listPresbiterios[k].sinodo != v) {
                    $scope.listPresbiterios[k].show = false;
                }
            }
            for(var k in $scope.listSinodais) {
                if($scope.listSinodais[k].sinodo != v) {
                    $scope.listSinodais[k].show = false;
                }
            }
        }
    };
    
    /* search */
    $scope.storage_cache_name = 'search_federacoes';
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
            sinodal: '',
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
    $scope.sinodal = '';
    $scope.filterUsed = false;
    $scope.isFilterUsed = function() {
        $scope.filterUsed = ($scope.stat != '' || $scope.sinodo != '' || $scope.presbiterio != '' || $scope.sinodal != '');
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
                sinodal: $scope.sinodal,
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
                $scope.sinodal = prev_search.sinodal;
                $scope.orderBy = prev_search.orderBy;
                var o_v = $scope.orderBy.split(',');
                $scope.orderField = o_v[0];
                $scope.orderOrientation = o_v[1];
            }
        }
        
        $scope.isFilterUsed();
        $scope.filterPresbiterios($scope.sinodo);

        $scope.list = [];
        Federacao.getAll($scope.page, $scope.pageSize, $scope.searchBy, $scope.orderBy, '', $scope.stat, 
                                $scope.sinodo, $scope.presbiterio, $scope.sinodal).then(function(r) {
            if (r.datas.length > 0) {
                for (var k in r.datas) {
                    r.datas[k]['time_cad_str'] = moment(r.datas[k].time_cad).format('DD/MM/YYYY');
                    r.datas[k]['last_mod_str'] = moment(r.datas[k].last_mod).format('DD/MM/YYYY HH:mm');
                    if(r.datas[k].fundacao != null) {
                        r.datas[k]['fundacao_str'] = moment(r.datas[k].fundacao).format('DD/MM/YYYY');
                    }
                    else {
                        r.datas[k]['fundacao_str'] = '';
                    }
                    
                    r.datas[k]['sinodo_str'] = '';
                    var sd = $scope.listSinodos.filter(x => x.id == r.datas[k].sinodo);
                    if(sd.length > 0) {
                        r.datas[k]['sinodo_str'] = sd[0].sigla;
                    }
                    
                    r.datas[k]['presbiterio_str'] = '';
                    var pb = $scope.listPresbiterios.filter(x => x.id == r.datas[k].presbiterio);
                    if(pb.length > 0) {
                        r.datas[k]['presbiterio_str'] = pb[0].sigla;
                    }
                    
                    r.datas[k]['sinodal_str'] = '';
                    var sn = $scope.listSinodais.filter(x => x.id == r.datas[k].sinodal);
                    if(sn.length > 0) {
                        r.datas[k]['sinodal_str'] = sn[0].sigla;
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
    
    $scope.dataFrm = {
        isValid: false,
        data: {
            id: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            sinodo: { value: '', notEmpty: true, valid: true, StringfyFrom: '' },
            presbiterio: { value: '', notEmpty: true, valid: true, StringfyFrom: '' },
            sinodal: { value: '', notEmpty: true, valid: true, StringfyFrom: '' },
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
            id: '',
            sinodo: '',
            presbiterio: '',
            sinodal: '',
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
            Federacao.getMe($scope.id).then(function(r) {
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
        
        if($scope.dataFrm.data.fundacao.value != null) {
            $scope.dataFrm.data.fundacao.value = moment($scope.dataFrm.data.fundacao.value).format('DD/MM/YYYY');
        }
        else {
            $scope.dataFrm.data.fundacao.value = '';
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
                promise = Federacao.create($scope.dataFrm.toSend);
            }
            else
            {
                promise = Federacao.edit($scope.id, $scope.dataFrm.toSend);
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
            Federacao.changeStat(d.id, d).then(function(r) {
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
            Federacao.remove(d.id).then(function() {
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
            Federacao.removeAll({
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


