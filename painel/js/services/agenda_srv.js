angular.module('SmartChurchPanel').factory('Agenda', function ($localstorage, $q, $http, ApiEndpoint) {
    
    var create = function(dados) {
        
        var token = $localstorage.get('token');
        var data = JSON.stringify(dados);
        
        var deferred = $q.defer();
        $http
            .post(ApiEndpoint.url+'/agendas/create', data, {
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
            .post(ApiEndpoint.url+'/agendas/createForIgreja', data, {
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
    
    var createForFederacao = function(dados) {
        
        var token = $localstorage.get('token');
        var data = JSON.stringify(dados);
        
        var deferred = $q.defer();
        $http
            .post(ApiEndpoint.url+'/agendas/createForFederacao', data, {
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
    
    var createForSinodal = function(dados) {
        
        var token = $localstorage.get('token');
        var data = JSON.stringify(dados);
        
        var deferred = $q.defer();
        $http
            .post(ApiEndpoint.url+'/agendas/createForSinodal', data, {
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
            .put(ApiEndpoint.url+'/agendas/edit/'+id, data, {
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
            .put(ApiEndpoint.url+'/agendas/changestat/'+id, data, {
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
            .get(ApiEndpoint.url+'/agendas/me?id='+id, {
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
    
    var getAll = function(page, pagesize, searchBy, orderBy, groupBy, stat, ref, ref_tp, responsavel, inicio, termino,
                            recorrente, domingo, segunda, terca, quarta, quinta, sexta, sabado, tags, igreja, 
                            federacao, sinodal) {
        
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
        var rs = (responsavel == undefined) ? '' : responsavel;
        var ic = (inicio == undefined) ? '' : inicio;
        var tm = (termino == undefined) ? '' : termino;
        var rr = (recorrente == undefined) ? '' : recorrente;
        var dm = (domingo == undefined) ? '' : domingo;
        var sg = (segunda == undefined) ? '' : segunda;
        var tr = (terca == undefined) ? '' : terca;
        var qa = (quarta == undefined) ? '' : quarta;
        var qu = (quinta == undefined) ? '' : quinta;
        var sx = (sexta == undefined) ? '' : sexta;
        var sa = (sabado == undefined) ? '' : sabado;
        var ta = (tags == undefined) ? '' : tags;
        var ig = (igreja == undefined) ? '' : igreja;
        var fd = (federacao == undefined) ? '' : federacao;
        var sd = (sinodal == undefined) ? '' : sinodal;
        
        $http
            .get(ApiEndpoint.url+'/agendas/all?page='+pg+'&pageSize='+ps+'&searchBy='+sb+
                        '&orderBy='+ob+'&groupBy='+gb+'&stat='+st+'&ref='+rf+'&ref_tp='+rt+
                        '&responsavel='+rs+'&inicio='+ic+'&termino='+tm+'&recorrente='+rr+
                        '&domingo='+dm+'&segunda='+sg+'&terca='+tr+'&quarta='+qa+
                        '&quinta='+qu+'&sexta='+sx+'&sabado='+sa+'&tags='+ta+
                        '&igreja='+ig+'&federacao='+fd+'&sinodal='+sd, {
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
    
    var getAllForIgreja = function(page, pagesize, searchBy, orderBy, groupBy, stat, igreja, responsavel, inicio, termino,
                            recorrente, domingo, segunda, terca, quarta, quinta, sexta, sabado, tags) {
        return getAll(page, pagesize, searchBy, orderBy, groupBy, stat, '', '', responsavel, inicio, termino,
                            recorrente, domingo, segunda, terca, quarta, quinta, sexta, sabado, tags, igreja);
    };
    
    var getAllForFederacao = function(page, pagesize, searchBy, orderBy, groupBy, stat, federacao, responsavel, inicio, termino,
                            recorrente, domingo, segunda, terca, quarta, quinta, sexta, sabado, tags) {
        return getAll(page, pagesize, searchBy, orderBy, groupBy, stat, '', '', responsavel, inicio, termino,
                            recorrente, domingo, segunda, terca, quarta, quinta, sexta, sabado, tags, '', federacao);
    };
    
    var getAllForSinodal = function(page, pagesize, searchBy, orderBy, groupBy, stat, sinodal, responsavel, inicio, termino,
                            recorrente, domingo, segunda, terca, quarta, quinta, sexta, sabado, tags) {
        return getAll(page, pagesize, searchBy, orderBy, groupBy, stat, '', '', responsavel, inicio, termino,
                            recorrente, domingo, segunda, terca, quarta, quinta, sexta, sabado, tags, '', '', sinodal);
    };
    
    var remove = function(id) {
        
        var token = $localstorage.get('token');
        
        var deferred = $q.defer();
        $http
            .delete(ApiEndpoint.url+'/agendas/remove/'+id, {
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
            .post(ApiEndpoint.url+'/agendas/removeAll', data, {
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
        createForFederacao: createForFederacao,
        createForSinodal: createForSinodal,
        edit: edit,
        changeStat: changeStat,
        getMe: getMe,
        getAll: getAll,
        getAllForIgreja: getAllForIgreja,
        getAllForFederacao: getAllForFederacao,
        getAllForSinodal: getAllForSinodal,
        remove: remove,
        removeAll: removeAll
    };
});


