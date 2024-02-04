
/*******************************************************************************
 * SmartChurchPanel.sociedade
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.sociedade', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                
                .state('SmartChurchPanel.sociedade', {
                    url: "/sociedade",
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
 * SmartChurchPanel.sociedade.socios
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.sociedade.socios', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.sociedade.socios', {
                    url: "/socios",
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
                                'js/controllers/sociedade/socio/socio_ctrl.js',
                                'js/services/membro_srv.js',
                                'js/services/socio_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.sociedade.socios.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: '',
                        perms: [ "Socio", "SocioSave", "SocioRemove", "SocioBlock"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/sociedade/socio/socio_list.html?v=" + Versions.html,
                            controller: 'SocioCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.sociedade.socios.adicionar', {
                    url: "/adicionar",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        back: '',
                        perms: [ "SocioSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/sociedade/socio/socio_form.html?v=" + Versions.html,
                            controller: 'SocioCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.sociedade.socios.editar', {
                    url: "/editar?id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        back: '',
                        perms: [ "SocioSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/sociedade/socio/socio_form.html?v=" + Versions.html,
                            controller: 'SocioCtrl'
                        }
                    }
                });
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.sociedade.diretorias
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.sociedade.diretorias', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.sociedade.diretorias', {
                    url: "/diretorias",
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
                                'js/controllers/sociedade/diretorias/diretoria_ctrl.js',
                                'js/services/diretoria_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.sociedade.diretorias.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: '',
                        perms: [ "DiretoriaSociedade", "DiretoriaSociedadeSave", "DiretoriaSociedadeRemove", "DiretoriaSociedadeBlock", "DiretoriaSociedadeOficiais"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/common/diretorias/diretoria_list.html?v=" + Versions.html,
                            controller: 'DiretoriaSociedadeCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.sociedade.diretorias.adicionar', {
                    url: "/adicionar",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        back: '',
                        perms: [ "DiretoriaSociedadeSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/common/diretorias/diretoria_form.html?v=" + Versions.html,
                            controller: 'DiretoriaSociedadeCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.sociedade.diretorias.editar', {
                    url: "/editar?id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        back: '',
                        perms: [ "DiretoriaSociedadeSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/common/diretorias/diretoria_form.html?v=" + Versions.html,
                            controller: 'DiretoriaSociedadeCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.sociedade.diretorias.oficiais', {
                    url: "/oficiais",
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
                                'js/controllers/sociedade/diretorias/oficial_ctrl.js',
                                'js/services/socio_srv.js',
                                'js/services/cargo_srv.js',
                                'js/services/oficial_srv.js'
                            ], { cache: false});
                        }]
                    }
                })
                
                .state('SmartChurchPanel.sociedade.diretorias.oficiais.buscar', {
                    url: "/buscar?diretoria",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: 'SmartChurchPanel.sociedade.diretorias.buscar',
                        perms: [ "DiretoriaSociedadeOficiais" ]
                    },
                    cache: false,
                    views: {
                        'subInternalContent': {
                            templateUrl: "templates/common/diretorias/oficial_list.html?v=" + Versions.html,
                            controller: 'OficialSociedadeCtrl'
                        }
                    }
                })
                
                .state('SmartChurchPanel.sociedade.diretorias.oficiais.adicionar', {
                    url: "/adicionar?diretoria",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        back: '',
                        perms: [ "DiretoriaSociedadeOficiais" ]
                    },
                    cache: false,
                    views: {
                        'subInternalContent': {
                            templateUrl: "templates/common/diretorias/oficial_form.html?v=" + Versions.html,
                            controller: 'OficialSociedadeCtrl'
                        }
                    }
                })
                
                .state('SmartChurchPanel.sociedade.diretorias.oficiais.editar', {
                    url: "/editar?diretoria&id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        back: '',
                        perms: [ "DiretoriaSociedadeOficiais" ]
                    },
                    cache: false,
                    views: {
                        'subInternalContent': {
                            templateUrl: "templates/common/diretorias/oficial_form.html?v=" + Versions.html,
                            controller: 'OficialSociedadeCtrl'
                        }
                    }
                })
                
                ;
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.sociedade.secretarias
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.sociedade.secretarias', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.sociedade.secretarias', {
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
                                'js/controllers/sociedade/secretarias/secretaria_ctrl.js',
                                'js/services/secretaria_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.sociedade.secretarias.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: '',
                        perms: [ "SecretariaSociedade", "SecretariaSociedadeSave", "SecretariaSociedadeRemove", 
                                    "SecretariaSociedadeBlock", "SecretariaSociedadeManage"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/common/secretaria/secretaria_list.html?v=" + Versions.html,
                            controller: 'SecretariaDaSociedadeCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.sociedade.secretarias.adicionar', {
                    url: "/adicionar",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        back: '',
                        perms: [ "SecretariaSociedadeSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/common/secretaria/secretaria_form.html?v=" + Versions.html,
                            controller: 'SecretariaDaSociedadeCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.sociedade.secretarias.editar', {
                    url: "/editar?id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        back: '',
                        perms: [ "SecretariaSociedadeSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/common/secretaria/secretaria_form.html?v=" + Versions.html,
                            controller: 'SecretariaDaSociedadeCtrl'
                        }
                    }
                })
                
                .state('SmartChurchPanel.sociedade.secretarias.secretarios', {
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
                                'js/controllers/sociedade/secretarias/secretario_ctrl.js',
                                'js/services/socio_srv.js',
                                'js/services/perfil_srv.js',
                                'js/services/sysconfig_srv.js',
                                'js/services/secretario_srv.js',
                                'js/services/secretaria_srv.js'
                            ], { cache: false});
                        }]
                    }
                })
                
                .state('SmartChurchPanel.sociedade.secretarias.secretarios.buscar', {
                    url: "/buscar?secretaria",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: 'SmartChurchPanel.sociedade.secretarias.buscar',
                        perms: [ "SecretariaSociedadePessoa" ]
                    },
                    cache: false,
                    views: {
                        'subInternalContent': {
                            templateUrl: "templates/common/secretario/secretario_list.html?v=" + Versions.html,
                            controller: 'SecretarioDaSociedadeCtrl'
                        }
                    }
                })
                
                .state('SmartChurchPanel.sociedade.secretarias.secretarios.adicionar', {
                    url: "/adicionar?secretaria",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        back: '',
                        perms: [ "SecretariaSociedadePessoa" ]
                    },
                    cache: false,
                    views: {
                        'subInternalContent': {
                            templateUrl: "templates/common/secretario/secretario_form.html?v=" + Versions.html,
                            controller: 'SecretarioDaSociedadeCtrl'
                        }
                    }
                })
                
                .state('SmartChurchPanel.sociedade.secretarias.secretarios.editar', {
                    url: "/editar?secretaria&id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        back: '',
                        perms: [ "SecretariaSociedadePessoa" ]
                    },
                    cache: false,
                    views: {
                        'subInternalContent': {
                            templateUrl: "templates/common/secretario/secretario_form.html?v=" + Versions.html,
                            controller: 'SecretarioDaSociedadeCtrl'
                        }
                    }
                })
            ;
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.sociedade.agenda
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.sociedade.agenda', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.sociedade.agenda', {
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
                                'js/controllers/sociedade/agenda/agenda_ctrl.js',
                                'js/services/tagsagenda_srv.js',
                                'js/services/agenda_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.sociedade.agenda.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: '',
                        perms: [ "AgendaSociedade", "AgendaSociedadeSave", "AgendaSociedadeRemove", "AgendaSociedadeBlock"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/sociedade/agenda/agenda_list.html?v=" + Versions.html,
                            controller: 'AgendaDaSociedadeCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.sociedade.agenda.adicionar', {
                    url: "/adicionar",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        back: '',
                        perms: [ "AgendaSociedadeSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/sociedade/agenda/agenda_form.html?v=" + Versions.html,
                            controller: 'AgendaDaSociedadeCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.sociedade.agenda.editar', {
                    url: "/editar?id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        back: '',
                        perms: [ "AgendaSociedadeSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/sociedade/agenda/agenda_form.html?v=" + Versions.html,
                            controller: 'AgendaDaSociedadeCtrl'
                        }
                    }
                });
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.sociedade.relatorios
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.sociedade.relatorios', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.sociedade.relatorios', {
                    url: "/relatorios",
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
                                'js/controllers/sociedade/relatorios/relatorio_ctrl.js',
                                'js/services/relatorio_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.sociedade.relatorios.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: '',
                        perms: [ "RelatorioSociedade", "RelatorioSociedadeSave", "RelatorioSociedadeRemove", "RelatorioSociedadeBlock", "RelatorioSociedadeAvail"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/sociedade/relatorios/relatorio_list.html?v=" + Versions.html,
                            controller: 'RelatorioDaSociedadeCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.sociedade.relatorios.adicionar', {
                    url: "/adicionar",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        back: '',
                        perms: [ "RelatorioSociedadeSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/sociedade/relatorios/relatorio_form.html?v=" + Versions.html,
                            controller: 'RelatorioDaSociedadeCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.sociedade.relatorios.editar', {
                    url: "/editar?id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        back: '',
                        perms: [ "RelatorioSociedadeSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/sociedade/relatorios/relatorio_form.html?v=" + Versions.html,
                            controller: 'RelatorioDaSociedadeCtrl'
                        }
                    }
                })
                
            ;
                    
        };
})();
