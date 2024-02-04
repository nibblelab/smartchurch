
/*******************************************************************************
 * SmartChurchPanel.evento
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.evento', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                
                .state('SmartChurchPanel.evento', {
                    url: "/evento",
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
 * SmartChurchPanel.evento.inscricoes
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.evento.inscricoes', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.evento.inscricoes', {
                    url: "/inscricoes",
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
                                'js/controllers/evento/inscricoes/inscricao_ctrl.js',
                                'js/services/evento_srv.js',
                                'js/services/pessoa_srv.js',
                                'js/services/membro_srv.js',
                                'js/services/cargo_srv.js',
                                'js/services/templo_srv.js',
                                'js/services/presbiterio_srv.js',
                                'js/services/sinodo_srv.js',
                                'js/services/sociedade_srv.js',
                                'js/services/federacao_srv.js',
                                'js/services/sinodal_srv.js',
                                'js/services/motivorecusa_srv.js',
                                'js/services/credencial_srv.js',
                                'js/services/inscricao_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.evento.inscricoes.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: '',
                        perms: [ "Inscricao", "InscricaoSave", "InscricaoRemove", "InscricaoBlock"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/evento/inscricoes/inscricao_list.html?v=" + Versions.html,
                            controller: 'InscricaoCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.evento.inscricoes.adicionar', {
                    url: "/adicionar",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        back: '',
                        perms: [ "InscricaoSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/evento/inscricoes/inscricao_form.html?v=" + Versions.html,
                            controller: 'InscricaoCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.evento.inscricoes.editar', {
                    url: "/editar?id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        back: '',
                        perms: [ "InscricaoSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/evento/inscricoes/inscricao_form.html?v=" + Versions.html,
                            controller: 'InscricaoCtrl'
                        }
                    }
                })
                
                .state('SmartChurchPanel.evento.inscricoes.ver', {
                    url: "/ver?id",
                    params: {
                        data: null,
                        title: 'Visualizando',
                        search: false,
                        back: '',
                        perms: [ "InscricaoSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/evento/inscricoes/inscricao_form.html?v=" + Versions.html,
                            controller: 'InscricaoCtrl'
                        }
                    }
                })
                ;
                    
        };
})();