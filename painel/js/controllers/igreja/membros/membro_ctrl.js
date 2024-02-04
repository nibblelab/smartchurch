angular.module('SmartChurchPanel').controller('MembroDaIgrejaCtrl', function ($scope, $state, $stateParams, $localstorage, $notifications, $rootScope, $dialogs, $timeout, 
                    $ibge, $cep, $context, $cache,
                    FamiliaDTO,
                    Pessoa, Perfil, Profissao, Doacao, Necessidade, SysConfig, MembroDaIgreja) { 
    
    /* config */
    $scope.opTitle = $stateParams.title;
    $scope.search = $stateParams.search;
    $scope.data = $stateParams.data;
    $scope.perms = $stateParams.perms;
    $scope.back = ($stateParams.back == '') ? 'SmartChurchPanel.igreja.membros.buscar()' : $stateParams.back + '()';
    $scope.id = $stateParams.id;
    $scope.objForm = 'Membro';
    $scope.objList = 'Membros';
    $scope.igreja = $context.getIgrejaContext();
    $scope.igrejaData = $rootScope.USER.getContextByKeyAndId(Contexts.IGREJAS, $scope.igreja);
    $scope.isCreate = false;

    /* verificação de permissão do state */
    if(!$scope.USER.doIHaveAccess($scope.user, $scope.perms)) {
        $state.go('SmartChurchPanel.sempermissao');
    }
    
    $scope.localPerms = {
        add: 'MembroIgrejaSave',
        edit: 'MembroIgrejaSave',
        changeStat: 'MembroIgrejaBlock',
        remove: 'MembroIgrejaRemove',
        especiais: 'MembroIgrejaEspeciais'
    };
    
    /* common */
    $scope.listStatus = $cache.get().status;
    $scope.listSexos = $cache.get().sexo;
    $scope.listEstadosCivis = $cache.get().estado_civil;
    $scope.listEscolaridade = $cache.get().escolaridade;
    $scope.listProfissoes = [];
    $scope.listDoacoes = [];
    $scope.listNecessidadesEspeciais = [];
    $scope.listPerfis = [];
    $scope.listConfigs = [];
    $scope.listUFs = $cache.get().ufs;
    $scope.listCidades = $cache.get().cidades;
    $scope.listRelacoes = $cache.get().relacao_familiar;
    $scope.familiaFactory = FamiliaDTO;
    Profissao.getAll('', '', '', 'nome,asc').then(function(r) {
        if(r.total > 0) {
            $scope.listProfissoes = r.datas;
        }

        Doacao.getAll('', '', '', 'nome,asc').then(function(r) {
            if(r.total > 0) {
                $scope.listDoacoes = r.datas;
            }

            Necessidade.getAll('', '', '', 'nome,asc').then(function(r) {
                if(r.total > 0) {
                    $scope.listNecessidadesEspeciais = r.datas;
                }

                Perfil.getAllNoStaff().then(function(r) {
                    if(r.total > 0) {
                        $scope.listPerfis = r.datas;
                    }

                    SysConfig.getAll().then(function(r) {
                        if(r.total > 0) {
                            $scope.listConfigs = r.datas;
                        }

                        $scope.$broadcast('preLoad');
                    }, function(e) { console.log(e); $scope.testError(e); });
                }, function(e) { console.log(e); $scope.testError(e); });
            }, function(e) { console.log(e); $scope.testError(e); });
        }, function(e) { console.log(e); $scope.testError(e); });
    }, function(e) { console.log(e); $scope.testError(e); });
    
    /* search */
    $scope.storage_cache_name = 'search_membrosdaigreja';
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
            orderBy: 'time_cad,desc',
            comungante: false, 
            nao_comungante: false, 
            arrolado: false, 
            nao_arrolado: false, 
            especial: false, 
            criancas: false, 
            adolescentes: false, 
            jovens: false, 
            adultos: false, 
            idosos: false, 
            aniversariantes: false, 
            sexo: '',
            escolaridade: '',
            tem_filhos: false, 
            sem_filhos: false, 
            estado_civil: ''
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
    $scope.comungante = false;
    $scope.nao_comungante = false;
    $scope.arrolado = false;
    $scope.nao_arrolado = false;
    $scope.especial = false; 
    $scope.criancas = false; 
    $scope.adolescentes = false; 
    $scope.jovens = false;
    $scope.adultos = false; 
    $scope.idosos = false; 
    $scope.aniversariantes = false;
    $scope.sexo = '';
    $scope.escolaridade = '';
    $scope.tem_filhos = false; 
    $scope.sem_filhos = false; 
    $scope.estado_civil = '';
    $scope.filterUsed = false;
    $scope.isFilterUsed = function() {
        $scope.filterUsed = ($scope.stat != '' || $scope.comungante || $scope.nao_comungante || 
                $scope.arrolado || $scope.nao_arrolado || $scope.especial ||
                $scope.criancas || $scope.adolescentes || $scope.jovens || $scope.adultos || $scope.idosos ||
                $scope.aniversariantes || $scope.sexo != '' || $scope.escolaridade != '' || $scope.tem_filhos || 
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
                orderBy: $scope.orderBy,
                comungante: $scope.comungante, 
                nao_comungante: $scope.nao_comungante, 
                arrolado: $scope.arrolado, 
                nao_arrolado: $scope.nao_arrolado, 
                especial: $scope.especial, 
                criancas: $scope.criancas, 
                adolescentes: $scope.adolescentes, 
                jovens: $scope.jovens, 
                adultos: $scope.adultos, 
                idosos: $scope.idosos, 
                aniversariantes: $scope.aniversariantes, 
                sexo: $scope.sexo,
                escolaridade: $scope.escolaridade,
                tem_filhos: $scope.tem_filhos, 
                sem_filhos: $scope.sem_filhos, 
                estado_civil: $scope.estado_civil
            });
        } else {
            var prev_search = $localstorage.getObject($scope.storage_cache_name, {});
            if (!$.isEmptyObject(prev_search)) {
                $scope.page = prev_search.page;
                $scope.pageSize = prev_search.pageSize;
                $scope.searchBy = prev_search.searchBy;
                $scope.stat = prev_search.stat;
                $scope.comungante = prev_search.comungante;
                $scope.nao_comungante = prev_search.nao_comungante;
                $scope.arrolado = prev_search.arrolado;
                $scope.nao_arrolado = prev_search.nao_arrolado;
                $scope.especial = prev_search.especial; 
                $scope.criancas = prev_search.criancas; 
                $scope.adolescentes = prev_search.adolescentes; 
                $scope.jovens = prev_search.jovens;
                $scope.adultos = prev_search.adultos;
                $scope.idosos = prev_search.idosos; 
                $scope.aniversariantes = prev_search.aniversariantes;
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
        MembroDaIgreja.getAll($scope.page, $scope.pageSize, $scope.searchBy, $scope.orderBy, '', $scope.stat, 
                            $scope.igreja, $scope.comungante, $scope.nao_comungante, $scope.especial, 
                            $scope.criancas, $scope.adolescentes, $scope.jovens, $scope.adultos, 
                            $scope.idosos, $scope.aniversariantes, $scope.sexo, $scope.escolaridade, 
                            $scope.tem_filhos, $scope.sem_filhos, $scope.estado_civil,
                            $scope.arrolado,$scope.nao_arrolado).then(function(r) {
            if (r.datas.length > 0) {
                for (var k in r.datas) {
                    r.datas[k]['time_cad_str'] = moment(r.datas[k].time_cad).format('DD/MM/YYYY');
                    r.datas[k]['last_mod_str'] = moment(r.datas[k].last_mod).format('DD/MM/YYYY HH:mm');
                    if(r.datas[k].data_nascimento != null) {
                        r.datas[k]['data_nascimento_str'] = moment(r.datas[k].data_nascimento).format('DD/MM/YYYY');
                    }
                    else {
                        r.datas[k]['data_nascimento_str'] = '';
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
    $('.datepicker-endform').datepicker({
        todayHighlight: true,
        language: 'pt-BR',
        format: 'dd/mm/yyyy',
        orientation: 'top'
    });
    
    $scope.dataFrm = {
        isValid: false,
        data: {
            id: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            pessoa_id: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            nome: { value: '', notEmpty: true, valid: true, StringfyFrom: '' },
            perfil: { value: '', notEmpty: true, valid: true, StringfyFrom: '', show: true },
            email: { value: '', notEmpty: true, valid: true, StringfyFrom: '' },
            senha: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            r_senha: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            sexo: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            data_nascimento: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            crianca: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            responsavel: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            estado_civil: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            especial: { value: false, notEmpty: false, valid: true, StringfyFrom: '' },
            escolaridade: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            profissao: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            necessidades_especiais: { value: [], notEmpty: false, valid: true, StringfyFrom: '' },
            telefone: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            celular_1: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            celular_2: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            pai: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            mae: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            conjuge: { value: {}, notEmpty: false, valid: true, StringfyFrom: '' },
            tem_filhos: { value: false, notEmpty: false, valid: true, StringfyFrom: '' },
            filhos: { value: [], notEmpty: false, valid: true, StringfyFrom: '' },
            naturalidade: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            nacionalidade: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            doacoes: { value: [], notEmpty: false, valid: true, StringfyFrom: '' },
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
            comungante: { value: false, notEmpty: false, valid: true, StringfyFrom: '' },
            arrolado: { value: true, notEmpty: false, valid: true, StringfyFrom: '' },
            data_admissao: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            data_demissao: { value: '', notEmpty: false, valid: true, StringfyFrom: '' }
        },
        toSend: {
            id: '',
            pessoa: {
                id: '',
                igreja: $scope.igreja,
                perfil: '',
                nome: '',
                email: '',
                senha: '',
                sexo: '',
                data_nascimento: '',
                crianca: '',
                responsavel: '',
                estado_civil: '',
                escolaridade: '',
                profissao: '',
                telefone: '',
                celular_1: '',
                celular_2: '',
                pai: '',
                mae: '',
                conjuge: {},
                tem_filhos: false,
                filhos: [],
                naturalidade: '',
                nacionalidade: '',
                doacoes: [],
                necessidades_especiais: [],
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
                vimeo: ''
            },
            igreja: $scope.igreja,
            comungante: false,
            arrolado: true,
            especial: false,
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
                if ($scope.dataFrm.toSend.pessoa.hasOwnProperty(k)) {
                    $scope.dataFrm.toSend.pessoa[k] = $scope.dataFrm.data[k].value;
                }
            }
            
            if(($scope.dataFrm.toSend.pessoa.estado_civil == EstadoCivil.CASADO) || ($scope.dataFrm.data.conjuge.value.remove.value)) {
                $scope.dataFrm.toSend.pessoa.conjuge = $scope.familiaFactory
                                                                .getDataSendConjuge($scope.dataFrm.toSend.pessoa.id, 
                                                                            $scope.dataFrm.data.conjuge.value);
            }
            
            if($scope.dataFrm.toSend.pessoa.tem_filhos) {
                $scope.dataFrm.toSend.pessoa.filhos = $scope.familiaFactory
                                                                .getDataSendFilho($scope.dataFrm.toSend.pessoa.id, 
                                                                            $scope.dataFrm.data.filhos.value);
            }
            
            $scope.dataFrm.toSend.pessoa.id = $scope.dataFrm.data.pessoa_id.value;
            
            console.log($scope.dataFrm.toSend.pessoa);
        }
    };
    
    // services e dados padrão para as diretivas
    $scope.Pessoa = Pessoa;
    $scope.MembroDaIgreja = MembroDaIgreja;
    $scope.perfilDefault = "";
    
    // monte os arrays de seleção de necessidades e doações
    $scope.mountNecessidadesEspeciais = function(old) {
        $scope.dataFrm.data.necessidades_especiais.value = [];
        for(var k in $scope.listNecessidadesEspeciais) {
            var checked = (old != undefined) ? old.includes($scope.listNecessidadesEspeciais[k].id) : false;
            $scope.dataFrm.data.necessidades_especiais.value.push({
                checked: checked,
                id: $scope.listNecessidadesEspeciais[k].id,
                label: $scope.listNecessidadesEspeciais[k].nome
            });
        }
    };
    $scope.mountDoacoes = function(old) {
        $scope.dataFrm.data.doacoes.value = [];
        for(var k in $scope.listDoacoes) {
            var checked = (old != undefined) ? old.includes($scope.listDoacoes[k].id) : false;
            $scope.dataFrm.data.doacoes.value.push({
                checked: checked,
                id: $scope.listDoacoes[k].id,
                label: $scope.listDoacoes[k].nome
            });
        }
    };
    
    if ($state.current.name.includes('editar') && $scope.id) {
        // edição - via id 
        $scope.$on('preLoad', function() {
            MembroDaIgreja.getMe($scope.id).then(function(r) {
                $scope.data = r;
                Pessoa.getMe($scope.data.pessoa).then(function(r) {
                    $scope.data.pessoa = r;
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
        for (var k in $scope.data) {
            if(k != 'pessoa') {
                if ($scope.dataFrm.data.hasOwnProperty(k)) {
                    $scope.dataFrm.data[k].value = $scope.data[k];
                }
            }
            else {
                for(var j in $scope.data.pessoa) {
                    if ($scope.dataFrm.data.hasOwnProperty(j)) {
                        if(j != 'id') {
                            $scope.dataFrm.data[j].value = $scope.data.pessoa[j]; 
                        }
                        else {
                            $scope.dataFrm.data.pessoa_id.value = $scope.data.pessoa.id; 
                        }
                    }
                }
                
                $scope.mountNecessidadesEspeciais($scope.data.pessoa.necessidades_especiais);
                $scope.mountDoacoes($scope.data.pessoa.doacoes);
            }
        }
        
        if($scope.dataFrm.data.data_nascimento.value != null) {
            $scope.dataFrm.data.data_nascimento.value = moment($scope.dataFrm.data.data_nascimento.value).format('DD/MM/YYYY');
        }
        else {
            $scope.dataFrm.data.data_nascimento.value = '';
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
            // marque o perfil padrão
            $scope.perfilDefault = $scope.listConfigs[0].perfil_membro;
            $scope.dataFrm.data.perfil.value = $scope.perfilDefault;
            $scope.mountNecessidadesEspeciais();
            $scope.mountDoacoes();
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
            
            // sincronize a pessoa primeiro
            var promise_pessoa = {};
            if($scope.dataFrm.toSend.pessoa.id == '') 
            {
                promise_pessoa = Pessoa.create($scope.dataFrm.toSend.pessoa);
            }
            else
            {
                promise_pessoa = Pessoa.edit($scope.dataFrm.toSend.pessoa.id, $scope.dataFrm.toSend.pessoa);
            }
            
            promise_pessoa.then(function(r) {
                $scope.dataFrm.toSend.pessoa.id = r.id;
                // sincronize a membresia
                var promise = {};
                if($scope.isCreate)
                {
                    promise = MembroDaIgreja.create($scope.dataFrm.toSend);
                }
                else
                {
                    promise = MembroDaIgreja.edit($scope.id, $scope.dataFrm.toSend);
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
            MembroDaIgreja.changeStat(d.id, d).then(function(r) {
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
            MembroDaIgreja.remove(d.id).then(function() {
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
            MembroDaIgreja.removeAll({
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


