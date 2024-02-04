
/*******************************************************************************
 * SmartChurchPanel.smartapp
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.smartapp', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                
                .state('SmartChurchPanel.smartapp', {
                    url: "/smartapp",
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
 * SmartChurchPanel.smartapp.palavra
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.smartapp.palavra', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.smartapp.palavra', {
                    url: "/palavra",
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
                                'js/controllers/smartapp/palavra/palavra_ctrl.js',
                                'js/services/sermao_srv.js',
                                'js/services/seriedesermao_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.smartapp.palavra.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        view: false,
                        back: '',
                        perms: [ "Dados"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/smartapp/palavra/palavra_list.html?v=" + Versions.html,
                            controller: 'PalavraCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.smartapp.palavra.ver', {
                    url: "/ver?id",
                    params: {
                        data: null,
                        title: 'Visualizando',
                        search: false,
                        view: true,
                        back: '',
                        perms: [ "Dados" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/smartapp/palavra/palavra_view.html?v=" + Versions.html,
                            controller: 'PalavraCtrl'
                        }
                    }
                })
                
                ;
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.smartapp.transmissoes
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.smartapp.transmissoes', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.smartapp.transmissoes', {
                    url: "/transmissoes",
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
                                'js/controllers/smartapp/transmissoes/transmissao_ctrl.js',
                                'js/services/transmissao_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.smartapp.transmissoes.buscar', {
                    url: "/buscar",
                    params: {
                        title: 'Transmissões',
                        perms: [ "Dados"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/common/transmissoes/transmissao_list.html?v=" + Versions.html,
                            controller: 'TransmissaoCtrl'
                        }
                    }
                })
                
                ;
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.smartapp.pedidosdeoracaonaigreja
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.smartapp.pedidosdeoracaonaigreja', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.smartapp.pedidosdeoracaonaigreja', {
                    url: "/pedidosdeoracaonaigreja",
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
                                'js/controllers/smartapp/pedidosdeoracao/pedidonaigreja_ctrl.js',
                                'js/services/pedidodeoracao_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.smartapp.pedidosdeoracaonaigreja.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        perms: [ "Dados"],
                        search: true
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/common/pedidosdeoracao/pedido_list.html?v=" + Versions.html,
                            controller: 'PedidosDeOracaoNaIgrejaCtrl'
                        }
                    }
                })
                
                .state('SmartChurchPanel.smartapp.pedidosdeoracaonaigreja.adicionar', {
                    url: "/adicionar",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        back: '',
                        perms: [ "PedidosOracaoIgrejaSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/common/pedidosdeoracao/pedido_form.html?v=" + Versions.html,
                            controller: 'PedidosDeOracaoNaIgrejaCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.smartapp.pedidosdeoracaonaigreja.editar', {
                    url: "/editar?id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        back: '',
                        perms: [ "PedidosOracaoIgrejaSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/common/pedidosdeoracao/pedido_form.html?v=" + Versions.html,
                            controller: 'PedidosDeOracaoNaIgrejaCtrl'
                        }
                    }
                })
                
                ;
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.smartapp.pedidosdeoracaonasociedade
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.smartapp.pedidosdeoracaonasociedade', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.smartapp.pedidosdeoracaonasociedade', {
                    url: "/pedidosdeoracaonasociedade",
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
                                'js/controllers/smartapp/pedidosdeoracao/pedidonasociedade_ctrl.js',
                                'js/services/pedidodeoracao_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.smartapp.pedidosdeoracaonasociedade.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        perms: [ "Dados"],
                        search: true
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/common/pedidosdeoracao/pedido_list.html?v=" + Versions.html,
                            controller: 'PedidosDeOracaoNaSociedadeCtrl'
                        }
                    }
                })
                
                .state('SmartChurchPanel.smartapp.pedidosdeoracaonasociedade.adicionar', {
                    url: "/adicionar",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        back: '',
                        perms: [ "PedidosOracaoSociedadeSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/common/pedidosdeoracao/pedido_form.html?v=" + Versions.html,
                            controller: 'PedidosDeOracaoNaSociedadeCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.smartapp.pedidosdeoracaonasociedade.editar', {
                    url: "/editar?id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        back: '',
                        perms: [ "PedidosOracaoSociedadeSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/common/pedidosdeoracao/pedido_form.html?v=" + Versions.html,
                            controller: 'PedidosDeOracaoNaSociedadeCtrl'
                        }
                    }
                })
                
                ;
                    
        };
})();