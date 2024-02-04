angular.module('SmartChurchPanel').controller('HomeCtrl', function ($scope, $state, $context, $rootScope, $smartapp, $cache, Data, User, Home) { 
    
    /* contextos */
    $rootScope.igrejasContextos = User.getContextsByKey(Contexts.IGREJAS);
    $rootScope.conselhosContextos = User.getContextsByKey(Contexts.CONSELHOS);
    $rootScope.juntasContextos = User.getContextsByKey(Contexts.JUNTAS);
    $rootScope.sociedadesContextos = User.getContextsByKey(Contexts.SOCIEDADES);
    $rootScope.federacoesContextos = User.getContextsByKey(Contexts.FEDERACOES);
    $rootScope.sinodaisContextos = User.getContextsByKey(Contexts.SINODAIS);
    $rootScope.secretariasContextos = User.getContextsByKey(Contexts.SECRETARIAS);
    $rootScope.ministeriosContextos = User.getContextsByKey(Contexts.MINISTERIOS);
    $rootScope.pastoresContextos = User.getContextsByKey(Contexts.PASTORES);
    $rootScope.evangelistasContextos = User.getContextsByKey(Contexts.EVANGELISTAS);
    $rootScope.ebdsContextos = User.getContextsByKey(Contexts.EBDS);
    
    /* setup cache */
    Data.getAll().then(function(r) {
        $cache.set(r);
    }, function(e) { });
    
    
    /****************************************************
     *      CONTEXTO DE IGREJA
     ****************************************************/
    
    $scope.igreja = $context.getIgrejaContext();
    $scope.activeIgrejaContext = function(ctx) {
        $context.end();
        User.getTokenForInstancia($scope.user.id, ctx.instancia, ctx.contexto).then(function() {
            $context.setIgrejaContext(ctx.id);
            $context.addContextMenu(Menus.IGREJA);
            $state.reload();
        }, function() {});
    };
    
    /****************************************************
     *      CONTEXTO DE PASTOR
     ****************************************************/
    
    $scope.pastor = $context.getPastorContext();
    $scope.activePastorContext = function(ctx) {
        $context.end();
        User.getTokenForInstancia($scope.user.id, ctx.instancia, ctx.contexto).then(function() {
            $context.setPastorContext(ctx.id);
            $context.addContextMenu(Menus.PASTOR);
            $state.reload();
        }, function() {});
    };
    
    /****************************************************
     *      CONTEXTO DE EVANGELISTA
     ****************************************************/
    
    $scope.evangelista = $context.getEvangelistaContext();
    $scope.activeEvangelistaContext = function(ctx) {
        $context.end();
        User.getTokenForInstancia($scope.user.id, ctx.instancia, ctx.contexto).then(function() {
            $context.setEvangelistaContext(ctx.id);
            $context.addContextMenu(Menus.EVANGELISTA);
            $state.reload();
        }, function() {});
    };
        
    /****************************************************
     *      CONTEXTO DE CONSELHO
     ****************************************************/
    
    $scope.conselho = $context.getConselhoContext();
    $scope.activeConselhoContext = function(ctx) {
        $context.end();
        User.getTokenForInstancia($scope.user.id, ctx.instancia, ctx.contexto).then(function() {
            $context.setConselhoContext(ctx.id);
            $state.reload();
        }, function() {});
    };
    
    /****************************************************
     *      CONTEXTO DE JUNTA DIACONAL
     ****************************************************/
    
    $scope.junta = $context.getJuntaContext();
    $scope.activeJuntaContext = function(ctx) {
        $context.end();
        User.getTokenForInstancia($scope.user.id, ctx.instancia, ctx.contexto).then(function() {
            $context.setJuntaContext(ctx.id);
            $state.reload();
        }, function() {});
    };
    
    /****************************************************
     *      CONTEXTO DE SOCIEDADE INTERNA
     ****************************************************/
    $scope.sociedade = $context.getSociedadeContext();
    $scope.activeSociedadeContext = function(ctx) {
        $context.end();
        User.getTokenForInstancia($scope.user.id, ctx.instancia, ctx.contexto).then(function() {
            $context.setSociedadeContext(ctx.id);
            $context.addContextMenu(Menus.SOCIEDADE);
            $state.reload();
        }, function() {});
    };
    
    
    /****************************************************
     *      CONTEXTO DE FEDERAÇÃO
     ****************************************************/
    $scope.federacao = $context.getFederacaoContext();
    $scope.activeFederacaoContext = function(ctx) {
        $context.end();
        User.getTokenForInstancia($scope.user.id, ctx.instancia, ctx.contexto).then(function() {
            $context.setFederacaoContext(ctx.id);
            $context.addContextMenu(Menus.FEDERACAO);
            $state.reload();
        }, function() {});
    };
    
    /****************************************************
     *      CONTEXTO DE SINODAL
     ****************************************************/
    $scope.sinodal = $context.getSinodalContext();
    $scope.activeSinodalContext = function(ctx) {
        $context.end();
        User.getTokenForInstancia($scope.user.id, ctx.instancia, ctx.contexto).then(function() {
            $context.setSinodalContext(ctx.id);
            $context.addContextMenu(Menus.SINODAL);
            $state.reload();
        }, function() {});
    };
    
    /****************************************************
     *      CONTEXTO DE SECRETARIA
     ****************************************************/
    $scope.secretaria = $context.getSecretariaContext();
    $scope.activeSecretariaContext = function(ctx) {
        $context.end();
        User.getTokenForInstancia($scope.user.id, ctx.instancia, ctx.contexto).then(function() {
            $context.setSecretariaContext(ctx.id);
            $state.reload();
        }, function() {});
    };
    
    /****************************************************
     *      CONTEXTO DE MINISTÉRIO
     ****************************************************/
    $scope.ministerio = $context.getMinisterioContext();
    $scope.activeMinisterioContext = function(ctx) {
        $context.end();
        User.getTokenForInstancia($scope.user.id, ctx.instancia, ctx.contexto).then(function() {
            $context.setMinisterioContext(ctx.id);
            $context.addContextMenu(Menus.MINISTERIO);
            $state.reload();
        }, function() {});
    };
    
    /****************************************************
     *      CONTEXTO DE EBD
     ****************************************************/
    $scope.ebd = $context.getEbdContext();
    $scope.activeEbdContext = function(ctx) {
        $context.end();
        User.getTokenForInstancia($scope.user.id, ctx.instancia, ctx.contexto).then(function() {
            $context.setEbdContext(ctx.id);
            $context.addContextMenu(Menus.EBD);
            $state.reload();
        }, function() {});
    };
    
    /****************************************************
     *      SMARTAPP - BACKUP WEB DO APP SMARTCHURCH
     ****************************************************/
    if(!$context.hasActive() && !User.amIAdmin($scope.user)) {
        if($smartapp.checkViews()) {
            try {
                if($smartapp.getIgrejaView() == '' && User.getMembresiaData().igreja != '') {
                    // smartapp igreja
                    $smartapp.setIgrejaView(User.getMembresiaData().igreja);
                }
                if($smartapp.getSociedadeView() == '' && User.getSociedadeData().id != '') {
                    // smartapp sociedade
                    $smartapp.setSociedadeView(User.getSociedadeData().id);
                }
            }
            catch(e) { }
        }
    }
    else {
        // staff ou admin com contexto ativo não podem acessar funções de administração e smartapp ao mesmo tempo
        $smartapp.end();
    }
    
});


