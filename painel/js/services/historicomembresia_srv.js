angular.module('SmartChurchPanel').factory('HistoricoDeMembresia', function ($localstorage, $q, $http, ApiEndpoint) {
    
    var create = function(dados) {
        
        var token = $localstorage.get('token');
        var data = JSON.stringify(dados);
        
        var deferred = $q.defer();
        $http
            .post(ApiEndpoint.url+'/historicomembresia/create', data, {
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
            .put(ApiEndpoint.url+'/historicomembresia/edit/'+id, data, {
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
            .put(ApiEndpoint.url+'/historicomembresia/changestat/'+id, data, {
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
            .get(ApiEndpoint.url+'/historicomembresia/me?id='+id, {
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
    
    var getAssociacaoByPessoa = function(pessoa) {
        
        var token = $localstorage.get('token');
        var deferred = $q.defer();
        
        $http
            .get(ApiEndpoint.url+'/historicomembresia/associacaobypessoa?pessoa='+pessoa, {
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
    
    var check = function(pessoa, igreja) {
        
        var token = $localstorage.get('token');
        var deferred = $q.defer();
        
        $http
            .get(ApiEndpoint.url+'/historicomembresia/check?pessoa='+pessoa+'&igreja='+igreja, {
                headers: {
                    "Authorization": "token=" + token
                }
            })
            .then(function(r) {
        
                if(r.data.success)
                {
                    deferred.resolve({exists: r.data.exists});
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
    
    var getAll = function(page, pagesize, searchBy, orderBy, groupBy, stat, igreja, comungante, nao_comungante, especial, 
                            criancas, adolescentes, jovens, adultos, idosos, aniversariantes, sexo,
                            escolaridade, tem_filhos, sem_filhos, estado_civil, arrolado, nao_arrolado, pessoa, exceto) {
        
        var token = $localstorage.get('token');
        var deferred = $q.defer();
        
        var pg = (page == undefined) ? '' : page;
        var ps = (pagesize == undefined) ? '' : pagesize;
        var sb = (searchBy == undefined) ? '' : searchBy;
        var ob = (orderBy == undefined) ? '' : orderBy;
        var gb = (groupBy == undefined) ? '' : groupBy;
        var st = (stat == undefined) ? '' : stat;
        var ig = (igreja == undefined) ? '' : igreja;
        var cm = (comungante == undefined) ? '' : comungante;
        var nc = (nao_comungante == undefined) ? '' : nao_comungante;
        var es = (especial == undefined) ? '' : especial;
        var cr = (criancas == undefined) ? '' : criancas;
        var ad = (adolescentes == undefined) ? '' : adolescentes;
        var jv = (jovens == undefined) ? '' : jovens;
        var al = (adultos == undefined) ? '' : adultos;
        var od = (idosos == undefined) ? '' : idosos;
        var nv = (aniversariantes == undefined) ? '' : aniversariantes;
        var sx = (sexo == undefined) ? '' : sexo;
        var ec = (escolaridade == undefined) ? '' : escolaridade;
        var tf = (tem_filhos == undefined) ? '' : tem_filhos;
        var sf = (sem_filhos == undefined) ? '' : sem_filhos;
        var et = (estado_civil == undefined) ? '' : estado_civil;
        var ar = (arrolado == undefined) ? '' : arrolado;
        var na = (nao_arrolado == undefined) ? '' : nao_arrolado;
        var pe = (pessoa == undefined) ? '' : pessoa;
        var ex = (exceto == undefined) ? '' : exceto;
        
        $http
            .get(ApiEndpoint.url+'/historicomembresia/all?page='+pg+'&pageSize='+ps+'&searchBy='+sb+
                        '&orderBy='+ob+'&groupBy='+gb+'&stat='+st+'&igreja='+ig+'&comungante='+cm+
                        '&nao_comungante='+nc+'&especial='+es+'&criancas='+cr+'&adolescentes='+ad+
                        '&jovens='+jv+'&adultos='+al+'&idosos='+od+'&aniversariantes='+nv+
                        '&sexo='+sx+'&escolaridade='+ec+'&tem_filhos='+tf+'&sem_filhos='+sf+
                        '&estado_civil='+et+'&arrolado='+ar+'&nao_arrolado='+na+'&pessoa='+pe+
                        '&exceto='+ex, {
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
    
    var remove = function(id) {
        
        var token = $localstorage.get('token');
        
        var deferred = $q.defer();
        $http
            .delete(ApiEndpoint.url+'/historicomembresia/remove/'+id, {
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
            .post(ApiEndpoint.url+'/historicomembresia/removeAll', data, {
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
        getAssociacaoByPessoa: getAssociacaoByPessoa,
        check: check,
        getAll: getAll,
        remove: remove,
        removeAll: removeAll
    };
});


