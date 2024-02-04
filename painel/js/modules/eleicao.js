
/*******************************************************************************
 * SmartChurchPanel.votacoes
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.votacoes', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('dashboard.votacoes', {
                    url: "/votacoes",
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
                                'js/controllers/eleicao/votacoes/votacao_ctrl.js',
                                'js/services/votacao_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('dashboard.votacoes.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: '',
                        perms: [ "Votacao", "VotacaoSave", "VotacaoRemove"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/eleicao/votacoes/votacao_list.html?v=" + Versions.html,
                            controller: 'VotacaoCtrl'
                        }
                    }
                })

                .state('dashboard.votacoes.adicionar', {
                    url: "/adicionar",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        back: '',
                        perms: [ "VotacaoSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/eleicao/votacoes/votacao_form.html?v=" + Versions.html,
                            controller: 'VotacaoCtrl'
                        }
                    }
                })

                .state('dashboard.votacoes.editar', {
                    url: "/editar?id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        back: '',
                        perms: [ "VotacaoSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/eleicao/votacoes/votacao_form.html?v=" + Versions.html,
                            controller: 'VotacaoCtrl'
                        }
                    }
                })
                
                .state('dashboard.votacoes.escrutinios', {
                    url: "/escrutinios",
                    abstract: true,
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/votacoes/internal.html"
                        }
                    },
                    resolve: {
                        loadMyCtrl: ['$ocLazyLoad', function($ocLazyLoad) {
                            return $ocLazyLoad.load([
                                'js/controllers/eleicao/votacoes/votacao_ctrl.js',
                                'js/services/votacao_srv.js'
                            ], { cache: false});
                        }]
                    }
                })
                ;
                    
        };
})();