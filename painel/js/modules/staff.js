
/*******************************************************************************
 * SmartChurchPanel.staff
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.staff', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                
                .state('SmartChurchPanel.staff', {
                    url: "/staff",
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
 * SmartChurchPanel.staff.perfis
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.staff.perfis', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.staff.perfis', {
                    url: "/perfis",
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
                                'js/controllers/staff/perfil/perfil_ctrl.js',
                                'js/services/perfil_srv.js',
                                'js/services/permissao_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.staff.perfis.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: '',
                        perms: [ "Perfil", "PerfilSave", "PerfilRemove"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/staff/perfil/perfil_list.html?v=" + Versions.html,
                            controller: 'PerfilCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.staff.perfis.adicionar', {
                    url: "/adicionar",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        back: '',
                        perms: [ "PerfilSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/staff/perfil/perfil_form.html?v=" + Versions.html,
                            controller: 'PerfilCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.staff.perfis.editar', {
                    url: "/editar?id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        back: '',
                        perms: [ "PerfilSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/staff/perfil/perfil_form.html?v=" + Versions.html,
                            controller: 'PerfilCtrl'
                        }
                    }
                });
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.staff.sysconfig
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.staff.sysconfig', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.staff.sysconfig', {
                    url: "/sysconfig",
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
                                'js/controllers/staff/sysconfig/sysconfig_ctrl.js',
                                'js/services/perfil_srv.js',
                                'js/services/tagsagenda_srv.js',
                                'js/services/sysconfig_srv.js'
                            ], { cache: false});
                        }]
                    }
                })
                
                .state('SmartChurchPanel.staff.sysconfig.editar', {
                    url: "/editar",
                    params: {
                        id: '201410281411411185581553',
                        data: null,
                        title: 'Editando',
                        search: false,
                        back: '',
                        perms: [ "SysConfig" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/staff/sysconfig/sysconfig_form.html?v=" + Versions.html,
                            controller: 'SysConfigCtrl'
                        }
                    }
                });
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.staff.supremoconcilio
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.staff.supremoconcilio', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.staff.supremoconcilio', {
                    url: "/supremoconcilio",
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
                                'js/controllers/staff/supremo/supremo_ctrl.js',
                                'js/services/supremo_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.staff.supremoconcilio.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: '',
                        perms: [ "Supremo", "SupremoSave", "SupremoRemove", "SupremoBlock"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/staff/supremo/supremo_list.html?v=" + Versions.html,
                            controller: 'SupremoCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.staff.supremoconcilio.adicionar', {
                    url: "/adicionar",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        back: '',
                        perms: [ "SupremoSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/staff/supremo/supremo_form.html?v=" + Versions.html,
                            controller: 'SupremoCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.staff.supremoconcilio.editar', {
                    url: "/editar?id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        back: '',
                        perms: [ "SupremoSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/staff/supremo/supremo_form.html?v=" + Versions.html,
                            controller: 'SupremoCtrl'
                        }
                    }
                });
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.staff.sinodos
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.staff.sinodos', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.staff.sinodos', {
                    url: "/sinodos",
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
                                'js/controllers/staff/sinodo/sinodo_ctrl.js',
                                'js/services/supremo_srv.js',
                                'js/services/sinodo_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.staff.sinodos.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: '',
                        perms: [ "Sinodo", "SinodoSave", "SinodoRemove", "SinodoBlock"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/staff/sinodo/sinodo_list.html?v=" + Versions.html,
                            controller: 'SinodoCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.staff.sinodos.adicionar', {
                    url: "/adicionar",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        back: '',
                        perms: [ "SinodoSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/staff/sinodo/sinodo_form.html?v=" + Versions.html,
                            controller: 'SinodoCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.staff.sinodos.editar', {
                    url: "/editar?id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        back: '',
                        perms: [ "SinodoSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/staff/sinodo/sinodo_form.html?v=" + Versions.html,
                            controller: 'SinodoCtrl'
                        }
                    }
                });
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.staff.presbiterios
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.staff.presbiterios', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.staff.presbiterios', {
                    url: "/presbiterios",
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
                                'js/controllers/staff/presbiterio/presbiterio_ctrl.js',
                                'js/services/presbiterio_srv.js',
                                'js/services/sinodo_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.staff.presbiterios.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: '',
                        perms: [ "Presbiterio", "PresbiterioSave", "PresbiterioRemove", "PresbiterioBlock"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/staff/presbiterio/presbiterio_list.html?v=" + Versions.html,
                            controller: 'PresbiterioCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.staff.presbiterios.adicionar', {
                    url: "/adicionar",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        back: '',
                        perms: [ "PresbiterioSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/staff/presbiterio/presbiterio_form.html?v=" + Versions.html,
                            controller: 'PresbiterioCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.staff.presbiterios.editar', {
                    url: "/editar?id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        back: '',
                        perms: [ "PresbiterioSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/staff/presbiterio/presbiterio_form.html?v=" + Versions.html,
                            controller: 'PresbiterioCtrl'
                        }
                    }
                });
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.staff.igrejas
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.staff.igrejas', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.staff.igrejas', {
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
                                'js/controllers/staff/igreja/igreja_ctrl.js',
                                'js/services/igreja_srv.js',
                                'js/services/presbiterio_srv.js',
                                'js/services/sinodo_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.staff.igrejas.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: '',
                        perms: [ "Igreja", "IgrejaSave", "IgrejaRemove", "IgrejaBlock"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/staff/igreja/igreja_list.html?v=" + Versions.html,
                            controller: 'IgrejaCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.staff.igrejas.adicionar', {
                    url: "/adicionar",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        back: '',
                        perms: [ "IgrejaSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/staff/igreja/igreja_form.html?v=" + Versions.html,
                            controller: 'IgrejaCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.staff.igrejas.editar', {
                    url: "/editar?id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        back: '',
                        perms: [ "IgrejaSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/staff/igreja/igreja_form.html?v=" + Versions.html,
                            controller: 'IgrejaCtrl'
                        }
                    }
                });
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.staff.congregacoes
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.staff.congregacoes', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.staff.congregacoes', {
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
                                'js/controllers/staff/congregacao/congregacao_ctrl.js',
                                'js/services/congregacao_srv.js',
                                'js/services/igreja_srv.js',
                                'js/services/presbiterio_srv.js',
                                'js/services/sinodo_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.staff.congregacoes.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: '',
                        perms: [ "Congregacao", "CongregacaoSave", "CongregacaoRemove", "CongregacaoBlock"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/staff/congregacao/congregacao_list.html?v=" + Versions.html,
                            controller: 'CongregacaoCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.staff.congregacoes.adicionar', {
                    url: "/adicionar",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        back: '',
                        perms: [ "CongregacaoSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/staff/congregacao/congregacao_form.html?v=" + Versions.html,
                            controller: 'CongregacaoCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.staff.congregacoes.editar', {
                    url: "/editar?id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        back: '',
                        perms: [ "CongregacaoSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/staff/congregacao/congregacao_form.html?v=" + Versions.html,
                            controller: 'CongregacaoCtrl'
                        }
                    }
                });
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.staff.pontosdepregacao
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.staff.pontosdepregacao', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.staff.pontosdepregacao', {
                    url: "/pontosdepregacao",
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
                                'js/controllers/staff/pontodepregacao/pontodepregacao_ctrl.js',
                                'js/services/pontodepregacao_srv.js',
                                'js/services/igreja_srv.js',
                                'js/services/presbiterio_srv.js',
                                'js/services/sinodo_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.staff.pontosdepregacao.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: '',
                        perms: [ "Ponto", "PontoSave", "PontoRemove", "PontoBlock"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/staff/pontosdepregacao/pontodepregacao_list.html?v=" + Versions.html,
                            controller: 'PontoDePregacaoCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.staff.pontosdepregacao.adicionar', {
                    url: "/adicionar",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        back: '',
                        perms: [ "PontoSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/staff/pontosdepregacao/pontodepregacao_form.html?v=" + Versions.html,
                            controller: 'PontoDePregacaoCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.staff.pontosdepregacao.editar', {
                    url: "/editar?id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        back: '',
                        perms: [ "PontoSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/staff/pontosdepregacao/pontodepregacao_form.html?v=" + Versions.html,
                            controller: 'PontoDePregacaoCtrl'
                        }
                    }
                });
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.staff.nacionais
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.staff.nacionais', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.staff.nacionais', {
                    url: "/nacionais",
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
                                'js/controllers/staff/nacional/nacional_ctrl.js',
                                'js/services/nacional_srv.js',
                                'js/services/supremo_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.staff.nacionais.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: '',
                        perms: [ "Nacional", "NacionalSave", "NacionalRemove", "NacionalBlock"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/staff/nacional/nacional_list.html?v=" + Versions.html,
                            controller: 'NacionalCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.staff.nacionais.adicionar', {
                    url: "/adicionar",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        back: '',
                        perms: [ "NacionalSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/staff/nacional/nacional_form.html?v=" + Versions.html,
                            controller: 'NacionalCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.staff.nacionais.editar', {
                    url: "/editar?id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        back: '',
                        perms: [ "NacionalSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/staff/nacional/nacional_form.html?v=" + Versions.html,
                            controller: 'NacionalCtrl'
                        }
                    }
                });
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.staff.sinodais
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.staff.sinodais', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.staff.sinodais', {
                    url: "/sinodais",
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
                                'js/controllers/staff/sinodal/sinodal_ctrl.js',
                                'js/controllers/staff/sinodal/sinodal_admin_ctrl.js',
                                'js/services/sinodal_srv.js',
                                'js/services/nacional_srv.js',
                                'js/services/sinodo_srv.js',
                                'js/services/sysconfig_srv.js',
                                'js/services/perfil_srv.js',
                                'js/services/admin_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.staff.sinodais.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: '',
                        perms: [ "Sinodal", "SinodalSave", "SinodalRemove", "SinodalBlock"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/staff/sinodal/sinodal_list.html?v=" + Versions.html,
                            controller: 'SinodalCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.staff.sinodais.adicionar', {
                    url: "/adicionar",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        back: '',
                        perms: [ "SinodalSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/staff/sinodal/sinodal_form.html?v=" + Versions.html,
                            controller: 'SinodalCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.staff.sinodais.editar', {
                    url: "/editar?id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        back: '',
                        perms: [ "SinodalSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/staff/sinodal/sinodal_form.html?v=" + Versions.html,
                            controller: 'SinodalCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.staff.sinodais.admin', {
                    url: "/admin?id",
                    params: {
                        data: null,
                        title: 'Configurando',
                        search: false,
                        back: '',
                        perms: [ "SinodalAdmin" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/staff/sinodal/sinodal_admin_form.html?v=" + Versions.html,
                            controller: 'SinodalAdminCtrl'
                        }
                    }
                })
                
                ;
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.staff.federacoes
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.staff.federacoes', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.staff.federacoes', {
                    url: "/federacoes",
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
                                'js/controllers/staff/federacao/federacao_ctrl.js',
                                'js/controllers/staff/federacao/federacao_admin_ctrl.js',
                                'js/services/federacao_srv.js',
                                'js/services/sinodal_srv.js',
                                'js/services/presbiterio_srv.js',
                                'js/services/sinodo_srv.js',
                                'js/services/sysconfig_srv.js',
                                'js/services/perfil_srv.js',
                                'js/services/admin_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.staff.federacoes.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: '',
                        perms: [ "Federacao", "FederacaoSave", "FederacaoRemove", "FederacaoBlock", "FederacaoAdmin"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/staff/federacao/federacao_list.html?v=" + Versions.html,
                            controller: 'FederacaoCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.staff.federacoes.adicionar', {
                    url: "/adicionar",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        back: '',
                        perms: [ "FederacaoSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/staff/federacao/federacao_form.html?v=" + Versions.html,
                            controller: 'FederacaoCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.staff.federacoes.editar', {
                    url: "/editar?id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        back: '',
                        perms: [ "FederacaoSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/staff/federacao/federacao_form.html?v=" + Versions.html,
                            controller: 'FederacaoCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.staff.federacoes.admin', {
                    url: "/admin?id",
                    params: {
                        data: null,
                        title: 'Configurando',
                        search: false,
                        back: '',
                        perms: [ "FederacaoAdmin" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/staff/federacao/federacao_admin_form.html?v=" + Versions.html,
                            controller: 'FederacaoAdminCtrl'
                        }
                    }
                })
                
                ;
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.staff.cargos
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.staff.cargos', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.staff.cargos', {
                    url: "/cargos",
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
                                'js/controllers/staff/cargo/cargo_ctrl.js',
                                'js/services/cargo_srv.js',
                                'js/services/perfil_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.staff.cargos.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: '',
                        perms: [ "Cargo", "CargoSave", "CargoRemove"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/staff/cargo/cargo_list.html?v=" + Versions.html,
                            controller: 'CargoCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.staff.cargos.adicionar', {
                    url: "/adicionar",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        back: '',
                        perms: [ "CargoSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/staff/cargo/cargo_form.html?v=" + Versions.html,
                            controller: 'CargoCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.staff.cargos.editar', {
                    url: "/editar?id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        back: '',
                        perms: [ "CargoSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/staff/cargo/cargo_form.html?v=" + Versions.html,
                            controller: 'CargoCtrl'
                        }
                    }
                });
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.staff.tagsagenda
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.staff.tagsagenda', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.staff.tagsagenda', {
                    url: "/tagsagenda",
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
                                'js/controllers/staff/tagsagenda/tagsagenda_ctrl.js',
                                'js/services/tagsagenda_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.staff.tagsagenda.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: '',
                        perms: [ "TagAgenda", "TagAgendaSave", "TagAgendaRemove", "TagAgendaBlock"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/staff/tagsagenda/tagsagenda_list.html?v=" + Versions.html,
                            controller: 'TagAgendaCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.staff.tagsagenda.adicionar', {
                    url: "/adicionar",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        back: '',
                        perms: [ "TagAgendaSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/staff/tagsagenda/tagsagenda_form.html?v=" + Versions.html,
                            controller: 'TagAgendaCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.staff.tagsagenda.editar', {
                    url: "/editar?id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        back: '',
                        perms: [ "TagAgendaSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/staff/tagsagenda/tagsagenda_form.html?v=" + Versions.html,
                            controller: 'TagAgendaCtrl'
                        }
                    }
                });
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.staff.clientes
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.staff.clientes', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.staff.clientes', {
                    url: "/clientes",
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
                                'js/controllers/staff/cliente/cliente_ctrl.js',
                                'js/services/cliente_srv.js',
                                'js/services/templo_srv.js',
                                'js/services/presbiterio_srv.js',
                                'js/services/sinodo_srv.js',
                                'js/services/sysconfig_srv.js',
                                'js/services/perfil_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.staff.clientes.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: '',
                        perms: [ "Cliente", "ClienteSave", "ClienteRemove", "ClienteBlock"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/staff/cliente/cliente_list.html?v=" + Versions.html,
                            controller: 'ClienteCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.staff.clientes.adicionar', {
                    url: "/adicionar",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        back: '',
                        perms: [ "ClienteSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/staff/cliente/cliente_form.html?v=" + Versions.html,
                            controller: 'ClienteCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.staff.clientes.editar', {
                    url: "/editar?id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        back: '',
                        perms: [ "ClienteSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/staff/cliente/cliente_form.html?v=" + Versions.html,
                            controller: 'ClienteCtrl'
                        }
                    }
                });
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.staff.profissoes
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.staff.profissoes', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.staff.profissoes', {
                    url: "/profissoes",
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
                                'js/controllers/staff/profissoes/profissao_ctrl.js',
                                'js/services/profissao_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.staff.profissoes.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: '',
                        perms: [ "Profissao", "ProfissaoSave", "ProfissaoRemove"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/staff/profissoes/profissao_list.html?v=" + Versions.html,
                            controller: 'ProfissaoCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.staff.profissoes.adicionar', {
                    url: "/adicionar",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        back: '',
                        perms: [ "ProfissaoSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/staff/profissoes/profissao_form.html?v=" + Versions.html,
                            controller: 'ProfissaoCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.staff.profissoes.editar', {
                    url: "/editar?id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        back: '',
                        perms: [ "ProfissaoSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/staff/profissoes/profissao_form.html?v=" + Versions.html,
                            controller: 'ProfissaoCtrl'
                        }
                    }
                });
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.staff.doacoes
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.staff.doacoes', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.staff.doacoes', {
                    url: "/doacoes",
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
                                'js/controllers/staff/doacoes/doacao_ctrl.js',
                                'js/services/doacao_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.staff.doacoes.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: '',
                        perms: [ "Doacao", "DoacaoSave", "DoacaoRemove"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/staff/doacoes/doacao_list.html?v=" + Versions.html,
                            controller: 'DoacaoCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.staff.doacoes.adicionar', {
                    url: "/adicionar",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        back: '',
                        perms: [ "DoacaoSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/staff/doacoes/doacao_form.html?v=" + Versions.html,
                            controller: 'DoacaoCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.staff.doacoes.editar', {
                    url: "/editar?id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        back: '',
                        perms: [ "DoacaoSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/staff/doacoes/doacao_form.html?v=" + Versions.html,
                            controller: 'DoacaoCtrl'
                        }
                    }
                });
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.staff.necessidadesespeciais
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.staff.necessidadesespeciais', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.staff.necessidadesespeciais', {
                    url: "/necessidadesespeciais",
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
                                'js/controllers/staff/necessidadesespeciais/necessidade_ctrl.js',
                                'js/services/necessidade_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.staff.necessidadesespeciais.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: '',
                        perms: [ "Necessidade", "NecessidadeSave", "NecessidadeRemove"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/staff/necessidadesespeciais/necessidade_list.html?v=" + Versions.html,
                            controller: 'NecessidadeCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.staff.necessidadesespeciais.adicionar', {
                    url: "/adicionar",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        back: '',
                        perms: [ "NecessidadeSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/staff/necessidadesespeciais/necessidade_form.html?v=" + Versions.html,
                            controller: 'NecessidadeCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.staff.necessidadesespeciais.editar', {
                    url: "/editar?id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        back: '',
                        perms: [ "NecessidadeSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/staff/necessidadesespeciais/necessidade_form.html?v=" + Versions.html,
                            controller: 'NecessidadeCtrl'
                        }
                    }
                });
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.staff.motivosrecusa
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.staff.motivosrecusa', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.staff.motivosrecusa', {
                    url: "/motivosrecusa",
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
                                'js/controllers/staff/motivosrecusa/motivo_ctrl.js',
                                'js/services/motivorecusa_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.staff.motivosrecusa.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: '',
                        perms: [ "MotivoRecusa", "MotivoRecusaSave", "MotivoRecusaRemove"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/staff/motivosrecusa/motivo_list.html?v=" + Versions.html,
                            controller: 'MotivoRecusaCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.staff.motivosrecusa.adicionar', {
                    url: "/adicionar",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        back: '',
                        perms: [ "MotivoRecusaSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/staff/motivosrecusa/motivo_form.html?v=" + Versions.html,
                            controller: 'MotivoRecusaCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.staff.motivosrecusa.editar', {
                    url: "/editar?id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        back: '',
                        perms: [ "MotivoRecusaSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/staff/motivosrecusa/motivo_form.html?v=" + Versions.html,
                            controller: 'MotivoRecusaCtrl'
                        }
                    }
                });
                    
        };
})();
