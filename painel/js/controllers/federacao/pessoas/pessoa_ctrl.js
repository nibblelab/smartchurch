angular.module('SmartChurchPanel').controller('PessoadaFederacaoCtrl', function ($scope, $state, $stateParams, $localstorage, $notifications, $rootScope, $dialogs, $timeout, 
                    $context,
                    Data, Templo, Sociedade, Socio) { 
    
    /* config */
    $scope.opTitle = $stateParams.title;
    $scope.search = $stateParams.search;
    $scope.perms = $stateParams.perms;
    $scope.objList = 'Pessoas';
    $scope.federacao = $context.getFederacaoContext();
    $scope.federacaoData = $rootScope.USER.getContextByKeyAndId(Contexts.FEDERACOES, $scope.federacao);

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
    $scope.listSexos = [];
    $scope.listEstadosCivis = [];
    $scope.listEscolaridade = [];
    $scope.listTemplos = [];
    $scope.listSociedades = [];
    Data.getAll().then(function(r) {
        $scope.listStatus = r.status;
        $scope.listSexos = r.sexo;
        $scope.listEstadosCivis = r.estado_civil;
        $scope.listEscolaridade = r.escolaridade;
        
        Templo.getAll('', '', '', '', '', '', $scope.federacaoData.sinodo, $scope.federacaoData.presbiterio).then(function(r) {
            if(r.total > 0) {
                $scope.listTemplos = r.datas;
            }
            
            Sociedade.getAll('', '', '', '', '', '', '', $scope.federacao, '', '', $scope.federacaoData.reference).then(function(r) {
                if(r.total > 0) {
                    for(var k in r.datas) {
                        r.datas[k]['label'] = r.datas[k].nome;
                        var ig = $scope.listTemplos.find(x => x.id == r.datas[k].igreja);
                        if(ig != undefined) {
                            r.datas[k]['label'] = r.datas[k].nome + ' - ' + ig.nome;
                        }
                    }
                    $scope.listSociedades = r.datas;
                }
                
                $scope.$broadcast('preLoad');
            }, function(e) { console.log(e); $scope.testError(e); });
        }, function(e) { console.log(e); $scope.testError(e); });
    }, function(e) { console.log(e); $scope.testError(e); });
    
    /* search */
    $scope.storage_cache_name = 'search_pessoasdafederacao';
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
            apenas_diretoria: false,
            sociedade: '',
            sexo: '',
            escolaridade: '',
            tem_filhos: false, 
            sem_filhos: false, 
            estado_civil: '',
            orderBy: 'nome,asc'
        });
    };
    $scope.createSearchObject(true);
    $scope.clear = function() {
        $scope.searchBy = '';
        $scope.page = 1;
        $scope.createSearchObject();
        $scope.orderField = 'nome';
        $scope.orderOrientation = 'asc';
        $scope.doSearch();
        $scope.clearMarkList();
    };
    $scope.orderField = 'nome';
    $scope.orderOrientation = 'asc';
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
    $scope.orderBy = 'nome,asc';
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
    $scope.apenas_diretoria = false;
    $scope.sociedade = '';
    $scope.sexo = '';
    $scope.escolaridade = '';
    $scope.tem_filhos = false; 
    $scope.sem_filhos = false; 
    $scope.estado_civil = '';
    $scope.filterUsed = false;
    $scope.isFilterUsed = function() {
        $scope.filterUsed = ($scope.stat != '' || $scope.apenas_diretoria || $scope.sociedade != '' || 
                $scope.sexo != '' || $scope.escolaridade != '' || $scope.tem_filhos || 
                $scope.sem_filhos || $scope.estado_civil != '');
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
                apenas_diretoria: $scope.apenas_diretoria,
                sociedade: $scope.sociedade, 
                sexo: $scope.sexo,
                escolaridade: $scope.escolaridade,
                tem_filhos: $scope.tem_filhos, 
                sem_filhos: $scope.sem_filhos, 
                estado_civil: $scope.estado_civil,
                orderBy: $scope.orderBy
            });
        } else {
            var prev_search = $localstorage.getObject($scope.storage_cache_name, {});
            if (!$.isEmptyObject(prev_search)) {
                $scope.page = prev_search.page;
                $scope.pageSize = prev_search.pageSize;
                $scope.searchBy = prev_search.searchBy;
                $scope.stat = prev_search.stat;
                $scope.apenas_diretoria = prev_search.apenas_diretoria;
                $scope.sociedade = prev_search.sociedade;
                $scope.sexo = prev_search.sexo;
                $scope.escolaridade = prev_search.escolaridade;
                $scope.tem_filhos = prev_search.tem_filhos; 
                $scope.sem_filhos = prev_search.sem_filhos; 
                $scope.estado_civil = prev_search.estado_civil;
                $scope.orderBy = prev_search.orderBy;
                var o_v = $scope.orderBy.split(',');
                $scope.orderField = o_v[0];
                $scope.orderOrientation = o_v[1];
            }
        }
        
        $scope.isFilterUsed();

        $scope.list = [];
        
        Socio.getAll($scope.page, $scope.pageSize, $scope.searchBy, $scope.orderBy, '', $scope.sociedade, '', $scope.stat, '',
                            $scope.apenas_diretoria, '', true, $scope.sexo, $scope.estado_civil, $scope.escolaridade, 
                            $scope.tem_filhos, $scope.sem_filhos, '', $scope.federacao).then(function(r) {
            if (r.datas.length > 0) {
                for (var k in r.datas) {
                    r.datas[k]['sociedade_nome'] = '';
                    var sc = $scope.listSociedades.find(x => x.id == r.datas[k].sociedade);
                    if(sc != undefined) {
                        r.datas[k]['sociedade_nome'] = sc.label;
                    }
                    
                    r.datas[k]['data_nascimento_str'] = '';
                    if(r.datas[k].data_nascimento != null) {
                        r.datas[k]['data_nascimento_str'] = moment(r.datas[k].data_nascimento).format('DD/MM/YYYY');
                    }
                    
                    r.datas[k]['escolaridade_str'] = '';
                    if(r.datas[k].escolaridade != null && r.datas[k].escolaridade != '') {
                        var escolaridade = $scope.listEscolaridade.find(x => x.value == r.datas[k].escolaridade);
                        if(escolaridade != undefined) {
                            r.datas[k]['escolaridade_str'] = escolaridade.label;
                        }
                    }
                    
                    r.datas[k]['estado_civil_str'] = '';
                    if(r.datas[k].estado_civil != null && r.datas[k].estado_civil != '') {
                        var estado_civil = $scope.listEstadosCivis.find(x => x.value == r.datas[k].estado_civil);
                        if(estado_civil != undefined) {
                            r.datas[k]['estado_civil_str'] = estado_civil.label;
                        }
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
    
    
    
});


