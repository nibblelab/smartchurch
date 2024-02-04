angular.module('SmartChurchPanel').controller('AgendaDaSinodalCtrl', function ($scope, $state, $stateParams, $localstorage, $notifications, $rootScope, $dialogs, $timeout, 
                    $ibge, $cep, $context, $cache,
                    Data, TagAgenda, Agenda) { 
    
    /* config */
    $scope.opTitle = $stateParams.title;
    $scope.search = $stateParams.search;
    $scope.data = $stateParams.data;
    $scope.perms = $stateParams.perms;
    $scope.back = ($stateParams.back == '') ? 'SmartChurchPanel.sinodal.agenda.buscar()' : $stateParams.back + '()';
    $scope.id = $stateParams.id;
    $scope.objForm = 'Agenda';
    $scope.objList = 'Agenda';
    $scope.sinodal = $context.getSinodalContext();
    $scope.sinodalData = $rootScope.USER.getContextByKeyAndId(Contexts.SINODAIS, $scope.sinodal);
    $scope.isCreate = false;

    /* verificação de permissão do state */
    if(!$scope.USER.doIHaveAccess($scope.user, $scope.perms)) {
        $state.go('SmartChurchPanel.sempermissao');
    }
    
    $scope.localPerms = {
        add: 'AgendaSinodalSave',
        edit: 'AgendaSinodalSave',
        changeStat: 'AgendaSinodalBlock',
        remove: 'AgendaSinodalRemove'
    };
    
    /* common */
    $scope.listTags = [];
    $scope.listTiposResponsaveis = [];
    $scope.listResponsaveis = [];
    $scope.listStatus = $cache.get().status;
    $scope.listUFs = $cache.get().ufs;
    $scope.listCidades = $cache.get().cidades;
    
    TagAgenda.getAll('', '', '', '', '', '', 'geral').then(function(r) {
        if(r.total > 0) {
            for(var k in r.datas) {
                r.datas[k]['checked'] = false;
            }
            $scope.listTags = r.datas;
        }

        $scope.$broadcast('preLoad');
    }, function(e) { console.log(e); $scope.testError(e); });
    
    /* search */
    $scope.storage_cache_name = 'search_agendadasinodal';
    $scope.searchBy = '';
    $scope.list = [];
    $scope.createSearchObject = function(only_non_existent) {
        if(only_non_existent != undefined && only_non_existent == true) {
            var prev_search = $localstorage.getObject($scope.storage_cache_name, {});
            if (!$.isEmptyObject(prev_search)) {
                return;
            }
        }
        
        $localstorage.setObject($scope.storage_cache_name, {
            searchBy: '',
            inicio: '',
            termino: '',
            stat: '',
            responsavel: $scope.sinodal,
            recorrente: false,
            domingo: false,
            segunda: false,
            terca: false,
            quarta: false,
            quinta: false,
            sexta: false,
            sabado: false,
            tags: '',
            orderBy: 'time_ini,asc'
        });
    };
    $scope.createSearchObject(true);
    $scope.clear = function() {
        $scope.searchBy = '';
        $scope.inicio = '';
        $scope.termino = '';
        $scope.stat = '';
        $scope.responsavel = $scope.sinodal;
        $scope.recorrente = false;
        $scope.domingo = false;
        $scope.segunda = false;
        $scope.terca = false;
        $scope.quarta = false;
        $scope.quinta = false;
        $scope.sexta = false;
        $scope.sabado = false;
        $scope.tags = '';
        $scope.createSearchObject();
        $scope.doSearch();
        $scope.clearMarkList();
    };
    $scope.filterEnabled = false;
    $scope.enableFilters = function() {
        $scope.filterEnabled = !$scope.filterEnabled;
    };
    $scope.inicio = '';
    $scope.termino = '';
    $scope.stat = '';
    $scope.responsavel = $scope.sinodal;
    $scope.recorrente = false;
    $scope.domingo = false;
    $scope.segunda = false;
    $scope.terca = false;
    $scope.quarta = false;
    $scope.quinta = false;
    $scope.sexta = false;
    $scope.sabado = false;
    $scope.tags = '';
    $scope.filterUsed = false;
    $scope.isFilterUsed = function() {
        $scope.filterUsed = ($scope.stat != '' || $scope.inicio != '' || $scope.termino != '' || 
                                $scope.recorrente || $scope.domingo || $scope.segunda || $scope.terca || $scope.quarta || 
                                $scope.quinta || $scope.sexta || $scope.sabado || $scope.tags != '');
    };
    $scope.filterTags = function() {
        $scope.tags = '';
        for(var k in $scope.listTags) {
            if($scope.listTags[k].checked) {
                if($scope.tags != '') {
                    $scope.tags += ',';
                }
                $scope.tags += $scope.listTags[k].id;
            }
        }
    };
    $scope.checkFilteredTags = function() {
        for(var k in $scope.listTags) {
            $scope.listTags[k].checked = ($scope.tags.includes($scope.listTags[k].id));
        }
    };
    $scope.calendarObj = {};
    $scope.resetCalendar = function() {
        if(!$.isEmptyObject($scope.calendarObj)) {
            $scope.calendarObj.fullCalendar('destroy');
            $scope.calendarObj = {};
        }
    };
    $scope.loadCalendar = function() {
        
        if($scope.list.length > 0) {
            $scope.calendarObj = $('#calendar').fullCalendar({
                header: {
                    left: 'month basicWeek basicDay agendaWeek agendaDay',
                    center: 'title',
                    right: 'today prev,next'
                },
                locale: 'pt-br',
                events: $scope.list,
                eventClick: $scope.onSelectEvent,
                eventRender: function(event, element) {
                    var stat = (event.stat == 'ATV') ? 'Ativo' : 'Não Ativo';
                    var ttl = '<b>Atividade: </b> '+ event.title + '<br>'+
                                '<b>Status: </b> '+ stat + '<br>'+
                                '<b>Início: </b> '+ event.inicio_str + '<br>'+
                                '<b>Término: </b> '+ event.termino_str + '<br>'
                        ;
                    if(event.tags_str != '') {
                        ttl += '<b>Tags: </b> '+ event.tags_str + '<br>';
                    }
                    ttl += '<i>Clique para ver as opções</i><br>';
                    
                    $(element).tooltip({
                        html: true,
                        title: ttl
                    });
                    
                    var popover_id = getRandomId();
                    element.attr('data-popover-id',popover_id);
                    
                    var opts = '';
                    if($scope.USER.doIHavePermission($scope.user, 'AgendaSinodalBlock')) {
                        if(event.stat == 'ATV')
                        {
                            opts += '<button type="button" class="btn btn-white btn-xs calendar-status" data-popover-id="'+popover_id+'" data-status="'+event.stat+'" > '+
                                    '    <i class="fas fa-lock"></i>'+
                                '</button>'
                            ;
                        }
                        else {
                            opts += '<button type="button" class="btn btn-white btn-xs calendar-status" data-popover-id="'+popover_id+'" data-status="'+event.stat+'" > '+
                                    '    <i class="fas fa-lock-open"></i>'+
                                '</button>'
                            ;
                        }
                    }
                    if($scope.USER.doIHavePermission($scope.user, 'AgendaSinodalSave')) {
                        opts += '&nbsp;<button type="button" class="btn btn-warning btn-xs calendar-edit" data-popover-id="'+popover_id+'"> '+
                                    '    <i class="fas fa-edit"></i>'+
                                '</button>'
                            ;
                    }
                    if($scope.USER.doIHavePermission($scope.user, 'AgendaSinodalRemove')) {
                        opts += '&nbsp;<button type="button" class="btn btn-danger btn-xs calendar-remove" data-popover-id="'+popover_id+'" > '+
                                    '    <i class="fas fa-trash-alt"></i>'+
                                '</button>'
                            ;
                    }
                    
                    $(element).popover({
                        html: true,
                        sanitize: false,
                        title: 'Opções',
                        content: opts
                    });
                }
            });
        }
    };
    $scope.onSelectEvent = function(event) {
        $('.calendar-status').click(function() {
            $('.fc-event[data-popover-id="'+$(this).data('popover-id')+'"]').popover('hide');
            $scope.changeStat(event.data_id, $(this).data('status'));
        });
        $('.calendar-edit').click(function() {
            $('.fc-event[data-popover-id="'+$(this).data('popover-id')+'"]').popover('hide');
            $scope.toEdit(event.data_id);
        });
        $('.calendar-remove').click(function() {
            $('.fc-event[data-popover-id="'+$(this).data('popover-id')+'"]').popover('hide');
            $scope.removebyId(event.data_id);
        });
    };
    $scope.toEdit = function(id) {
        $state.go('SmartChurchPanel.sinodal.agenda.editar', {id: id, title: 'Editando', back: 'SmartChurchPanel.sinodal.agenda.buscar'});
    };
    $scope.doSearch = function(is_new) {
        
        if (is_new != undefined && is_new == true) {
            
            $scope.filterTags();
            var prev_search = $localstorage.getObject($scope.storage_cache_name, {});
            $localstorage.setObject($scope.storage_cache_name, {
                searchBy: $scope.searchBy,
                inicio: $scope.inicio,
                termino: $scope.termino,
                stat: $scope.stat,
                responsavel: $scope.responsavel,
                recorrente: $scope.recorrente,
                domingo: $scope.domingo,
                segunda: $scope.segunda,
                terca: $scope.terca,
                quarta: $scope.quarta,
                quinta: $scope.quinta,
                sexta: $scope.sexta,
                sabado: $scope.sabado,
                tags: $scope.tags
            });
            
        } else {
            var prev_search = $localstorage.getObject($scope.storage_cache_name, {});
            if (!$.isEmptyObject(prev_search)) {
                $scope.searchBy = prev_search.searchBy;
                $scope.inicio = prev_search.inicio;
                $scope.termino = prev_search.termino;
                $scope.stat = prev_search.stat;
                $scope.responsavel = prev_search.responsavel;
                $scope.recorrente = prev_search.recorrente;
                $scope.domingo = prev_search.domingo;
                $scope.segunda = prev_search.segunda;
                $scope.terca = prev_search.terca;
                $scope.quarta = prev_search.quarta;
                $scope.quinta = prev_search.quinta;
                $scope.sexta = prev_search.sexta;
                $scope.sabado = prev_search.sabado;
                $scope.tags = prev_search.tags;
            }
            $scope.checkFilteredTags();
        }
        
        $scope.isFilterUsed();
        $scope.resetCalendar();

        $scope.list = [];
        Agenda.getAllForSinodal('', '', $scope.searchBy, '', '', $scope.stat, $scope.sinodal,
                                    $scope.responsavel, $scope.inicio, $scope.termino,
                                    $scope.recorrente, $scope.domingo, $scope.segunda, 
                                    $scope.terca, $scope.quarta, $scope.quinta, 
                                    $scope.sexta, $scope.sabado, $scope.tags).then(function(r) {
            
            if (r.datas.length > 0) {
                for (var k in r.datas) {
                    r.datas[k]['data_id'] = r.datas[k].id;
                    r.datas[k]['title'] = r.datas[k].nome;
                    
                    if(r.datas[k].stat == 'ATV') {
                        r.datas[k]['backgroundColor'] = '';
                    }
                    else {
                        r.datas[k]['backgroundColor'] = '#ff5b57';
                    }
                    
                    r.datas[k]['tags_str'] = '';
                    if(r.datas[k].tags.length > 0) {
                        for(var j in r.datas[k].tags) {
                            var t = $scope.listTags.filter(x => x.id == r.datas[k].tags[j]);
                            if(t.length > 0) {
                                if(r.datas[k]['tags_str'] != '') {
                                    r.datas[k]['tags_str'] += ', ';
                                }
                                
                                r.datas[k]['tags_str'] += t[0].tag;
                            }
                        }
                    }
                                        
                    if(r.datas[k].recorrente) {
                        // gere os eventos fixos do período
                        var dias_horarios = $.parseJSON(r.datas[k].dias_horarios);
                        for(var d in dias_horarios) {
                            var dias = [];
                            if(dias_horarios[d].domingo) { dias.push(0); }
                            if(dias_horarios[d].segunda) { dias.push(1); }
                            if(dias_horarios[d].terca) { dias.push(2); }
                            if(dias_horarios[d].quarta) { dias.push(3); }
                            if(dias_horarios[d].quinta) { dias.push(4); }
                            if(dias_horarios[d].sexta) { dias.push(5); }
                            if(dias_horarios[d].sabado) { dias.push(6); }
                            r.datas.push({
                                id: getRandomId(),
                                data_id: r.datas[k].id,
                                title: r.datas[k].title,
                                stat: r.datas[k].stat,
                                backgroundColor: r.datas[k].backgroundColor,
                                responsavel_str: r.datas[k].responsavel_str,
                                tags_str: r.datas[k].tags_str,
                                start: dias_horarios[d].inicio,
                                end: dias_horarios[d].termino,
                                inicio_str: dias_horarios[d].inicio,
                                termino_str: dias_horarios[d].termino,
                                dow: dias,
                                recorrente: true
                            });
                        }
                    }
                    else {
                        r.datas[k]['start'] = r.datas[k].time_ini;
                        r.datas[k]['end'] = r.datas[k].time_end;
                        r.datas[k]['inicio_str'] = moment(r.datas[k].time_ini).format('HH:mm');
                        r.datas[k]['termino_str'] = moment(r.datas[k].time_end).format('HH:mm');
                    }
                    
                }
                
                $scope.list = r.datas;
                
                $scope.loadCalendar();
                
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
    
    $('.timepicker').timepicker({
        showMeridian: false
    });
    
    $scope.addDiaHorario = function() {
        $scope.dataFrm.data.dias_horarios.value.push({
            id: getRandomId(),
            domingo: false,
            segunda: false,
            terca: false,
            quarta: false,
            quinta: false,
            sexta: false,
            sabado: false,
            inicio: '',
            termino: ''
        });
    };
    $scope.prepareTags = function(chooseds) {
        $scope.dataFrm.data.tags.value = [];
        for(var k in $scope.listTags) {
            var checked = false;
            if(chooseds != undefined) {
                checked = chooseds.includes($scope.listTags[k].id);
            }
            $scope.dataFrm.data.tags.value.push({
                id: $scope.listTags[k].id,
                tag: $scope.listTags[k].tag,
                cor: $scope.listTags[k].cor,
                checked: checked,
                old_checked: checked
            });
        }
    };
    
    $scope.dataFrm = {
        isValid: false,
        data: {
            id: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            nome: { value: '', notEmpty: true, valid: true, StringfyFrom: '' },
            recorrente: { value: false, notEmpty: false, valid: true, StringfyFrom: '' },
            dias_horarios: { value: [], notEmpty: false, valid: true, StringfyFrom: '' },
            data_ini: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            hora_ini: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            data_end: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            hora_end: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            endereco: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            numero: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            complemento: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            bairro: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            cidade: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            uf: { value: '--', notEmpty: false, valid: true, StringfyFrom: '' },
            cep: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            site: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            facebook: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            instagram: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            youtube: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            vimeo: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            observacoes: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            tags: { value: [], notEmpty: false, valid: true, StringfyFrom: '' }
        },
        toSend: {
            id: '',
            sinodal: $scope.sinodal,
            nome: '',
            logo: '',
            responsavel: $scope.sinodal,
            recorrente: false,
            dias_horarios: '',
            time_ini: '',
            time_end: '',
            endereco: '',
            numero: '',
            complemento: '',
            bairro: '',
            cidade: '',
            uf: '',
            cep: '',
            site: '',
            facebook: '',
            instagram: '',
            youtube: '',
            vimeo: '',
            observacoes: '',
            tags: []
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
            
            if(!$scope.dataFrm.data.recorrente.value) {
                /* se não é recorrente, precisa ter data e hora de começo e fim */
                var tests = ['data_ini','hora_ini','data_end','hora_end'];
                for(var k in tests) {
                    if($scope.dataFrm.data[tests[k]].value == '') {
                        valid = false;
                        $scope.dataFrm.data[tests[k]].valid = false;
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
            
            $scope.dataFrm.toSend.dias_horarios = JSON.stringify($scope.dataFrm.data.dias_horarios.value);
            
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
            
        }
    };
    
    if ($state.current.name.includes('editar') && $scope.id) {
        // edição - via id 
        $scope.$on('preLoad', function() {
            Agenda.getMe($scope.id).then(function(r) {
                $scope.data = r;
                $scope.load();
            }, function(e) { 
                $state.go($scope.back.parseState().state, $scope.back.parseState().params); 
            });
        });
    }
    
    $scope.load = function() {
        for (var k in $scope.data) {
            if ($scope.dataFrm.data.hasOwnProperty(k) && k != 'dias_horarios') {
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
        
        if($scope.dataFrm.data.recorrente.value) {
            $scope.dataFrm.data.dias_horarios.value = $.parseJSON($scope.data.dias_horarios);
        }
                
        $scope.prepareTags($scope.data.tags);
    };
    
    if(!$scope.data && !$scope.search && !$scope.id) {
        // adição
        $scope.isCreate = true;
        $scope.$on('preLoad', function() {
            $scope.prepareTags();
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
                promise = Agenda.createForSinodal($scope.dataFrm.toSend);
            }
            else
            {
                promise = Agenda.edit($scope.id, $scope.dataFrm.toSend);
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
    
    $scope.changeStat = function(id, stat) {
        $dialogs.beforeChange().then(function() {
            Agenda.changeStat(id, {id: id, stat: stat}).then(function(r) {
                $dialogs.onChange().then(function() {
                    $state.reload();
                }, function() {});
            }, function(e) {
                $notifications.err(e);
            });
        }, function() {});
    };
    
    /* remove */
    $scope.removebyId = function(id) {
        $dialogs.beforeRemove().then(function() {
            Agenda.remove(id).then(function() {
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


