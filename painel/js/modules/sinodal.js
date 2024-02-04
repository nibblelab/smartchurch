
/*******************************************************************************
 * SmartChurchPanel.sinodal
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.sinodal', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                
                .state('SmartChurchPanel.sinodal', {
                    url: "/sinodal",
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
 * SmartChurchPanel.sinodal.igrejas
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.sinodal.igrejas', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.sinodal.igrejas', {
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
                                'js/controllers/sinodal/igrejas/igreja_ctrl.js',
                                'js/services/igreja_srv.js',
                                'js/services/federacao_srv.js',
                                'js/services/sociedade_srv.js',
                                'js/services/sinodal_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.sinodal.igrejas.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: '',
                        perms: [ "IgrejaSinodal"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/sinodal/igrejas/igreja_list.html?v=" + Versions.html,
                            controller: 'IgrejadaSinodalCtrl'
                        }
                    }
                })
                
                ;
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.sinodal.congregacoes
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.sinodal.congregacoes', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.sinodal.congregacoes', {
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
                                'js/controllers/sinodal/congregacoes/congregacao_ctrl.js',
                                'js/services/congregacao_srv.js',
                                'js/services/igreja_srv.js',
                                'js/services/federacao_srv.js',
                                'js/services/sociedade_srv.js',
                                'js/services/sinodal_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.sinodal.congregacoes.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: '',
                        perms: [ "CongregacaoSinodal"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/sinodal/congregacoes/congregacao_list.html?v=" + Versions.html,
                            controller: 'CongregacaodaSinodalCtrl'
                        }
                    }
                })
                
                ;
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.sinodal.pontos
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.sinodal.pontos', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.sinodal.pontos', {
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
                                'js/controllers/sinodal/pontos/ponto_ctrl.js',
                                'js/services/pontodepregacao_srv.js',
                                'js/services/igreja_srv.js',
                                'js/services/federacao_srv.js',
                                'js/services/sociedade_srv.js',
                                'js/services/sinodal_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.sinodal.pontos.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: '',
                        perms: [ "PontoSinodal"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/sinodal/pontos/ponto_list.html?v=" + Versions.html,
                            controller: 'PontodaSinodalCtrl'
                        }
                    }
                })
                
                ;
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.sinodal.federacoes
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.sinodal.federacoes', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.sinodal.federacoes', {
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
                                'js/controllers/sinodal/federacao/federacao_ctrl.js',
                                'js/services/federacao_srv.js',
                                'js/services/sociedade_srv.js',
                                'js/services/sinodal_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.sinodal.federacoes.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: '',
                        perms: [ "FederacaoSinodal"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/sinodal/federacao/federacao_list.html?v=" + Versions.html,
                            controller: 'FederacaodaSinodalCtrl'
                        }
                    }
                })
                
                ;
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.sinodal.sociedades
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.sinodal.sociedades', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.sinodal.sociedades', {
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
                                'js/controllers/sinodal/sociedades/sociedade_ctrl.js',
                                'js/services/sociedade_srv.js',
                                'js/services/templo_srv.js',
                                'js/services/federacao_srv.js',
                                'js/services/socio_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.sinodal.sociedades.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: '',
                        perms: [ "SociedadeSinodal"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/sinodal/sociedades/sociedade_list.html?v=" + Versions.html,
                            controller: 'SociedadedaSinodalCtrl'
                        }
                    }
                })
                
                ;
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.sinodal.pessoas
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.sinodal.pessoas', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.sinodal.pessoas', {
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
                                'js/controllers/sinodal/pessoas/pessoa_ctrl.js',
                                'js/services/socio_srv.js',
                                'js/services/sociedade_srv.js',
                                'js/services/templo_srv.js',
                                'js/services/federacao_srv.js',
                                'js/services/sinodal_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.sinodal.pessoas.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: '',
                        perms: [ "PessoaSinodal"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/sinodal/pessoas/pessoa_list.html?v=" + Versions.html,
                            controller: 'PessoadaSinodalCtrl'
                        }
                    }
                })
                
                ;
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.sinodal.diretorias
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.sinodal.diretorias', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.sinodal.diretorias', {
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
                                'js/controllers/sinodal/diretorias/diretoria_ctrl.js',
                                'js/services/diretoria_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.sinodal.diretorias.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: '',
                        perms: [ "DiretoriaSinodal", "DiretoriaSinodalSave", "DiretoriaSinodalRemove", "DiretoriaSinodalBlock", "DiretoriaSinodalOficiais"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/common/diretorias/diretoria_list.html?v=" + Versions.html,
                            controller: 'DiretoriaSinodalCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.sinodal.diretorias.adicionar', {
                    url: "/adicionar",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        back: '',
                        perms: [ "DiretoriaSinodalSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/common/diretorias/diretoria_form.html?v=" + Versions.html,
                            controller: 'DiretoriaSinodalCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.sinodal.diretorias.editar', {
                    url: "/editar?id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        back: '',
                        perms: [ "DiretoriaSinodalSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/common/diretorias/diretoria_form.html?v=" + Versions.html,
                            controller: 'DiretoriaSinodalCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.sinodal.diretorias.oficiais', {
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
                                'js/controllers/sinodal/diretorias/oficial_ctrl.js',
                                'js/services/socio_srv.js',
                                'js/services/cargo_srv.js',
                                'js/services/oficial_srv.js'
                            ], { cache: false});
                        }]
                    }
                })
                
                .state('SmartChurchPanel.sinodal.diretorias.oficiais.buscar', {
                    url: "/buscar?diretoria",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: 'SmartChurchPanel.sinodal.diretorias.buscar',
                        perms: [ "DiretoriaSinodalOficiais" ]
                    },
                    cache: false,
                    views: {
                        'subInternalContent': {
                            templateUrl: "templates/common/diretorias/oficial_list.html?v=" + Versions.html,
                            controller: 'OficialSinodalCtrl'
                        }
                    }
                })
                
                .state('SmartChurchPanel.sinodal.diretorias.oficiais.adicionar', {
                    url: "/adicionar?diretoria",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        back: '',
                        perms: [ "DiretoriaSinodalOficiais" ]
                    },
                    cache: false,
                    views: {
                        'subInternalContent': {
                            templateUrl: "templates/common/diretorias/oficial_form.html?v=" + Versions.html,
                            controller: 'OficialSinodalCtrl'
                        }
                    }
                })
                
                .state('SmartChurchPanel.sinodal.diretorias.oficiais.editar', {
                    url: "/editar?diretoria&id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        back: '',
                        perms: [ "DiretoriaSinodalOficiais" ]
                    },
                    cache: false,
                    views: {
                        'subInternalContent': {
                            templateUrl: "templates/common/diretorias/oficial_form.html?v=" + Versions.html,
                            controller: 'OficialSinodalCtrl'
                        }
                    }
                })
                
                ;
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.sinodal.secretarias
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.sinodal.secretarias', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.sinodal.secretarias', {
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
                                'js/controllers/sinodal/secretarias/secretaria_ctrl.js',
                                'js/services/secretaria_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.sinodal.secretarias.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: '',
                        perms: [ "SecretariaSinodal", "SecretariaSinodalSave", "SecretariaSinodalRemove", "SecretariaSinodalBlock", 
                                    "SecretariaSinodalManage", "SecretariaSinodalPessoa"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/common/secretaria/secretaria_list.html?v=" + Versions.html,
                            controller: 'SecretariaDaSinodalCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.sinodal.secretarias.adicionar', {
                    url: "/adicionar",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        back: '',
                        perms: [ "SecretariaSinodalSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/common/secretaria/secretaria_form.html?v=" + Versions.html,
                            controller: 'SecretariaDaSinodalCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.sinodal.secretarias.editar', {
                    url: "/editar?id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        back: '',
                        perms: [ "SecretariaSinodalSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/common/secretaria/secretaria_form.html?v=" + Versions.html,
                            controller: 'SecretariaDaSinodalCtrl'
                        }
                    }
                })
                
                .state('SmartChurchPanel.sinodal.secretarias.secretarios', {
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
                                'js/controllers/sinodal/secretarias/secretario_ctrl.js',
                                'js/services/socio_srv.js',
                                'js/services/perfil_srv.js',
                                'js/services/sysconfig_srv.js',
                                'js/services/secretario_srv.js',
                                'js/services/secretaria_srv.js'
                            ], { cache: false});
                        }]
                    }
                })
                
                .state('SmartChurchPanel.sinodal.secretarias.secretarios.buscar', {
                    url: "/buscar?secretaria",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: 'SmartChurchPanel.sinodal.secretarias.buscar',
                        perms: [ "SecretariaSinodalPessoa" ]
                    },
                    cache: false,
                    views: {
                        'subInternalContent': {
                            templateUrl: "templates/common/secretario/secretario_list.html?v=" + Versions.html,
                            controller: 'SecretarioDaSinodalCtrl'
                        }
                    }
                })
                
                .state('SmartChurchPanel.sinodal.secretarias.secretarios.adicionar', {
                    url: "/adicionar?secretaria",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        back: '',
                        perms: [ "SecretariaSinodalPessoa" ]
                    },
                    cache: false,
                    views: {
                        'subInternalContent': {
                            templateUrl: "templates/common/secretario/secretario_form.html?v=" + Versions.html,
                            controller: 'SecretarioDaSinodalCtrl'
                        }
                    }
                })
                
                .state('SmartChurchPanel.sinodal.secretarias.secretarios.editar', {
                    url: "/editar?secretaria&id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        back: '',
                        perms: [ "SecretariaSinodalPessoa" ]
                    },
                    cache: false,
                    views: {
                        'subInternalContent': {
                            templateUrl: "templates/common/secretario/secretario_form.html?v=" + Versions.html,
                            controller: 'SecretarioDaSinodalCtrl'
                        }
                    }
                })
            ;
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.sinodal.agenda
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.sinodal.agenda', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.sinodal.agenda', {
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
                                'js/controllers/sinodal/agenda/agenda_ctrl.js',
                                'js/services/tagsagenda_srv.js',
                                'js/services/agenda_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.sinodal.agenda.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: '',
                        perms: [ "AgendaSinodal", "AgendaSinodalSave", "AgendaSinodalRemove", "AgendaSinodalBlock"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/sinodal/agenda/agenda_list.html?v=" + Versions.html,
                            controller: 'AgendaDaSinodalCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.sinodal.agenda.adicionar', {
                    url: "/adicionar",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        back: '',
                        perms: [ "AgendaSinodalSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/sinodal/agenda/agenda_form.html?v=" + Versions.html,
                            controller: 'AgendaDaSinodalCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.sinodal.agenda.editar', {
                    url: "/editar?id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        back: '',
                        perms: [ "AgendaSinodalSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/sinodal/agenda/agenda_form.html?v=" + Versions.html,
                            controller: 'AgendaDaSinodalCtrl'
                        }
                    }
                });
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.sinodal.eventos
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.sinodal.eventos', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.sinodal.eventos', {
                    url: "/eventos",
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
                                'js/controllers/sinodal/eventos/evento_ctrl.js',
                                'js/factories/evento_fac.js',
                                'js/services/agenda_srv.js',
                                'js/services/sysconfig_srv.js',
                                'js/services/evento_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.sinodal.eventos.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: '',
                        perms: [ "EventoSinodal", "EventoSinodalSave", "EventoSinodalRemove", "EventoSinodalBlock"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/common/eventos/evento_list.html?v=" + Versions.html,
                            controller: 'EventoDaSinodalCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.sinodal.eventos.adicionar', {
                    url: "/adicionar",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        back: '',
                        perms: [ "EventoSinodalSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/common/eventos/evento_form.html?v=" + Versions.html,
                            controller: 'EventoDaSinodalCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.sinodal.eventos.editar', {
                    url: "/editar?id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        back: '',
                        perms: [ "EventoSinodalSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/common/eventos/evento_form.html?v=" + Versions.html,
                            controller: 'EventoDaSinodalCtrl'
                        }
                    }
                });
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.sinodal.eleicoes
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.sinodal.eleicoes', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.sinodal.eleicoes', {
                    url: "/eleicoes",
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
                                'js/controllers/sinodal/eleicoes/eleicao_ctrl.js',
                                'js/services/agenda_srv.js',
                                'js/services/sysconfig_srv.js',
                                'js/services/evento_srv.js',
                                'js/services/eleicao_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.sinodal.eleicoes.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: '',
                        perms: [ "EleicaoSinodal", "EleicaoSinodalSave", "EleicaoSinodalRemove", "EleicaoSinodalBlock"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/common/eleicoes/eleicao_list.html?v=" + Versions.html,
                            controller: 'EleicaoDaSinodalCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.sinodal.eleicoes.adicionar', {
                    url: "/adicionar",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        back: '',
                        perms: [ "EleicaoSinodalSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/common/eleicoes/eleicao_form.html?v=" + Versions.html,
                            controller: 'EleicaoDaSinodalCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.sinodal.eleicoes.editar', {
                    url: "/editar?id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        back: '',
                        perms: [ "EleicaoSinodalSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/common/eleicoes/eleicao_form.html?v=" + Versions.html,
                            controller: 'EleicaoDaSinodalCtrl'
                        }
                    }
                });
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.sinodal.token
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.sinodal.token', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.sinodal.token', {
                    url: "/token",
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
                                'js/controllers/sinodal/token/token_ctrl.js',
                                'js/services/integracao_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.sinodal.token.gerar', {
                    url: "/gerar",
                    params: {
                        title: 'Gerando',
                        perms: [ "IntegracaoSinodal"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/common/token/token_form.html?v=" + Versions.html,
                            controller: 'TokenSinodalCtrl'
                        }
                    }
                })

                ;
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.sinodal.dados
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.sinodal.dados', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.sinodal.dados', {
                    url: "/dados",
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
                                'js/controllers/sinodal/dados/dados_ctrl.js',
                                'js/services/sinodo_srv.js',
                                'js/services/nacional_srv.js',
                                'js/services/sinodal_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.sinodal.dados.editar', {
                    url: "/editar",
                    params: {
                        title: 'Dados da Igreja',
                        perms: [ "InstanciaSave"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/sinodal/dados/dados_form.html?v=" + Versions.html,
                            controller: 'DadosSinodalCtrl'
                        }
                    }
                })

                ;
                    
        };
})();