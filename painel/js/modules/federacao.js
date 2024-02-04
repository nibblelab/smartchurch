
/*******************************************************************************
 * SmartChurchPanel.federacao
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.federacao', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                
                .state('SmartChurchPanel.federacao', {
                    url: "/federacao",
                    abstract: true,
                    cache: false,
                    views: {
                        'panelContent': {
                            template: "<div ui-view='menuContent'></div>"
                        }
                    }
                });
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.federacao.igrejas
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.federacao.igrejas', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.federacao.igrejas', {
                    url: "/igrejas",
                    abstract: true,
                    cache: false,
                    views: {
                        'menuContent': {
                            templateUrl: "templates/internal.html"
                        }
                    },
                    resolve: {
                        loadMyCtrl: ['$ocLazyLoad', function($ocLazyLoad) {
                            return $ocLazyLoad.load([
                                'js/controllers/federacao/igrejas/igreja_ctrl.js',
                                'js/services/igreja_srv.js',
                                'js/services/sociedade_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.federacao.igrejas.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: '',
                        perms: [ "IgrejaFederacao"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/federacao/igrejas/igreja_list.html?v=" + Versions.html,
                            controller: 'IgrejadaFederacaoCtrl'
                        }
                    }
                })
                
                ;
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.federacao.congregacoes
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.federacao.congregacoes', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.federacao.congregacoes', {
                    url: "/congregacoes",
                    abstract: true,
                    cache: false,
                    views: {
                        'menuContent': {
                            templateUrl: "templates/internal.html"
                        }
                    },
                    resolve: {
                        loadMyCtrl: ['$ocLazyLoad', function($ocLazyLoad) {
                            return $ocLazyLoad.load([
                                'js/controllers/federacao/congregacoes/congregacao_ctrl.js',
                                'js/services/congregacao_srv.js',
                                'js/services/igreja_srv.js',
                                'js/services/sociedade_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.federacao.congregacoes.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: '',
                        perms: [ "CongregacaoFederacao"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/federacao/congregacoes/congregacao_list.html?v=" + Versions.html,
                            controller: 'CongregacaodaFederacaoCtrl'
                        }
                    }
                })
                
                ;
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.federacao.pontos
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.federacao.pontos', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.federacao.pontos', {
                    url: "/pontos",
                    abstract: true,
                    cache: false,
                    views: {
                        'menuContent': {
                            templateUrl: "templates/internal.html"
                        }
                    },
                    resolve: {
                        loadMyCtrl: ['$ocLazyLoad', function($ocLazyLoad) {
                            return $ocLazyLoad.load([
                                'js/controllers/federacao/pontos/ponto_ctrl.js',
                                'js/services/pontodepregacao_srv.js',
                                'js/services/igreja_srv.js',
                                'js/services/sociedade_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.federacao.pontos.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: '',
                        perms: [ "PontoFederacao"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/federacao/pontos/ponto_list.html?v=" + Versions.html,
                            controller: 'PontodaFederacaoCtrl'
                        }
                    }
                })
                
                ;
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.federacao.sociedades
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.federacao.sociedades', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.federacao.sociedades', {
                    url: "/sociedades",
                    abstract: true,
                    cache: false,
                    views: {
                        'menuContent': {
                            templateUrl: "templates/internal.html"
                        }
                    },
                    resolve: {
                        loadMyCtrl: ['$ocLazyLoad', function($ocLazyLoad) {
                            return $ocLazyLoad.load([
                                'js/controllers/federacao/sociedades/sociedade_ctrl.js',
                                'js/services/sociedade_srv.js',
                                'js/services/templo_srv.js',
                                'js/services/socio_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.federacao.sociedades.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: '',
                        perms: [ "SociedadeFederacao"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/federacao/sociedades/sociedade_list.html?v=" + Versions.html,
                            controller: 'SociedadedaFederacaoCtrl'
                        }
                    }
                })
                
                ;
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.federacao.pessoas
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.federacao.pessoas', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.federacao.pessoas', {
                    url: "/pessoas",
                    abstract: true,
                    cache: false,
                    views: {
                        'menuContent': {
                            templateUrl: "templates/internal.html"
                        }
                    },
                    resolve: {
                        loadMyCtrl: ['$ocLazyLoad', function($ocLazyLoad) {
                            return $ocLazyLoad.load([
                                'js/controllers/federacao/pessoas/pessoa_ctrl.js',
                                'js/services/socio_srv.js',
                                'js/services/templo_srv.js',
                                'js/services/sociedade_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.federacao.pessoas.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: '',
                        perms: [ "PessoaFederacao"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/federacao/pessoas/pessoa_list.html?v=" + Versions.html,
                            controller: 'PessoadaFederacaoCtrl'
                        }
                    }
                })
                
                ;
                    
        };
})();