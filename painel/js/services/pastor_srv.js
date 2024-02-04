angular.module('SmartChurchPanel').factory('Pastor', function ($localstorage, $q, $http, ApiEndpoint) {
    
    var create = function(dados) {
        
        var token = $localstorage.get('token');
        var data = JSON.stringify(dados);
        
        var deferred = $q.defer();
        $http
            .post(ApiEndpoint.url+'/pastores/create', data, {
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
            .put(ApiEndpoint.url+'/pastores/edit/'+id, data, {
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
            .put(ApiEndpoint.url+'/pastores/changestat/'+id, data, {
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
            .get(ApiEndpoint.url+'/pastores/me?id='+id, {
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
    
    var getByPessoa = function(pessoa) {
        
        var token = $localstorage.get('token');
        var deferred = $q.defer();
        
        $http
            .get(ApiEndpoint.url+'/pastores/idByPessoa?pessoa='+pessoa, {
                headers: {
                    "Authorization": "token=" + token
                }
            })
            .then(function(r) {
        
                if(r.data.success)
                {
                    deferred.resolve(r.data.id);
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
    
    var getAll = function(page, pagesize, searchBy, orderBy, groupBy, stat, igreja, disponibilidade, pessoa,
                            sexo, estado_civil, escolaridade, com_filhos, sem_filhos) {
        
        var token = $localstorage.get('token');
        var deferred = $q.defer();
        
        var pg = (page == undefined) ? '' : page;
        var ps = (pagesize == undefined) ? '' : pagesize;
        var sb = (searchBy == undefined) ? '' : searchBy;
        var ob = (orderBy == undefined) ? '' : orderBy;
        var gb = (groupBy == undefined) ? '' : groupBy;
        var st = (stat == undefined) ? '' : stat;
        var ig = (igreja == undefined) ? '' : igreja;
        var dp = (disponibilidade == undefined) ? '' : disponibilidade;
        var pe = (pessoa == undefined) ? '' : pessoa;
        var sx = (sexo == undefined) ? '' : sexo;
        var ec = (estado_civil == undefined) ? '' : estado_civil;
        var es = (escolaridade == undefined) ? '' : escolaridade;
        var cf = (com_filhos == undefined) ? '' : com_filhos;
        var sf = (sem_filhos == undefined) ? '' : sem_filhos;
        
        $http
            .get(ApiEndpoint.url+'/pastores/all?page='+pg+'&pageSize='+ps+'&searchBy='+sb+
                        '&orderBy='+ob+'&groupBy='+gb+'&stat='+st+'&igreja='+ig+
                        '&disponibilidade='+dp+'&pessoa='+pe+
                        '&sexo='+sx+'&estado_civil='+ec+'&escolaridade='+es+
                        '&com_filhos='+cf+'&sem_filhos='+sf, {
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
    
    var getAllEmServicoForIgreja = function(igreja)
    {
        return getAll('', '', '', '', '', Status.ATIVO, igreja, Disponibilidade.DISPONIVEL);
    };
    
    var getAllForIgreja = function(igreja)
    {
        return getAll('', '', '', '', '', '', igreja);
    };
    
    var remove = function(id) {
        
        var token = $localstorage.get('token');
        
        var deferred = $q.defer();
        $http
            .delete(ApiEndpoint.url+'/pastores/remove/'+id, {
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
            .post(ApiEndpoint.url+'/pastores/removeAll', data, {
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
        edit: edit,
        changeStat: changeStat,
        getMe: getMe,
        getByPessoa: getByPessoa,
        getAll: getAll,
        getAllEmServicoForIgreja: getAllEmServicoForIgreja,
        getAllForIgreja: getAllForIgreja,
        remove: remove,
        removeAll: removeAll
    };
});


