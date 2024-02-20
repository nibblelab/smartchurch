
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel', [
            'SmartChurchPanel.config', 
            'SmartChurchPanel.directives',
            'SmartChurchPanel.states', 
            'SmartChurchPanel.utils',  
            'angularFileUpload',
            'ui.mask', 
            'ui.utils.masks',
            '720kb.tooltips',
            'summernote',
            'color.picker',
            'ae-datetimepicker',
            'ngclipboard',
            'angularFileUpload',
            'ngSanitize',
            'ngclipboard',
            'nblutils'
        ]);
})();

(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.config', []);
})();


(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.config', [])
        .constant('ApiEndpoint', {
                url: Endpoint.URL,
                rc: Endpoint.RC,
                base: Endpoint.BASE,
                inscricoes: Endpoint.INSCRICOES,
                mural: Endpoint.MURAL,
                validacao: Endpoint.VALIDACAO
        })
        
        .constant('Versions', {
            html: '1.1.8',
            data: '0.9.6'
        })
        
        .directive('focusOn', function() {
            return function(scope, elem, attr) {
               scope.$on(attr.focusOn, function(e) {
                   elem[0].focus();
               });
            };
        })
        
        .directive("fileread", [function () {
            return {
                scope: {
                    fileread: "="
                },
                link: function (scope, element, attributes) {
                    element.bind("change", function (changeEvent) {
                        var reader = new FileReader();
                        var f = changeEvent.target.files[0].name;
                        reader.onload = function (loadEvent) {
                            scope.$apply(function () {
                                scope.fileread = {
                                    name: f,
                                    content: loadEvent.target.result
                                };
                            });
                        }
                        reader.readAsDataURL(changeEvent.target.files[0]);
                    });
                }
            };
        }])
        
        .directive("imgread", [function () {
            return {
                scope: {
                    imgread: "="
                },
                link: function (scope, element, attributes) {
                    element.bind("change", function (changeEvent) {
                        var reader = new FileReader();
                        var f = changeEvent.target.files[0].name;
                        reader.onload = function (loadEvent) {
                            
                            // check size
                            var sizeErr = false;
                            var base64str = loadEvent.target.result.split('base64,')[1];
                            var decoded = atob(base64str);
                            var size = attributes.imgreadSize.toLowerCase();
                            if(size.indexOf('kb') > -1) {
                                // check for KB
                                size = size.replace('kb', '');
                                size = parseInt(size) * 1024;
                                sizeErr = (decoded.length > size);
                            }
                            else if(size.indexOf('mb') > -1) {
                                // check for MB
                                size = size.replace('mb', '');
                                size = parseInt(size) * (1024 * 1024);
                                sizeErr = (decoded.length > size);
                            }
                            
                            if(sizeErr) {
                                scope.$emit('imgReadSizeErr');
                            }
                            else {
                                // check resolution
                                var img = new Image();
                                img.src = loadEvent.target.result;
                                img.onload = function() {
                                    var w = this.width;
                                    var h = this.height;
                                    if(attributes.imgreadWidth == w && attributes.imgreadHeight == h) {
                                        scope.$apply(function () {
                                            scope.imgread = {
                                                name: f,
                                                content: loadEvent.target.result
                                            };
                                        });
                                    }
                                    else {
                                        scope.$emit('imgReadResolutionErr');
                                    }
                                };
                            }
                        };
                        reader.readAsDataURL(changeEvent.target.files[0]);
                    });
                }
            };
        }])
    
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
        .module('SmartChurchPanel.directives', [
            'SmartChurchPanel.usuarioDirective',
            'SmartChurchPanel.pessoaDirective',
            'SmartChurchPanel.enderecoDirective',
            'SmartChurchPanel.siteDirective',
            'SmartChurchPanel.secretariaDirective',
            'SmartChurchPanel.secretarioDirective',
            'SmartChurchPanel.igrejaHomeDirective',
            'SmartChurchPanel.sociedadeHomeDirective',
            'SmartChurchPanel.federacaoHomeDirective',
            'SmartChurchPanel.sinodalHomeDirective',
            'SmartChurchPanel.agendaDirective'
        ]);
})();

