
/*******************************************************************************
 * SmartChurchPanel.pastor
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.pastor', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                
                .state('SmartChurchPanel.pastor', {
                    url: "/pastor",
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
 * SmartChurchPanel.pastor.agenda
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.pastor.agenda', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.pastor.agenda', {
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
                                'js/controllers/pastor/agenda/agenda_ctrl.js',
                                'js/services/tagsagenda_srv.js',
                                'js/services/agenda_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.pastor.agenda.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: '',
                        perms: [ "AgendaPastor", "AgendaPastorSave", "AgendaPastorRemove", "AgendaPastorBlock"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/pastor/agenda/agenda_list.html?v=" + Versions.html,
                            controller: 'AgendaDoPastorCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.pastor.agenda.adicionar', {
                    url: "/adicionar",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        back: '',
                        perms: [ "AgendaPastorSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/pastor/agenda/agenda_form.html?v=" + Versions.html,
                            controller: 'AgendaDoPastorCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.pastor.agenda.editar', {
                    url: "/editar?id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        back: '',
                        perms: [ "AgendaPastorSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/pastor/agenda/agenda_form.html?v=" + Versions.html,
                            controller: 'AgendaDoPastorCtrl'
                        }
                    }
                });
                    
        };
})();
