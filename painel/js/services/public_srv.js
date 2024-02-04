
angular.module('SmartChurchPanel').factory('Public', function ($localstorage, $q, $http, ApiEndpoint) {
    
    var login = function(email, pass) {

        var deferred = $q.defer();
        $http
            .get(ApiEndpoint.url+'/public/login?email='+email+'&pass='+pass+'&is_api=false')
            .then(function(r) {
                if(r.data.success)
                {
                    $localstorage.setObject('user', r.data.data);
                    $localstorage.set('token', r.data.token);
                    deferred.resolve();
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
    
    var requestNewPwd = function(email) {
        var deferred = $q.defer();
        $http
            .get(ApiEndpoint.url+'/public/requestPwdReset?email='+email)
            .then(function(r) {
                if(r.data.success)
                {
                    deferred.resolve();
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
    
    var resetPwd = function(dados) {
        
        var data = JSON.stringify(dados);
        
        var deferred = $q.defer();
        $http
            .post(ApiEndpoint.url+'/public/resetPwd', data)
            .then(function(r) {
        
                if(r.data.success)
                {
                    deferred.resolve();
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
    
    var register = function(dados) {
        
        var data = JSON.stringify(dados);
        
        var deferred = $q.defer();
        $http
            .post(ApiEndpoint.url+'/public/register', data)
            .then(function(r) {
        
                if(r.data.success)
                {
                    deferred.resolve();
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
    
    var termos = function() {
        
        var deferred = $q.defer();
        $http
            .get(ApiEndpoint.url+'/public/termos')
            .then(function(r) {
                if(r.data.success)
                {
                    deferred.resolve(r.data.data);
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
        login: login,
        requestNewPwd: requestNewPwd,
        resetPwd: resetPwd,
        register: register,
        termos: termos
    };
});