(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.states', [
            /* main */
            'SmartChurchPanel.main.login',
            'SmartChurchPanel.main.esquecisenha',
            'SmartChurchPanel.main.alterarsenha',
            'SmartChurchPanel.main.registrar',
            'SmartChurchPanel.main.termos',
            'SmartChurchPanel.main.validacao',
            'SmartChurchPanel.main.painel',
            'SmartChurchPanel.main.sempermissao',
            'SmartChurchPanel.main.home',
            'SmartChurchPanel.main.me',
            'SmartChurchPanel.main.membresia',
            /* staff */
            'SmartChurchPanel.staff',
            'SmartChurchPanel.staff.perfis',
            'SmartChurchPanel.staff.sysconfig',
            'SmartChurchPanel.staff.supremoconcilio',
            'SmartChurchPanel.staff.sinodos',
            'SmartChurchPanel.staff.presbiterios',
            'SmartChurchPanel.staff.igrejas',
            'SmartChurchPanel.staff.congregacoes',
            'SmartChurchPanel.staff.pontosdepregacao',
            'SmartChurchPanel.staff.nacionais',
            'SmartChurchPanel.staff.sinodais',
            'SmartChurchPanel.staff.federacoes',
            'SmartChurchPanel.staff.cargos',
            'SmartChurchPanel.staff.tagsagenda',
            'SmartChurchPanel.staff.clientes',
            'SmartChurchPanel.staff.profissoes',
            'SmartChurchPanel.staff.doacoes',
            'SmartChurchPanel.staff.necessidadesespeciais',
            'SmartChurchPanel.staff.motivosrecusa',
            /* igreja */
            'SmartChurchPanel.igreja',
            'SmartChurchPanel.igreja.dados',
            'SmartChurchPanel.igreja.congregacoes',
            'SmartChurchPanel.igreja.pontosdepregacao',
            'SmartChurchPanel.igreja.membros',
            'SmartChurchPanel.igreja.secretarias',
            'SmartChurchPanel.igreja.ministerios',
            'SmartChurchPanel.igreja.sociedades',
            'SmartChurchPanel.igreja.pequenosgrupos',
            'SmartChurchPanel.igreja.pastores',
            'SmartChurchPanel.igreja.evangelistas',
            'SmartChurchPanel.igreja.presbiteros',
            'SmartChurchPanel.igreja.diaconos',
            'SmartChurchPanel.igreja.oficiais',
            'SmartChurchPanel.igreja.conselho',
            'SmartChurchPanel.igreja.junta',
            'SmartChurchPanel.igreja.agenda',
            'SmartChurchPanel.igreja.transmissoes',
            'SmartChurchPanel.igreja.eventos',
            'SmartChurchPanel.igreja.eleicoes',
            'SmartChurchPanel.igreja.seriesdesermoes',
            'SmartChurchPanel.igreja.sermoes',
            'SmartChurchPanel.igreja.seriesdeestudos',
            'SmartChurchPanel.igreja.estudos',
            'SmartChurchPanel.igreja.superintendencia',
            'SmartChurchPanel.igreja.token',
            'SmartChurchPanel.igreja.mural',
            'SmartChurchPanel.igreja.pedidosdeoracao',
            /* smartapp */
            'SmartChurchPanel.smartapp',
            'SmartChurchPanel.smartapp.palavra',
            'SmartChurchPanel.smartapp.transmissoes',
            'SmartChurchPanel.smartapp.pedidosdeoracaonaigreja',
            'SmartChurchPanel.smartapp.pedidosdeoracaonasociedade',
            /* sinodal */
            'SmartChurchPanel.sinodal',
            'SmartChurchPanel.sinodal.igrejas',
            'SmartChurchPanel.sinodal.congregacoes',
            'SmartChurchPanel.sinodal.pontos',
            'SmartChurchPanel.sinodal.federacoes',
            'SmartChurchPanel.sinodal.sociedades',
            'SmartChurchPanel.sinodal.pessoas',
            'SmartChurchPanel.sinodal.diretorias',
            'SmartChurchPanel.sinodal.secretarias',
            'SmartChurchPanel.sinodal.agenda',
            'SmartChurchPanel.sinodal.eventos',
            'SmartChurchPanel.sinodal.token',
            'SmartChurchPanel.sinodal.dados',
            /* federação */
            'SmartChurchPanel.federacao',
            'SmartChurchPanel.federacao.igrejas',
            'SmartChurchPanel.federacao.congregacoes',
            'SmartChurchPanel.federacao.pontos',
            'SmartChurchPanel.federacao.sociedades',
            'SmartChurchPanel.federacao.pessoas',
            /* sociedade */
            'SmartChurchPanel.sociedade',
            'SmartChurchPanel.sociedade.socios',
            'SmartChurchPanel.sociedade.diretorias',
            'SmartChurchPanel.sociedade.secretarias',
            'SmartChurchPanel.sociedade.agenda',
            /* ministério */
            'SmartChurchPanel.ministerio',
            'SmartChurchPanel.ministerio.participantes',
            'SmartChurchPanel.ministerio.secretarias',
            'SmartChurchPanel.ministerio.agenda',
            /* evento */
            'SmartChurchPanel.evento',
            'SmartChurchPanel.evento.inscricoes',
            /* pastor */
            'SmartChurchPanel.pastor',
            'SmartChurchPanel.pastor.agenda',
            /* evangelista */
            'SmartChurchPanel.evangelista',
            'SmartChurchPanel.evangelista.agenda',
            /* ebd */
            'SmartChurchPanel.ebd',
            'SmartChurchPanel.ebd.salas',
            /* dependências */
            'ui.router', 
            'ui.router.state.events'
        ]);
})();

(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.states')
        .config(statesConfig);

    statesConfig.$inject = ['$urlRouterProvider'];
    function statesConfig($urlRouterProvider) {
        
        $urlRouterProvider.otherwise('/login');
    };
    
})();


