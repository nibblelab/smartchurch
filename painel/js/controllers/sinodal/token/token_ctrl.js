angular.module('SmartChurchPanel').controller('TokenSinodalCtrl', function ($scope, $state, $stateParams, $localstorage, $notifications, $rootScope, $dialogs, $timeout, 
                    $context, 
                    Integracao) { 
    
    /* config */
    $scope.opTitle = $stateParams.title;
    $scope.perms = $stateParams.perms;
    $scope.objForm = 'Token';
    $scope.sinodal = $context.getSinodalContext();
    $scope.sinodalData = $rootScope.USER.getContextByKeyAndId(Contexts.SINODAIS, $scope.sinodal);
    
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
            id: { value: $scope.sinodal, notEmpty: false, valid: true, StringfyFrom: '' },
            token: { value: '', notEmpty: false, valid: true, StringfyFrom: '' }
        }
    };
    
    Integracao.getForSinodal().then(function(r) {
        $scope.dataFrm.data.token.value = r;
    }, function(e) { 
        $notifications.err(e);
    });
    
    
});


