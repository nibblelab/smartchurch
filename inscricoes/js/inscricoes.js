
(function() {
    'use strict';

    angular
        .module('SmartChurchInscricoes', [
            'SmartChurchInscricoes.config',
            'ui.mask', 
            'angularFileUpload',
            'ngSanitize',
            'nblutils'
        ]);
})();

(function() {
    'use strict';

    angular
        .module('SmartChurchInscricoes.config', []);
})();

(function() {
    'use strict';

    angular
        .module('SmartChurchInscricoes.config', [])
        .constant('ApiEndpoint', {
                url: Endpoint.URL,
                rc: Endpoint.RC,
                base: Endpoint.BASE
        })
        
        .constant('Versions', {
            html: '0.8.4'
        })
        
        .factory('loadingIntercept', ['$q', function ($q) {
            return {
                response: function (response) {
                    angular.element('.pace').addClass('pace-inactive');
                    return response;
                },
                responseError: function (response) {
                    angular.element('.pace').addClass('pace-inactive');
                    return $q.reject(response);
                }
            };
        }])
        
        .config(['$httpProvider', function($httpProvider) {
                $httpProvider.defaults.headers.common = {};
                $httpProvider.defaults.headers.post = {};
                $httpProvider.defaults.headers.get = {};
                $httpProvider.defaults.headers.put = {};
                $httpProvider.defaults.headers.patch = {};
                $httpProvider.interceptors.push('loadingIntercept');
                var spinnerFunction = function (data, headersGetter) {
                    if(data != undefined) {
                        var d = $.parseJSON(data);
                        if(d.hasOwnProperty('ignoreLoading')) {
                            if(d.ignoreLoading) {
                                return data;
                            }
                        }
                    }
                    angular.element('.pace').removeClass('pace-inactive');
                    return data;
                };
                $httpProvider.defaults.transformRequest.push(spinnerFunction);
            }
        ])
        
        .config(['$locationProvider', function($locationProvider) {
                $locationProvider.html5Mode(true);
                $locationProvider.hashPrefix('');
        }])
    
        .run(function() {
            moment.locale('pt-BR');
        })
        
    ;
})();

