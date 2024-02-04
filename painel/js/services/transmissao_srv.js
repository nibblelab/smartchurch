angular.module('SmartChurchPanel').factory('Transmissao', function ($localstorage, $q, $http, ApiEndpoint) {
    
    var create = function(dados) {
        
        var token = $localstorage.get('token');
        var data = JSON.stringify(dados);
        
        var deferred = $q.defer();
        $http
            .post(ApiEndpoint.url+'/transmissoes/create', data, {
                headers: {
                    "Authorization": "token=" + token
                }
            })
            .then(function(r) {
        
                if(r.data.success)
                {
                    deferred.resolve({
                        id: r.data.id
                    });
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
    
    var createForIgreja = function(dados) {
        
        var token = $localstorage.get('token');
        var data = JSON.stringify(dados);
        
        var deferred = $q.defer();
        $http
            .post(ApiEndpoint.url+'/transmissoes/createForIgreja', data, {
                headers: {
                    "Authorization": "token=" + token
                }
            })
            .then(function(r) {
        
                if(r.data.success)
                {
                    deferred.resolve({
                        id: r.data.id
                    });
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
    
    var edit = function(id, dados) {
        
        var token = $localstorage.get('token');
        var data = JSON.stringify(dados);
        
        var deferred = $q.defer();
        $http
            .put(ApiEndpoint.url+'/transmissoes/edit/'+id, data, {
                headers: {
                    "Authorization": "token=" + token
                }
            })
            .then(function(r) {
        
                if(r.data.success)
                {
                    deferred.resolve({
                        id: r.data.id
                    });
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
    
    var changeStat = function(id, dados) {
        
        var token = $localstorage.get('token');
        var data = JSON.stringify(dados);
        
        var deferred = $q.defer();
        $http
            .put(ApiEndpoint.url+'/transmissoes/changestat/'+id, data, {
                headers: {
                    "Authorization": "token=" + token
                }
            })
            .then(function(r) {
        
                if(r.data.success)
                {
                    deferred.resolve({
                        stat: r.data.stat
                    });
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
    
    var getMe = function(id) {
        
        var token = $localstorage.get('token');
        var deferred = $q.defer();
        
        $http
            .get(ApiEndpoint.url+'/transmissoes/me?id='+id, {
                headers: {
                    "Authorization": "token=" + token
                }
            })
            .then(function(r) {
        
                if(r.data.success)
                {
                    deferred.resolve(r.data.datas);
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
    
    var getAll = function(page, pagesize, searchBy, orderBy, groupBy, stat, ref, ref_tp, igreja) {
        
        var token = $localstorage.get('token');
        var deferred = $q.defer();
        
        var pg = (page == undefined) ? '' : page;
        var ps = (pagesize == undefined) ? '' : pagesize;
        var sb = (searchBy == undefined) ? '' : searchBy;
        var ob = (orderBy == undefined) ? '' : orderBy;
        var gb = (groupBy == undefined) ? '' : groupBy;
        var st = (stat == undefined) ? '' : stat;
        var rf = (ref == undefined) ? '' : ref;
        var rt = (ref_tp == undefined) ? '' : ref_tp;
        var ig = (igreja == undefined) ? '' : igreja;
        
        $http
            .get(ApiEndpoint.url+'/transmissoes/all?page='+pg+'&pageSize='+ps+'&searchBy='+sb+
                        '&orderBy='+ob+'&groupBy='+gb+'&stat='+st+'&ref='+rf+
                        '&ref_tp='+rt+'&igreja='+ig, {
                headers: {
                    "Authorization": "token=" + token
                }
            })
            .then(function(r) {
        
                if(r.data.success)
                {
                    deferred.resolve({ datas: r.data.datas, total: r.data.total });
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
    
    var getAllForIgreja = function(page, pagesize, searchBy, orderBy, groupBy, stat, igreja) {
        return getAll(page, pagesize, searchBy, orderBy, groupBy, stat, '', '', igreja);
    };
    
    var remove = function(id) {
        
        var token = $localstorage.get('token');
        var deferred = $q.defer();
        
        $http
            .delete(ApiEndpoint.url+'/transmissoes/remove/'+id, {
                headers: {
                    "Authorization": "token=" + token
                }
            })
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
    
    var removeAll = function(dados) {
        
        var token = $localstorage.get('token');
        var data = JSON.stringify(dados);
        
        var deferred = $q.defer();
        $http
            .post(ApiEndpoint.url+'/transmissoes/removeAll', data, {
                headers: {
                    "Authorization": "token=" + token
                }
            })
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
    
    return {
        create: create,
        createForIgreja: createForIgreja,
        edit: edit,
        changeStat: changeStat,
        getMe: getMe,
        getAll: getAll,
        getAllForIgreja: getAllForIgreja,
        remove: remove,
        removeAll: removeAll
    };
});


