angular.module('SmartChurchPanel').controller('TransmissaoCtrl', function ($scope, $state, $stateParams, $notifications, $rootScope, 
                    $sce,
                    Transmissao) { 
    
    /* config */
    $scope.opTitle = $stateParams.title;
    $scope.perms = $stateParams.perms;
    $scope.id = $stateParams.id;
    $scope.objList = 'Transmissão';
    $scope.igreja = $rootScope.USER.getMembresiaData().igreja;
    $scope.isCreate = false;

    /* verificação de permissão do state */
    if(!$scope.USER.doIHaveAccess($scope.user, $scope.perms)) {
        $state.go('SmartChurchPanel.sempermissao');
    }
    
    $scope.searchTransmissoesDaIgreja = function() {
        Transmissao.getAllForIgreja('', '', '', 'time_cad,desc', '', Status.ATIVO, $scope.igreja).then(function(r) {
            if (r.datas.length > 0) {
                for (var k in r.datas) {
                    
                    r.datas[k]['videoData'] = {
                        url: '',
                        type: ''
                    };
                    if(r.datas[k].video.includes('youtube')) {
                        var found = r.datas[k].video.match(/youtube\.com\/watch\?v=([\da-zA-Z_\-]*)/i);
                        if(found != null && found.length > 1) {
                            r.datas[k].videoData.url = $sce.trustAsResourceUrl('https://www.youtube.com/embed/' + found[1]);
                            r.datas[k].videoData.type = 'Youtube';
                        }
                    }
                    else if(r.datas[k].video.includes('youtu.be')) {
                        var found  = r.datas[k].video.match(/youtu\.be\/([\da-zA-Z_\-]*)/i);
                        if(found != null && found.length > 1) {
                            r.datas[k].videoData.url = $sce.trustAsResourceUrl('https://www.youtube.com/embed/' + found[1]);
                            r.datas[k].videoData.type = 'Youtube';
                        }
                    }
                }
                
                $scope.list = $scope.list.concat(r.datas);
            }
        }, function() {
            
        });
    };
    
    $scope.doSearch = function() {
        $scope.list = [];
        $scope.searchTransmissoesDaIgreja();
    };
    $scope.doSearch();
    
    
});