(function() {
    'use strict';

    angular
        .module('SmartChurchInscricoes')
        .factory('$uploadProgress', function ($window) {
            var toastr = {};
            return {
                show: function(progress) {
                    if(progress < 100) {
                        if($.isEmptyObject(toastr)) {
                            toastr = $.toast({ 
                                text : 'Envio em ' + progress + '', 
                                showHideTransition : 'plain',  
                                allowToastClose : false,       
                                hideAfter : false,             
                                stack : 5,                     
                                textAlign : 'left',           
                                position : 'bottom-right'      
                            });
                        }
                        else {
                            toastr.update({text : 'Envio em ' + progress + '%'});
                        }
                    }
                    else {
                        if(!$.isEmptyObject(toastr)) {
                            toastr.reset();
                        }
                    }
                }
            };
        })
        .factory('Public', function ($localstorage, $q, $http, ApiEndpoint) {
            
            var login = function(ref, ref_tp) {
                var deferred = $q.defer();
                $http
                    .get(ApiEndpoint.url+'/public/loginByReferencia?ref='+ref+'&ref_tp='+ref_tp)
                    .then(function(r) {

                        if(r.data.success)
                        {
                            $localstorage.set('token', r.data.token);
                            deferred.resolve();
                        }
                        else
                        {
                            deferred.reject(r.data.msg);
                        }
                    }, 
                    function(r) {
                        deferred.reject('Não foi possível realizar a operação :( erro #'+r.status);
                    });

                return deferred.promise;
            };
            
            return {
                login: login
            };
        })
        .factory('Data', function ($localstorage, $q, $http, ApiEndpoint) {
            
            var getAll = function() {
                var token = $localstorage.get('token');
                var deferred = $q.defer();
                $http
                    .get(ApiEndpoint.url+'/data/all', {
                        headers: {
                            "Authorization": "token=" + token
                        }
                    })
                    .then(function(r) {

                        if(r.data.success)
                        {
                            deferred.resolve(r.data.datas);
                        }
                        else
                        {
                            deferred.reject(r.data.msg);
                        }
                    }, 
                    function(r) {
                        deferred.reject('Não foi possível realizar a operação :( erro #'+r.status);
                    });

                return deferred.promise;
            };
            
            return {
                getAll: getAll
            };
        })
        .factory('Sinodo', function ($localstorage, $q, $http, ApiEndpoint) {
            
            var getAll = function() {
                var token = $localstorage.get('token');
                var deferred = $q.defer();
                $http
                    .get(ApiEndpoint.url+'/sinodos/all', {
                        headers: {
                            "Authorization": "token=" + token
                        }
                    })
                    .then(function(r) {

                        if(r.data.success)
                        {
                            deferred.resolve(r.data.datas);
                        }
                        else
                        {
                            deferred.reject(r.data.msg);
                        }
                    }, 
                    function(r) {
                        deferred.reject('Não foi possível realizar a operação :( erro #'+r.status);
                    });

                return deferred.promise;
            };
            
            return {
                getAll: getAll
            };
        })
        .factory('Presbiterio', function ($localstorage, $q, $http, ApiEndpoint) {
            
            var getAll = function() {
                var token = $localstorage.get('token');
                var deferred = $q.defer();
                $http
                    .get(ApiEndpoint.url+'/presbiterios/all', {
                        headers: {
                            "Authorization": "token=" + token
                        }
                    })
                    .then(function(r) {

                        if(r.data.success)
                        {
                            deferred.resolve(r.data.datas);
                        }
                        else
                        {
                            deferred.reject(r.data.msg);
                        }
                    }, 
                    function(r) {
                        deferred.reject('Não foi possível realizar a operação :( erro #'+r.status);
                    });

                return deferred.promise;
            };
            
            return {
                getAll: getAll
            };
        })
        .factory('Igreja', function ($localstorage, $q, $http, ApiEndpoint) {
            
            var getAll = function() {
                var token = $localstorage.get('token');
                var deferred = $q.defer();
                $http
                    .get(ApiEndpoint.url+'/templos/all', {
                        headers: {
                            "Authorization": "token=" + token
                        }
                    })
                    .then(function(r) {

                        if(r.data.success)
                        {
                            deferred.resolve(r.data.datas);
                        }
                        else
                        {
                            deferred.reject(r.data.msg);
                        }
                    }, 
                    function(r) {
                        deferred.reject('Não foi possível realizar a operação :( erro #'+r.status);
                    });

                return deferred.promise;
            };
            
            return {
                getAll: getAll
            };
        })
        .factory('Sinodal', function ($localstorage, $q, $http, ApiEndpoint) {
            
            var getAll = function() {
                var token = $localstorage.get('token');
                var deferred = $q.defer();
                $http
                    .get(ApiEndpoint.url+'/sinodais/all', {
                        headers: {
                            "Authorization": "token=" + token
                        }
                    })
                    .then(function(r) {

                        if(r.data.success)
                        {
                            deferred.resolve(r.data.datas);
                        }
                        else
                        {
                            deferred.reject(r.data.msg);
                        }
                    }, 
                    function(r) {
                        deferred.reject('Não foi possível realizar a operação :( erro #'+r.status);
                    });

                return deferred.promise;
            };
            
            return {
                getAll: getAll
            };
        })
        .factory('Federacao', function ($localstorage, $q, $http, ApiEndpoint) {
            
            var getAll = function() {
                var token = $localstorage.get('token');
                var deferred = $q.defer();
                $http
                    .get(ApiEndpoint.url+'/federacoes/all', {
                        headers: {
                            "Authorization": "token=" + token
                        }
                    })
                    .then(function(r) {

                        if(r.data.success)
                        {
                            deferred.resolve(r.data.datas);
                        }
                        else
                        {
                            deferred.reject(r.data.msg);
                        }
                    }, 
                    function(r) {
                        deferred.reject('Não foi possível realizar a operação :( erro #'+r.status);
                    });

                return deferred.promise;
            };
            
            return {
                getAll: getAll
            };
        })
        .factory('Sociedade', function ($localstorage, $q, $http, ApiEndpoint) {
            
            var getAll = function() {
                var token = $localstorage.get('token');
                var deferred = $q.defer();
                $http
                    .get(ApiEndpoint.url+'/sociedades/all', {
                        headers: {
                            "Authorization": "token=" + token
                        }
                    })
                    .then(function(r) {

                        if(r.data.success)
                        {
                            deferred.resolve(r.data.datas);
                        }
                        else
                        {
                            deferred.reject(r.data.msg);
                        }
                    }, 
                    function(r) {
                        deferred.reject('Não foi possível realizar a operação :( erro #'+r.status);
                    });

                return deferred.promise;
            };
            
            return {
                getAll: getAll
            };
        })
        .factory('Cargo', function ($localstorage, $q, $http, ApiEndpoint) {
            
            var getAll = function() {
                var token = $localstorage.get('token');
                var deferred = $q.defer();
                $http
                    .get(ApiEndpoint.url+'/cargos/all', {
                        headers: {
                            "Authorization": "token=" + token
                        }
                    })
                    .then(function(r) {

                        if(r.data.success)
                        {
                            deferred.resolve(r.data.datas);
                        }
                        else
                        {
                            deferred.reject(r.data.msg);
                        }
                    }, 
                    function(r) {
                        deferred.reject('Não foi possível realizar a operação :( erro #'+r.status);
                    });

                return deferred.promise;
            };
            
            return {
                getAll: getAll
            };
        })
        .factory('Evento', function ($localstorage, $q, $http, ApiEndpoint) {
            
            var getByChave = function(chave) {
                var token = $localstorage.get('token');
                var deferred = $q.defer();
                $http
                    .get(ApiEndpoint.url+'/eventos/byChave?chave='+chave, {
                        headers: {
                            "Authorization": "token=" + token
                        }
                    })
                    .then(function(r) {

                        if(r.data.success)
                        {
                            deferred.resolve(r.data.datas);
                        }
                        else
                        {
                            deferred.reject(r.data.msg);
                        }
                    }, 
                    function(r) {
                        deferred.reject('Não foi possível realizar a operação :( erro #'+r.status);
                    });

                return deferred.promise;
            };
            
            return {
                getByChave: getByChave
            };
        })
        .factory('Inscricao', function ($localstorage, $q, $http, ApiEndpoint) {
            
            var doInscricao = function(dados) {
                var token = $localstorage.get('token');
                var data = JSON.stringify(dados);
                var deferred = $q.defer();
                $http
                    .post(ApiEndpoint.url+'/inscricoes/inscrever', data, {
                        headers: {
                            "Authorization": "token=" + token
                        }
                    })
                    .then(function(r) {

                        if(r.data.success)
                        {
                            deferred.resolve();
                        }
                        else
                        {
                            deferred.reject(r.data.msg);
                        }
                    }, 
                    function(r) {
                        deferred.reject('Não foi possível realizar a operação :( erro #'+r.status);
                    });

                return deferred.promise;
            };

            return {
                doInscricao: doInscricao
            };
        })
        .factory('User', function ($localstorage, ApiEndpoint) {
            var getUserHeader = function() {
                var token = $localstorage.get('token');
                var header = {
                    "Authorization": "token=" + token
                };
                return header;
            };
            
            return {
                getUserHeader: getUserHeader
            };
        })
        ;
})();

