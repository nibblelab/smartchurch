angular.module('SmartChurchPanel').controller('SerieDeEstudoDaIgrejaCtrl', function ($scope, $state, $stateParams, $localstorage, $notifications, 
                    $rootScope, $dialogs, $timeout, 
                    $context, ApiEndpoint,
                    Data, CongregacaoIgreja, PontoDePregacaoIgreja, Secretaria, Ministerio, 
                    Sociedade, PequenoGrupo, SerieDeEstudo) { 
    
    /* config */
    $scope.opTitle = $stateParams.title;
    $scope.search = $stateParams.search;
    $scope.data = $stateParams.data;
    $scope.perms = $stateParams.perms;
    $scope.back = ($stateParams.back == '') ? 'SmartChurchPanel.igreja.seriesdeestudos.buscar()' : $stateParams.back + '()';
    $scope.id = $stateParams.id;
    $scope.objForm = 'Série de Estudo';
    $scope.objList = 'Séries de Estudo';
    $scope.igreja = $context.getIgrejaContext();
    $scope.igrejaData = $rootScope.USER.getContextByKeyAndId(Contexts.IGREJAS, $scope.igreja);
    $scope.isCreate = false;

    /* verificação de permissão do state */
    if(!$scope.USER.doIHaveAccess($scope.user, $scope.perms)) {
        $state.go('SmartChurchPanel.sempermissao');
    }
    
    $scope.localPerms = {
        add: 'SerieEstudoIgrejaSave',
        edit: 'SerieEstudoIgrejaSave',
        changeStat: 'SerieEstudoIgrejaBlock',
        remove: 'SerieEstudoIgrejaRemove'
    };
    
    /* common */
    $scope.listStatus = [];
    $scope.listCongregacoes = [];
    $scope.listPontos = [];
    $scope.listSecretarias = [];
    $scope.listMinisterios = [];
    $scope.listSociedades = [];
    $scope.listPequenoGrupos = [];
    Data.getAll().then(function(r) {
        $scope.listStatus = r.status;
        CongregacaoIgreja.getAll('', '', '', 'nome,asc', '', Status.ATIVO, $scope.igreja).then(function(r) {
            $scope.listCongregacoes = r.datas;
            PontoDePregacaoIgreja.getAll('', '', '', 'nome,asc', '', Status.ATIVO, $scope.igreja).then(function(r) {
                $scope.listPontos = r.datas;
                Secretaria.getAll('', '', '', 'nome,asc', '', Status.ATIVO, $scope.igreja).then(function(r) {
                    $scope.listSecretarias = r.datas;
                    Ministerio.getAll('', '', '', 'nome,asc', '', Status.ATIVO, $scope.igreja).then(function(r) {
                        $scope.listMinisterios = r.datas;
                        Sociedade.getAll('', '', '', 'nome,asc', '', Status.ATIVO, $scope.igreja).then(function(r) {
                            $scope.listSociedades = r.datas;
                            PequenoGrupo.getAll('', '', '', 'nome,asc', '', Status.ATIVO, $scope.igreja).then(function(r) {
                                $scope.listPequenoGrupos = r.datas;
                                $scope.$broadcast('preLoad');
                            }, function(e) { console.log(e); $scope.testError(e); });
                        }, function(e) { console.log(e); $scope.testError(e); });
                    }, function(e) { console.log(e); $scope.testError(e); });
                }, function(e) { console.log(e); $scope.testError(e); });
            }, function(e) { console.log(e); $scope.testError(e); });
        }, function(e) { console.log(e); $scope.testError(e); });
    }, function(e) { console.log(e); $scope.testError(e); });
    
    
    /* search */
    $scope.storage_cache_name = 'search_seriesdeestudosdaigreja';
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
            destinatarios: $scope.generateDestinarios(),
            orderBy: 'time_cad,desc'
        });
    };
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
    $scope.destinatarios = [];
    $scope.filterUsed = false;
    $scope.checkDestinatarioFilter = function() {
        for(var k in $scope.destinatarios) {
            for(var o in $scope.destinatarios[k].opcoes) {
                if($scope.destinatarios[k].opcoes[o].checked) {
                    return true;
                }
            }
        }
        
        return false;
    };
    $scope.generateDestinatariosIds = function() {
        var ids = '';
        for(var k in $scope.destinatarios) {
            if($scope.destinatarios[k].label != 'general') {
                for(var o in $scope.destinatarios[k].opcoes) {
                    if($scope.destinatarios[k].opcoes[o].checked) {
                        if(ids != '') {
                            ids += ";";
                        }
                        ids += $scope.destinatarios[k].opcoes[o].label;
                    }
                }
            }
        }
        return ids;
    };
    $scope.isFilterUsed = function() {
        $scope.filterUsed = ($scope.stat != '' || $scope.checkDestinatarioFilter());
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
                destinatarios: $scope.destinatarios,
                orderBy: $scope.orderBy
            });
        } else {
            var prev_search = $localstorage.getObject($scope.storage_cache_name, {});
            if (!$.isEmptyObject(prev_search)) {
                $scope.page = prev_search.page;
                $scope.pageSize = prev_search.pageSize;
                $scope.searchBy = prev_search.searchBy;
                $scope.stat = prev_search.stat;
                $scope.destinatarios = prev_search.destinatarios;
                $scope.orderBy = prev_search.orderBy;
                var o_v = $scope.orderBy.split(',');
                $scope.orderField = o_v[0];
                $scope.orderOrientation = o_v[1];
            }
        }
        
        $scope.isFilterUsed();

        $scope.list = [];
        SerieDeEstudo.getAll($scope.page, $scope.pageSize, $scope.searchBy, $scope.orderBy, '', 
                                $scope.stat, $scope.igreja, $scope.generateDestinatariosIds($scope.destinatarios)).then(function(r) {
            if (r.datas.length > 0) {
                for (var k in r.datas) {
                    r.datas[k]['time_cad_str'] = moment(r.datas[k].time_cad).format('DD/MM/YYYY');
                    r.datas[k]['last_mod_str'] = moment(r.datas[k].last_mod).format('DD/MM/YYYY HH:mm');
                    
                    if(r.datas[k].logo != '') {
                        r.datas[k]['logo_url'] = ApiEndpoint.rc + '/' + r.datas[k].logo;
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
            $scope.createSearchObject(true);
            $scope.doSearch();
        });
    }
    
    
    /* form */
    $scope.clearURLLikeString = function(str) {
        return str.toLowerCase()
                    .normalize("NFD")
                    .replace(/[\u0300-\u036f]/g, "")
                    .replace(/[&]/g, "e")
                    .replace(/[\\\?\|\/\$'"]/g, "")
                    .replace(/[\s]/g, "-");
    };
    
    $scope.generateChave = function() {
        $scope.dataFrm.data.chave.value = $scope.clearURLLikeString($scope.dataFrm.data.nome.value);
    };
    
    $scope.generateDestinarioForList = function(list, label, title, destinatarios) {
        var opts = [];
        for(var k in list) {
            opts.push({
                checked: false, 
                label: list[k].id, 
                title: list[k].nome, 
                select: ''
            });
        }
        
        destinatarios.push({
            label: label,
            title: title,
            opcoes: opts
        });
        
        return destinatarios;
    };
    
    $scope.generateDestinarios = function() {
        var destinatarios = [];
        
        destinatarios.push({
            label: 'general',
            title: 'Gerais',
            opcoes: [
                { checked: false, label: 'igreja', title: 'Toda a Igreja', select: 'all' },
                { checked: false, label: 'congregacoes', title: 'Todas as Congregações', select: 'congregacao' },
                { checked: false, label: 'pontos', title: 'Todos os Pontos de Pregação', select: 'ponto' },
                { checked: false, label: 'secretarias', title: 'Todas as Secretarias', select: 'secretaria' },
                { checked: false, label: 'ministerios', title: 'Todos os Ministérios', select: 'ministerio' },
                { checked: false, label: 'sociedades', title: 'Todas as Sociedades', select: 'sociedade' },
                { checked: false, label: 'grupos', title: 'Todos os Pequenos Grupos', select: 'grupo' }
            ]
        });
        
        destinatarios = $scope.generateDestinarioForList($scope.listCongregacoes, 'congregacao', 'Congregações', destinatarios);
        destinatarios = $scope.generateDestinarioForList($scope.listPontos, 'ponto', 'Pontos de Pregação', destinatarios);
        destinatarios = $scope.generateDestinarioForList($scope.listSecretarias, 'secretaria', 'Pontos de Pregação', destinatarios);
        destinatarios = $scope.generateDestinarioForList($scope.listMinisterios, 'ministerio', 'Ministérios', destinatarios);
        destinatarios = $scope.generateDestinarioForList($scope.listSociedades, 'sociedade', 'Sociedades', destinatarios);
        destinatarios = $scope.generateDestinarioForList($scope.listPequenoGrupos, 'grupo', 'Pequenos Grupos', destinatarios);
        
        return destinatarios;
    };
    
    $scope.checkBlock = function(opt) {
        var arr = $scope.dataFrm.data.destinatarios_opts.value;
        if($scope.search) {
            arr = $scope.destinatarios;
        }
        for(var k in arr) {
            if((opt.select == 'all') || (arr[k].label == opt.select)) {
                for(var o in arr[k].opcoes) {
                    arr[k].opcoes[o].checked = opt.checked;
                }
            }
        }
        if($scope.search) {
            $scope.doSearch(true);
        }
    };
    
    $scope.dataFrm = {
        isValid: false,
        data: {
            id: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            nome: { value: '', notEmpty: true, valid: true, StringfyFrom: '' },
            chave: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            arquivo: { value: { name: '', content: '' }, notEmpty: false, valid: true, StringfyFrom: '' },
            logo_url: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            destinatarios_opts: { value: [], notEmpty: false, valid: true, StringfyFrom: '' }
        },
        toSend: {
            id: '',
            igreja: $scope.igreja,
            nome: '',
            chave: '',
            arquivo: { name: '', content: '' },
            destinatarios: ''
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
            
            $scope.dataFrm.toSend.destinatarios = JSON.stringify($scope.dataFrm.data.destinatarios_opts.value);
        }
    };
    
    if ($state.current.name.includes('editar') && $scope.id) {
        // edição - via id 
        $scope.$on('preLoad', function() {
            SerieDeEstudo.getMe($scope.id).then(function(r) {
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
        
        if($scope.data.logo != '') {
            $scope.dataFrm.data.logo_url.value = ApiEndpoint.rc + '/' + $scope.data.logo;
        }
        
        $scope.dataFrm.data.destinatarios_opts.value = $.parseJSON($scope.data.destinatarios);
    };
    
    if(!$scope.data && !$scope.search && !$scope.id) {
        // adição
        $scope.isCreate = true;
        $scope.$on('preLoad', function() {
            $scope.dataFrm.data.destinatarios_opts.value = $scope.generateDestinarios();
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
                promise = SerieDeEstudo.createForIgreja($scope.dataFrm.toSend);
            }
            else
            {
                promise = SerieDeEstudo.edit($scope.id, $scope.dataFrm.toSend);
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
            SerieDeEstudo.changeStat(d.id, d).then(function(r) {
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
            SerieDeEstudo.remove(d.id).then(function() {
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
            SerieDeEstudo.removeAll({
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


