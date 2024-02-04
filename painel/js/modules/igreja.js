
/*******************************************************************************
 * SmartChurchPanel.igreja
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.igreja', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                
                .state('SmartChurchPanel.igreja', {
                    url: "/igreja",
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
 * SmartChurchPanel.igreja.dados
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.igreja.dados', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.igreja.dados', {
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
                                'js/controllers/igreja/dados/dados_ctrl.js',
                                'js/services/sinodo_srv.js',
                                'js/services/presbiterio_srv.js',
                                'js/services/igreja_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.igreja.dados.editar', {
                    url: "/editar",
                    params: {
                        title: 'Dados da Igreja',
                        perms: [ "InstanciaSave"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/igreja/dados/dados_form.html?v=" + Versions.html,
                            controller: 'DadosIgrejaCtrl'
                        }
                    }
                })

                ;
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.igreja.congregacoes
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.igreja.congregacoes', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                
                .state('SmartChurchPanel.igreja.congregacoes', {
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
                                'js/controllers/igreja/congregacoes/congregacao_ctrl.js',
                                'js/services/congregacaoigreja_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.igreja.congregacoes.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: '',
                        perms: [ "CongregacaoIgreja", "CongregacaoIgrejaSave", "CongregacaoIgrejaRemove", "CongregacaoIgrejaBlock"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/igreja/congregacoes/congregacoes_list.html?v=" + Versions.html,
                            controller: 'CongregacaoIgrejaCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.igreja.congregacoes.adicionar', {
                    url: "/adicionar",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        back: '',
                        perms: [ "CongregacaoIgrejaSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/igreja/congregacoes/congregacoes_form.html?v=" + Versions.html,
                            controller: 'CongregacaoIgrejaCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.igreja.congregacoes.editar', {
                    url: "/editar?id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        back: '',
                        perms: [ "CongregacaoIgrejaSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/igreja/congregacoes/congregacoes_form.html?v=" + Versions.html,
                            controller: 'CongregacaoIgrejaCtrl'
                        }
                    }
                });
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.igreja.pontosdepregacao
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.igreja.pontosdepregacao', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.igreja.pontosdepregacao', {
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
                                'js/controllers/igreja/pontosdepregacao/pontodepregacao_ctrl.js',
                                'js/services/pontodepregacaoigreja_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.igreja.pontosdepregacao.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: '',
                        perms: [ "PontoIgreja", "PontoIgrejaSave", "PontoIgrejaRemove", "PontoIgrejaBlock"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/igreja/pontosdepregacao/pontodepregacao_list.html?v=" + Versions.html,
                            controller: 'PontoDePregacaoIgrejaCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.igreja.pontosdepregacao.adicionar', {
                    url: "/adicionar",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        back: '',
                        perms: [ "PontoIgrejaSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/igreja/pontosdepregacao/pontodepregacao_form.html?v=" + Versions.html,
                            controller: 'PontoDePregacaoIgrejaCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.igreja.pontosdepregacao.editar', {
                    url: "/editar?id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        back: '',
                        perms: [ "PontoIgrejaSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/igreja/pontosdepregacao/pontodepregacao_form.html?v=" + Versions.html,
                            controller: 'PontoDePregacaoIgrejaCtrl'
                        }
                    }
                });
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.igreja.membros
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.igreja.membros', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.igreja.membros', {
                    url: "/membros",
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
                                'js/controllers/igreja/membros/membro_ctrl.js',                                
                                'js/factories/familia_fac.js',
                                'js/services/membro_srv.js',
                                'js/services/pessoa_srv.js',
                                'js/services/profissao_srv.js',
                                'js/services/doacao_srv.js',
                                'js/services/necessidade_srv.js',
                                'js/services/sysconfig_srv.js',
                                'js/services/perfil_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.igreja.membros.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: '',
                        perms: [ "MembroIgreja", "MembroIgrejaSave", "MembroIgrejaRemove", "MembroIgrejaBlock", "MembroIgrejaEspeciais"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/igreja/membros/membro_list.html?v=" + Versions.html,
                            controller: 'MembroDaIgrejaCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.igreja.membros.adicionar', {
                    url: "/adicionar",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        back: '',
                        perms: [ "MembroIgrejaSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/igreja/membros/membro_form.html?v=" + Versions.html,
                            controller: 'MembroDaIgrejaCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.igreja.membros.editar', {
                    url: "/editar?id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        back: '',
                        perms: [ "MembroIgrejaSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/igreja/membros/membro_form.html?v=" + Versions.html,
                            controller: 'MembroDaIgrejaCtrl'
                        }
                    }
                });
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.igreja.secretarias
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.igreja.secretarias', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.igreja.secretarias', {
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
                                'js/controllers/igreja/secretarias/secretaria_ctrl.js',
                                'js/services/secretaria_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.igreja.secretarias.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: '',
                        perms: [ "SecretariaIgreja", "SecretariaIgrejaSave", "SecretariaIgrejaRemove", "SecretariaIgrejaBlock", "SecretariaIgrejaManage"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/common/secretaria/secretaria_list.html?v=" + Versions.html,
                            controller: 'SecretariaDaIgrejaCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.igreja.secretarias.adicionar', {
                    url: "/adicionar",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        back: '',
                        perms: [ "SecretariaIgrejaSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/common/secretaria/secretaria_form.html?v=" + Versions.html,
                            controller: 'SecretariaDaIgrejaCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.igreja.secretarias.editar', {
                    url: "/editar?id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        back: '',
                        perms: [ "SecretariaIgrejaSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/common/secretaria/secretaria_form.html?v=" + Versions.html,
                            controller: 'SecretariaDaIgrejaCtrl'
                        }
                    }
                })
                
                .state('SmartChurchPanel.igreja.secretarias.secretarios', {
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
                                'js/controllers/igreja/secretarias/secretario_ctrl.js',
                                'js/services/membro_srv.js',
                                'js/services/perfil_srv.js',
                                'js/services/sysconfig_srv.js',
                                'js/services/secretario_srv.js',
                                'js/services/secretaria_srv.js'
                            ], { cache: false});
                        }]
                    }
                })
                
                .state('SmartChurchPanel.igreja.secretarias.secretarios.buscar', {
                    url: "/buscar?secretaria",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: 'SmartChurchPanel.igreja.secretarias.buscar',
                        perms: [ "SecretariaIgrejaPessoa" ]
                    },
                    cache: false,
                    views: {
                        'subInternalContent': {
                            templateUrl: "templates/common/secretario/secretario_list.html?v=" + Versions.html,
                            controller: 'SecretarioDaIgrejaCtrl'
                        }
                    }
                })
                
                .state('SmartChurchPanel.igreja.secretarias.secretarios.adicionar', {
                    url: "/adicionar?secretaria",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        back: '',
                        perms: [ "SecretariaIgrejaPessoa" ]
                    },
                    cache: false,
                    views: {
                        'subInternalContent': {
                            templateUrl: "templates/common/secretario/secretario_form.html?v=" + Versions.html,
                            controller: 'SecretarioDaIgrejaCtrl'
                        }
                    }
                })
                
                .state('SmartChurchPanel.igreja.secretarias.secretarios.editar', {
                    url: "/editar?secretaria&id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        back: '',
                        perms: [ "SecretariaIgrejaPessoa" ]
                    },
                    cache: false,
                    views: {
                        'subInternalContent': {
                            templateUrl: "templates/common/secretario/secretario_form.html?v=" + Versions.html,
                            controller: 'SecretarioDaIgrejaCtrl'
                        }
                    }
                })
            ;
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.igreja.ministerios
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.igreja.ministerios', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.igreja.ministerios', {
                    url: "/ministerios",
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
                                'js/controllers/igreja/ministerios/ministerio_ctrl.js',
                                'js/services/ministerio_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.igreja.ministerios.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: '',
                        perms: [ "MinisterioIgreja", "MinisterioIgrejaSave", "MinisterioIgrejaRemove", "MinisterioIgrejaBlock", "MinisterioIgrejaManage"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/igreja/ministerios/ministerio_list.html?v=" + Versions.html,
                            controller: 'MinisterioDaIgrejaCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.igreja.ministerios.adicionar', {
                    url: "/adicionar",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        back: '',
                        perms: [ "MinisterioIgrejaSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/igreja/ministerios/ministerio_form.html?v=" + Versions.html,
                            controller: 'MinisterioDaIgrejaCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.igreja.ministerios.editar', {
                    url: "/editar?id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        back: '',
                        perms: [ "MinisterioIgrejaSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/igreja/ministerios/ministerio_form.html?v=" + Versions.html,
                            controller: 'MinisterioDaIgrejaCtrl'
                        }
                    }
                })
                
                .state('SmartChurchPanel.igreja.ministerios.participantes', {
                    url: "/participantes",
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
                                'js/controllers/igreja/ministerios/participante_ctrl.js',
                                'js/services/membro_srv.js',
                                'js/services/servo_srv.js',
                                'js/services/perfil_srv.js',
                                'js/services/sysconfig_srv.js'
                            ], { cache: false});
                        }]
                    }
                })
                
                .state('SmartChurchPanel.igreja.ministerios.participantes.buscar', {
                    url: "/buscar?ministerio",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: 'SmartChurchPanel.igreja.sociedades.buscar',
                        perms: [ "MinisterioIgrejaPessoa" ]
                    },
                    cache: false,
                    views: {
                        'subInternalContent': {
                            templateUrl: "templates/igreja/ministerios/participante_list.html?v=" + Versions.html,
                            controller: 'ParticipanteMinisterioCtrl'
                        }
                    }
                })
                
                .state('SmartChurchPanel.igreja.ministerios.participantes.adicionar', {
                    url: "/adicionar?ministerio",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        back: '',
                        perms: [ "MinisterioIgrejaPessoa" ]
                    },
                    cache: false,
                    views: {
                        'subInternalContent': {
                            templateUrl: "templates/igreja/ministerios/participante_form.html?v=" + Versions.html,
                            controller: 'ParticipanteMinisterioCtrl'
                        }
                    }
                })
                
                .state('SmartChurchPanel.igreja.ministerios.participantes.editar', {
                    url: "/editar?ministerio&id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        back: '',
                        perms: [ "MinisterioIgrejaPessoa" ]
                    },
                    cache: false,
                    views: {
                        'subInternalContent': {
                            templateUrl: "templates/igreja/ministerios/participante_form.html?v=" + Versions.html,
                            controller: 'ParticipanteMinisterioCtrl'
                        }
                    }
                })
            ;
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.igreja.sociedades
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.igreja.sociedades', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.igreja.sociedades', {
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
                                'js/controllers/igreja/sociedades/sociedade_ctrl.js',
                                'js/services/sociedade_srv.js',
                                'js/services/sinodal_srv.js',
                                'js/services/federacao_srv.js',
                                'js/services/nacional_srv.js',
                                'js/services/igreja_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.igreja.sociedades.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: '',
                        perms: [ "SociedadeIgreja", "SociedadeIgrejaSave", "SociedadeIgrejaRemove", "SociedadeIgrejaBlock", "SociedadeIgrejaManage"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/igreja/sociedades/sociedade_list.html?v=" + Versions.html,
                            controller: 'SociedadeDaIgrejaCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.igreja.sociedades.adicionar', {
                    url: "/adicionar",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        back: '',
                        perms: [ "SociedadeIgrejaSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/igreja/sociedades/sociedade_form.html?v=" + Versions.html,
                            controller: 'SociedadeDaIgrejaCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.igreja.sociedades.editar', {
                    url: "/editar?id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        back: '',
                        perms: [ "SociedadeIgrejaSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/igreja/sociedades/sociedade_form.html?v=" + Versions.html,
                            controller: 'SociedadeDaIgrejaCtrl'
                        }
                    }
                })
                
                .state('SmartChurchPanel.igreja.sociedades.socios', {
                    url: "/socios",
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
                                'js/controllers/igreja/sociedades/socioigreja_ctrl.js',
                                'js/services/membro_srv.js',
                                'js/services/socio_srv.js',
                                'js/services/perfil_srv.js',
                                'js/services/sysconfig_srv.js'
                            ], { cache: false});
                        }]
                    }
                })
                
                .state('SmartChurchPanel.igreja.sociedades.socios.buscar', {
                    url: "/buscar?sociedade",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: 'SmartChurchPanel.igreja.sociedades.buscar',
                        perms: [ "SociedadeIgrejaSocio" ]
                    },
                    cache: false,
                    views: {
                        'subInternalContent': {
                            templateUrl: "templates/igreja/sociedades/socioigreja_list.html?v=" + Versions.html,
                            controller: 'SocioIgrejaCtrl'
                        }
                    }
                })
                
                .state('SmartChurchPanel.igreja.sociedades.socios.adicionar', {
                    url: "/adicionar?sociedade",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        back: '',
                        perms: [ "SociedadeIgrejaSocio" ]
                    },
                    cache: false,
                    views: {
                        'subInternalContent': {
                            templateUrl: "templates/igreja/sociedades/socioigreja_form.html?v=" + Versions.html,
                            controller: 'SocioIgrejaCtrl'
                        }
                    }
                })
                
                .state('SmartChurchPanel.igreja.sociedades.socios.editar', {
                    url: "/editar?sociedade&id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        back: '',
                        perms: [ "SociedadeIgrejaSocio" ]
                    },
                    cache: false,
                    views: {
                        'subInternalContent': {
                            templateUrl: "templates/igreja/sociedades/socioigreja_form.html?v=" + Versions.html,
                            controller: 'SocioIgrejaCtrl'
                        }
                    }
                })
            ;
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.igreja.pequenosgrupos
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.igreja.pequenosgrupos', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.igreja.pequenosgrupos', {
                    url: "/pequenosgrupos",
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
                                'js/controllers/igreja/pequenosgrupos/pequenogrupo_ctrl.js',
                                'js/services/pequenogrupo_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.igreja.pequenosgrupos.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: '',
                        perms: [ "PequenoGrupoIgreja", "PequenoGrupoIgrejaSave", "PequenoGrupoIgrejaRemove", "PequenoGrupoIgrejaBlock", 
                                "PequenoGrupoIgrejaParticipantes"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/igreja/pequenosgrupos/pequenogrupo_list.html?v=" + Versions.html,
                            controller: 'PequenoGrupoDaIgrejaCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.igreja.pequenosgrupos.adicionar', {
                    url: "/adicionar",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        back: '',
                        perms: [ "PequenoGrupoIgrejaSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/igreja/pequenosgrupos/pequenogrupo_form.html?v=" + Versions.html,
                            controller: 'PequenoGrupoDaIgrejaCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.igreja.pequenosgrupos.editar', {
                    url: "/editar?id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        back: '',
                        perms: [ "PequenoGrupoIgrejaSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/igreja/pequenosgrupos/pequenogrupo_form.html?v=" + Versions.html,
                            controller: 'PequenoGrupoDaIgrejaCtrl'
                        }
                    }
                })
                
                .state('SmartChurchPanel.igreja.pequenosgrupos.participantes', {
                    url: "/participantes",
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
                                'js/controllers/igreja/pequenosgrupos/participantepg_ctrl.js',
                                'js/services/membro_srv.js',
                                'js/services/participante_srv.js',
                                'js/services/perfil_srv.js',
                                'js/services/sysconfig_srv.js'
                            ], { cache: false});
                        }]
                    }
                })
                
                .state('SmartChurchPanel.igreja.pequenosgrupos.participantes.buscar', {
                    url: "/buscar?pequenogrupo",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: 'SmartChurchPanel.igreja.pequenosgrupos.buscar',
                        perms: [ "PequenoGrupoIgrejaParticipantes" ]
                    },
                    cache: false,
                    views: {
                        'subInternalContent': {
                            templateUrl: "templates/igreja/pequenosgrupos/participantepg_list.html?v=" + Versions.html,
                            controller: 'ParticipantePGDaIgrejaCtrl'
                        }
                    }
                })
                
                .state('SmartChurchPanel.igreja.pequenosgrupos.participantes.adicionar', {
                    url: "/adicionar?pequenogrupo",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        back: '',
                        perms: [ "PequenoGrupoIgrejaParticipantes" ]
                    },
                    cache: false,
                    views: {
                        'subInternalContent': {
                            templateUrl: "templates/igreja/pequenosgrupos/participantepg_form.html?v=" + Versions.html,
                            controller: 'ParticipantePGDaIgrejaCtrl'
                        }
                    }
                })
                
                .state('SmartChurchPanel.igreja.pequenosgrupos.participantes.editar', {
                    url: "/editar?pequenogrupo&id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        back: '',
                        perms: [ "PequenoGrupoIgrejaParticipantes" ]
                    },
                    cache: false,
                    views: {
                        'subInternalContent': {
                            templateUrl: "templates/igreja/pequenosgrupos/participantepg_form.html?v=" + Versions.html,
                            controller: 'ParticipantePGDaIgrejaCtrl'
                        }
                    }
                })
                
            ;
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.igreja.pastores
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.igreja.pastores', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.igreja.pastores', {
                    url: "/pastores",
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
                                'js/controllers/igreja/pastores/pastor_ctrl.js',
                                'js/services/membro_srv.js',
                                'js/services/sysconfig_srv.js',
                                'js/services/pastor_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.igreja.pastores.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: '',
                        perms: [ "PastorIgreja", "PastorIgrejaSave", "PastorIgrejaRemove", "PastorIgrejaBlock"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/igreja/pastores/pastor_list.html?v=" + Versions.html,
                            controller: 'PastorDaIgrejaCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.igreja.pastores.adicionar', {
                    url: "/adicionar",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        back: '',
                        perms: [ "PastorIgrejaSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/igreja/pastores/pastor_form.html?v=" + Versions.html,
                            controller: 'PastorDaIgrejaCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.igreja.pastores.editar', {
                    url: "/editar?id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        back: '',
                        perms: [ "PastorIgrejaSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/igreja/pastores/pastor_form.html?v=" + Versions.html,
                            controller: 'PastorDaIgrejaCtrl'
                        }
                    }
                });
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.igreja.evangelistas
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.igreja.evangelistas', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.igreja.evangelistas', {
                    url: "/evangelistas",
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
                                'js/controllers/igreja/evangelistas/evangelista_ctrl.js',
                                'js/services/membro_srv.js',
                                'js/services/sysconfig_srv.js',
                                'js/services/evangelista_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.igreja.evangelistas.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: '',
                        perms: [ "EvangelistaIgreja", "EvangelistaIgrejaSave", "EvangelistaIgrejaRemove", "EvangelistaIgrejaBlock"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/igreja/evangelistas/evangelista_list.html?v=" + Versions.html,
                            controller: 'EvangelistaDaIgrejaCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.igreja.evangelistas.adicionar', {
                    url: "/adicionar",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        back: '',
                        perms: [ "EvangelistaIgrejaSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/igreja/evangelistas/evangelista_form.html?v=" + Versions.html,
                            controller: 'EvangelistaDaIgrejaCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.igreja.evangelistas.editar', {
                    url: "/editar?id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        back: '',
                        perms: [ "EvangelistaIgrejaSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/igreja/evangelistas/evangelista_form.html?v=" + Versions.html,
                            controller: 'EvangelistaDaIgrejaCtrl'
                        }
                    }
                });
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.igreja.presbiteros
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.igreja.presbiteros', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.igreja.presbiteros', {
                    url: "/presbiteros",
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
                                'js/controllers/igreja/presbiteros/presbitero_ctrl.js',
                                'js/services/membro_srv.js',
                                'js/services/sysconfig_srv.js',
                                'js/services/presbitero_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.igreja.presbiteros.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: '',
                        perms: [ "PresbiteroIgreja", "PresbiteroIgrejaSave", "PresbiteroIgrejaRemove", "PresbiteroIgrejaBlock"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/igreja/presbiteros/presbitero_list.html?v=" + Versions.html,
                            controller: 'PresbiteroDaIgrejaCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.igreja.presbiteros.adicionar', {
                    url: "/adicionar",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        back: '',
                        perms: [ "PresbiteroIgrejaSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/igreja/presbiteros/presbitero_form.html?v=" + Versions.html,
                            controller: 'PresbiteroDaIgrejaCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.igreja.presbiteros.editar', {
                    url: "/editar?id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        back: '',
                        perms: [ "PresbiteroIgrejaSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/igreja/presbiteros/presbitero_form.html?v=" + Versions.html,
                            controller: 'PresbiteroDaIgrejaCtrl'
                        }
                    }
                });
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.igreja.diaconos
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.igreja.diaconos', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.igreja.diaconos', {
                    url: "/diaconos",
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
                                'js/controllers/igreja/diaconos/diacono_ctrl.js',
                                'js/services/membro_srv.js',
                                'js/services/sysconfig_srv.js',
                                'js/services/diacono_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.igreja.diaconos.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: '',
                        perms: [ "DiaconoIgreja", "DiaconoIgrejaSave", "DiaconoIgrejaRemove", "DiaconoIgrejaBlock"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/igreja/diaconos/diacono_list.html?v=" + Versions.html,
                            controller: 'DiaconoDaIgrejaCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.igreja.diaconos.adicionar', {
                    url: "/adicionar",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        back: '',
                        perms: [ "DiaconoIgrejaSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/igreja/diaconos/diacono_form.html?v=" + Versions.html,
                            controller: 'DiaconoDaIgrejaCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.igreja.diaconos.editar', {
                    url: "/editar?id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        back: '',
                        perms: [ "DiaconoIgrejaSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/igreja/diaconos/diacono_form.html?v=" + Versions.html,
                            controller: 'DiaconoDaIgrejaCtrl'
                        }
                    }
                });
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.igreja.oficiais
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.igreja.oficiais', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.igreja.oficiais', {
                    url: "/oficiais",
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
                                'js/controllers/igreja/oficiais/oficial_ctrl.js',
                                'js/services/membro_srv.js',
                                'js/services/cargo_srv.js',
                                'js/services/oficial_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.igreja.oficiais.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: '',
                        perms: [ "OficialIgreja", "OficialIgrejaSave", "OficialIgrejaRemove", "OficialIgrejaBlock"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/igreja/oficiais/oficial_list.html?v=" + Versions.html,
                            controller: 'OficialDaIgrejaCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.igreja.oficiais.adicionar', {
                    url: "/adicionar",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        back: '',
                        perms: [ "OficialIgrejaSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/igreja/oficiais/oficial_form.html?v=" + Versions.html,
                            controller: 'OficialDaIgrejaCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.igreja.oficiais.editar', {
                    url: "/editar?id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        back: '',
                        perms: [ "OficialIgrejaSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/igreja/oficiais/oficial_form.html?v=" + Versions.html,
                            controller: 'OficialDaIgrejaCtrl'
                        }
                    }
                });
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.igreja.conselho
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.igreja.conselho', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.igreja.conselho', {
                    url: "/conselho",
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
                                'js/controllers/igreja/conselho/conselho_ctrl.js',
                                'js/services/conselho_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.igreja.conselho.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: '',
                        perms: [ "ConselhoIgreja", "ConselhoIgrejaManage"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/igreja/conselho/conselho_list.html?v=" + Versions.html,
                            controller: 'ConselhoDaIgrejaCtrl'
                        }
                    }
                });
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.igreja.junta
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.igreja.junta', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.igreja.junta', {
                    url: "/junta",
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
                                'js/controllers/igreja/junta/junta_ctrl.js',
                                'js/services/junta_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.igreja.junta.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: '',
                        perms: [ "JuntaIgreja", "JuntaIgrejaManage"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/igreja/junta/junta_list.html?v=" + Versions.html,
                            controller: 'JuntaDaIgrejaCtrl'
                        }
                    }
                });
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.igreja.agenda
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.igreja.agenda', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.igreja.agenda', {
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
                                'js/controllers/igreja/agenda/agenda_ctrl.js',
                                'js/services/membro_srv.js',
                                'js/services/cargo_srv.js',
                                'js/services/oficial_srv.js',
                                'js/services/diacono_srv.js',
                                'js/services/presbitero_srv.js',
                                'js/services/evangelista_srv.js',
                                'js/services/pastor_srv.js',
                                'js/services/sociedade_srv.js',
                                'js/services/ministerio_srv.js',
                                'js/services/secretaria_srv.js',
                                'js/services/tagsagenda_srv.js',
                                'js/services/agenda_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.igreja.agenda.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: '',
                        perms: [ "AgendaIgreja", "AgendaIgrejaSave", "AgendaIgrejaRemove", "AgendaIgrejaBlock"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/igreja/agenda/agenda_list.html?v=" + Versions.html,
                            controller: 'AgendaDaIgrejaCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.igreja.agenda.adicionar', {
                    url: "/adicionar",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        back: '',
                        perms: [ "AgendaIgrejaSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/igreja/agenda/agenda_form.html?v=" + Versions.html,
                            controller: 'AgendaDaIgrejaCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.igreja.agenda.editar', {
                    url: "/editar?id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        back: '',
                        perms: [ "AgendaIgrejaSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/igreja/agenda/agenda_form.html?v=" + Versions.html,
                            controller: 'AgendaDaIgrejaCtrl'
                        }
                    }
                });
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.igreja.transmissoes
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.igreja.transmissoes', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.igreja.transmissoes', {
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
                                'js/controllers/igreja/transmissoes/transmissao_ctrl.js',
                                'js/services/transmissao_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.igreja.transmissoes.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        view: false,
                        back: '',
                        perms: [ "TransmissaoIgreja", "TransmissaoIgrejaSave", "TransmissaoIgrejaRemove", "TransmissaoIgrejaBlock"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/igreja/transmissoes/transmissao_list.html?v=" + Versions.html,
                            controller: 'TransmissaoIgrejaCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.igreja.transmissoes.adicionar', {
                    url: "/adicionar",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        view: false,
                        back: '',
                        perms: [ "TransmissaoIgrejaSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/common/transmissoes/transmissao_form.html?v=" + Versions.html,
                            controller: 'TransmissaoIgrejaCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.igreja.transmissoes.editar', {
                    url: "/editar?id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        view: false,
                        back: '',
                        perms: [ "TransmissaoIgrejaSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/common/transmissoes/transmissao_form.html?v=" + Versions.html,
                            controller: 'TransmissaoIgrejaCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.igreja.transmissoes.ver', {
                    url: "/ver?id",
                    params: {
                        data: null,
                        title: 'Visualizando',
                        search: false,
                        view: true,
                        back: '',
                        perms: [ "TransmissaoIgreja" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/common/transmissoes/transmissao_view.html?v=" + Versions.html,
                            controller: 'TransmissaoIgrejaCtrl'
                        }
                    }
                })
                
                ;
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.igreja.eventos
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.igreja.eventos', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.igreja.eventos', {
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
                                'js/controllers/eventos/evento_ctrl.js',
                                'js/services/agenda_srv.js',
                                'js/services/evento_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.igreja.eventos.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: '',
                        perms: [ "EventoIgreja", "EventoIgrejaSave", "EventoIgrejaRemove", "EventoIgrejaBlock", "EventoIgrejaManage"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/igreja/eventos/evento_list.html?v=" + Versions.html,
                            controller: 'EventoDaIgrejaCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.igreja.eventos.adicionar', {
                    url: "/adicionar",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        back: '',
                        perms: [ "EventoIgrejaSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/igreja/eventos/evento_form.html?v=" + Versions.html,
                            controller: 'EventoDaIgrejaCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.igreja.eventos.editar', {
                    url: "/editar?id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        back: '',
                        perms: [ "EventoIgrejaSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/igreja/eventos/evento_form.html?v=" + Versions.html,
                            controller: 'EventoDaIgrejaCtrl'
                        }
                    }
                });
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.igreja.eleicoes
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.igreja.eleicoes', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.igreja.eleicoes', {
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
                                'js/controllers/igreja/eleicoes/eleicao_ctrl.js',
                                'js/services/agenda_srv.js',
                                'js/services/evento_srv.js',
                                'js/services/eleicao_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.igreja.eleicoes.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: '',
                        perms: [ "EleicaoIgreja", "EleicaoIgrejaSave", "EleicaoIgrejaRemove", "EleicaoIgrejaBlock", "EleicaoIgrejaManage"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/igreja/eleicoes/eleicao_list.html?v=" + Versions.html,
                            controller: 'EleicaoDaIgrejaCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.igreja.eleicoes.adicionar', {
                    url: "/adicionar",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        back: '',
                        perms: [ "EleicaoIgrejaSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/igreja/eleicoes/eleicao_form.html?v=" + Versions.html,
                            controller: 'EleicaoDaIgrejaCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.igreja.eleicoes.editar', {
                    url: "/editar?id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        back: '',
                        perms: [ "EleicaoIgrejaSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/igreja/eleicoes/eleicao_form.html?v=" + Versions.html,
                            controller: 'EleicaoDaIgrejaCtrl'
                        }
                    }
                });
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.igreja.seriesdesermoes
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.igreja.seriesdesermoes', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.igreja.seriesdesermoes', {
                    url: "/seriesdesermoes",
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
                                'js/controllers/igreja/seriesdesermoes/seriedesermao_ctrl.js',
                                'js/services/seriedesermao_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.igreja.seriesdesermoes.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: '',
                        perms: [ "SerieSermao", "SerieSermaoSave", "SerieSermaoRemove", "SerieSermaoBlock"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/igreja/seriesdesermoes/seriedesermao_list.html?v=" + Versions.html,
                            controller: 'SerieDeSermaoCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.igreja.seriesdesermoes.adicionar', {
                    url: "/adicionar",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        back: '',
                        perms: [ "SerieSermaoSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/igreja/seriesdesermoes/seriedesermao_form.html?v=" + Versions.html,
                            controller: 'SerieDeSermaoCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.igreja.seriesdesermoes.editar', {
                    url: "/editar?id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        back: '',
                        perms: [ "SerieSermaoSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/igreja/seriesdesermoes/seriedesermao_form.html?v=" + Versions.html,
                            controller: 'SerieDeSermaoCtrl'
                        }
                    }
                });
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.igreja.sermoes
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.igreja.sermoes', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.igreja.sermoes', {
                    url: "/sermoes",
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
                                'js/controllers/igreja/sermoes/sermao_ctrl.js',
                                'js/services/sermao_srv.js',
                                'js/services/seriedesermao_srv.js',
                                'js/services/membro_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.igreja.sermoes.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        view: false,
                        back: '',
                        perms: [ "Sermao", "SermaoSave", "SermaoRemove", "SermaoBlock"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/igreja/sermoes/sermao_list.html?v=" + Versions.html,
                            controller: 'SermaoCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.igreja.sermoes.adicionar', {
                    url: "/adicionar",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        view: false,
                        back: '',
                        perms: [ "SermaoSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/igreja/sermoes/sermao_form.html?v=" + Versions.html,
                            controller: 'SermaoCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.igreja.sermoes.editar', {
                    url: "/editar?id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        view: false,
                        back: '',
                        perms: [ "SermaoSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/igreja/sermoes/sermao_form.html?v=" + Versions.html,
                            controller: 'SermaoCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.igreja.sermoes.ver', {
                    url: "/ver?id",
                    params: {
                        data: null,
                        title: 'Visualizando',
                        search: false,
                        view: true,
                        back: '',
                        perms: [ "Sermao" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/igreja/sermoes/sermao_view.html?v=" + Versions.html,
                            controller: 'SermaoCtrl'
                        }
                    }
                })
                
                ;
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.igreja.seriesdeestudos
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.igreja.seriesdeestudos', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.igreja.seriesdeestudos', {
                    url: "/seriesdeestudos",
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
                                'js/controllers/igreja/seriesdeestudos/seriedeestudo_ctrl.js',
                                'js/services/seriedeestudo_srv.js',
                                'js/services/congregacaoigreja_srv.js',
                                'js/services/pontodepregacaoigreja_srv.js',
                                'js/services/secretaria_srv.js',
                                'js/services/ministerio_srv.js',
                                'js/services/sociedade_srv.js',
                                'js/services/pequenogrupo_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.igreja.seriesdeestudos.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: '',
                        perms: [ "SerieEstudoIgreja", "SerieEstudoIgrejaSave", "SerieEstudoIgrejaRemove", "SerieEstudoIgrejaBlock"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/igreja/seriesdeestudos/seriedeestudo_list.html?v=" + Versions.html,
                            controller: 'SerieDeEstudoDaIgrejaCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.igreja.seriesdeestudos.adicionar', {
                    url: "/adicionar",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        back: '',
                        perms: [ "SerieEstudoIgrejaSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/igreja/seriesdeestudos/seriedeestudo_form.html?v=" + Versions.html,
                            controller: 'SerieDeEstudoDaIgrejaCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.igreja.seriesdeestudos.editar', {
                    url: "/editar?id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        back: '',
                        perms: [ "SerieEstudoIgrejaSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/igreja/seriesdeestudos/seriedeestudo_form.html?v=" + Versions.html,
                            controller: 'SerieDeEstudoDaIgrejaCtrl'
                        }
                    }
                });
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.igreja.estudos
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.igreja.estudos', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.igreja.estudos', {
                    url: "/estudos",
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
                                'js/controllers/igreja/estudos/estudo_ctrl.js',
                                'js/services/estudo_srv.js',
                                'js/services/seriedeestudo_srv.js',
                                'js/services/congregacaoigreja_srv.js',
                                'js/services/pontodepregacaoigreja_srv.js',
                                'js/services/secretaria_srv.js',
                                'js/services/ministerio_srv.js',
                                'js/services/sociedade_srv.js',
                                'js/services/pequenogrupo_srv.js',
                                'js/services/membro_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.igreja.estudos.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: '',
                        perms: [ "EstudoIgreja", "EstudoIgrejaSave", "EstudoIgrejaRemove", "EstudoIgrejaBlock"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/igreja/estudos/estudo_list.html?v=" + Versions.html,
                            controller: 'EstudoIgrejaCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.igreja.estudos.adicionar', {
                    url: "/adicionar",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        back: '',
                        perms: [ "EstudoIgrejaSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/igreja/estudos/estudo_form.html?v=" + Versions.html,
                            controller: 'EstudoIgrejaCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.igreja.estudos.editar', {
                    url: "/editar?id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        back: '',
                        perms: [ "EstudoIgrejaSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/igreja/estudos/estudo_form.html?v=" + Versions.html,
                            controller: 'EstudoIgrejaCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.igreja.estudos.ver', {
                    url: "/ver?id",
                    params: {
                        data: null,
                        title: 'Visualizando',
                        search: false,
                        view: true,
                        back: '',
                        perms: [ "EstudoIgreja" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/igreja/estudos/estudo_view.html?v=" + Versions.html,
                            controller: 'EstudoIgrejaCtrl'
                        }
                    }
                })
                
                ;
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.igreja.superintendencia
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.igreja.superintendencia', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.igreja.superintendencia', {
                    url: "/superintendencia",
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
                                'js/controllers/igreja/superintendencia/superintendencia_ctrl.js',
                                'js/services/membro_srv.js',
                                'js/services/sysconfig_srv.js',
                                'js/services/perfil_srv.js',
                                'js/services/superintendencia_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.igreja.superintendencia.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: '',
                        perms: [ "SuperintendenciaIgreja", "SuperintendenciaIgrejaSave", "SuperintendenciaIgrejaRemove", "SuperintendenciaIgrejaBlock"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/igreja/superintendencia/superintendencia_list.html?v=" + Versions.html,
                            controller: 'SuperintendenciaDaIgrejaCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.igreja.superintendencia.adicionar', {
                    url: "/adicionar",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        back: '',
                        perms: [ "SuperintendenciaIgrejaSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/igreja/superintendencia/superintendencia_form.html?v=" + Versions.html,
                            controller: 'SuperintendenciaDaIgrejaCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.igreja.superintendencia.editar', {
                    url: "/editar?id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        back: '',
                        perms: [ "SuperintendenciaIgrejaSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/igreja/superintendencia/superintendencia_form.html?v=" + Versions.html,
                            controller: 'SuperintendenciaDaIgrejaCtrl'
                        }
                    }
                })
                
            ;
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.igreja.tokenigreja
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.igreja.token', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.igreja.token', {
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
                                'js/controllers/igreja/token/token_ctrl.js',
                                'js/services/integracao_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.igreja.token.gerar', {
                    url: "/gerar",
                    params: {
                        title: 'Gerando',
                        perms: [ "IntegracaoIgreja"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/common/token/token_form.html?v=" + Versions.html,
                            controller: 'TokenIgrejaCtrl'
                        }
                    }
                })

                ;
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.igreja.mural
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.igreja.mural', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.igreja.mural', {
                    url: "/mural",
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
                                'js/controllers/igreja/mural/mural_ctrl.js',
                                'js/services/mural_srv.js',
                                'js/services/congregacaoigreja_srv.js',
                                'js/services/pontodepregacaoigreja_srv.js',
                                'js/services/secretaria_srv.js',
                                'js/services/ministerio_srv.js',
                                'js/services/sociedade_srv.js',
                                'js/services/pequenogrupo_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.igreja.mural.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: '',
                        perms: [ "Mural", "MuralSave", "MuralRemove", "MuralBlock"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/igreja/mural/mural_list.html?v=" + Versions.html,
                            controller: 'MuralDaIgrejaCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.igreja.mural.adicionar', {
                    url: "/adicionar",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        back: '',
                        perms: [ "MuralSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/igreja/mural/mural_form.html?v=" + Versions.html,
                            controller: 'MuralDaIgrejaCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.igreja.mural.editar', {
                    url: "/editar?id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        back: '',
                        perms: [ "MuralSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/igreja/mural/mural_form.html?v=" + Versions.html,
                            controller: 'MuralDaIgrejaCtrl'
                        }
                    }
                })
                
            ;
                    
        };
})();

/*******************************************************************************
 * SmartChurchPanel.igreja.pedidosdeoracao
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.igreja.pedidosdeoracao', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.igreja.pedidosdeoracao', {
                    url: "/pedidosdeoracao",
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
                                'js/controllers/igreja/pedidosdeoracao/pedido_ctrl.js',
                                'js/services/pedidodeoracao_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.igreja.pedidosdeoracao.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: '',
                        perms: [ "PedidosOracaoIgreja"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/common/pedidosdeoracao/pedido_list.html?v=" + Versions.html,
                            controller: 'PedidosOracaoDaIgrejaCtrl'
                        }
                    }
                })
                
            ;
                    
        };
})();

