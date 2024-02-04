angular.module('SmartChurchPanel').controller('MuralDaIgrejaCtrl', function ($scope, $state, $stateParams, $localstorage, $notifications, $rootScope, $dialogs, $timeout, 
                    $context, $sce,
                    FileUploader, ApiEndpoint, 
                    User, Data, CongregacaoIgreja, PontoDePregacaoIgreja, Secretaria, Ministerio, 
                    Sociedade, PequenoGrupo, Mural) { 
    
    /* config */
    $scope.opTitle = $stateParams.title;
    $scope.search = $stateParams.search;
    $scope.view = $stateParams.view;
    $scope.data = $stateParams.data;
    $scope.perms = $stateParams.perms;
    $scope.back = ($stateParams.back == '') ? 'SmartChurchPanel.igreja.mural.buscar()' : $stateParams.back + '()';
    $scope.id = $stateParams.id;
    $scope.objForm = 'Mural';
    $scope.objList = 'Mural';
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
    
    $scope.navStates = {
        add: 'SmartChurchPanel.igreja.mural.adicionar',
        edit: 'SmartChurchPanel.igreja.mural.editar',
        search: 'SmartChurchPanel.igreja.mural.buscar'
    };
    
    $scope.localPerms = {
        add: 'MuralSave',
        edit: 'MuralSave',
        changeStat: 'MuralBlock',
        remove: 'MuralRemove'
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
    $scope.storage_cache_name = 'search_muraldaigreja';
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
            video: false,
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
    $scope.video = false;
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
        $scope.filterUsed = ($scope.stat != '' || $scope.video || $scope.checkDestinatarioFilter());
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
                video: $scope.video,
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
                $scope.video = prev_search.video;
                $scope.destinatarios = prev_search.destinatarios;
                $scope.orderBy = prev_search.orderBy;
                var o_v = $scope.orderBy.split(',');
                $scope.orderField = o_v[0];
                $scope.orderOrientation = o_v[1];
            }
        }
        
        $scope.isFilterUsed();

        $scope.list = [];
        Mural.getAll($scope.page, $scope.pageSize, $scope.searchBy, $scope.orderBy, '', $scope.stat, $scope.igreja, 
                        $scope.video, $scope.generateDestinatariosIds($scope.destinatarios)).then(function(r) {
            if (r.datas.length > 0) {
                for (var k in r.datas) {
                    r.datas[k]['time_cad_str'] = moment(r.datas[k].time_cad).format('DD/MM/YYYY');
                    r.datas[k]['last_mod_str'] = moment(r.datas[k].last_mod).format('DD/MM/YYYY HH:mm');
                    
                    r.datas[k]['mural_content'] = {
                        hasOne: false,
                        text: false,
                        img: false,
                        video: false,
                        audio: false
                    };
                    
                    if(r.datas[k].img != '') {
                        r.datas[k]['img_url'] = ApiEndpoint.rc + '/' + r.datas[k].img;
                        r.datas[k].mural_content.img = true;
                        r.datas[k].mural_content.hasOne = true;
                    }
                    
                    if(r.datas[k].video != '' && !r.datas[k].mural_content.hasOne) {
                        r.datas[k]['videoData'] = $scope.procVideo(r.datas[k].video);
                        r.datas[k].mural_content.video = true;
                        r.datas[k].mural_content.hasOne = true;
                    }
                    
                    if(r.datas[k].audio != '' && !r.datas[k].mural_content.hasOne) {
                        r.datas[k]['audioData'] = $scope.procAudio(r.datas[k].audio);
                        r.datas[k].mural_content.audio = true;
                        r.datas[k].mural_content.hasOne = true;
                    }
                    
                    if(!r.datas[k].mural_content.hasOne) {
                        r.datas[k].mural_content.text = true;
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
        destinatarios = $scope.generateDestinarioForList($scope.listSecretarias, 'secretaria', 'Secretarias', destinatarios);
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
    
    $scope.removeImg = function() {
        $scope.dataFrm.data.arquivo.value = { name: '', content: '', removed: true };
        $scope.dataFrm.data.img_url.value = '';
    };
    
    $scope.dataFrm = {
        isValid: false,
        data: {
            id: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            titulo: { value: '', notEmpty: true, valid: true, StringfyFrom: '' },
            chave: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            arquivo: { value: { name: '', content: '' }, notEmpty: false, valid: true, StringfyFrom: '' },
            img_url: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            conteudo: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            video: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            audio: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            destinatarios_opts: { value: [], notEmpty: false, valid: true, StringfyFrom: '' }
        },
        toSend: {
            id: '',
            igreja: $scope.igreja,
            titulo: '',
            chave: '',
            arquivo: { name: '', content: '' },
            conteudo: '',
            video: '',
            audio: '',
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
    
    if (($state.current.name.includes('editar') || $scope.view) && $scope.id) {
        // edição - via id 
        $scope.$on('preLoad', function() {
            Mural.getMe($scope.id).then(function(r) {
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
    
    $scope.procVideo = function(video) {
        var videoData = {
            url: '',
            type: ''
        };
        
        // veja se é youtube ou vimeo
        if(video.includes('vimeo')) {
            var found  = video.match(/vimeo\.com\/([\da-zA-Z_\-]*)/i);
            if(found != null && found.length > 1) {
                videoData.url = $sce.trustAsResourceUrl('https://player.vimeo.com/video/' + found[1] + '?title=0&byline=0');
                videoData.type = 'Vimeo';
            }
        }
        else if(video.includes('youtube')) {
            var found = video.match(/youtube\.com\/watch\?v=([\da-zA-Z_\-]*)/i);
            if(found != null && found.length > 1) {
                videoData.url = $sce.trustAsResourceUrl('https://www.youtube.com/embed/' + found[1]);
                videoData.type = 'Youtube';
            }
        }
        else if(video.includes('youtu.be')) {
            var found  = video.match(/youtu\.be\/([\da-zA-Z_\-]*)/i);
            if(found != null && found.length > 1) {
                videoData.url = $sce.trustAsResourceUrl('https://www.youtube.com/embed/' + found[1]);
                videoData.type = 'Youtube';
            }
        }
        
        return videoData;
    };
    
    $scope.procAudio = function(audio) {
        var audioData = {
            url: ''
        };
        
        var found  = audio.match(/tracks\/([\da-zA-Z_\-]*)/i);
        if(found != null && found.length > 1) {
            audioData.url = $sce.trustAsResourceUrl("https://w.soundcloud.com/player/?url="+
                    "https%3A//api.soundcloud.com/tracks/"+ found[1] +
                    "&color=%23ff5500&auto_play=false&hide_related=false"+
                    "&show_comments=false&show_user=true&show_reposts=false"+
                    "&show_teaser=true&visual=false");
        }
        
        return audioData;
    };
    
    $scope.load = function() {
        
        for (var k in $scope.data) {
            if ($scope.dataFrm.data.hasOwnProperty(k)) {
                $scope.dataFrm.data[k].value = $scope.data[k];
            }
        }
        
        if($scope.data.img != '') {
            $scope.dataFrm.data.img_url.value = ApiEndpoint.rc + '/' + $scope.data.img;
        }
                
        $scope.dataFrm.data.destinatarios_opts.value = $.parseJSON($scope.data.destinatarios);
        
        if($scope.view) {
            $scope.videoData = $scope.procVideo($scope.dataFrm.data.video.value);
            
            if($scope.dataFrm.data.audio.value != '') {
                $scope.audioData = $scope.procAudio($scope.dataFrm.data.audio.value);
            }
        }
    };
    
    if(!$scope.data && !$scope.search && !$scope.view && !$scope.id) {
        // adição
        $scope.isCreate = true;
        $scope.$on('preLoad', function() {
            $scope.dataFrm.data.destinatarios_opts.value = $scope.generateDestinarios();
        });
    }
    
    $scope.$on('imgReadResolutionErr', function() {
        $notifications.err("A Imagem (App) deve ter uma resolução de 1080x1350 pixels");
    });
    
    $scope.$on('imgReadSizeErr', function() {
        $notifications.err("A Imagem (App) não pode ter mais do que 2MB");
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
    
    $scope.doSave = function() {
        $scope.dataFrm.validate();
        if ($scope.dataFrm.isValid) {
            $scope.dataFrm.prepare();
            var promise = {};
            if($scope.isCreate)
            {
                promise = Mural.createForIgreja($scope.dataFrm.toSend);
            }
            else
            {
                promise = Mural.edit($scope.id, $scope.dataFrm.toSend);
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
            Mural.changeStat(d.id, d).then(function(r) {
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
            Mural.remove(d.id).then(function() {
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
            Mural.removeAll({
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
    
    /* link do mural */
    $scope.link_mural = '';
    $scope.linkMural = function(d) {
        $scope.link_mural = ApiEndpoint.mural + '/' + btoa(d.id);
        $('#linkModal').modal('show');
    };
});