(function() {
    'use strict';

    angular
        .module('SmartChurchInscricoes')
        .controller('InscricoesCtrl', function($location, $scope, $timeout, $uploadProgress, ApiEndpoint, Public, Data, Sinodo, Presbiterio, Igreja, 
                                                Sinodal, Federacao, Sociedade, Cargo, Evento, User, Inscricao) {
            var vm = this;
    
            vm.loaded = false;
            
            vm.listSexos = [];
            vm.listEstadosCivis = [];
            vm.listReferencias = [];
            vm.listSinodos = [];
            vm.listPresbiterios = [];
            vm.listIgrejas = [];
            vm.listSinodais = [];
            vm.listFederacoes = [];
            vm.listSociedades = [];
            vm.listCargos = [];
            
            vm.data = {
                nome: '',
                descricao: '',
                inicio: '',
                termino: '',
                local: {
                    rua: '',
                    numero: '',
                    complemento: '',
                    bairro: '',
                    cidade: '',
                    uf: '',
                    cep: ''
                },
                links: {
                    site: '',
                    facebook: '',
                    instagram: '',
                    youtube: '',
                    vimeo: ''
                },
                inscricoes_ativas: false,
                form: []
            };
            vm.inscricao = {
                inscrito_data: {
                    nome: '', 
                    email: '',
                    sexo: '',
                    data_nascimento: '',
                    estado_civil: '',
                    telefone: '',
                    celular_1: '',
                    celular_2: ''
                },
                nome: '', 
                email: '',
                nome_evento: '',
                evento: '',
                sociedade: '',
                federacao: '',
                sinodal: '',
                igreja: '',
                presbiterio: '',
                sinodo: '',
                delegado: false,
                cargo_ref: '',
                cargo: '',
                has_pagto: false,
                stat_pagto: '',
                valor_pago: '0.00',
                data_pagto: ''
            };
            
            /* exibição do evento e formulário */
            
            vm.filterList = function(list, field, value) {
                vm[list].forEach(function (element, index, array) { array[index].show = true; });
                if(value != '') {
                    for(var k in vm[list]) {
                        if(vm[list][k][field] != value) {
                            vm[list][k].show = false;
                        }
                    }
                }
            };
            
            try
            {
                var param = atob($location.path().replace('/', ''));
                var match = [...param.matchAll(/([\w]+)|([0-9]+)|([0-9]+)/g)];
                var chave = match[0][0];
                var ref_tp = match[1][0];
                var ref = match[2][0];
                
                Public.login(ref, ref_tp).then(function() {
                    Data.getAll().then(function(r) {
                        vm.listSexos = r.sexo;
                        vm.listEstadosCivis = r.estado_civil;
                        vm.listReferencias = r.referencias_cargos;
                        
                        Cargo.getAll().then(function(r) {
                            for(var k in r) {
                                var ref = vm.listReferencias.find(x => x.value == r[k].instancia);
                                if(ref != undefined) {
                                    r[k].nome += ' na ' + ref.label;
                                }
                            }
                            vm.listCargos = r;
                            
                            Sinodo.getAll().then(function(r) {
                                vm.listSinodos = r;
                                
                                Presbiterio.getAll().then(function(r) { 
                                    r.forEach(function (element, index, array) { array[index].show = true; });
                                    vm.listPresbiterios = r;
                                    
                                    Igreja.getAll().then(function(r) {
                                        r.forEach(function (element, index, array) { array[index].show = true; });
                                        vm.listIgrejas = r;
                                        
                                        Sinodal.getAll().then(function(r) {
                                            r.forEach(function (element, index, array) { array[index].show = true; });
                                            vm.listSinodais = r;
                                            
                                            Federacao.getAll().then(function(r) {
                                                r.forEach(function (element, index, array) { array[index].show = true; });
                                                vm.listFederacoes = r;
                                                
                                                Sociedade.getAll().then(function(r) {
                                                    r.forEach(function (element, index, array) { array[index].show = true; });
                                                    vm.listSociedades = r;
                                                    
                                                    Evento.getByChave(chave).then(function(r) {

                                                        vm.data.nome = r.nome;
                                                        vm.data.descricao = r.descricao;

                                                        var t_ini = moment(r.time_ini);
                                                        if(t_ini) {
                                                            vm.data.inicio = t_ini.format('DD/MM/YYYY') + ' às ' + t_ini.format('HH:mm');
                                                        }

                                                        var t_end = moment(r.time_end);
                                                        if(t_end) {
                                                            vm.data.termino = t_end.format('DD/MM/YYYY') + ' às ' + t_end.format('HH:mm');
                                                        }

                                                        vm.data.local.rua = r.agenda.endereco;
                                                        vm.data.local.numero = (r.agenda.numero != '') ? ', ' + r.agenda.numero : '';
                                                        vm.data.local.complemento = (r.agenda.complemento != '') ? ' - ' + r.agenda.complemento : '';
                                                        vm.data.local.bairro = r.agenda.bairro;
                                                        vm.data.local.cidade = r.agenda.cidade;
                                                        vm.data.local.uf = r.agenda.uf;
                                                        vm.data.local.cep = r.agenda.cep;

                                                        vm.data.links.site = r.agenda.site;
                                                        vm.data.links.facebook = r.agenda.facebook;
                                                        vm.data.links.instagram = r.agenda.instagram;
                                                        vm.data.links.youtube = r.agenda.youtube;
                                                        vm.data.links.vimeo = r.agenda.vimeo;


                                                        vm.data.inscricoes_ativas = r.inscricoes_ativas;

                                                        vm.data.form = $.parseJSON(r.formulario_inscricao);
                                                        for(var k in vm.data.form) {
                                                            vm.data.form[k]['value'] = '';
                                                        }
                                                        
                                                        for(var k in vm.data.form) {
                                                            if(vm.data.form[k].type == 'file') {
                                                                vm.data.form[k]['uploader'] = new FileUploader();
                                                                vm.data.form[k]['uploader'].url = ApiEndpoint.url+'/fileupload/save';
                                                                vm.data.form[k]['uploader'].headers = User.getUserHeader();
                                                            }
                                                        }
                                                        
                                                        vm.loaded = true;
                                                        
                                                        vm.inscricao.nome_evento = r.nome;
                                                        vm.inscricao.evento = r.id;

                                                        $timeout(function() {
                                                            
                                                            $('.datepicker').datepicker({
                                                                todayHighlight: true,
                                                                language: 'pt-BR',
                                                                format: 'dd/mm/yyyy',
                                                                orientation: 'bottom'
                                                            });
                                                        }, 500);
                                                    }, function(e) { console.log(e); });
                                                }, function(e) { console.log(e); });
                                            }, function(e) { console.log(e); });
                                        }, function(e) { console.log(e); });
                                    }, function(e) { console.log(e); });
                                }, function(e) { console.log(e); });
                            }, function(e) { console.log(e); });
                        }, function(e) { console.log(e); });
                    }, function(e) { console.log(e); });
                }, function(e) { console.log(e); });
            } catch (e) { console.log(e); }
            
            /* form */
            $scope.$on('doSaveMe', function() {
                vm.doInscricao();
            });
            
            vm.uploads = { total: 0, completed: 0};
            vm.procInscricaoFiles = function() {
                /* envie os arquivos */
                for(var k in vm.data.form) {
                    if(vm.data.form[k].type == 'file' && vm.data.form[k].value == '') {
                        if(vm.data.form[k].uploader.queue.length > 0) {
                            vm.data.form[k].uploader.onCompleteItem = function(item, response, status, headers) {
                                if(response.ok)
                                {
                                    vm.data.form[k].value = response.file;
                                    vm.uploads.completed++;
                                }
                                else {
                                    vm.uploads.completed++;
                                }
                                if(vm.uploads.completed == vm.uploads.total) {
                                    $scope.$broadcast('doSaveMe');
                                }
                            };
                            vm.data.form[k].uploader.onProgressItem = function(item, progress) {
                                $uploadProgress.show(progress);
                            };
                            vm.data.form[k].uploader.uploadAll();
                            vm.uploads.total++;
                        }
                    }
                }
                console.log('here');
            };
    
            vm.doInscricao = function() {
                /* valide o from e gere os dados de inscrição */
                for(var k in vm.data.form) {
                    if(vm.data.form[k].needed && vm.data.form[k].value == '') {
                        $.toast({
                            heading: 'Erro!',
                            text: vm.data.form[k].label + ' é necessário!',
                            showHideTransition: 'fade',
                            icon: 'error'
                        });
                        return;
                    }
                    
                    if(vm.inscricao.inscrito_data.hasOwnProperty(vm.data.form[k].field)) {
                        vm.inscricao.inscrito_data[vm.data.form[k].field] = vm.data.form[k].value;
                    }
                    
                    if(vm.inscricao.hasOwnProperty(vm.data.form[k].field)) {
                        vm.inscricao[vm.data.form[k].field] = vm.data.form[k].value;
                    }
                }
                
                // obtenha uma possível referência para o cargo
                var c_indx = vm.data.form.findIndex(x => x.field == 'cargo');
                if(c_indx >= 0) {
                    if(vm.data.form[c_indx].value != '') {
                        var cargo = vm.listCargos.find(x => x.id == vm.data.form[c_indx].value);
                        if(cargo != undefined) {
                            vm.inscricao.cargo_ref = cargo.instancia;
                        }
                    }
                }
                
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
                
                vm.inscricao.nome = vm.inscricao.inscrito_data.nome;
                vm.inscricao.email = vm.inscricao.inscrito_data.email;
                
                Inscricao.doInscricao(vm.inscricao).then(function(r) {
                    for(var k in vm.data.form) {
                        vm.data.form[k].value = '';
                    }
                    
                    $.toast({
                        heading: 'Deu certo!',
                        text: 'Sua inscrição foi realizada com sucesso',
                        hideAfter: false
                    });
                }, function(e) { 
                    $.toast({
                        heading: 'Oops, não deu!',
                        text: e,
                        hideAfter: false,
                        icon: 'error'
                    });
                });
            };
            
            
        });
})();
