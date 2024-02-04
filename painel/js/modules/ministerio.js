
/*******************************************************************************
 * SmartChurchPanel.ministerio
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.ministerio', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                
                .state('SmartChurchPanel.ministerio', {
                    url: "/ministerio",
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
 * SmartChurchPanel.ministerio.participantes
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.ministerio.participantes', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.ministerio.participantes', {
                    url: "/participantes",
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
                                'js/controllers/ministerio/participantes/participante_ctrl.js',
                                'js/services/membro_srv.js',
                                'js/services/servo_srv.js',
                                'js/services/perfil_srv.js',
                                'js/services/sysconfig_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.ministerio.participantes.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: '',
                        perms: [ "Servo", "ServoSave", "ServoRemove", "ServoBlock"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/ministerio/participantes/participante_list.html?v=" + Versions.html,
                            controller: 'ParticipanteDoMinisterioCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.ministerio.participantes.adicionar', {
                    url: "/adicionar",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        back: '',
                        perms: [ "ServoSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/ministerio/participantes/participante_form.html?v=" + Versions.html,
                            controller: 'ParticipanteDoMinisterioCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.ministerio.participantes.editar', {
                    url: "/editar?id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        back: '',
                        perms: [ "ServoSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/ministerio/participantes/participante_form.html?v=" + Versions.html,
                            controller: 'ParticipanteDoMinisterioCtrl'
                        }
                    }
                })
                
            ;
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.ministerio.secretarias
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.ministerio.secretarias', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.ministerio.secretarias', {
                    url: "/secretarias",
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
                                'js/controllers/ministerio/secretarias/secretaria_ctrl.js',
                                'js/services/secretaria_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.ministerio.secretarias.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: '',
                        perms: [ "SecretariaMinisterio", "SecretariaMinisterioSave", "SecretariaMinisterioRemove", 
                                    "SecretariaMinisterioBlock", "SecretariaMinisterioPessoa"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/common/secretaria/secretaria_list.html?v=" + Versions.html,
                            controller: 'SecretariaDoMinisterioCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.ministerio.secretarias.adicionar', {
                    url: "/adicionar",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        back: '',
                        perms: [ "SecretariaMinisterioSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/common/secretaria/secretaria_form.html?v=" + Versions.html,
                            controller: 'SecretariaDoMinisterioCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.ministerio.secretarias.editar', {
                    url: "/editar?id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        back: '',
                        perms: [ "SecretariaMinisterioSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/common/secretaria/secretaria_form.html?v=" + Versions.html,
                            controller: 'SecretariaDoMinisterioCtrl'
                        }
                    }
                })
                
                .state('SmartChurchPanel.ministerio.secretarias.secretarios', {
                    url: "/secretarios",
                    abstract: true,
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/subinternal.html"
                        }
                    },
                    resolve: {
                        loadMyCtrl: ['$ocLazyLoad', function($ocLazyLoad) {
                            return $ocLazyLoad.load([
                                'js/controllers/ministerio/secretarias/secretario_ctrl.js',
                                'js/services/servo_srv.js',
                                'js/services/perfil_srv.js',
                                'js/services/sysconfig_srv.js',
                                'js/services/secretario_srv.js',
                                'js/services/secretaria_srv.js'
                            ], { cache: false});
                        }]
                    }
                })
                
                .state('SmartChurchPanel.ministerio.secretarias.secretarios.buscar', {
                    url: "/buscar?secretaria",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: 'SmartChurchPanel.ministerio.secretarias.buscar',
                        perms: [ "SecretariaMinisterioPessoa" ]
                    },
                    cache: false,
                    views: {
                        'subInternalContent': {
                            templateUrl: "templates/common/secretario/secretario_list.html?v=" + Versions.html,
                            controller: 'SecretarioDoMinisterioCtrl'
                        }
                    }
                })
                
                .state('SmartChurchPanel.ministerio.secretarias.secretarios.adicionar', {
                    url: "/adicionar?secretaria",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        back: '',
                        perms: [ "SecretariaMinisterioPessoa" ]
                    },
                    cache: false,
                    views: {
                        'subInternalContent': {
                            templateUrl: "templates/common/secretario/secretario_form.html?v=" + Versions.html,
                            controller: 'SecretarioDoMinisterioCtrl'
                        }
                    }
                })
                
                .state('SmartChurchPanel.ministerio.secretarias.secretarios.editar', {
                    url: "/editar?secretaria&id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        back: '',
                        perms: [ "SecretariaMinisterioPessoa" ]
                    },
                    cache: false,
                    views: {
                        'subInternalContent': {
                            templateUrl: "templates/common/secretario/secretario_form.html?v=" + Versions.html,
                            controller: 'SecretarioDoMinisterioCtrl'
                        }
                    }
                })
            ;
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.ministerio.agenda
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.ministerio.agenda', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.ministerio.agenda', {
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
                                'js/controllers/ministerio/agenda/agenda_ctrl.js',
                                'js/services/tagsagenda_srv.js',
                                'js/services/agenda_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.ministerio.agenda.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: '',
                        perms: [ "AgendaMinisterio", "AgendaMinisterioSave", "AgendaMinisterioRemove", "AgendaMinisterioBlock"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/ministerio/agenda/agenda_list.html?v=" + Versions.html,
                            controller: 'AgendaDoMinisterioCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.ministerio.agenda.adicionar', {
                    url: "/adicionar",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        back: '',
                        perms: [ "AgendaMinisterioSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/ministerio/agenda/agenda_form.html?v=" + Versions.html,
                            controller: 'AgendaDoMinisterioCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.ministerio.agenda.editar', {
                    url: "/editar?id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        back: '',
                        perms: [ "AgendaMinisterioSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/ministerio/agenda/agenda_form.html?v=" + Versions.html,
                            controller: 'AgendaDoMinisterioCtrl'
                        }
                    }
                });
                    
        };
})();