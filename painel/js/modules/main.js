

/*******************************************************************************
 * SmartChurchPanel.main.login
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.main.login', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('login', {
                    url: "/login",
                    cache: false,
                    templateUrl: "templates/main/login.html?v=" + Versions.html,
                    controller: 'LoginCtrl',
                    resolve: {
                        loadMyCtrl: ['$ocLazyLoad', function($ocLazyLoad) {
                            return $ocLazyLoad.load([
                                'js/controllers/main/login/login_ctrl.js',
                                'js/services/public_srv.js'
                            ], { cache: false});
                        }]
                    }
                });
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.main.esquecisenha
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.main.esquecisenha', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('esquecisenha', {
                    url: "/esquecisenha",
                    cache: false,
                    templateUrl: "templates/main/esquecisenha.html?v=" + Versions.html,
                    controller: 'EsqueciSenhaCtrl',
                    resolve: {
                        loadMyCtrl: ['$ocLazyLoad', function($ocLazyLoad) {
                            return $ocLazyLoad.load([
                                'js/controllers/main/esquecisenha/esquecisenha_ctrl.js',
                                'js/services/public_srv.js'
                            ], { cache: false});
                        }]
                    }
                });
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.main.alterarsenha
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.main.alterarsenha', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('alterarsenha', {
                    url: "/alterarsenha",
                    cache: false,
                    templateUrl: "templates/main/alterarsenha.html?v=" + Versions.html,
                    controller: 'AlterarSenhaCtrl',
                    resolve: {
                        loadMyCtrl: ['$ocLazyLoad', function($ocLazyLoad) {
                            return $ocLazyLoad.load([
                                'js/controllers/main/alterarsenha/alterarsenha_ctrl.js',
                                'js/services/public_srv.js'
                            ], { cache: false});
                        }]
                    }
                });
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.main.registrar
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.main.registrar', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('registrar', {
                    url: "/registrar",
                    cache: false,
                    templateUrl: "templates/main/registrar.html?v=" + Versions.html,
                    controller: 'RegistrarCtrl',
                    resolve: {
                        loadMyCtrl: ['$ocLazyLoad', function($ocLazyLoad) {
                            return $ocLazyLoad.load([
                                'js/controllers/main/registrar/registrar_ctrl.js',
                                'js/services/public_srv.js'
                            ], { cache: false});
                        }]
                    }
                });
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.main.termos
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.main.termos', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('termos', {
                    url: "/termos",
                    cache: false,
                    templateUrl: "templates/main/termos.html?v=" + Versions.html,
                    controller: 'TermosCtrl',
                    resolve: {
                        loadMyCtrl: ['$ocLazyLoad', function($ocLazyLoad) {
                            return $ocLazyLoad.load([
                                'js/controllers/main/termos/termos_ctrl.js',
                                'js/services/public_srv.js'
                            ], { cache: false});
                        }]
                    }
                });
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.main.validacao
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.main.validacao', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('validacao', {
                    url: "/validacao",
                    cache: false,
                    templateUrl: "templates/main/validacao.html?v=" + Versions.html,
                    controller: 'ValidacaoCtrl',
                    resolve: {
                        loadMyCtrl: ['$ocLazyLoad', function($ocLazyLoad) {
                            return $ocLazyLoad.load([
                                'js/controllers/main/validacao/validacao_ctrl.js',
                                'js/services/credencial_srv.js'
                            ], { cache: false});
                        }]
                    }
                });
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.main.painel
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.main.painel', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel', {
                    url: "/SmartChurchPanel",
                    abstract: true,
                    cache: false,
                    templateUrl: "templates/main/smartchurchpanel.html?v=" + Versions.html,
                    controller: 'SmartChurchPanelCtrl',
                    resolve: {
                        loadMyCtrl: ['$ocLazyLoad', function($ocLazyLoad) {
                            return $ocLazyLoad.load([
                                'js/controllers/main/smartchurchpanel/smartchurchpanel_ctrl.js',
                                'js/services/user_srv.js',
                                'js/services/data_srv.js'
                            ], { cache: false});
                        }]
                    }
                });
                    
        };
})();


/*******************************************************************************
 * SmartChurchPanel.main.sempermissao
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.main.sempermissao', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.sempermissao', {
                    url: "/sempermissao",
                    params: {
                        title: 'Sem permissão :('
                    },
                    cache: false,
                    views: {
                        'menuContent': {
                            templateUrl: "templates/main/sempermissao.html?v=" + Versions.html,
                            controller: 'SemPermissaoCtrl'
                        }
                    },
                    resolve: {
                        loadMyCtrl: ['$ocLazyLoad', function($ocLazyLoad) {
                            return $ocLazyLoad.load([
                                'js/controllers/main/sempermissao/sempermissao_ctrl.js'
                            ], { cache: false});
                        }]
                    }
                });
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.main.home
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.main.home', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.painel', {
                    url: "/painel",
                    cache: false,
                    views: {
                        'panelContent': {
                            templateUrl: "templates/main/painel.html?v=" + Versions.html,
                            controller: 'HomeCtrl'
                        }
                    },
                    resolve: {
                        loadMyCtrl: ['$ocLazyLoad', function($ocLazyLoad) {
                            return $ocLazyLoad.load([
                                'js/controllers/main/painel/painel_ctrl.js',
                                'js/services/home_srv.js'
                            ], { cache: false});
                        }]
                    }
                });
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.main.me
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.main.me', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.me', {
                    url: "/me",
                    cache: false,
                    params: {
                        data: null,
                        title: 'Meu Usuário',
                        search: false,
                        back: '',
                        perms: [ "Me", "MeSave"]
                    },
                    views: {
                        'panelContent': {
                            templateUrl: "templates/main/me/me_form.html?v=" + Versions.html,
                            controller: 'MeCtrl'
                        }
                    },
                    resolve: {
                        loadMyCtrl: ['$ocLazyLoad', function($ocLazyLoad) {
                            return $ocLazyLoad.load([
                                'js/controllers/main/me/me_ctrl.js',
                                'js/factories/familia_fac.js',
                                'js/services/pessoa_srv.js',
                                'js/services/profissao_srv.js',
                                'js/services/doacao_srv.js',
                                'js/services/necessidade_srv.js',
                                'js/services/perfil_srv.js',
                                'js/services/sysconfig_srv.js',
                                'js/services/private_srv.js'
                            ], { cache: false});
                        }]
                    }
                });
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.main.membresia
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.main.membresia', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.membresia', {
                    url: "/membresia",
                    cache: false,
                    params: {
                        data: null,
                        title: 'Membresia',
                        search: false,
                        back: '',
                        perms: [ "Me", "MeSave"]
                    },
                    views: {
                        'panelContent': {
                            templateUrl: "templates/main/me/membresia_form.html?v=" + Versions.html,
                            controller: 'MembresiaCtrl'
                        }
                    },
                    resolve: {
                        loadMyCtrl: ['$ocLazyLoad', function($ocLazyLoad) {
                            return $ocLazyLoad.load([
                                'js/controllers/main/me/membresia_ctrl.js',
                                'js/services/membro_srv.js',
                                'js/services/templo_srv.js',
                                'js/services/presbiterio_srv.js',
                                'js/services/sinodo_srv.js'
                            ], { cache: false});
                        }]
                    }
                });
                    
        };
})();
