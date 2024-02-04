
/*******************************************************************************
 * SmartChurchPanel.ebd
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.ebd', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                
                .state('SmartChurchPanel.ebd', {
                    url: "/ebd",
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
 * SmartChurchPanel.ebd.salas
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.ebd.salas', [
            'ui.router', 
            'oc.lazyLoad'
        ])
        .config(stateConfig);
        
        stateConfig.$inject = ['$stateProvider', 'Versions'];
        function stateConfig($stateProvider, Versions) {
            
            $stateProvider
                .state('SmartChurchPanel.ebd.salas', {
                    url: "/salas",
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
                                'js/controllers/ebd/salas/sala_ctrl.js',
                                'js/services/salaebd_srv.js'
                            ], { cache: false});
                        }]
                    }
                })

                .state('SmartChurchPanel.ebd.salas.buscar', {
                    url: "/buscar",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: '',
                        perms: [ "SalaEBD", "SalaEBDSave", "SalaEBDRemove", "SalaEBDBlock"]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/ebd/salas/sala_list.html?v=" + Versions.html,
                            controller: 'SalaEBDCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.ebd.salas.adicionar', {
                    url: "/adicionar",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        back: '',
                        perms: [ "SalaEBDSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/ebd/salas/sala_form.html?v=" + Versions.html,
                            controller: 'SalaEBDCtrl'
                        }
                    }
                })

                .state('SmartChurchPanel.ebd.salas.editar', {
                    url: "/editar?id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        back: '',
                        perms: [ "SalaEBDSave" ]
                    },
                    cache: false,
                    views: {
                        'internalContent': {
                            templateUrl: "templates/ebd/salas/sala_form.html?v=" + Versions.html,
                            controller: 'SalaEBDCtrl'
                        }
                    }
                })
                
                .state('SmartChurchPanel.ebd.salas.professores', {
                    url: "/professores",
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
                                'js/controllers/ebd/professores/professor_ctrl.js',
                                'js/services/membro_srv.js',
                                'js/services/sysconfig_srv.js',
                                'js/services/professordasala_srv.js'
                            ], { cache: false});
                        }]
                    }
                })
                
                .state('SmartChurchPanel.ebd.salas.professores.buscar', {
                    url: "/buscar?sala",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: 'SmartChurchPanel.ebd.salas.buscar',
                        perms: [ "SalaEBDProfessores" ]
                    },
                    cache: false,
                    views: {
                        'subInternalContent': {
                            templateUrl: "templates/ebd/professor/professor_list.html?v=" + Versions.html,
                            controller: 'ProfessorDaEBDCtrl'
                        }
                    }
                })
                
                .state('SmartChurchPanel.ebd.salas.professores.adicionar', {
                    url: "/adicionar?sala",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        back: '',
                        perms: [ "SalaEBDProfessores" ]
                    },
                    cache: false,
                    views: {
                        'subInternalContent': {
                            templateUrl: "templates/ebd/professor/professor_form.html?v=" + Versions.html,
                            controller: 'ProfessorDaEBDCtrl'
                        }
                    }
                })
                
                .state('SmartChurchPanel.ebd.salas.professores.editar', {
                    url: "/editar?sala&id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        back: '',
                        perms: [ "SalaEBDProfessores" ]
                    },
                    cache: false,
                    views: {
                        'subInternalContent': {
                            templateUrl: "templates/ebd/professor/professor_form.html?v=" + Versions.html,
                            controller: 'ProfessorDaEBDCtrl'
                        }
                    }
                })
                
                .state('SmartChurchPanel.ebd.salas.alunos', {
                    url: "/alunos",
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
                                'js/controllers/ebd/alunos/aluno_ctrl.js',
                                'js/services/membro_srv.js',
                                'js/services/alunodasala_srv.js'
                            ], { cache: false});
                        }]
                    }
                })
                
                .state('SmartChurchPanel.ebd.salas.alunos.buscar', {
                    url: "/buscar?sala",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        multiple: false,
                        back: 'SmartChurchPanel.ebd.salas.buscar',
                        perms: [ "SalaEBDAlunos" ]
                    },
                    cache: false,
                    views: {
                        'subInternalContent': {
                            templateUrl: "templates/ebd/aluno/aluno_list.html?v=" + Versions.html,
                            controller: 'AlunoDaEBDCtrl'
                        }
                    }
                })
                
                .state('SmartChurchPanel.ebd.salas.alunos.adicionar', {
                    url: "/adicionar?sala",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        multiple: false,
                        back: '',
                        perms: [ "SalaEBDAlunos" ]
                    },
                    cache: false,
                    views: {
                        'subInternalContent': {
                            templateUrl: "templates/ebd/aluno/aluno_form.html?v=" + Versions.html,
                            controller: 'AlunoDaEBDCtrl'
                        }
                    }
                })
                
                .state('SmartChurchPanel.ebd.salas.alunos.editar', {
                    url: "/editar?sala&id",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        multiple: false,
                        back: '',
                        perms: [ "SalaEBDAlunos" ]
                    },
                    cache: false,
                    views: {
                        'subInternalContent': {
                            templateUrl: "templates/ebd/aluno/aluno_form.html?v=" + Versions.html,
                            controller: 'AlunoDaEBDCtrl'
                        }
                    }
                })
                
                .state('SmartChurchPanel.ebd.salas.frequencias', {
                    url: "/frequencias",
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
                                'js/controllers/ebd/frequencias/frequencia_ctrl.js',
                                'js/services/alunodasala_srv.js',
                                'js/services/frequenciadasala_srv.js'
                            ], { cache: false});
                        }]
                    }
                })
                
                .state('SmartChurchPanel.ebd.salas.frequencias.buscar', {
                    url: "/buscar?sala",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: 'SmartChurchPanel.ebd.salas.buscar',
                        perms: [ "FrequenciaEBD" ]
                    },
                    cache: false,
                    views: {
                        'subInternalContent': {
                            templateUrl: "templates/ebd/frequencia/frequencia_list.html?v=" + Versions.html,
                            controller: 'FrequenciaDaSalaEBDCtrl'
                        }
                    }
                })
                
                .state('SmartChurchPanel.ebd.salas.frequencias.adicionar', {
                    url: "/adicionar?sala",
                    params: {
                        data: null,
                        title: 'Adicionando',
                        search: false,
                        back: '',
                        perms: [ "FrequenciaEBDSave" ]
                    },
                    cache: false,
                    views: {
                        'subInternalContent': {
                            templateUrl: "templates/ebd/frequencia/frequencia_form.html?v=" + Versions.html,
                            controller: 'FrequenciaDaSalaEBDCtrl'
                        }
                    }
                })
                
                .state('SmartChurchPanel.ebd.salas.frequencias.editar', {
                    url: "/editar?sala&dia",
                    params: {
                        data: null,
                        title: 'Editando',
                        search: false,
                        back: '',
                        perms: [ "FrequenciaEBDSave" ]
                    },
                    cache: false,
                    views: {
                        'subInternalContent': {
                            templateUrl: "templates/ebd/frequencia/frequencia_form.html?v=" + Versions.html,
                            controller: 'FrequenciaDaSalaEBDCtrl'
                        }
                    }
                })
                
                .state('SmartChurchPanel.ebd.salas.sumarios', {
                    url: "/sumarios",
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
                                'js/controllers/ebd/sumarios/sumario_ctrl.js',
                                'js/services/freqsumario_srv.js'
                            ], { cache: false});
                        }]
                    }
                })
                
                .state('SmartChurchPanel.ebd.salas.sumarios.buscar', {
                    url: "/buscar?sala",
                    params: {
                        data: null,
                        title: 'Buscando',
                        search: true,
                        back: 'SmartChurchPanel.ebd.salas.buscar',
                        perms: [ "SumarioEBD" ]
                    },
                    cache: false,
                    views: {
                        'subInternalContent': {
                            templateUrl: "templates/ebd/sumario/sumario_list.html?v=" + Versions.html,
                            controller: 'SumarioDaSalaEBDCtrl'
                        }
                    }
                })
                
                ;
                    
        };
})();
