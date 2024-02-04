angular.module('SmartChurchPanel').controller('SecretarioDoMinisterioCtrl', function ($scope, $state, $stateParams, $localstorage, $notifications, 
                    $rootScope, $dialogs, $timeout, $context,
                    Data, Secretaria, Servo, Perfil, SysConfig, Secretario) { 
    
    /* config */
    $scope.secretaria = $stateParams.secretaria;
    $scope.opTitle = $stateParams.title;
    $scope.search = $stateParams.search;
    $scope.data = $stateParams.data;
    $scope.perms = $stateParams.perms;
    $scope.sref = {
        back_level2: 'SmartChurchPanel.ministerio.secretarias.buscar()',
        back: ($stateParams.back == '') ? 'SmartChurchPanel.ministerio.secretarias.secretarios.buscar' : $stateParams.back,
        backParams: { secretaria: $scope.secretaria },
        add: "SmartChurchPanel.ministerio.secretarias.secretarios.adicionar",
        addParams: { back: 'SmartChurchPanel.ministerio.secretarias.secretarios.buscar', secretaria: $scope.secretaria },
        edit: "SmartChurchPanel.ministerio.secretarias.secretarios.editar",
        editParams: { id: '', title: 'Editando', back: 'SmartChurchPanel.ministerio.secretarias.secretarios.buscar', secretaria: $scope.secretaria }
    };
    $scope.id = $stateParams.id;
    $scope.objForm = 'Secretário';
    $scope.objList = 'Secretários';
    $scope.context = $context.getActiveContext();
    $scope.ministerio = $context.getMinisterioContext();
    $scope.ministerioData = $rootScope.USER.getContextByKeyAndId(Contexts.MINISTERIOS, $scope.ministerio);
    $scope.isCreate = false;
    
    /* verificação de permissão do state */
    if(!$scope.USER.doIHaveAccess($scope.user, $scope.perms)) {
        $state.go('SmartChurchPanel.sempermissao');
    }
    
    $scope.localPerms = {
        add: 'SecretariaMinisterioPessoa',
        edit: 'SecretariaMinisterioPessoa',
        changeStat: 'SecretariaMinisterioPessoa',
        remove: 'SecretariaMinisterioPessoa'
    };
    
    /* common */
    $scope.listTipos = [];
    $scope.listStatus = [];
    $scope.listSexos = [];
    $scope.listEscolaridade = [];
    $scope.listEstadoCivil = [];
    $scope.secretariaData = { nome: '' };
    $scope.listServos = [];
    $scope.listPerfis = [];
    $scope.listConfigs = [];
    Data.getAll().then(function(r) {
        $scope.listTipos = r.tipos_secretario;
        $scope.listStatus = r.status;
        $scope.listSexos = r.sexo;
        $scope.listEscolaridade = r.escolaridade;
        $scope.listEstadoCivil = r.estado_civil;
        
        Secretaria.getMe($scope.secretaria).then(function(r) {
            $scope.secretariaData = r;

            Servo.getAll('', '', '', '', '', $scope.ministerio).then(function(r) {
                if(r.total > 0) {
                    $scope.listServos = r.datas;
                }
                
                Perfil.getAllNoStaff().then(function(r) {
                    if(r.total > 0) {
                        $scope.listPerfis = r.datas;
                    }
                    
                    SysConfig.getAll().then(function(r) {
                        if(r.total > 0) {
                            $scope.listConfigs = r.datas;
                            $scope.dataFrm.data.perfil.value = $scope.listConfigs[0].perfil_secretario;
                        }

                        $scope.$broadcast('preLoad');
                    }, function(e) { console.log(e); $scope.testError(e); });
                }, function(e) { console.log(e); $scope.testError(e); });
            }, function(e) { console.log(e); $scope.testError(e); });
        }, function(e) { console.log(e); $state.go('SmartChurchPanel.ministeriosecretariasdasociedade.buscar()'); });
    }, function(e) { console.log(e); $scope.testError(e); });
    
    /* search */
    $scope.storage_cache_name = 'search_secretariosdoministerio';
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
            sexo: '',
            escolaridade: '',
            estado_civil: '',
            oficial: false,
            auxiliar: false,
            com_filhos: false,
            sem_filhos: false,
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
    $scope.orderBy = 'nome,desc';
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
    $scope.sexo = '';
    $scope.escolaridade = '';
    $scope.estado_civil = '';
    $scope.oficial = false;
    $scope.auxiliar = false;
    $scope.com_filhos = false;
    $scope.sem_filhos = false;
    $scope.filterUsed = false;
    $scope.isFilterUsed = function() {
        $scope.filterUsed = ($scope.stat != '' || $scope.sexo != '' || $scope.escolaridade != '' || 
                                $scope.estado_civil != '' || $scope.oficial || $scope.auxiliar || 
                                $scope.com_filhos || $scope.sem_filhos);
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
                sexo: $scope.sexo,
                escolaridade: $scope.escolaridade,
                estado_civil: $scope.estado_civil,
                auxiliar: $scope.auxiliar,
                oficial: $scope.oficial,
                com_filhos: $scope.com_filhos,
                sem_filhos: $scope.sem_filhos,
                orderBy: $scope.orderBy
            });
        } else {
            var prev_search = $localstorage.getObject($scope.storage_cache_name, {});
            if (!$.isEmptyObject(prev_search)) {
                $scope.page = prev_search.page;
                $scope.pageSize = prev_search.pageSize;
                $scope.searchBy = prev_search.searchBy;
                $scope.stat = prev_search.stat;
                $scope.sexo = prev_search.sexo;
                $scope.escolaridade = prev_search.escolaridade;
                $scope.estado_civil = prev_search.estado_civil;
                $scope.auxiliar = prev_search.auxiliar;
                $scope.oficial = prev_search.oficial;
                $scope.com_filhos = prev_search.com_filhos;
                $scope.sem_filhos = prev_search.sem_filhos;
                $scope.orderBy = prev_search.orderBy;
                var o_v = $scope.orderBy.split(',');
                $scope.orderField = o_v[0];
                $scope.orderOrientation = o_v[1];
            }
        }
        
        $scope.isFilterUsed();

        $scope.list = [];
        Secretario.getAll($scope.page, $scope.pageSize, $scope.searchBy, $scope.orderBy, '', $scope.secretaria, '', $scope.stat, '', 
                            $scope.auxiliar, $scope.oficial, $scope.sexo, $scope.estado_civil, $scope.escolaridade, 
                            $scope.com_filhos, $scope.sem_filhos).then(function(r) {
            if (r.datas.length > 0) {
                for (var k in r.datas) {
                    r.datas[k]['data_admissao_str'] = '';
                    if(r.datas[k].data_admissao != null) {
                        r.datas[k]['data_admissao_str'] = moment(r.datas[k].data_admissao).format('DD/MM/YYYY');
                    }
                    
                    r.datas[k]['data_demissao_str'] = '';
                    if(r.datas[k].data_demissao != null) {
                        r.datas[k]['data_demissao_str'] = moment(r.datas[k].data_demissao).format('DD/MM/YYYY');
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
                        var estado_civil = $scope.listEstadoCivil.find(x => x.value == r.datas[k].estado_civil);
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
    
    
    /* form */
    
    $scope.onSelectNome = function(item) {
        $scope.dataFrm.data.pessoa.value = item.pessoa;
        $scope.dataFrm.data.nome.value = item.nome;
        $scope.$apply();
    };
    
    $scope.prepareTypeAhead = function() {
        var options = {
            data: $scope.listServos,
            getValue: "nome",
            list: {
                match: {
                    enabled: true
                },
                onChooseEvent: function() {
                    $scope.onSelectNome($("#nome").getSelectedItemData());
                }
            }
        };
        $('#nome').easyAutocomplete(options);
    };
    
    $scope.dataFrm = {
        isValid: false,
        data: {
            id: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            nome: { value: '', notEmpty: true, valid: true, StringfyFrom: '' },
            pessoa: { value: '', notEmpty: true, valid: true, StringfyFrom: '' },
            perfil: { value: '', notEmpty: true, valid: true, StringfyFrom: '' },
            auxiliar: { value: false, notEmpty: false, valid: true, StringfyFrom: '' },
            data_admissao: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            data_demissao: { value: '', notEmpty: false, valid: true, StringfyFrom: '' }
        },
        toSend: {
            id: '',
            secretaria: $scope.secretaria,
            pessoa: '',
            perfil: '',
            auxiliar: false,
            data_admissao: '',
            data_demissao: ''
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
            Secretario.getMe($scope.id).then(function(r) {
                $scope.data = r;
                $scope.load();
            }, function(e) { 
                $state.go($scope.back.parseState().state, $scope.back.parseState().params); 
            });
        });
    }
    
    $scope.load = function() {
        $scope.prepareTypeAhead();
        
        for (var k in $scope.data) {
            if ($scope.dataFrm.data.hasOwnProperty(k)) {
                $scope.dataFrm.data[k].value = $scope.data[k];
            }
        }
        
        if($scope.dataFrm.data.data_admissao.value != null) {
            $scope.dataFrm.data.data_admissao.value = moment($scope.dataFrm.data.data_admissao.value).format('DD/MM/YYYY');
        }
        else {
            $scope.dataFrm.data.data_admissao.value = '';
        }
        
        if($scope.dataFrm.data.data_demissao.value != null) {
            $scope.dataFrm.data.data_demissao.value = moment($scope.dataFrm.data.data_demissao.value).format('DD/MM/YYYY');
        }
        else {
            $scope.dataFrm.data.data_demissao.value = '';
        }
    };
    
    if(!$scope.data && !$scope.search && !$scope.id) {
        // adição
        $scope.isCreate = true;
        $scope.$on('preLoad', function() {
            $scope.prepareTypeAhead();
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
                promise = Secretario.create($scope.dataFrm.toSend);
            }
            else
            {
                promise = Secretario.edit($scope.id, $scope.dataFrm.toSend);
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
            Secretario.changeStat(d.id, d).then(function(r) {
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
            Secretario.remove(d.id).then(function() {
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
            Secretario.removeAll({
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


