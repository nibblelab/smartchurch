angular.module('SmartChurchPanel').controller('TermosCtrl', function ($scope, $state, $dialogs, $notifications, $location, $sce, Public) { 
    
    
    $scope.termos = { texto: ''};
    Public.termos().then(function(r) {
        $scope.termos.texto = $sce.trustAsHtml(r);
    }, function() {});
    
});


