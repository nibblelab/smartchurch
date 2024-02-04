
/*******************************************************************************
 * SmartChurchPanel.evangelista
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.evangelista', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                
                .state('SmartChurchPanel.evangelista', {
                    url: "/evangelista",
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
 * SmartChurchPanel.evangelista.agenda
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.evangelista.agenda', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.evangelista.agenda', {
                    url: "/agenda",
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
                                'js/controllers/evangelista/agenda/agenda_ctrl.js',
                                'js/services/tagsagenda_srv.js',
                                'js/services/agenda_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.evangelista.agenda.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: '',
                        perms: [ "AgendaEvangelista", "AgendaEvangelistaSave", "AgendaEvangelistaRemove", "AgendaEvangelistaBlock"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/evangelista/agenda/agenda_list.html?v=" + Versions.html,
                            controller: 'AgendaDoEvangelistaCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.evangelista.agenda.adicionar', {
                    url: "/adicionar",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        back: '',
                        perms: [ "AgendaEvangelistaSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/evangelista/agenda/agenda_form.html?v=" + Versions.html,
                            controller: 'AgendaDoEvangelistaCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.evangelista.agenda.editar', {
                    url: "/editar?id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        back: '',
                        perms: [ "AgendaEvangelistaSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/evangelista/agenda/agenda_form.html?v=" + Versions.html,
                            controller: 'AgendaDoEvangelistaCtrl'
                        }
                    }
                });
                    
        };
})();