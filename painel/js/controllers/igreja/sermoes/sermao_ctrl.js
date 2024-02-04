angular.module('SmartChurchPanel').controller('SermaoCtrl', function ($scope, $state, $stateParams, $localstorage, $notifications, $rootScope, $dialogs, $timeout, 
                    $context, $uploadProgress, $sce,
                    FileUploader, ApiEndpoint, User, Data, SerieDeSermao, MembroDaIgreja, Sermao) { 
    
    /* config */
    $scope.opTitle = $stateParams.title;
    $scope.search = $stateParams.search;
    $scope.view = $stateParams.view;
    $scope.data = $stateParams.data;
    $scope.perms = $stateParams.perms;
    $scope.back = ($stateParams.back == '') ? 'SmartChurchPanel.igreja.sermoes.buscar()' : $stateParams.back + '()';
    $scope.id = $stateParams.id;
    $scope.objForm = 'Sermão';
    $scope.objList = 'Sermões';
    $scope.igreja = $context.getIgrejaContext();
    $scope.igrejaData = $rootScope.USER.getContextByKeyAndId(Contexts.IGREJAS, $scope.igreja);
    $scope.uploader = new FileUploader();
    $scope.uploader.url = ApiEndpoint.url+'/fileupload/save';
    $scope.uploader.headers = User.getUserHeader();
    $scope.isCreate = false;

    /* verificação de permissão do state */
    if(!$scope.USER.doIHaveAccess($scope.user, $scope.perms)) {
        $state.go('SmartChurchPanel.sempermissao');
    }
    
    $scope.localPerms = {
        add: 'SermaoSave',
        edit: 'SermaoSave',
        changeStat: 'SermaoBlock',
        remove: 'SermaoRemove'
    };
    
    /* common */
    $scope.listStatus = [];
    $scope.listSeries = [];
    $scope.listMembros = [];
    Data.getAll().then(function(r) {
        $scope.listStatus = r.status;
        SerieDeSermao.getAll('', '', '', 'time_cad,desc', '', '', $scope.igreja).then(function(r) {
            if(r.total > 0) {
                $scope.listSeries = r.datas;
            }
            
            MembroDaIgreja.getAll('', '', '', '', '', '', $scope.igreja).then(function(r) {
                if(r.total > 0) {
                    $scope.listMembros = r.datas;
                }

                $scope.$broadcast('preLoad');
            }, function(e) { console.log(e); $scope.testError(e); });
        }, function(e) { console.log(e); $scope.testError(e); });
    }, function(e) { console.log(e); $scope.testError(e); });
    
    
    /* search */
    $scope.storage_cache_name = 'search_sermoes';
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
            serie: '',
            autor: '',
            anexo: false,
            video: false,
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
    $scope.serie = '';
    $scope.autor = '';
    $scope.anexo = false;
    $scope.video = false;
    $scope.filterUsed = false;
    $scope.isFilterUsed = function() {
        $scope.filterUsed = ($scope.stat != '' || $scope.serie != '' || 
                                $scope.autor != '' || $scope.anexo || 
                                $scope.video);
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
                serie: $scope.serie,
                autor: $scope.autor,
                anexo: $scope.anexo,
                video: $scope.video,
                orderBy: $scope.orderBy
            });
        } else {
            var prev_search = $localstorage.getObject($scope.storage_cache_name, {});
            if (!$.isEmptyObject(prev_search)) {
                $scope.page = prev_search.page;
                $scope.pageSize = prev_search.pageSize;
                $scope.searchBy = prev_search.searchBy;
                $scope.stat = prev_search.stat;
                $scope.serie = prev_search.serie;
                $scope.autor = prev_search.autor;
                $scope.anexo = prev_search.anexo;
                $scope.video = prev_search.video;
                $scope.orderBy = prev_search.orderBy;
                var o_v = $scope.orderBy.split(',');
                $scope.orderField = o_v[0];
                $scope.orderOrientation = o_v[1];
            }
        }
        
        $scope.isFilterUsed();

        $scope.list = [];
        Sermao.getAll($scope.page, $scope.pageSize, $scope.searchBy, $scope.orderBy, '', $scope.stat, $scope.igreja, 
                        $scope.serie, $scope.autor, $scope.anexo, $scope.video).then(function(r) {
            if (r.datas.length > 0) {
                for (var k in r.datas) {
                    r.datas[k]['time_cad_str'] = moment(r.datas[k].time_cad).format('DD/MM/YYYY');
                    r.datas[k]['last_mod_str'] = moment(r.datas[k].last_mod).format('DD/MM/YYYY HH:mm');
                    
                    if(r.datas[k].logo != '') {
                        r.datas[k]['logo_url'] = ApiEndpoint.rc + '/' + r.datas[k].logo;
                    }
                    
                    r.datas[k]['serie_nome'] = '';
                    if(r.datas[k].serie != null) {
                        var s = $scope.listSeries.find(x => x.id == r.datas[k].serie);
                        if(s != null) {
                            r.datas[k]['serie_nome'] = s.nome;
                        }
                    }
                    
                    r.datas[k]['data_sermao_str'] = '';
                    if(r.datas[k].data_sermao != null) {
                        r.datas[k]['data_sermao_str'] = moment(r.datas[k].data_sermao).format('DD/MM/YYYY');
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
        orientation: 'bottom',
        zIndexOffset: 1000
    });
    
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
    
    $scope.onSelectNome = function(item) {
        $scope.dataFrm.data.autor.value = item.pessoa;
        $scope.dataFrm.data.nome.value = item.nome;
        $scope.$apply();
    };
    
    $scope.prepareTypeAhead = function() {
        var options = {
            data: $scope.listMembros,
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
    
    $scope.clearURLLikeString = function(str) {
        return str.toLowerCase()
                    .normalize("NFD")
                    .replace(/[\u0300-\u036f]/g, "")
                    .replace(/[&]/g, "e")
                    .replace(/[\\\?\|\/\$'"]/g, "")
                    .replace(/[\s]/g, "-");
    };
    
    $scope.generateChave = function() {
        $scope.dataFrm.data.chave.value = $scope.clearURLLikeString($scope.dataFrm.data.titulo.value);
    };
    
    $scope.dataFrm = {
        isValid: false,
        data: {
            id: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            titulo: { value: '', notEmpty: true, valid: true, StringfyFrom: '' },
            chave: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            arquivo: { value: { name: '', content: '' }, notEmpty: false, valid: true, StringfyFrom: '' },
            arquivo_app: { value: { name: '', content: '' }, notEmpty: false, valid: true, StringfyFrom: '' },
            logo_url: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            logo_app_url: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            data_sermao: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            serie: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            nome: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            autor: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            conteudo: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            anexo: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            video: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            audio: { value: '', notEmpty: false, valid: true, StringfyFrom: '' }
        },
        toSend: {
            id: '',
            igreja: $scope.igreja,
            titulo: '',
            chave: '',
            arquivo: { name: '', content: '' },
            arquivo_app: { name: '', content: '' },
            data_sermao: '',
            serie: '',
            autor: '',
            conteudo: '',
            anexo: '',
            video: '',
            audio: ''
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
    
    if (($state.current.name.includes('editar') || $scope.view) && $scope.id) {
        // edição - via id 
        $scope.$on('preLoad', function() {
            Sermao.getMe($scope.id).then(function(r) {
                $scope.data = r;
                $scope.load();
            }, function(e) { 
                $state.go($scope.back.parseState().state, $scope.back.parseState().params); 
            });
        });
    }
    
    $scope.videoData = {
        url: '',
        type: ''
    };
    
    $scope.audioData = {
        url: ''
    };
    
    $scope.load = function() {
        if(!$scope.view) {
            $scope.prepareTypeAhead();
        }
        
        for (var k in $scope.data) {
            if ($scope.dataFrm.data.hasOwnProperty(k)) {
                $scope.dataFrm.data[k].value = $scope.data[k];
            }
        }
        
        if($scope.data.logo != '') {
            $scope.dataFrm.data.logo_url.value = ApiEndpoint.rc + '/' + $scope.data.logo;
        }
        
        if($scope.data.logo_app != '') {
            $scope.dataFrm.data.logo_app_url.value = ApiEndpoint.rc + '/' + $scope.data.logo_app;
        }
        
        if($scope.dataFrm.data.data_sermao.value != null) {
            $scope.dataFrm.data.data_sermao.value = moment($scope.dataFrm.data.data_sermao.value).format('DD/MM/YYYY');
        }
        else {
            $scope.dataFrm.data.data_sermao.value = '';
        }
        
        if($scope.view) {
            // veja se é youtube ou vimeo
            if($scope.dataFrm.data.video.value.includes('vimeo')) {
                var found  = $scope.dataFrm.data.video.value.match(/vimeo\.com\/([\da-zA-Z_\-]*)/i);
                if(found != null && found.length > 1) {
                    $scope.videoData.url = $sce.trustAsResourceUrl('https://player.vimeo.com/video/' + found[1] + '?title=0&byline=0');
                    $scope.videoData.type = 'Vimeo';
                }
            }
            else if($scope.dataFrm.data.video.value.includes('youtube')) {
                var found = $scope.dataFrm.data.video.value.match(/youtube\.com\/watch\?v=([\da-zA-Z_\-]*)/i);
                if(found != null && found.length > 1) {
                    $scope.videoData.url = $sce.trustAsResourceUrl('https://www.youtube.com/embed/' + found[1]);
                    $scope.videoData.type = 'Youtube';
                }
            }
            else if($scope.dataFrm.data.video.value.includes('youtu.be')) {
                var found  = $scope.dataFrm.data.video.value.match(/youtu\.be\/([\da-zA-Z_\-]*)/i);
                if(found != null && found.length > 1) {
                    $scope.videoData.url = $sce.trustAsResourceUrl('https://www.youtube.com/embed/' + found[1]);
                    $scope.videoData.type = 'Youtube';
                }
            }
            
            if($scope.dataFrm.data.audio.value != '') {
                var found  = $scope.dataFrm.data.audio.value.match(/tracks\/([\da-zA-Z_\-]*)/i);
                if(found != null && found.length > 1) {
                    $scope.audioData.url = $sce.trustAsResourceUrl("https://w.soundcloud.com/player/?url="+
                            "https%3A//api.soundcloud.com/tracks/"+ found[1] +
                            "&color=%23ff5500&auto_play=false&hide_related=false"+
                            "&show_comments=false&show_user=true&show_reposts=false"+
                            "&show_teaser=true&visual=false");
                }
            }
        }
    };
    
    if(!$scope.data && !$scope.search && !$scope.view && !$scope.id) {
        // adição
        $scope.isCreate = true;
        $scope.$on('preLoad', function() {
            $scope.prepareTypeAhead();
        });
    }
    
    $scope.$on('imgReadResolutionErr', function() {
        $notifications.err("A Imagem (App) deve ter uma resolução de 500x500 pixels");
    });
    
    $scope.$on('imgReadSizeErr', function() {
        $notifications.err("A Imagem (App) não pode ter mais do que 1MB");
    });
    
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
    
    $scope.$on('doSaveMe', function() {
        var promise = {};
        if($scope.isCreate)
        {
            promise = Sermao.create($scope.dataFrm.toSend);
        }
        else
        {
            promise = Sermao.edit($scope.id, $scope.dataFrm.toSend);
        }

        promise.then(function(r) {
            $dialogs.onSave().then(function() {
                $state.reload();
            }, function() {});
        }, function(e) {
            $notifications.err(e);
        });
    });
    
    $scope.doSave = function() {
        $scope.dataFrm.validate();
        if ($scope.dataFrm.isValid) {
            $scope.dataFrm.prepare();
            if($scope.uploader.queue.length > 0) {
                $scope.uploader.onCompleteItem = function(item, response, status, headers) {
                    if(response.ok)
                    {
                        $scope.dataFrm.toSend.anexo = response.file;
                        $scope.$broadcast('doSaveMe');
                    }
                    else {
                        $scope.$broadcast('doSaveMe');
                    }
                };
                $scope.uploader.onProgressItem = function(item, progress) {
                    $uploadProgress.show(progress);
                };
                $scope.uploader.uploadAll();
            }
            else {
                $scope.$broadcast('doSaveMe');
            }
        } else {
            $notifications.err("Há campos incorretos!");
        }
    };
    
    $scope.changeStat = function(d) {
        $dialogs.beforeChange().then(function() {
            Sermao.changeStat(d.id, d).then(function(r) {
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
            Sermao.remove(d.id).then(function() {
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
            Sermao.removeAll({
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
    
    /* download */
    $scope.downloadArquivo = function() {
        Sermao.getDownload($scope.id).then(function(r) {
            
            download("data:" + r.type + ";base64," + r.data, r.filename, r.type);
            
        }, function(e) {
            $notifications.err(e);
        });
    };
    
    /* remove anexo */
    $scope.removerArquivo = function() {
        $dialogs.beforeRemove().then(function() {
            Sermao.removeAnexo($scope.id).then(function() {
                $timeout(function() {
                    $dialogs.onRemove().then(function() {
                        $scope.dataFrm.data.anexo.value = '';
                    }, function() {});
                }, 100);
            }, function(e) {
                $notifications.err(e);
            });
        }, function() {});
        
    };
    
});


