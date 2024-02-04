
/*******************************************************************************
 * SmartChurchPanel.usuarioDirective
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.usuarioDirective', [
            'oc.lazyLoad'
        ])
        
        .run(function($ocLazyLoad) {
            return $ocLazyLoad.load([
                'directives/usuario/usuario_dire.html',
                'directives/usuario/usuario_dire.js'
            ], { cache: false});
        })

        .directive('usuarioData', function() {
            return {
                restrict: 'EA',
                templateUrl: 'usuario_data.html',
                controller: 'UsuarioDireCtrl',
                controllerAs: 'ctrl',
                scope: {
                    'model': '=',
                    'igreja': '=',
                    'perfis': '=',
                    'pessoaService': '=',
                    'membroService': '=',
                    'checkNome': '=',
                    'perfilDefault': '=',
                    'readonly': '='
                },
                link:function(scope, elem, attrs, ctrl) {
                    scope.$watch('model', function(newObj){
                        ctrl.setModel(newObj);
                    });
                    scope.$watch('igreja', function(newObj){
                        ctrl.setIgreja(newObj);
                    });
                    scope.$watch('perfis', function(newObj){
                        ctrl.setPerfis(newObj);
                    });
                    scope.$watch('pessoaService', function(newObj){
                        ctrl.setPessoa(newObj);
                    });
                    scope.$watch('membroService', function(newObj){
                        ctrl.setMembroDaIgreja(newObj);
                    });
                    scope.$watch('checkNome', function(newObj){
                        ctrl.setCheckNome(newObj);
                    });
                    scope.$watch('perfilDefault', function(newObj){
                        ctrl.setPerfilDefault(newObj);
                    });
                    scope.$watch('readonly', function(newObj){
                        ctrl.setreadonly(newObj);
                    });
                }
            };
        })
        
        ;
})();

/*******************************************************************************
 * SmartChurchPanel.pessoaDirective
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.pessoaDirective', [
            'oc.lazyLoad'
        ])
        
        .run(function($ocLazyLoad) {
            return $ocLazyLoad.load([
                'directives/pessoa/pessoa_dire.html',
                'directives/pessoa/pessoa_dire.js'
            ], { cache: false});
        })

        .directive('pessoaData', function() {
            return {
                restrict: 'EA',
                templateUrl: 'pessoa_data.html',
                controller: 'PessoaDireCtrl',
                controllerAs: 'ctrl',
                scope: {
                    'model': '=',
                    'igreja': '=',
                    'escolaridades': '=',
                    'profissoes': '=',
                    'sexos': '=',
                    'estadoscivis': '=',
                    'relacoes': '=',
                    'pessoaService': '=',
                    'membroService': '=',
                    'familiaFactory': '=',
                    'userService': '=',
                    'user': '=',
                    'readonly': '='
                },
                link:function(scope, elem, attrs, ctrl) {
                    scope.$watch('model', function(newObj){
                        ctrl.setModel(newObj);
                    });
                    scope.$watch('igreja', function(newObj){
                        ctrl.setIgreja(newObj);
                    });
                    scope.$watch('escolaridades', function(newObj){
                        ctrl.setEscolaridades(newObj);
                    });
                    scope.$watch('profissoes', function(newObj){
                        ctrl.setProfissoes(newObj);
                    });
                    scope.$watch('sexos', function(newObj){
                        ctrl.setSexos(newObj);
                    });
                    scope.$watch('estadoscivis', function(newObj){
                        ctrl.setEstadosCivis(newObj);
                    });
                    scope.$watch('relacoes', function(newObj){
                        ctrl.setRelacoes(newObj);
                    });
                    scope.$watch('pessoaService', function(newObj){
                        ctrl.setPessoa(newObj);
                    });
                    scope.$watch('membroService', function(newObj){
                        ctrl.setMembroDaIgreja(newObj);
                    });
                    scope.$watch('familiaFactory', function(newObj){
                        ctrl.setFamiliaFactory(newObj);
                    });
                    scope.$watch('userService', function(newObj){
                        ctrl.setUSER(newObj);
                    });
                    scope.$watch('user', function(newObj){
                        ctrl.setuser(newObj);
                    });
                    scope.$watch('readonly', function(newObj){
                        ctrl.setreadonly(newObj);
                    });
                }
            };
        })
        
        ;
})();

/*******************************************************************************
 * SmartChurchPanel.enderecoDirective
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.enderecoDirective', [
            'oc.lazyLoad'
        ])
        
        .run(function($ocLazyLoad) {
            return $ocLazyLoad.load([
                'directives/endereco/endereco_dire.html',
                'directives/endereco/endereco_dire.js'
            ], { cache: false});
        })

        .directive('enderecoData', function() {
            return {
                restrict: 'EA',
                templateUrl: 'endereco_data.html',
                controller: 'EnderecoDireCtrl',
                controllerAs: 'ctrl',
                scope: {
                    'model': '=',
                    'ufs': '=',
                    'cidades': '=',
                    'readonly': '='
                },
                link:function(scope, elem, attrs, ctrl) {
                    scope.$watch('model', function(newObj){
                        ctrl.setModel(newObj);
                    });
                    scope.$watch('ufs', function(newObj){
                        ctrl.setUFs(newObj);
                    });
                    scope.$watch('cidades', function(newObj){
                        ctrl.setCidades(newObj);
                    });
                    scope.$watch('readonly', function(newObj){
                        ctrl.setreadonly(newObj);
                    });
                }
            };
        })
        
        ;
})();

/*******************************************************************************
 * SmartChurchPanel.siteDirective
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.siteDirective', [
            'oc.lazyLoad'
        ])
        
        .run(function($ocLazyLoad) {
            return $ocLazyLoad.load([
                'directives/site/site_dire.html',
                'directives/site/site_dire.js'
            ], { cache: false});
        })

        .directive('siteData', function() {
            return {
                restrict: 'EA',
                templateUrl: 'site_data.html',
                controller: 'SiteDireCtrl',
                controllerAs: 'ctrl',
                scope: {
                    'model': '=',
                    'readonly': '='
                },
                link:function(scope, elem, attrs, ctrl) {
                    scope.$watch('model', function(newObj){
                        ctrl.setModel(newObj);
                    });
                    scope.$watch('readonly', function(newObj){
                        ctrl.setreadonly(newObj);
                    });
                }
            };
        })
        
        ;
})();

/*******************************************************************************
 * SmartChurchPanel.secretariaDirective
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.secretariaDirective', [
            'oc.lazyLoad'
        ])
        
        .run(function($ocLazyLoad) {
            return $ocLazyLoad.load([
                'directives/secretaria/secretaria_dire.html',
                'directives/secretaria/secretaria_dire.js'
            ], { cache: false});
        })

        .directive('secretariaData', function() {
            return {
                restrict: 'EA',
                templateUrl: 'secretaria_data.html',
                controller: 'SecretariaDireCtrl',
                controllerAs: 'ctrl',
                scope: {
                    'model': '='
                },
                link:function(scope, elem, attrs, ctrl) {
                    scope.$watch('model', function(newObj){
                        ctrl.setModel(newObj);
                    });
                }
            };
        })
        
        ;
})();

/*******************************************************************************
 * SmartChurchPanel.secretarioDirective
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.secretarioDirective', [
            'oc.lazyLoad'
        ])
        
        .run(function($ocLazyLoad) {
            return $ocLazyLoad.load([
                'directives/secretario/secretario_dire.html',
                'directives/secretario/secretario_dire.js'
            ], { cache: false});
        })

        .directive('secretarioData', function() {
            return {
                restrict: 'EA',
                templateUrl: 'secretario_data.html',
                controller: 'SecretarioDireCtrl',
                controllerAs: 'ctrl',
                scope: {
                    'model': '=',
                    'perfis': '='
                },
                link:function(scope, elem, attrs, ctrl) {
                    scope.$watch('model', function(newObj){
                        ctrl.setModel(newObj);
                    });
                    scope.$watch('perfis', function(newObj){
                        ctrl.setPerfis(newObj);
                    });
                }
            };
        })
        
        ;
})();

/*******************************************************************************
 * SmartChurchPanel.igrejaHomeDirective
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.igrejaHomeDirective', [
            'oc.lazyLoad'
        ])
        
        .run(function($ocLazyLoad) {
            return $ocLazyLoad.load([
                'directives/igrejahome/igrejahome_dire.html',
                'directives/igrejahome/igrejahome_dire.js',
                'js/services/user_srv.js',
                'js/services/home_srv.js'
            ], { cache: false});
        })

        .directive('igrejaHome', function() {
            return {
                restrict: 'EA',
                templateUrl: 'igreja_home.html',
                controller: 'IgrejaHomeDireCtrl',
                controllerAs: 'ctrl',
                scope: {
                    'igreja': '='
                },
                link:function(scope, elem, attrs, ctrl) {
                    scope.$watch('igreja', function(newObj){
                        ctrl.setIgreja(newObj);
                    });
                    
                }
            };
        })
        
        ;
})();

/*******************************************************************************
 * SmartChurchPanel.sociedadeHomeDirective
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.sociedadeHomeDirective', [
            'oc.lazyLoad'
        ])
        
        .run(function($ocLazyLoad) {
            return $ocLazyLoad.load([
                'directives/sociedadehome/sociedadehome_dire.html',
                'directives/sociedadehome/sociedadehome_dire.js',
                'js/services/user_srv.js',
                'js/services/home_srv.js'
            ], { cache: false});
        })

        .directive('sociedadeHome', function() {
            return {
                restrict: 'EA',
                templateUrl: 'sociedade_home.html',
                controller: 'SociedadeHomeDireCtrl',
                controllerAs: 'ctrl',
                scope: {
                    'sociedade': '='
                },
                link:function(scope, elem, attrs, ctrl) {
                    scope.$watch('sociedade', function(newObj){
                        ctrl.setSociedade(newObj);
                    });
                    
                }
            };
        })
        
        ;
})();

/*******************************************************************************
 * SmartChurchPanel.federacaoHomeDirective
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.federacaoHomeDirective', [
            'oc.lazyLoad'
        ])
        
        .run(function($ocLazyLoad) {
            return $ocLazyLoad.load([
                'directives/federacaohome/federacaohome_dire.html',
                'directives/federacaohome/federacaohome_dire.js',
                'js/services/user_srv.js',
                'js/services/home_srv.js'
            ], { cache: false});
        })

        .directive('federacaoHome', function() {
            return {
                restrict: 'EA',
                templateUrl: 'federacao_home.html',
                controller: 'FederacaoHomeDireCtrl',
                controllerAs: 'ctrl',
                scope: {
                    'federacao': '='
                },
                link:function(scope, elem, attrs, ctrl) {
                    scope.$watch('federacao', function(newObj){
                        ctrl.setFederacao(newObj);
                    });
                    
                }
            };
        })
        
        ;
})();

/*******************************************************************************
 * SmartChurchPanel.sinodalHomeDirective
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.sinodalHomeDirective', [
            'oc.lazyLoad'
        ])
        
        .run(function($ocLazyLoad) {
            return $ocLazyLoad.load([
                'directives/sinodalhome/sinodalhome_dire.html',
                'directives/sinodalhome/sinodalhome_dire.js',
                'js/services/user_srv.js',
                'js/services/home_srv.js'
            ], { cache: false});
        })

        .directive('sinodalHome', function() {
            return {
                restrict: 'EA',
                templateUrl: 'sinodal_home.html',
                controller: 'SinodalHomeDireCtrl',
                controllerAs: 'ctrl',
                scope: {
                    'sinodal': '='
                },
                link:function(scope, elem, attrs, ctrl) {
                    scope.$watch('sinodal', function(newObj){
                        ctrl.setSinodal(newObj);
                    });
                    
                }
            };
        })
        
        ;
})();

/*******************************************************************************
 * SmartChurchPanel.agendaDirective
 *******************************************************************************/
(function() {
    'use strict';

    angular
        .module('SmartChurchPanel.agendaDirective', [
            'oc.lazyLoad'
        ])
        
        .run(function($ocLazyLoad) {
            return $ocLazyLoad.load([
                'directives/agenda/agenda_dire.html',
                'directives/agenda/agenda_dire.js'
            ], { cache: false});
        })

        .directive('agendaData', function() {
            return {
                restrict: 'EA',
                templateUrl: 'agenda_data.html',
                controller: 'AgendaDireCtrl',
                controllerAs: 'ctrl',
                scope: {
                    'model': '=',
                    'tipos': '=',
                    'responsaveis': '='
                },
                link:function(scope, elem, attrs, ctrl) {
                    scope.$watch('model', function(newObj){
                        ctrl.setModel(newObj);
                    });
                    scope.$watch('tipos', function(newObj){
                        ctrl.setTiposResponsaveis(newObj);
                    });
                    scope.$watch('responsaveis', function(newObj){
                        ctrl.setResponsaveis(newObj);
                    });
                }
            };
        })
        
        ;
})();