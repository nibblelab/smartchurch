angular.module('SmartChurchPanel').controller('EleicaoDaIgrejaCtrl', function ($scope, $state, $stateParams, $localstorage, $notifications, $rootScope, $dialogs, $timeout,
                    $context,
                    Data, Agenda, Evento, Eleicao) { 
    
    /* config */
    $scope.opTitle = $stateParams.title;
    $scope.search = $stateParams.search;
    $scope.data = $stateParams.data;
    $scope.perms = $stateParams.perms;
    $scope.back = ($stateParams.back == '') ? 'dashboard.eleicoesdaigreja.buscar()' : $stateParams.back + '()';
    $scope.id = $stateParams.id;
    $scope.objForm = 'Eleição';
    $scope.objList = 'Eleições';
    $scope.igreja = $context.getIgrejaContext();
    $scope.igrejaData = $rootScope.USER.getContextByKeyAndId(Contexts.IGREJAS, $scope.igreja);
    $scope.isCreate = false;

    /* verificação de permissão do state */
    if(!$scope.USER.doIHaveAccess($scope.user, $scope.perms)) {
        $state.go('SmartChurchPanel.sempermissao');
    }
    
    $scope.localPerms = {
        add: '',
        edit: '',
        changeStat: '',
        remove: ''
    };
    
    /* common */
    $scope.listStatus = [];
    $scope.listFormasPagto = [];
    $scope.listAgendas = [];
    $scope.listEventos = [];
    $scope.formularioInscricao = [];
    $scope.opcaoPagto = {};
    $scope.lotePagto = {};
    Data.getAll().then(function(r) {
        $scope.listStatus = r.status;
        $scope.listFormasPagto = r.formas_pagto;
        $scope.formularioInscricao = r.formulario_inscricao;
        $scope.opcaoPagto = r.opcao_pagto;
        $scope.lotePagto = r.lote_pagto;
        Agenda.getAllForIgreja('', '', '', '', '', '', $scope.igreja).then(function(r) {
            if(r.total > 0) {
                $scope.listAgendas = r.datas;
            }
            
            Evento.getAllForIgreja('', '', '', '', '', '', '', '', '', '', $scope.igreja).then(function(r) {
                if(r.total > 0) {
                    $scope.listEventos = r.datas;
                }
                
                $scope.$broadcast('preLoad');
            }, function(e) { console.log(e); $scope.testError(e); });
        }, function(e) { console.log(e); $scope.testError(e); });
    }, function(e) { console.log(e); $scope.testError(e); });
    
    /* search */
    $scope.storage_cache_name = 'search_eleicoesdaigreja';
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
            inicio: '',
            termino: '',
            apenas_presentes: false,
            apenas_delegados: false,
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
    $scope.inicio = '';
    $scope.termino = '';
    $scope.apenas_presentes = false;
    $scope.apenas_delegados = false;
    $scope.filterUsed = false;
    $scope.isFilterUsed = function() {
        $scope.filterUsed = ($scope.stat != '' || $scope.inicio != '' || $scope.termino != '' || 
                                $scope.apenas_presentes || $scope.apenas_delegados);
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
                inicio: $scope.inicio,
                termino: $scope.termino,
                apenas_presentes: $scope.apenas_presentes,
                apenas_delegados: $scope.apenas_delegados,
                orderBy: $scope.orderBy
            });
        } else {
            var prev_search = $localstorage.getObject($scope.storage_cache_name, {});
            if (!$.isEmptyObject(prev_search)) {
                $scope.page = prev_search.page;
                $scope.pageSize = prev_search.pageSize;
                $scope.searchBy = prev_search.searchBy;
                $scope.stat = prev_search.stat;
                $scope.inicio = prev_search.inicio;
                $scope.termino = prev_search.termino;
                $scope.apenas_presentes = prev_search.apenas_presentes;
                $scope.apenas_delegados = prev_search.apenas_delegados;
                $scope.orderBy = prev_search.orderBy;
                var o_v = $scope.orderBy.split(',');
                $scope.orderField = o_v[0];
                $scope.orderOrientation = o_v[1];
            }
        }
        
        $scope.isFilterUsed();

        $scope.list = [];
        Eleicao.getAllForIgreja($scope.page, $scope.pageSize, $scope.searchBy, $scope.orderBy, '', $scope.stat, '', '',
                                    $scope.inicio, $scope.termino, $scope.apenas_presentes, $scope.apenas_delegados, 
                                    $scope.igreja).then(function(r) {
            if (r.datas.length > 0) {
                for (var k in r.datas) {
                    r.datas[k]['time_cad_str'] = moment(r.datas[k].time_cad).format('DD/MM/YYYY');
                    r.datas[k]['last_mod_str'] = moment(r.datas[k].last_mod).format('DD/MM/YYYY HH:mm');
                    
                    r.datas[k]['time_ini_str'] = '';
                    if(r.datas[k].time_ini != null) {
                        r.datas[k]['time_ini_str'] = moment(r.datas[k].time_ini).format('DD/MM/YYYY HH:mm');
                    }
                    
                    r.datas[k]['time_end_str'] = '';
                    if(r.datas[k].time_end != null) {
                        r.datas[k]['time_end_str'] = moment(r.datas[k].time_end).format('DD/MM/YYYY HH:mm');
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
    $scope.datetimepickerOpts = {
        locale: 'pt-BR'
    };
    
    $scope.onSelectLigacao = function() {
        $timeout(function() { // timeout necessário por conta da sincronização do ng-if no view
            if($scope.dataFrm.data.ligacao.value == 'evt') {
                $scope.prepareTypeAheadEvento();
            }
            else if($scope.dataFrm.data.ligacao.value == 'agd') {
                $scope.prepareTypeAheadAgenda();
            }
        } , 500);
    };
        
    /* ----------- TYPEAHEAD (AGENDA) ------------ */
    $scope.onSelectAgenda = function(item, use_apply, reset_nome) {
        $scope.dataFrm.data.agenda.value = item.id;
        
        if(reset_nome) {
            $scope.dataFrm.data.nome.value = item.nome;
        }
        
        if(item.time_ini != null) {
            $scope.dataFrm.data.time_ini.value = moment(item.time_ini);
        }
        else {
            $scope.dataFrm.data.time_ini.value = '';
        }
        
        if(item.time_end != null) {
            $scope.dataFrm.data.time_end.value = moment(item.time_end);
        }
        else {
            $scope.dataFrm.data.time_end.value = '';
        }
        
        if(use_apply) {
            $scope.$apply();
        }
        
    };
    
    $scope.prepareTypeAheadAgenda = function() {
        var options = {
            data: $scope.listAgendas,
            getValue: "nome",
            list: {
                match: {
                    enabled: true
                },
                onChooseEvent: function() {
                    $scope.onSelectAgenda($("#atividade").getSelectedItemData(), true, true);
                }
            }
        };
        $('#atividade').easyAutocomplete(options);
        
    };
    
    /* ----------- TYPEAHEAD (EVENTOS) ------------ */
    $scope.onSelectEvento = function(item, use_apply, reset_nome) {
        $scope.dataFrm.data.evento.value = item.id;
        
        if(reset_nome) {
            $scope.dataFrm.data.nome.value = item.nome;
        }
        
        if(item.time_ini != null) {
            $scope.dataFrm.data.time_ini.value = moment(item.time_ini);
        }
        else {
            $scope.dataFrm.data.time_ini.value = '';
        }
        
        if(item.time_end != null) {
            $scope.dataFrm.data.time_end.value = moment(item.time_end);
        }
        else {
            $scope.dataFrm.data.time_end.value = '';
        }
        
        if(use_apply) {
            $scope.$apply();
        }
        
    };
    
    $scope.prepareTypeAheadEvento = function() {
        var options = {
            data: $scope.listEventos,
            getValue: "nome",
            list: {
                match: {
                    enabled: true
                },
                onChooseEvent: function() {
                    $scope.onSelectEvento($("#event").getSelectedItemData(), true, true);
                }
            }
        };
        $('#event').easyAutocomplete(options);
    };
    
    /* ----------- FORMULÁRIO PROPRIAMENTE DITO ------------ */
    $scope.dataFrm = {
        isValid: false,
        data: {
            id: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            ligacao: { value: '', notEmpty: true, valid: true, StringfyFrom: '' },
            nome: { value: '', notEmpty: true, valid: true, StringfyFrom: '' },
            apenas_presentes: { value: false, notEmpty: false, valid: true, StringfyFrom: '' },
            apenas_delegados: { value: false, notEmpty: false, valid: true, StringfyFrom: '' },
            atividade: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            agenda: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            event: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            evento: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            time_ini: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            time_end: { value: '', notEmpty: false, valid: true, StringfyFrom: '' }
        },
        toSend: {
            id: '',
            igreja: $scope.igreja,
            nome: '',
            apenas_presentes: false,
            apenas_delegados: false,
            agenda: '',
            evento: '',
            time_ini: '',
            time_end: ''
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
            
            if($scope.dataFrm.data.time_ini.value != '') {
                $scope.dataFrm.toSend.time_ini = moment($scope.dataFrm.data.time_ini.value).format('YYYY-MM-DD HH:mm') + ':00';
            }
            
            if($scope.dataFrm.data.time_end.value != '') {
                $scope.dataFrm.toSend.time_end = moment($scope.dataFrm.data.time_end.value).format('YYYY-MM-DD HH:mm') + ':00';
            }
            
        }
    };
    
    if ($state.current.name.includes('editar') && $scope.id) {
        // edição - via id 
        $scope.$on('preLoad', function() {
            Eleicao.getMe($scope.id).then(function(r) {
                $scope.data = r;
                $scope.load();
            }, function(e) {
                $state.go($scope.back.parseState().state, $scope.back.parseState().params); 
            });
        });
    }
    
    $scope.load = function() {
        var exclude = 'formulario_inscricao,opcoes_pagto,lotes';
        for (var k in $scope.data) {
            if ($scope.dataFrm.data.hasOwnProperty(k) && !exclude.includes(k)) {
                $scope.dataFrm.data[k].value = $scope.data[k];
            }
        }
        
        if($scope.data.agenda != null) {
            $scope.dataFrm.data.ligacao.value = 'agd';
            $scope.onSelectLigacao();
            var ag = $scope.listAgendas.filter(x => x.id == $scope.data.agenda);
            if(ag.length > 0) {
                $scope.onSelectAgenda(ag[0], false, false);
                $scope.dataFrm.data.atividade.value = ag[0].nome;
            }
        }
        
        if($scope.data.evento != null) {
            $scope.dataFrm.data.ligacao.value = 'evt';
            $scope.onSelectLigacao();
            var ev = $scope.listEventos.filter(x => x.id == $scope.data.evento);
            if(ev.length > 0) {
                $scope.onSelectEvento(ev[0], false, false);
                $scope.dataFrm.data.event.value = ev[0].nome;
            }
        }
        
    };
    
    if(!$scope.data && !$scope.search && !$scope.id) {
        // adição
        $scope.isCreate = true;
        $scope.$on('preLoad', function() {
            
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
                promise = Eleicao.createForIgreja($scope.dataFrm.toSend);
            }
            else
            {
                promise = Eleicao.edit($scope.id, $scope.dataFrm.toSend);
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
            Eleicao.changeStat(d.id, d).then(function(r) {
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
            Eleicao.remove(d.id).then(function() {
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
            Eleicao.removeAll({
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
    
    /* ativação de contexto de eleição */
    $scope.eleicao = $context.getEleicaoContext();
    $scope.toggleEleicao = function(d) {
        var id = d.id;
        if($scope.eleicao == id) {
            // desative
            id = '';
        }
        $context.setEleicaoContext(d.id);
        $scope.eleicao = id;
        $state.reload();
    };
    
});


