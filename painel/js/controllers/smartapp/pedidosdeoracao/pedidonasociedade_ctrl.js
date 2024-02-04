angular.module('SmartChurchPanel').controller('PedidosDeOracaoNaSociedadeCtrl', function ($scope, $state, $stateParams, $localstorage, $notifications, $rootScope, 
                    $dialogs, $timeout,
                    Data, PedidoDeOracao) { 
    
    /* config */
    $scope.opTitle = $stateParams.title;
    $scope.search = $stateParams.search;
    $scope.view = $stateParams.view;
    $scope.data = $stateParams.data;
    $scope.perms = $stateParams.perms;
    $scope.id = $stateParams.id;
    $scope.objForm = 'Pedido de Oração';
    $scope.objList = 'Pedidos de Oração';
    $scope.sociedade = $rootScope.USER.getSociedadeData().id;
    $scope.isCreate = false;    

    /* verificação de permissão do state */
    if(!$scope.USER.doIHaveAccess($scope.user, $scope.perms)) {
        $state.go('SmartChurchPanel.sempermissao');
    }
    
    $scope.navStates = {
        add: 'SmartChurchPanel.smartapp.pedidosdeoracaonasociedade.adicionar',
        edit: 'SmartChurchPanel.smartapp.pedidosdeoracaonasociedade.editar',
        search: 'SmartChurchPanel.smartapp.pedidosdeoracaonasociedade.buscar'
    };
    
    $scope.localPerms = {
        add: 'PedidosOracaoSociedadeSave',
        edit: 'PedidosOracaoSociedadeSave',
        remove: 'PedidosOracaoSociedadeRemove'
    };
    
    /* common */
    $scope.listStatus = [];
    Data.getAll().then(function(r) {
        $scope.listStatus = r.status;
        $scope.$broadcast('preLoad');
    }, function(e) { console.log(e); $scope.testError(e); });
    
    /* search */
    $scope.storage_cache_name = 'search_pedidosdeoracaonasociedade';
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
        PedidoDeOracao.getAllForSociedade($scope.page, $scope.pageSize, $scope.searchBy, $scope.orderBy, '', 
                                    $scope.stat, $scope.user.id, $scope.sociedade).then(function(r) {
            if (r.datas.length > 0) {
                for (var k in r.datas) {
                    r.datas[k]['time_cad_str'] = moment(r.datas[k].time_cad).format('DD/MM/YYYY');
                    r.datas[k]['last_mod_str'] = moment(r.datas[k].last_mod).format('DD/MM/YYYY HH:mm');
                    
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
    $scope.summerOpt = {
        height: 300,
        toolbar: [
            ['edit',['undo','redo']],
            ['headline', ['style']],
            ['style', ['bold', 'italic', 'underline', 'superscript', 'subscript', 'strikethrough', 'clear']],
            ['fontface', ['fontname']],
            ['textsize', ['fontsize']],
            ['fontclr', ['color']],
            ['alignment', ['ul', 'ol', 'paragraph', 'lineheight']],
            ['height', ['height']],
            ['table', ['table']],
            ['insert', ['link','picture','video','hr']],
            ['view', ['fullscreen', 'codeview']],
            ['help', ['help']]
        ]
    };
    
    /* ----------- FORMULÁRIO PROPRIAMENTE DITO ------------ */
    $scope.dataFrm = {
        isValid: false,
        data: {
            id: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            pedido: { value: '', notEmpty: true, valid: true, StringfyFrom: '' }
        },
        toSend: {
            id: '',
            sociedade: $scope.sociedade,
            autor: $scope.user.id,
            pedido: ''
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
            // prepare os dados de evento 
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
            PedidoDeOracao.getMe($scope.id).then(function(r) {
                $scope.data = r;
                $scope.load();
            }, function(e) {
                $state.go($scope.navStates.back); 
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
                promise = PedidoDeOracao.createForSociedade($scope.dataFrm.toSend);
            }
            else
            {
                promise = PedidoDeOracao.edit($scope.dataFrm.toSend.id, $scope.dataFrm.toSend);
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
    $scope.remove = function(d) {
        
        $dialogs.beforeRemove().then(function() {
            PedidoDeOracao.remove(d.id).then(function() {
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


