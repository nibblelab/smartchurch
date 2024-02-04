angular.module('SmartChurchPanel').factory('Integracao', function ($localstorage, $q, $http, ApiEndpoint) {
    
    var getForIgreja = function() {
        
        var token = $localstorage.get('token');
        var deferred = $q.defer();
        
        $http
            .get(ApiEndpoint.url+'/integracao/tokenForIgreja', {
                headers: {
                    "Authorization": "token=" + token
                }
            })
            .then(function(r) {
        
                if(r.data.success)
                {
                    deferred.resolve(r.data.token);
                }
                else
                {
                    deferred.reject(r.data.msg);
                }
            }, 
            function(r) {
                deferred.reject('Não foi possível realizar a operação :( erro #'+r.status);
            });
        
        return deferred.promise;
    };
    
    var getForSinodal = function() {
        
        var token = $localstorage.get('token');
        var deferred = $q.defer();
        
        $http
            .get(ApiEndpoint.url+'/integracao/tokenForSinodal', {
                headers: {
                    "Authorization": "token=" + token
                }
            })
            .then(function(r) {
        
                if(r.data.success)
                {
                    deferred.resolve(r.data.token);
                }
                else
                {
                    deferred.reject(r.data.msg);
                }
            }, 
            function(r) {
                deferred.reject('Não foi possível realizar a operação :( erro #'+r.status);
            });
        
        return deferred.promise;
    };
    
    return {
        getForIgreja: getForIgreja,
        getForSinodal: getForSinodal
    };
});


