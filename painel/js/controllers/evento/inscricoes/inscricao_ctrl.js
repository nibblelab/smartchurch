angular.module('SmartChurchPanel').controller('InscricaoCtrl', function ($scope, $state, $stateParams, $localstorage, 
                    $notifications, $rootScope, $dialogs, $timeout,
                    $context, $cache, ApiEndpoint,
                    Evento, Pessoa, MembroDaIgreja, Cargo, MotivoRecusa,
                    Sinodo, Presbiterio, Templo, 
                    Sinodal, Federacao, Sociedade, Credencial,
                    Inscricao) { 
    
    /* config */
    $scope.opTitle = $stateParams.title;
    $scope.search = $stateParams.search;
    $scope.data = $stateParams.data;
    $scope.perms = $stateParams.perms;
    $scope.back = ($stateParams.back == '') ? 'SmartChurchPanel.evento.inscricoes.buscar()' : $stateParams.back + '()';
    $scope.id = $stateParams.id;
    $scope.objForm = 'Inscrição';
    $scope.objList = 'Inscrições';
    $scope.evento = $context.getEventoContext();
    $scope.isCreate = false;
    $scope.isEdit = ($state.current.name.includes('editar'));
    $scope.isView = ($state.current.name.includes('ver'));

    /* verificação de permissão do state */
    if(!$scope.USER.doIHaveAccess($scope.user, $scope.perms)) {
        $state.go('SmartChurchPanel.sempermissao');
    }
    
    $scope.localPerms = {
        add: 'InscricaoSave',
        edit: 'InscricaoSave',
        changeStat: 'InscricaoBlock',
        remove: 'InscricaoRemove'
    };
    
    /* common */
    $scope.listStatus = $cache.get().status_inscricao;
    $scope.listInstancias = $cache.get().referencias_cargos;
    $scope.listSexos = $cache.get().sexo;
    $scope.listEstadosCivis = $cache.get().estado_civil;
    $scope.listFormasPagto = $cache.get().formas_pagto;
    $scope.listStatusPagto = $cache.get().pagamento_status;
    $scope.listReferencias = $cache.get().referencias_cargos;
    $scope.eventoData = {};
    $scope.listPessoas = [];
    $scope.listCargos = [];
    $scope.listMotivosRecusa = [];
    $scope.listSinodos = [];
    $scope.listPresbiterios = [];
    $scope.listIgrejas = [];
    $scope.listSinodais = [];
    $scope.listFederacoes = [];
    $scope.listSociedades = [];
    Evento.getMe($scope.evento).then(function(r) {
        $scope.eventoData = r;
        $scope.eventoData.formulario_inscricao = $.parseJSON($scope.eventoData.formulario_inscricao);
        $scope.eventoData.opcoes_pagto = $.parseJSON($scope.eventoData.opcoes_pagto);
        Pessoa.getAllNomes().then(function(r) {
            if(r.total > 0) {
                $scope.listPessoas = r.datas;
            }

            Cargo.getAll().then(function(r) {
                if(r.total > 0) {
                    for(var k in r.datas) {
                        r.datas[k]['show'] = true;
                        var ref = $scope.listReferencias.find(x => x.value == r.datas[k].instancia);
                        if(ref != undefined) {
                            r.datas[k].nome += ' na ' + ref.label;
                        }
                    }
                    $scope.listCargos = r.datas;
                }

                MotivoRecusa.getAll('', '', '', 'motivo,asc').then(function(r) {
                    if(r.total > 0) {
                        $scope.listMotivosRecusa = r.datas;
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
                                    $scope.listIgrejas = r.datas;
                                }

                                Sinodal.getAll('', '', '', 'nome,asc').then(function(r) {
                                    if(r.total > 0) {
                                        for(var k in r.datas) {
                                            r.datas[k]['show'] = true;
                                        }
                                        $scope.listSinodais = r.datas;
                                    }

                                    Federacao.getAll('', '', '', 'nome,asc').then(function(r) {
                                        if(r.total > 0) {
                                            for(var k in r.datas) {
                                                r.datas[k]['show'] = true;
                                            }
                                            $scope.listFederacoes = r.datas;
                                        }

                                        Sociedade.getAll('', '', '', 'nome,asc').then(function(r) {
                                            if(r.total > 0) {
                                                for(var k in r.datas) {
                                                    r.datas[k]['show'] = true;
                                                }
                                                $scope.listSociedades = r.datas;
                                            }

                                            $scope.$broadcast('preLoad');
                                        }, function(e) { console.log(e); $scope.testError(e); });
                                    }, function(e) { console.log(e); $scope.testError(e); });
                                }, function(e) { console.log(e); $scope.testError(e); });
                            }, function(e) { console.log(e); $scope.testError(e); });
                        }, function(e) { console.log(e); $scope.testError(e); });
                    }, function(e) { console.log(e); $scope.testError(e); });
                }, function(e) { console.log(e); $scope.testError(e); });
            }, function(e) { console.log(e); $scope.testError(e); });
        }, function(e) { console.log(e); $scope.testError(e); });
    }, function(e) { console.log(e); $scope.testError(e); });
    $scope.filterCargo = function(v) {
        $scope.listCargos.forEach(function (element, index, array) {
            array[index].show = true;
        });
        if(v != '') {
            for(var k in $scope.listCargos) {
                if($scope.listCargos[k].instancia != v) {
                    $scope.listCargos[k].show = false;
                }
            }
        }
    };
    $scope.filterPresbiterio = function(v) {
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
    $scope.filterIgreja = function(v) {
        $scope.listIgrejas.forEach(function (element, index, array) {
            array[index].show = true;
        });
        if(v != '') {
            for(var k in $scope.listIgrejas) {
                if($scope.listIgrejas[k].presbiterio != v) {
                    $scope.listIgrejas[k].show = false;
                }
            }
        }
    };
    $scope.filterFederacao = function(v) {
        $scope.listFederacoes.forEach(function (element, index, array) {
            array[index].show = true;
        });
        if(v != '') {
            for(var k in $scope.listFederacoes) {
                if($scope.listFederacoes[k].sinodal != v) {
                    $scope.listFederacoes[k].show = false;
                }
            }
        }
    };
    $scope.filterSociedade = function(v) {
        $scope.listSociedades.forEach(function (element, index, array) {
            array[index].show = true;
        });
        if(v != '') {
            for(var k in $scope.listSociedades) {
                if($scope.listSociedades[k].federacao != v) {
                    $scope.listSociedades[k].show = false;
                }
            }
        }
    };
    
    /* search */
    $scope.storage_cache_name = 'search_inscricoes';
    $scope.searchBy = '';
    $scope.page = 1;
    $scope.pageSize = '10';
    $scope.hasPrev = false;
    $scope.hasNext = false;
    $scope.showGraph = false;
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
            stat_pagto: '',
            forma_pagto: '',
            cargo: '',
            delegado: false,
            sinodal: '',
            federacao: '',
            sociedade: '',
            sexo: '',
            estado_civil: '',
            sinodo: '',
            presbiterio: '',
            f_igreja: '',
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
    $scope.stat_pagto = '';
    $scope.forma_pagto = '';
    $scope.cargo = '';
    $scope.delegado = false;
    $scope.sinodal = '';
    $scope.federacao = '';
    $scope.sociedade = '';
    $scope.sexo = '';
    $scope.estado_civil = '';
    $scope.sinodo = '';
    $scope.presbiterio = '';
    $scope.f_igreja = '';
    $scope.filterUsed = false;
    $scope.isFilterUsed = function() {
        $scope.filterUsed = ($scope.stat != '' || $scope.stat_pagto != '' || $scope.forma_pagto != '' || 
                                $scope.cargo != '' || $scope.delegado || $scope.sinodal != '' || 
                                $scope.federacao != '' || $scope.sociedade != '' || $scope.sexo != '' || 
                                $scope.estado_civil != '' || $scope.sinodo != '' || $scope.presbiterio != '' || 
                                $scope.f_igreja != '');
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
                stat_pagto: $scope.stat_pagto,
                forma_pagto: $scope.forma_pagto,
                cargo: $scope.cargo,
                delegado: $scope.delegado,
                sinodal: $scope.sinodal,
                federacao: $scope.federacao,
                sociedade: $scope.sociedade,
                sexo: $scope.sexo,
                estado_civil: $scope.estado_civil,
                sinodo: $scope.sinodo,
                presbiterio: $scope.presbiterio,
                f_igreja: $scope.f_igreja,
                orderBy: $scope.orderBy
            });
        } else {
            var prev_search = $localstorage.getObject($scope.storage_cache_name, {});
            if (!$.isEmptyObject(prev_search)) {
                $scope.page = prev_search.page;
                $scope.pageSize = prev_search.pageSize;
                $scope.searchBy = prev_search.searchBy;
                $scope.stat = prev_search.stat;
                $scope.stat_pagto = prev_search.stat_pagto;
                $scope.forma_pagto = prev_search.forma_pagto;
                $scope.cargo = prev_search.cargo;
                $scope.delegado = prev_search.delegado;
                $scope.sinodal = prev_search.sinodal;
                $scope.federacao = prev_search.federacao;
                $scope.sociedade = prev_search.sociedade;
                $scope.sexo = prev_search.sexo;
                $scope.estado_civil = prev_search.estado_civil;
                $scope.sinodo = prev_search.sinodo;
                $scope.presbiterio = prev_search.presbiterio;
                $scope.f_igreja = prev_search.f_igreja;
                $scope.orderBy = prev_search.orderBy;
                var o_v = $scope.orderBy.split(',');
                $scope.orderField = o_v[0];
                $scope.orderOrientation = o_v[1];
            }
        }
        
        $scope.isFilterUsed();
        $scope.filterPresbiterio($scope.sinodo);
        $scope.filterIgreja($scope.presbiterio);

        $scope.list = [];
        Inscricao.getAll($scope.page, $scope.pageSize, $scope.searchBy, $scope.orderBy, '', $scope.stat, 
                            $scope.evento, '', $scope.stat_pagto, $scope.forma_pagto,
                            $scope.cargo, $scope.delegado, $scope.sinodal, $scope.federacao, 
                            $scope.sociedade, $scope.sexo, $scope.estado_civil, $scope.sinodo, 
                            $scope.presbiterio, $scope.f_igreja).then(function(r) {
            if (r.datas.length > 0) {
                for (var k in r.datas) {
                    r.datas[k]['time_cad_str'] = moment(r.datas[k].time_cad).format('DD/MM/YYYY');
                    r.datas[k]['last_mod_str'] = moment(r.datas[k].last_mod).format('DD/MM/YYYY HH:mm');
                    
                    r.datas[k]['data_nascimento_str'] = '';
                    r.datas[k]['idade_str'] = '';
                    if(r.datas[k].data_nascimento != null) {
                        var _data_nascimento = moment(r.datas[k].data_nascimento);
                        r.datas[k]['data_nascimento_str'] = _data_nascimento.format('DD/MM/YYYY');
                        r.datas[k]['idade_str'] = $scope.now.diff(_data_nascimento, 'years');
                    }
                    
                    r.datas[k]['sinodo_str'] = '';
                    if(r.datas[k].sinodo != null) {
                        var sn = $scope.listSinodos.find(x => x.id == r.datas[k].sinodo);
                        if(sn != undefined) {
                            r.datas[k]['sinodo_str'] = sn.nome;
                        }
                    }
                    
                    r.datas[k]['presbiterio_str'] = '';
                    if(r.datas[k].presbiterio != null) {
                        var pb = $scope.listPresbiterios.find(x => x.id == r.datas[k].presbiterio);
                        if(pb != undefined) {
                            r.datas[k]['presbiterio_str'] = pb.nome;
                        }
                    }
                    
                    r.datas[k]['igreja_str'] = '';
                    if(r.datas[k].igreja != null) {
                        var ig = $scope.listIgrejas.find(x => x.id == r.datas[k].igreja);
                        if(ig != undefined) {
                            r.datas[k]['igreja_str'] = ig.nome;
                        }
                    }
                    
                    r.datas[k]['sinodal_str'] = '';
                    if(r.datas[k].sinodal != null) {
                        var sd = $scope.listSinodais.find(x => x.id == r.datas[k].sinodal);
                        if(sd != undefined) {
                            r.datas[k]['sinodal_str'] = sd.nome;
                        }
                    }
                    
                    r.datas[k]['federacao_str'] = '';
                    if(r.datas[k].federacao != null) {
                        var fd = $scope.listFederacoes.find(x => x.id == r.datas[k].federacao);
                        if(fd != undefined) {
                            r.datas[k]['federacao_str'] = fd.nome;
                        }
                    }
                    
                    r.datas[k]['sociedade_str'] = '';
                    if(r.datas[k].sociedade != null) {
                        var sc = $scope.listSociedades.find(x => x.id == r.datas[k].sociedade);
                        if(sc != undefined) {
                            r.datas[k]['sociedade_str'] = sc.nome;
                        }
                    }
                    
                    r.datas[k]['cargo_str'] = '';
                    if(r.datas[k].cargo != null) {
                        var c = $scope.listCargos.find(x => x.id == r.datas[k].cargo);
                        if(c != undefined) {
                            r.datas[k]['cargo_str'] = c.nome;
                        }
                    }
                    
                    r.datas[k]['valor_pago_str'] = '';
                    if(r.datas[k].valor_pago != 0) {
                        r.datas[k]['valor_pago_str'] = r.datas[k].valor_pago.formatMoney();
                    }
                    
                    r.datas[k]['stat_pagto_str'] = '';
                    if(r.datas[k].stat_pagto != PagtoStatus.NONE) {
                        var f = $scope.listStatusPagto.find(x => x.value == r.datas[k].stat_pagto);
                        if(f != undefined) {
                            r.datas[k]['stat_pagto_str'] = f.label;
                        }
                    }
                    
                    r.datas[k]['data_pagto_str'] = '';
                    if(r.datas[k].data_pagto != null) {
                        var _data_pagto = moment(r.datas[k].data_pagto);
                        r.datas[k]['data_pagto_str'] = _data_pagto.format('DD/MM/YYYY');
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
            $scope.prepareFields();
            $scope.doSearch();
        });
    }
    
    $scope.graphs = {
        status: { 
            data: { 
                NOT: { color: '#ccc', label: 'Não Informado', value: 0 } 
            }, 
            graph: '' 
        },
        sexo: { 
            data: {
                M: { color: '#348fe2', label: 'Masculino', value: 0 },
                F: { color: '#fb5597', label: 'Feminino', value: 0 },
                NOT: { color: '#ccc', label: 'Não Informado', value: 0 }
            }, 
            graph: '' 
        },
        estado_civil: { 
            data: { NOT: { color: '#ccc', label: 'Não Informado', value: 0 } },
            graph: '' 
        },
        cargo: { 
            data: { NOT: { color: '#ccc', label: 'Não Informado', value: 0 } },
            graph: '' 
        },
        sinodo: { 
            data: { NOT: { color: '#ccc', label: 'Não Informado', value: 0 } },
            graph: '' 
        },
        presbiterio: { 
            data: { NOT: { color: '#ccc', label: 'Não Informado', value: 0 } },
            graph: '' 
        },
        igreja: { 
            data: { NOT: { color: '#ccc', label: 'Não Informado', value: 0 } },
            graph: '' 
        },
        sinodal: { 
            data: { NOT: { color: '#ccc', label: 'Não Informado', value: 0 } },
            graph: '' 
        },
        federacao: { 
            data: { NOT: { color: '#ccc', label: 'Não Informado', value: 0 } },
            graph: ''  
        },
        sociedade_interna: { 
            data: { NOT: { color: '#ccc', label: 'Não Informado', value: 0 } },
            graph: '' 
        }
    };
    
    $scope.initGraphs = function() {
        
        for(var k in $scope.graphs) {
            if($scope.graphs[k].graph != '') {
                $('#'+$scope.graphs[k].graph).empty();
            }
        }
        
        for(var k in $scope.listStatus) {
            $scope.graphs.status.data[$scope.listStatus[k].value] = {
                color: randomColor(),
                label: $scope.listStatus[k].label,
                value: 0
            };
        }
        
        for(var k in $scope.listEstadosCivis) {
            $scope.graphs.estado_civil.data[$scope.listEstadosCivis[k].value] = {
                color: randomColor(),
                label: $scope.listEstadosCivis[k].label,
                value: 0
            };
        }
        
        for(var k in $scope.listCargos) {
            $scope.graphs.cargo.data[$scope.listCargos[k].id] = {
                color: randomColor(),
                label: $scope.listCargos[k].nome,
                value: 0
            };
        }
        
        for(var k in $scope.listSinodos) {
            $scope.graphs.sinodo.data[$scope.listSinodos[k].id] = {
                color: randomColor(),
                label: $scope.listSinodos[k].sigla,
                value: 0
            };
        }
        
        for(var k in $scope.listPresbiterios) {
            $scope.graphs.presbiterio.data[$scope.listPresbiterios[k].id] = {
                color: randomColor(),
                label: $scope.listPresbiterios[k].sigla,
                value: 0
            };
        }
        
        for(var k in $scope.listIgrejas) {
            $scope.graphs.igreja.data[$scope.listIgrejas[k].id] = {
                color: randomColor(),
                label: $scope.listIgrejas[k].nome,
                value: 0
            };
        }
        
        for(var k in $scope.listSinodais) {
            $scope.graphs.sinodal.data[$scope.listSinodais[k].id] = {
                color: randomColor(),
                label: $scope.listSinodais[k].sigla,
                value: 0
            };
        }
        
        for(var k in $scope.listFederacoes) {
            $scope.graphs.federacao.data[$scope.listFederacoes[k].id] = {
                color: randomColor(),
                label: $scope.listFederacoes[k].sigla,
                value: 0
            };
        }
        
        for(var k in $scope.listSociedades) {
            $scope.graphs.sociedade_interna.data[$scope.listSociedades[k].id] = {
                color: randomColor(),
                label: $scope.listSociedades[k].nome,
                value: 0
            };
        }
    };
    
    $scope.renderGraph = function(graph_id, graph_data) {
        
        var colors = [];
        var data = [];
        
        for(var k in graph_data.data) {
            colors.push(graph_data.data[k].color);
            data.push(graph_data.data[k]);
        }
        
        graph_data.graph = graph_id;
        
        try {
            Morris.Donut({
                element: graph_id,
                data: data,
                formatter: function (y) { return y },
                resize: true,
                colors: colors
            });
        } catch(e) {
            console.log(e);
        }
    };
    
    $scope.renderGraphs = function() {
        $scope.renderGraph('inscricoes_status', $scope.graphs.status);
        $scope.renderGraph('inscricoes_sexo', $scope.graphs.sexo);
        $scope.renderGraph('inscricoes_estado_civil', $scope.graphs.estado_civil);
        $scope.renderGraph('inscricoes_cargo', $scope.graphs.cargo);
        $scope.renderGraph('inscricoes_sinodo', $scope.graphs.sinodo);
        $scope.renderGraph('inscricoes_presbiterio', $scope.graphs.presbiterio);
        $scope.renderGraph('inscricoes_igreja', $scope.graphs.igreja);
        $scope.renderGraph('inscricoes_sinodal', $scope.graphs.sinodal);
        $scope.renderGraph('inscricoes_federacao', $scope.graphs.federacao);
        $scope.renderGraph('inscricoes_sociedade_interna', $scope.graphs.sociedade_interna);
    };
    
    $scope.prepareGraphs = function() {
        if($scope.showGraph) {
            Inscricao.getAll('', '', '', '', '', '', $scope.evento, '', '', '',
                                '', '', $scope.sinodal, $scope.federacao, 
                                $scope.sociedade, '', '', $scope.sinodo, 
                                $scope.presbiterio, $scope.f_igreja).then(function(r) {
                if (r.datas.length > 0) {
                    $scope.initGraphs();
                    for (var k in r.datas) {
                        
                        if(r.datas[k].stat != null) {
                            $scope.graphs.status.data[r.datas[k].stat].value++;
                        }
                        else {
                            $scope.graphs.status.data.NOT.value++;
                        }
                        
                        if(r.datas[k].sexo != null && r.datas[k].sexo != '-') {
                            $scope.graphs.sexo.data[r.datas[k].sexo].value++;
                        }
                        else {
                            $scope.graphs.sexo.data.NOT.value++;
                        }
                        
                        if(r.datas[k].estado_civil != null && r.datas[k].estado_civil != '---') {
                            $scope.graphs.estado_civil.data[r.datas[k].estado_civil].value++;
                        }
                        else {
                            $scope.graphs.estado_civil.data.NOT.value++;
                        }
                        
                        if(r.datas[k].sinodo != null) {
                            $scope.graphs.sinodo.data[r.datas[k].sinodo].value++;
                        }
                        else {
                            $scope.graphs.sinodo.data.NOT.value++;
                        }

                        if(r.datas[k].presbiterio != null) {
                            $scope.graphs.presbiterio.data[r.datas[k].presbiterio].value++;
                        }
                        else {
                            $scope.graphs.presbiterio.data.NOT.value++;
                        }

                        if(r.datas[k].igreja != null) {
                            $scope.graphs.igreja.data[r.datas[k].igreja].value++;
                        }
                        else {
                            $scope.graphs.igreja.data.NOT.value++;
                        }

                        if(r.datas[k].sinodal != null) {
                            $scope.graphs.sinodal.data[r.datas[k].sinodal].value++;
                        }
                        else {
                            $scope.graphs.sinodal.data.NOT.value++;
                        }

                        if(r.datas[k].federacao != null) {
                            $scope.graphs.federacao.data[r.datas[k].federacao].value++;
                        }
                        else {
                            $scope.graphs.federacao.data.NOT.value++;
                        }

                        if(r.datas[k].sociedade != null) {
                            $scope.graphs.sociedade_interna.data[r.datas[k].sociedade].value++;
                        }
                        else {
                            $scope.graphs.sociedade_interna.data.NOT.value++;
                        }

                        if(r.datas[k].cargo != null) {
                            $scope.graphs.cargo.data[r.datas[k].cargo].value++;
                        }
                        else {
                            $scope.graphs.cargo.data.NOT.value++;
                        }
                    }
                    
                    
                    $scope.renderGraphs();

                } else {
                    $notifications.info(':(', 'Sem dados para exibir');
                }
            }, function(e) {
                $notifications.err(e);
            });
        }
    };
    
    /* form */
    $scope.datetimepickerOpts = {
        locale: 'pt-BR'
    };
    
    $('.datepicker').datepicker({
        todayHighlight: true,
        language: 'pt-BR',
        format: 'dd/mm/yyyy',
        orientation: 'bottom'
    });
    
    /* ----------- TYPEAHEAD (NOMES) ------------ */
    $scope.onSelectNome = function(item, use_apply) {
        $scope.dataFrm.data.pessoa.value = item.id;
        
        /* sincronize a interface, se necessário */
        if(use_apply) {
            $scope.$apply();
        }
        
        /* busque os dados da pessoa */
        Pessoa.getMe(item.id).then(function(r) {
            $scope.dataFrm.data.email.value = r.email;
            $scope.dataFrm.data.sexo.value = r.sexo;
            $scope.dataFrm.data.estado_civil.value = r.estado_civil;
            $scope.dataFrm.data.telefone.value = r.telefone;
            $scope.dataFrm.data.celular_1.value = r.celular_1;
            $scope.dataFrm.data.celular_2.value = r.celular_2;
            
            if(r.data_nascimento != null) {
                $scope.dataFrm.data.data_nascimento.value = moment(r.data_nascimento).format('DD/MM/YYYY');
            }
            
        }, function() {});
        
        /* obtenha os dados de associação caso já não existam */
        if($scope.dataFrm.data.igreja.value == '') {
            MembroDaIgreja.getAssociacaoByPessoa(item.id).then(function(r) {
                $scope.dataFrm.data.sinodo.value = (r.sinodo == null) ? '' : r.sinodo;
                $scope.filterPresbiterio($scope.dataFrm.data.sinodo.value);
                $scope.dataFrm.data.presbiterio.value = (r.presbiterio == null) ? '' : r.presbiterio;
                $scope.filterIgreja($scope.dataFrm.data.presbiterio.value);
                $scope.dataFrm.data.igreja.value = (r.igreja == null) ? '' : r.igreja;
                $scope.dataFrm.data.sinodal.value = (r.sinodal == null) ? '' : r.sinodal;
                $scope.filterFederacao($scope.dataFrm.data.sinodal.value);
                $scope.dataFrm.data.federacao.value = (r.federacao == null) ? '' : r.federacao;
                $scope.filterSociedade($scope.dataFrm.data.federacao.value);
                $scope.dataFrm.data.sociedade.value = (r.sociedade == null) ? '' : r.sociedade;
            }, function() { });
        }
    };
    
    $scope.prepareTypeAhead = function() {
        var options = {
            data: $scope.listPessoas,
            getValue: "nome",
            list: {
                match: {
                    enabled: true
                },
                onChooseEvent: function() {
                    $scope.onSelectNome($("#nome_pessoa").getSelectedItemData(), true);
                }
            }
        };
        $('#nome_pessoa').easyAutocomplete(options);
    };
    
    $scope.formFieldVisibility = {
        inscrito: false,
        igreja: false,
        sociedade_interna: false,
        oficialato: false
    };
    
    $scope.prepareFields = function() {
        var actives = $scope.eventoData.formulario_inscricao.filter(x => x.checked);
        for (var a in actives) {
            if($scope.dataFrm.data.hasOwnProperty(actives[a].field)) {
                $scope.dataFrm.data[actives[a].field].show = true;
            }
        }

        $scope.formFieldVisibility.inscrito = ($scope.dataFrm.data.data_nascimento.show ||
                                                $scope.dataFrm.data.sexo.show || 
                                                $scope.dataFrm.data.estado_civil.show || 
                                                $scope.dataFrm.data.telefone.show ||
                                                $scope.dataFrm.data.celular_1.show || 
                                                $scope.dataFrm.data.celular_2.show);
        $scope.formFieldVisibility.igreja = ($scope.dataFrm.data.sinodo.show || 
                                                $scope.dataFrm.data.presbiterio.show || 
                                                $scope.dataFrm.data.igreja.show);
        $scope.formFieldVisibility.sociedade_interna = ($scope.dataFrm.data.sinodal.show || 
                                                            $scope.dataFrm.data.federacao.show || 
                                                            $scope.dataFrm.data.sociedade.show);
        $scope.formFieldVisibility.oficialato = ($scope.dataFrm.data.delegado.show || 
                                                        $scope.dataFrm.data.cargo.show);
        
        // prepare os campos referentes a pagamento, se houverem
        if($scope.eventoData.valor > 0.00) {
            $scope.dataFrm.data.has_pagto.value = true;
            $scope.dataFrm.data.valor.value = $scope.eventoData.valor;
            $scope.dataFrm.data.lote.value = 'normal';
            
            // teste os lotes
            var lotes = $.parseJSON($scope.eventoData.lotes);
            for(var k in lotes) {
                if(lotes[k].valor > 0.00) {
                    // teste a configuração de data
                    if(lotes[k].data_maxima != '' && lotes[k].data_maxima != null) {
                        var data_max = moment(lotes[k].data_maxima);
                        if($scope.now < data_max) {
                            // data atual menor que a máxima. Use o valor do lote
                            $scope.dataFrm.data.valor.value = lotes[k].valor;
                            $scope.dataFrm.data.lote.value = lotes[k].nome;
                            break;
                        }
                    }
                    else {
                        // não há limite de data nesse lote. Use
                        $scope.dataFrm.data.valor.value = lotes[k].valor;
                        $scope.dataFrm.data.lote.value = lotes[k].nome;
                        break;
                    }
                }
            }
            $scope.dataFrm.data.valor_pago.value = $scope.dataFrm.data.valor.value;
            
            
            // teste as opções de pagamento
            $scope.listFormasPagto.forEach(function (element, index, array) {
                array[index]['show'] = false;
            });
            
            for(var k in $scope.eventoData.opcoes_pagto) {
                var indx = $scope.listFormasPagto.findIndex(x => x.value == $scope.eventoData.opcoes_pagto[k].forma);
                if(indx > -1) {
                    $scope.listFormasPagto[indx].show = true;
                }
            }
        }
    };
    
    /* ----------- FORM EVENT HANDLERS ------------ */
    $scope.onSelectFormaPagto = function() {
        $scope.dataFrm.data.deposito.value.show = ($scope.dataFrm.data.forma_pagto.value == FormasPagto.DEPOSITO);
        var forma = $scope.eventoData.opcoes_pagto.find(x => x.forma == $scope.dataFrm.data.forma_pagto.value);
        if(forma != undefined) {
            $scope.dataFrm.data.deposito.value.banco = forma.deposito.banco;
            $scope.dataFrm.data.deposito.value.agencia = forma.deposito.agencia;
            $scope.dataFrm.data.deposito.value.conta = forma.deposito.conta;
            $scope.dataFrm.data.deposito.value.favorecido = forma.deposito.favorecido;
            $scope.dataFrm.data.deposito.value.documento = forma.deposito.documento;
        }
        
    };
    
    $scope.onSelectPagtoStat = function() {
        $scope.dataFrm.data.is_pago.value = ($scope.dataFrm.data.stat_pagto.value == PagtoStatus.PAGO);
        if(!$scope.dataFrm.data.is_pago.value) {
            $scope.dataFrm.data.valor_pago.value = 0.00;
            $scope.dataFrm.data.data_pagto.value = '';
        }
    };
    
    $scope.loadCredencialDigital = function(id) {
        Credencial.getMe(id).then(function(r) {
            $scope.dataFrm.data.credencial_digital_data.value.show = true;
            $scope.dataFrm.data.credencial_digital_data.value.nome_responsavel = r.nome_responsavel;
            $scope.dataFrm.data.credencial_digital_data.value.email_responsavel = r.email_responsavel;
            $scope.dataFrm.data.credencial_digital_data.value.telefone_responsavel = r.telefone_responsavel;
        }, function() {});
    };
    
    $scope.updateCredencialView = function() {
        if($scope.dataFrm.data.delegado.value) {
            $scope.dataFrm.data.credencial_digital_data.value.show = true;
        }
    };
    
    /* ----------- FORMULÁRIO PROPRIAMENTE DITO ------------ */
    $scope.dataFrm = {
        isValid: false,
        data: {
            id: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            nome: { value: '', notEmpty: true, valid: true, StringfyFrom: '', show: true },
            email: { value: '', notEmpty: true, valid: true, StringfyFrom: '', show: true },
            pessoa: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            sociedade: { value: '', notEmpty: false, valid: true, StringfyFrom: '', show: false },
            federacao: { value: '', notEmpty: false, valid: true, StringfyFrom: '', show: false },
            sinodal: { value: '', notEmpty: false, valid: true, StringfyFrom: '', show: false },
            igreja: { value: '', notEmpty: false, valid: true, StringfyFrom: '', show: false },
            presbiterio: { value: '', notEmpty: false, valid: true, StringfyFrom: '', show: false },
            sinodo: { value: '', notEmpty: false, valid: true, StringfyFrom: '', show: false },
            delegado: { value: false, notEmpty: false, valid: true, StringfyFrom: '', show: false },
            possui_cargo: { value: false, notEmpty: false, valid: true, StringfyFrom: '', show: false },
            cargo_ref: { value: '', notEmpty: false, valid: true, StringfyFrom: '', show: false },
            cargo: { value: '', notEmpty: false, valid: true, StringfyFrom: '', show: false },
            has_pagto: { value: false, notEmpty: false, valid: true, StringfyFrom: '', show: false },
            valor: { value: 0.00, notEmpty: false, valid: true, StringfyFrom: '', show: false },
            lote: { value: '', notEmpty: false, valid: true, StringfyFrom: '', show: false },
            forma_pagto: { value: '', notEmpty: false, valid: true, StringfyFrom: '', show: false },
            deposito: { value: {
                    show: false,
                    banco: '',
                    agencia: '',
                    conta: '',
                    favorecido: '',
                    documento: ''
            }, notEmpty: false, valid: true, StringfyFrom: '', show: false },
            credencial_digital: { value: '', notEmpty: false, valid: true, StringfyFrom: '', show: false },
            credencial_digital_data: { value: {
                    show: false,
                    nome_responsavel: '',
                    email_responsavel: '',
                    telefone_responsavel: ''
            }, notEmpty: false, valid: true, StringfyFrom: '', show: false},
            stat_pagto: { value: '', notEmpty: false, valid: true, StringfyFrom: '', show: false },
            is_pago: { value: false, notEmpty: false, valid: true, StringfyFrom: '', show: false },
            valor_pago: { value: 0.00, notEmpty: false, valid: true, StringfyFrom: '', show: false },
            data_pagto: { value: '', notEmpty: false, valid: true, StringfyFrom: '', show: false },
            data_nascimento: { value: '', notEmpty: false, valid: true, StringfyFrom: '', show: false },
            sexo: { value: '', notEmpty: false, valid: true, StringfyFrom: '', show: false },
            estado_civil: { value: '', notEmpty: false, valid: true, StringfyFrom: '', show: false },
            telefone: { value: '', notEmpty: false, valid: true, StringfyFrom: '', show: false },
            celular_1: { value: '', notEmpty: false, valid: true, StringfyFrom: '', show: false },
            celular_2: { value: '', notEmpty: false, valid: true, StringfyFrom: '', show: false }
        },
        toSend: {
            id: '',
            evento: $scope.evento,
            pessoa: '',
            sociedade: '',
            federacao: '',
            sinodal: '',
            igreja: '',
            presbiterio: '',
            sinodo: '',
            delegado: false,
            cargo_ref: '',
            cargo: '',
            credencial: '',
            has_pagto: false,
            forma_pagto: '',
            stat_pagto: '',
            valor_pago: '',
            data_pagto: '',
            credencial_digital: '',
            credencial_digital_data: {
                id: '',
                nome_responsavel: '',
                email_responsavel: '',
                telefone_responsavel: ''
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
                if ($scope.dataFrm.data[k].StringfyFrom != '') {
                    $scope.dataFrm.data[k].value = JSON.stringify($scope.dataFrm.data[$scope.dataFrm.data[k].StringfyFrom].value);
                }
                if ($scope.dataFrm.toSend.hasOwnProperty(k)) {
                    $scope.dataFrm.toSend[k] = $scope.dataFrm.data[k].value;
                }
            }
            
        }
    };
    
    if (($state.current.name.includes('editar') || $state.current.name.includes('ver')) && $scope.id) {
        // edição/visualização - via id 
        $scope.$on('preLoad', function() {
            $scope.prepareTypeAhead();
            $scope.prepareFields();
            Inscricao.getMe($scope.id).then(function(r) {
                $scope.data = r;
                $scope.load();
            }, function(e) {
                $state.go($scope.back.parseState().state, $scope.back.parseState().params); 
            });
        });
    }
    
    $scope.load = function() {
        if($scope.data.sinodo != null) {
            $scope.filterPresbiterio($scope.data.sinodo);
        }
        
        if($scope.data.presbiterio != null) {
            $scope.filterIgreja($scope.data.presbiterio);
        }
        
        if($scope.data.sinodal != null) {
            $scope.filterFederacao($scope.data.sinodal);
        }
        
        if($scope.data.federacao != null) {
            $scope.filterSociedade($scope.data.federacao);
        }
        
        for (var k in $scope.data) {
            if ($scope.dataFrm.data.hasOwnProperty(k)) {
                $scope.dataFrm.data[k].value = $scope.data[k];
            }
        }
        
        if($scope.dataFrm.data.data_pagto.value != null) {
            $scope.dataFrm.data.data_pagto.value = moment($scope.dataFrm.data.data_pagto.value).format('DD/MM/YYYY');
        }
        else {
            $scope.dataFrm.data.data_pagto.value = '';
        }
        
        var p = $scope.listPessoas.find(x => x.id == $scope.data.pessoa);
        if(p != undefined) {
            $scope.dataFrm.data.nome.value = p.nome;
            $scope.onSelectNome(p, false);
        }
        
        
        $scope.onSelectFormaPagto();
        $scope.onSelectPagtoStat();
        if($scope.data.cargo != null) {
            $scope.dataFrm.data.possui_cargo.value = true;
        }
        
        if($scope.data.credencial_digital != null) {
            $scope.loadCredencialDigital($scope.data.credencial_digital);
        }
        else {
            if($scope.data.delegado) {
                $scope.dataFrm.data.credencial_digital_data.value.show = true;
            }
        }
    };
    
    
    if(!$scope.data && !$scope.search && !$scope.id) {
        // adição
        $scope.isCreate = true;
        $scope.$on('preLoad', function() {
            if($scope.eventoData.inscricoes_ativas) {
                $scope.prepareTypeAhead();
                $scope.prepareFields();
            }
            else {
                $state.go('SmartChurchPanel.sempermissao');
            }
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
                promise = Inscricao.create($scope.dataFrm.toSend);
            }
            else
            {
                promise = Inscricao.edit($scope.id, $scope.dataFrm.toSend);
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
        Inscricao.changeStat(d.id, d).then(function(r) {
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
    };
    
    $scope.approve = function(d) {
        d.stat = 'APR';
        $dialogs.beforeChange().then(function() {
            $scope.changeStat(d);
        }, function() {});
    };
    
    $scope.recusaData = { motivo_rescusa : '' };
    $scope.recuse = function(d) {
        $scope.recusaData = $.extend(true, $scope.recusaData, d);
        $scope.recusaData.stat = 'DEN';
        $('#recusaModal').modal('show');
    };
    $scope.recusar = function() {
        $('#recusaModal').modal('hide');
        $scope.changeStat($scope.recusaData);
    };
    
    /* remove */
    $scope.remove = function(d) {
        
        $dialogs.beforeRemove().then(function() {
            Inscricao.remove(d.id).then(function() {
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
            Inscricao.removeAll({
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
    
    /* validação */
    $scope.validacao = { link: '' };
    $scope.generateChaveResponsavel = function() {
        if($scope.dataFrm.data.credencial_digital.value != null && $scope.dataFrm.data.credencial_digital.value != '') {
            Inscricao.getResponsavelKey($scope.evento, $scope.dataFrm.data.credencial_digital.value).then(function(r) {
                $scope.validacao.link = ApiEndpoint.validacao + '?key=' + r.chave;
                $('#linkModal').modal('show');
            }, function() {});
        }
        
    };
    
});


