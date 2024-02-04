angular.module('SmartChurchPanel').controller('PalavraCtrl', function ($scope, $state, $stateParams, $localstorage, $notifications, $rootScope, $sce,
                    SerieDeSermao, Sermao) { 
    
    /* config */
    $scope.opTitle = $stateParams.title;
    $scope.search = $stateParams.search;
    $scope.view = $stateParams.view;
    $scope.data = $stateParams.data;
    $scope.perms = $stateParams.perms;
    $scope.back = ($stateParams.back == '') ? 'SmartChurchPanel.smartapp.palavra.buscar()' : $stateParams.back + '()';
    $scope.id = $stateParams.id;
    $scope.objForm = 'Palavra';
    $scope.objList = 'Palavra';
    $scope.igreja = $rootScope.USER.getMembresiaData().igreja;
    $scope.isCreate = false;

    /* verificação de permissão do state */
    if(!$scope.USER.doIHaveAccess($scope.user, $scope.perms)) {
        $state.go('SmartChurchPanel.sempermissao');
    }
    
    /* common */
    $scope.listSeries = [];
    SerieDeSermao.getAll('', '', '', '', '', Status.ATIVO, $scope.igreja).then(function(r) {
        if(r.total > 0) {
            $scope.listSeries = r.datas;
        }

        $scope.$broadcast('preLoad');
    }, function(e) { console.log(e); $scope.testError(e); });
    
    /* search */
    $scope.storage_cache_name = 'search_palavra';
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
            serie: '',
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
    $scope.serie = '';
    $scope.filterUsed = false;
    $scope.isFilterUsed = function() {
        $scope.filterUsed = ($scope.serie != '');
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
                serie: $scope.serie,
                orderBy: $scope.orderBy
            });
        } else {
            var prev_search = $localstorage.getObject($scope.storage_cache_name, {});
            if (!$.isEmptyObject(prev_search)) {
                $scope.page = prev_search.page;
                $scope.pageSize = prev_search.pageSize;
                $scope.searchBy = prev_search.searchBy;
                $scope.serie = prev_search.serie;
                $scope.orderBy = prev_search.orderBy;
                var o_v = $scope.orderBy.split(',');
                $scope.orderField = o_v[0];
                $scope.orderOrientation = o_v[1];
            }
        }
        
        $scope.isFilterUsed();

        $scope.list = [];
        Sermao.getAll($scope.page, $scope.pageSize, $scope.searchBy, $scope.orderBy, '', Status.ATIVO, $scope.igreja, $scope.serie).then(function(r) {
            if (r.datas.length > 0) {
                for (var k in r.datas) {
                    r.datas[k]['time_cad_str'] = moment(r.datas[k].time_cad).format('DD/MM/YYYY');
                    r.datas[k]['last_mod_str'] = moment(r.datas[k].last_mod).format('DD/MM/YYYY HH:mm');
                    
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
    
    $scope.dataFrm = {
        isValid: false,
        data: {
            id: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            titulo: { value: '', notEmpty: true, valid: true, StringfyFrom: '' },
            chave: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
            arquivo: { value: { name: '', content: '' }, notEmpty: false, valid: true, StringfyFrom: '' },
            logo_url: { value: '', notEmpty: false, valid: true, StringfyFrom: '' },
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
    
    if ($scope.view && $scope.id) {
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
        
        for (var k in $scope.data) {
            if ($scope.dataFrm.data.hasOwnProperty(k)) {
                $scope.dataFrm.data[k].value = $scope.data[k];
            }
        }
        
        if($scope.dataFrm.data.data_sermao.value != null) {
            $scope.dataFrm.data.data_sermao.value = moment($scope.dataFrm.data.data_sermao.value).format('DD/MM/YYYY');
        }
        else {
            $scope.dataFrm.data.data_sermao.value = '';
        }
        
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
    };
    
    
    /* download */
    $scope.downloadArquivo = function() {
        Sermao.getDownload($scope.id).then(function(r) {
            
            download("data:" + r.type + ";base64," + r.data, r.filename, r.type);
            
        }, function(e) {
            $notifications.err(e);
        });
    };
    
});


