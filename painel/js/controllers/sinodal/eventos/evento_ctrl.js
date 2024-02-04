angular.module('SmartChurchPanel').controller('EventoDaSinodalCtrl', function ($scope, $state, $stateParams, $localstorage, $notifications, 
                    $rootScope, $dialogs, $timeout,
                    $context, ApiEndpoint, $cache,
                    Agenda, SysConfig, EventoDTO, Evento) { 
    
    /* config */
    $scope.opTitle = $stateParams.title;
    $scope.search = $stateParams.search;
    $scope.data = $stateParams.data;
    $scope.perms = $stateParams.perms;
    $scope.back = ($stateParams.back == '') ? 'SmartChurchPanel.sinodal.eventos.buscar()' : $stateParams.back + '()';
    $scope.id = $stateParams.id;
    $scope.objForm = 'Evento';
    $scope.objList = 'Eventos';
    $scope.sinodal = $context.getSinodalContext();
    $scope.sinodalData = $rootScope.USER.getContextByKeyAndId(Contexts.SINODAIS, $scope.sinodal);
    $scope.isCreate = false;
    $scope.formInscricaoScope = 'common,people,church,society,subscribe';

    /* verificação de permissão do state */
    if(!$scope.USER.doIHaveAccess($scope.user, $scope.perms)) {
        $state.go('SmartChurchPanel.sempermissao');
    }
    
    $scope.navStates = {
        add: 'SmartChurchPanel.sinodal.eventos.adicionar',
        edit: 'SmartChurchPanel.sinodal.eventos.editar',
        search: 'SmartChurchPanel.sinodal.eventos.buscar'
    };
    
    $scope.localPerms = {
        add: 'EventoSinodalSave',
        edit: 'EventoSinodalSave',
        changeStat: 'EventoSinodalBlock',
        manage: 'EventoSinodalManage',
        remove: 'EventoSinodalRemove'
    };
    
    /* common */
    $scope.listConfigs = [];
    $scope.listStatus = $cache.get().status;
    $scope.listFormasPagto = $cache.get().formas_pagto;
    $scope.formularioInscricao = $cache.get().formulario_inscricao;
    $scope.opcaoPagto = $cache.get().opcao_pagto;
    $scope.lotePagto = $cache.get().lote_pagto;
    $scope.listUFs = $cache.get().ufs;
    $scope.listCidades = $cache.get().cidades;
    SysConfig.getAll().then(function(r) {
        if(r.total > 0) {
            $scope.listConfigs = r.datas;
        }

        $scope.$broadcast('preLoad');
    }, function(e) { console.log(e); $scope.testError(e); });
    
    /* search */
    $scope.storage_cache_name = 'search_eventosdasinodal';
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
            inscricao_ativa: false,
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
    $scope.inscricao_ativa = false;
    $scope.filterUsed = false;
    $scope.isFilterUsed = function() {
        $scope.filterUsed = ($scope.stat != '' || $scope.inicio != '' || $scope.termino != '' || $scope.inscricao_ativa);
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
                inscricao_ativa: $scope.inscricao_ativa,
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
                $scope.inscricao_ativa = prev_search.inscricao_ativa;
                $scope.orderBy = prev_search.orderBy;
                var o_v = $scope.orderBy.split(',');
                $scope.orderField = o_v[0];
                $scope.orderOrientation = o_v[1];
            }
        }
        
        $scope.isFilterUsed();

        $scope.list = [];
        Evento.getAllForSinodal($scope.page, $scope.pageSize, $scope.searchBy, $scope.orderBy, '', $scope.stat, '', 
                                    $scope.inicio, $scope.termino, $scope.inscricao_ativa, $scope.sinodal).then(function(r) {
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
    $('.datepicker').datepicker({
        todayHighlight: true,
        language: 'pt-BR',
        format: 'dd/mm/yyyy',
        orientation: 'bottom'
    });
    $('.datepicker-top').datepicker({
        todayHighlight: true,
        language: 'pt-BR',
        format: 'dd/mm/yyyy',
        orientation: 'top'
    });
    
    $('.timepicker').timepicker({
        showMeridian: false
    });
        
    /**
     * Monta o formulário de inscrição
     * 
     * @param {string} scope escopo de campos
     * @param {array} old formulário anterior (caso de edição)
     * @returns {void}
     */
    $scope.prepareFormularioInscricao = function(scope, old) {
        $scope.dataFrm.data.formulario_inscricao.value = [];
        for(var k in $scope.formularioInscricao) {
            if(scope.includes($scope.formularioInscricao[k].scope)) {
                if(old != undefined) {
                    var f = old.filter(x => x.field == $scope.formularioInscricao[k].field);
                    if(f.length > 0) {
                        $scope.formularioInscricao[k].checked = f[0].checked;
                    }
                }
                
                $scope.dataFrm.data.formulario_inscricao.value.push($scope.formularioInscricao[k]);
            }
        }
        // reload triggers do formulário
        $scope.verifyInscForm();
    };
    
    /* ----------- FORMA DE PAGTO ------------ */
    $scope.addOpcaoPagto = function() {
        var opt = $.extend({ }, $scope.opcaoPagto);
        opt.id = getRandomId();
        $scope.dataFrm.data.opcoes_pagto.value.push(opt);
    };
    
    $scope.removeOpcaoPagto = function(opt) {
        var indx = $scope.dataFrm.data.opcoes_pagto.value.findIndex(x => x.id == opt.id);
        if(indx > -1) {
            $scope.dataFrm.data.opcoes_pagto.value.splice(indx, 1);
        }
    };
    
    $scope.onSelectFormaPagto = function(opt) {
        opt.deposito.selected = false;
        opt.pagseguro.selected = false;
        opt.boleto.selected = false;
        if(opt.forma == FormasPagto.DEPOSITO) {
            opt.deposito.selected = true;
        }
        else if(opt.forma == FormasPagto.PAGSEGURO) {
            opt.pagseguro.selected = true;
        }
        else if(opt.forma == FormasPagto.BOLETOFACIL) {
            opt.boleto.selected = true;
        }
    };
    
    /* ----------- LOTES ------------ */
    $scope.addLote = function() {
        var lote = $.extend({ }, $scope.lotePagto);
        lote.id = getRandomId();
        $scope.dataFrm.data.lotes.value.push(lote);
    };
    
    $scope.removeLote = function(lote) {
        var indx = $scope.dataFrm.data.lotes.value.findIndex(x => x.id == lote.id);
        if(indx > -1) {
            $scope.dataFrm.data.lotes.value.splice(indx, 1);
        }
    };
    
    
    /* ----------- FORMULÁRIO PROPRIAMENTE DITO ------------ */
    $scope.dataFrm = {
        isValid: false,
        data: EventoDTO.getDataForm(),
        toSend: EventoDTO.getDataSendForSinodal($scope.sinodal),
        triggers: {
            delegado: false
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
            var exclude = 'agenda';
            for (var k in $scope.dataFrm.data) {
                if(!exclude.includes(k)) {
                    if ($scope.dataFrm.data[k].StringfyFrom != '') {
                        $scope.dataFrm.data[k].value = JSON.stringify($scope.dataFrm.data[$scope.dataFrm.data[k].StringfyFrom].value);
                    }
                    if ($scope.dataFrm.toSend.hasOwnProperty(k)) {
                        $scope.dataFrm.toSend[k] = $scope.dataFrm.data[k].value;
                    }
                }
            }
            
            if($scope.dataFrm.data.data_ini.value != '') {
                $scope.dataFrm.toSend.time_ini = moment($scope.dataFrm.data.data_ini.value, 'DD/MM/YYYY').format('YYYY-MM-DD');
                if($scope.dataFrm.data.hora_ini.value != '') {
                    $scope.dataFrm.toSend.time_ini += ' ' + $scope.dataFrm.data.hora_ini.value + ':00';
                }
                else {
                    $scope.dataFrm.toSend.time_ini += ' 00:00:00';
                }
            }
            
            if($scope.dataFrm.data.data_end.value != '') {
                $scope.dataFrm.toSend.time_end = moment($scope.dataFrm.data.data_end.value, 'DD/MM/YYYY').format('YYYY-MM-DD');
                if($scope.dataFrm.data.hora_end.value != '') {
                    $scope.dataFrm.toSend.time_end += ' ' + $scope.dataFrm.data.hora_end.value + ':59';
                }
                else {
                    $scope.dataFrm.toSend.time_end += ' 23:59:59';
                }
            }
            
            if($scope.dataFrm.data.data_fim_inscricao.value != '') {
                $scope.dataFrm.toSend.fim_inscricao = moment($scope.dataFrm.data.data_fim_inscricao.value, 'DD/MM/YYYY').format('YYYY-MM-DD');
                if($scope.dataFrm.data.hora_fim_inscricao.value != '') {
                    $scope.dataFrm.toSend.fim_inscricao += ' ' + $scope.dataFrm.data.hora_fim_inscricao.value + ':59';
                }
                else {
                    $scope.dataFrm.toSend.fim_inscricao += ' 23:59:59';
                }
            }
            
            if($scope.dataFrm.data.data_max_delegados.value != '') {
                $scope.dataFrm.toSend.data_delegados = moment($scope.dataFrm.data.data_max_delegados.value, 'DD/MM/YYYY').format('YYYY-MM-DD');
                if($scope.dataFrm.data.hora_max_delegados.value != '') {
                    $scope.dataFrm.toSend.data_delegados += ' ' + $scope.dataFrm.data.hora_max_delegados.value + ':59';
                }
                else {
                    $scope.dataFrm.toSend.data_delegados += ' 23:59:59';
                }
            }
            
            $scope.dataFrm.toSend.formulario_inscricao = JSON.stringify($scope.dataFrm.data.formulario_inscricao.value);
            $scope.dataFrm.toSend.opcoes_pagto = JSON.stringify($scope.dataFrm.data.opcoes_pagto.value);
            $scope.dataFrm.toSend.lotes = JSON.stringify($scope.dataFrm.data.lotes.value);
            
            
            // prepare a agenda 
            for(var k in $scope.dataFrm.data.agenda.data) {
                if ($scope.dataFrm.toSend.agenda.hasOwnProperty(k)) {
                    $scope.dataFrm.toSend.agenda[k] = $scope.dataFrm.data.agenda.data[k].value;
                }
            }
            
            $scope.dataFrm.toSend.agenda.nome = $scope.dataFrm.toSend.nome;
            $scope.dataFrm.toSend.agenda.logo = $scope.dataFrm.toSend.logo;
            $scope.dataFrm.toSend.agenda.time_ini = $scope.dataFrm.toSend.time_ini;
            $scope.dataFrm.toSend.agenda.time_end = $scope.dataFrm.toSend.time_end;
        }
    };
    
    $scope.verifyInscForm = function() {
        $scope.dataFrm.triggers.delegado = false;
        for (var k in $scope.dataFrm.data.formulario_inscricao.value) {
            if($scope.dataFrm.data.formulario_inscricao.value[k].field == 'delegado' && $scope.dataFrm.data.formulario_inscricao.value[k].checked) {
                $scope.dataFrm.triggers.delegado = true;
            }
        }
    };
    
    $scope.prepareTags = function() {
        $scope.dataFrm.toSend.agenda.tags = [{
            id: $scope.listConfigs[0].tag_evento,
            checked: true,
            old_checked: true
        }];
    };
    
    if ($state.current.name.includes('editar') && $scope.id) {
        // edição - via id 
        $scope.$on('preLoad', function() {
            $scope.prepareTags();
            Evento.getMe($scope.id).then(function(r) {
                $scope.data = r;
                Agenda.getMe($scope.data.agenda).then(function(r) {
                    $scope.data.agenda = r;
                    $scope.load();
                }, function(e) {
                    $state.go($scope.back.parseState().state, $scope.back.parseState().params); 
                });
            }, function(e) {
                $state.go($scope.back.parseState().state, $scope.back.parseState().params); 
            });
        });
    }
    
    $scope.load = function() {
        // carregue os dados de evento
        var exclude = 'formulario_inscricao,opcoes_pagto,lotes,agenda';
        for (var k in $scope.data) {
            if ($scope.dataFrm.data.hasOwnProperty(k) && !exclude.includes(k)) {
                $scope.dataFrm.data[k].value = $scope.data[k];
            }
        }
        
        if($scope.data.time_ini != null) {
            var t_ini = moment($scope.data.time_ini);
            $scope.dataFrm.data.data_ini.value = t_ini.format('DD/MM/YYYY');
            $scope.dataFrm.data.hora_ini.value = t_ini.format('HH:mm');
        }
        else {
            $scope.dataFrm.data.data_ini.value = '';
            $scope.dataFrm.data.hora_ini.value = '';
        }
        
        if($scope.data.time_end != null) {
            var t_end = moment($scope.data.time_end);
            $scope.dataFrm.data.data_end.value = t_end.format('DD/MM/YYYY');
            $scope.dataFrm.data.hora_end.value = t_end.format('HH:mm');
        }
        else {
            $scope.dataFrm.data.data_end.value = '';
            $scope.dataFrm.data.hora_end.value = '';
        }
        
        if($scope.data.fim_inscricao != null) {
            var f_inc = moment($scope.data.fim_inscricao);
            $scope.dataFrm.data.data_fim_inscricao.value = f_inc.format('DD/MM/YYYY');
            $scope.dataFrm.data.hora_fim_inscricao.value = f_inc.format('HH:mm');
        }
        else {
            $scope.dataFrm.data.data_fim_inscricao.value = '';
            $scope.dataFrm.data.hora_fim_inscricao.value = '';
        }
        
        if($scope.data.data_delegados != null) {
            var d_inc = moment($scope.data.data_delegados);
            $scope.dataFrm.data.data_max_delegados.value = d_inc.format('DD/MM/YYYY');
            $scope.dataFrm.data.hora_max_delegados.value = d_inc.format('HH:mm');
        }
        else {
            $scope.dataFrm.data.data_max_delegados.value = '';
            $scope.dataFrm.data.hora_max_delegados.value = '';
        }
        
        $scope.prepareFormularioInscricao($scope.formInscricaoScope, $.parseJSON($scope.data.formulario_inscricao));
        $scope.dataFrm.data.opcoes_pagto.value = $.parseJSON($scope.data.opcoes_pagto);
        $scope.dataFrm.data.lotes.value = $.parseJSON($scope.data.lotes);
        
        // carregue os dados de agenda
        for(var k in $scope.data.agenda) {
            if ($scope.dataFrm.data.agenda.data.hasOwnProperty(k)) {
                $scope.dataFrm.data.agenda.data[k].value = $scope.data.agenda[k];
            }
        }
        $scope.dataFrm.toSend.agenda.id = $scope.data.agenda.id;
    };
    
    if(!$scope.data && !$scope.search && !$scope.id) {
        // adição
        $scope.isCreate = true;
        $scope.$on('preLoad', function() {
            $scope.prepareTags();
            $scope.prepareFormularioInscricao($scope.formInscricaoScope);
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
            var promise_agenda = {};
            var promise = {};
            if($scope.isCreate)
            {
                promise_agenda = Agenda.createForSinodal($scope.dataFrm.toSend.agenda);
            }
            else
            {
                promise_agenda = Agenda.edit($scope.dataFrm.toSend.agenda.id, $scope.dataFrm.toSend.agenda);
            }
            
            promise_agenda.then(function(r) {
                $scope.dataFrm.toSend.agenda.id = r.id;
                if($scope.isCreate)
                {
                    promise = Evento.createForSinodal($scope.dataFrm.toSend);
                }
                else
                {
                    promise = Evento.edit($scope.id, $scope.dataFrm.toSend);
                }
                promise.then(function(r) {
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
            Agenda.changeStat(d.agenda, d).then(function(r) {
                Evento.changeStat(d.id, d).then(function(r) {
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
            }, function(e) {
                $notifications.err(e);
            });
        }, function() {});
    };
    
    /* remove */
    $scope.remove = function(d) {
        
        $dialogs.beforeRemove().then(function() {
            Evento.remove(d.id).then(function() {
                Agenda.remove(d.agenda).then(function() { }, function(e) {});
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
        var ids_agenda = [];
        for (var k in $scope.markList) {
            ids.push($scope.markList[k].id);
            ids_agenda.push($scope.markList[k].agenda);
        }

        $dialogs.beforeRemove().then(function() {
            Evento.removeAll({
                ids: ids
            }).then(function() {
                Agenda.removeAll({ ids: ids_agenda }).then(function() { }, function(e) { });
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
    $scope.evento = $context.getEventoContext();
    $scope.toggleEvento = function(d) {
        var id = d.id;
        if($scope.evento == id) {
            // desative
            id = '';
        }
        $context.setEventoContext(d.id);
        $context.addContextMenu(Menus.EVENTO);
        $scope.evento = id;
        $state.reload();
    };
    
    /* link de inscrição */
    $scope.link_evento = '';
    $scope.linkEvento = function(d) {
        $scope.link_evento = ApiEndpoint.inscricoes + '/' + btoa(d.chave + '|' + d.ref_tp + '|' + d.ref);
        $('#linkModal').modal('show');
    };
    
});


