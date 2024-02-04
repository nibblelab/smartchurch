angular.module('SmartChurchPanel').controller('TokenIgrejaCtrl', function ($scope, $state, $stateParams, $localstorage, $notifications, $rootScope, $dialogs, $timeout, 
                    $context, 
                    Integracao) { 
    
    /* config */
    $scope.opTitle = $stateParams.title;
    $scope.perms = $stateParams.perms;
    $scope.objForm = 'Token';
    $scope.igreja = $context.getIgrejaContext();
    $scope.igrejaData = $rootScope.USER.getContextByKeyAndId(Contexts.IGREJAS, $scope.igreja);
    
    /* verificação de permissão do state */
    if(!$scope.USER.doIHaveAccess($scope.user, $scope.perms)) {
        $state.go('SmartChurchPanel.sempermissao');
    }
    
    $scope.localPerms = {
        add: '',
        edit: '',
        changeStat: '',
        remove: ''
    };
    
    /* form */
    $scope.dataFrm = {
        data: {
            id: { value: $scope.igreja, notEmpty: false, valid: true, StringfyFrom: '' },
            token: { value: '', notEmpty: false, valid: true, StringfyFrom: '' }
        }
    };
    
    Integracao.getForIgreja().then(function(r) {
        $scope.dataFrm.data.token.value = r;
    }, function(e) { 
        $notifications.err(e);
    });
    
    
});


