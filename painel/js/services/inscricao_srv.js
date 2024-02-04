angular.module('SmartChurchPanel').factory('Inscricao', function ($localstorage, $q, $http, ApiEndpoint) {
    
    var create = function(dados) {
        
        var token = $localstorage.get('token');
        var data = JSON.stringify(dados);
        
        var deferred = $q.defer();
        $http
            .post(ApiEndpoint.url+'/inscricoes/create', data, {
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
            .put(ApiEndpoint.url+'/inscricoes/edit/'+id, data, {
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
            .put(ApiEndpoint.url+'/inscricoes/changestat/'+id, data, {
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
            .get(ApiEndpoint.url+'/inscricoes/me?id='+id, {
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
    
    var getAll = function(page, pagesize, searchBy, orderBy, groupBy, stat, evento, pessoa, stat_pagto, forma_pagto,
                            cargo, delegado, sinodal, federacao, sociedade, sexo, estado_civil, sinodo, presbiterio, 
                            igreja) {
        
        var token = $localstorage.get('token');
        var deferred = $q.defer();
        
        var pg = (page == undefined) ? '' : page;
        var ps = (pagesize == undefined) ? '' : pagesize;
        var sb = (searchBy == undefined) ? '' : searchBy;
        var ob = (orderBy == undefined) ? '' : orderBy;
        var gb = (groupBy == undefined) ? '' : groupBy;
        var ev = (evento == undefined) ? '' : evento;
        var st = (stat == undefined) ? '' : stat;
        var pe = (pessoa == undefined) ? '' : pessoa;
        var sp = (stat_pagto == undefined) ? '' : stat_pagto;
        var fp = (forma_pagto == undefined) ? '' : forma_pagto;
        var ca = (cargo == undefined) ? '' : cargo;
        var dl = (delegado == undefined) ? '' : delegado;
        var si = (sinodal == undefined) ? '' : sinodal;
        var fe = (federacao == undefined) ? '' : federacao;
        var so = (sociedade == undefined) ? '' : sociedade;
        var sx = (sexo == undefined) ? '' : sexo;
        var ec = (estado_civil == undefined) ? '' : estado_civil;
        var sn = (sinodo == undefined) ? '' : sinodo;
        var pb = (presbiterio == undefined) ? '' : presbiterio;
        var ig = (igreja == undefined) ? '' : igreja;
        
        $http
            .get(ApiEndpoint.url+'/inscricoes/all?page='+pg+'&pageSize='+ps+'&searchBy='+sb+
                        '&orderBy='+ob+'&groupBy='+gb+'&evento='+ev+'&stat='+st+'&pessoa='+pe+
                        '&stat_pagto='+sp+'&forma_pagto='+fp+'&cargo='+ca+'&delegado='+dl+
                        '&sinodal='+si+'&federacao='+fe+'&sociedade='+so+'&sexo='+sx+
                        '&estado_civil='+ec+'&sinodo='+sn+'&presbiterio='+pb+'&igreja='+ig, {
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
    
    var getResponsavelKey = function(evento_id, credencial_id) {
        
        var token = $localstorage.get('token');
        var deferred = $q.defer();
        
        $http
            .get(ApiEndpoint.url+'/inscricoes/chave_responsavel?evento_id='+evento_id+'&credencial_id='+credencial_id, {
                headers: {
                    "Authorization": "token=" + token
                }
            })
            .then(function(r) {
        
                if(r.data.success)
                {
                    deferred.resolve({ chave: r.data.chave });
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
    
    var remove = function(id) {
        
        var token = $localstorage.get('token');
        
        var deferred = $q.defer();
        $http
            .delete(ApiEndpoint.url+'/inscricoes/remove/'+id, {
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
            .post(ApiEndpoint.url+'/inscricoes/removeAll', data, {
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
        getAll: getAll,
        getResponsavelKey: getResponsavelKey,
        remove: remove,
        removeAll: removeAll
    };
});


