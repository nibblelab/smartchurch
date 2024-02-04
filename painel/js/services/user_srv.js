angular.module('SmartChurchPanel').factory('User', function ($localstorage, $q, $http, $context, $smartapp, ApiEndpoint) {
    
    var logout = function() {
        var deferred = $q.defer();
        $localstorage.setObject('user', {});
        $localstorage.set('token', '');
        $context.end();
        $smartapp.end();
        deferred.resolve();
        return deferred.promise;
    };
    
    var updateMe = function(nome,email,avatar) {
        var usr = $localstorage.getObject('user', {});
        usr.nome = nome;
        usr.email = email;
        usr.avatar = avatar;
        $localstorage.setObject('user', usr);
    };
    
    var updateModulos = function(modulos) {
        var usr = $localstorage.getObject('user', {});
        usr.modulos = modulos;
        $localstorage.setObject('user', usr);
    };
    
    var updatePermissoes = function(permissoes) {
        var usr = $localstorage.getObject('user', {});
        usr.perms = permissoes;
        $localstorage.setObject('user', usr);
    };
    
    var saveMe = function(dados) {
        
        var token = $localstorage.get('token');
        var data = JSON.stringify(dados);
        
        var deferred = $q.defer();
        $http
            .post(ApiEndpoint.url+'/usuarios/saveme', data, {
                headers: {
                    "Authorization": "token=" + token
                }
            })
            .then(function(r) {
        
                if(r.data.success)
                {
                    updateMe(r.data.nome, r.data.email, r.data.avatar);
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
    
    var getTokenForInstancia = function(user_id, instancia_id, contexto_id) {
        
        var token = $localstorage.get('token');
        
        var deferred = $q.defer();
        $http
            .get(ApiEndpoint.url+'/usuarios/tokeninstancia?id='+user_id+'&instancia='+instancia_id+
                                    '&contexto='+contexto_id, {
                headers: {
                    "Authorization": "token=" + token
                }
            })
            .then(function(r) {
        
                if(r.data.success)
                {
                    $localstorage.set('token', r.data.token);
                    updateModulos(r.data.modulos);
                    updatePermissoes(r.data.perms);
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
    
    var checkByNome = function(nome) {
        
        var token = $localstorage.get('token');
        
        var deferred = $q.defer();
        $http
            .get(ApiEndpoint.url+'/usuarios/checkbynome?nome='+nome, {
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
    
    var get = function() {
        var usr = $localstorage.getObject('user', {});
        return usr;
    };
    
    var isLogged = function() {
        var usr = $localstorage.getObject('user', {});
        return !$.isEmptyObject(usr);
    };
    
    var canIAccessThis = function(me, perms, mods, setted) {
        
        var at_least_one = false;
        for(var k in mods) {
            if(me.modulos.indexOf('|'+mods[k]+':') >= 0) {
                at_least_one = true;
            }
        }
        
        if(!at_least_one) {
            return false;
        }
        
        for(var k in perms) {
            var finded = me.perms.filter(function(x) { return x.nome == perms[k]; });
                        
            if(finded.length > 0) {
                /* teste a obrigação de alguma variável de controle setada */
                if(setted == undefined || setted.length == 0) {
                    return true;
                }
                else {
                    for(var j in setted) {
                        var v = $localstorage.get(setted[j], '');
                        if(v != '') {
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    };
    
    var doIHavePermission = function(me, perm, mod) {
        var finded = me.perms.filter(function(x) { return x.nome == perm; });
        if(finded.length > 0) {
            if(mod != undefined) {
                if(me.modulos.indexOf('|'+mod+':') >= 0){
                    return true;
                }
            }
            else {
                return true;
            }
        }

        return false;
    };
    
    var doIHaveAccess = function(me, perms) {
        for(var k in perms) {
            var finded = me.perms.filter(function(x) { return x.nome == perms[k]; });
            if(finded.length > 0) {
                return true;
            }
        }

        return false;
    };
    
    var doIHaveFullAccess = function(me, perms) {
        for(var k in perms) {
            var finded = me.perms.filter(function(x) { return x.nome == perms[k]; });
            if(finded.length == 0) {
                return false;
            }
        }

        return true;
    };
    
    var doIHaveMod = function(me, mod) {
        return (me.modulos.indexOf('|'+mod+':') >= 0);
    };
    
    var amIAdmin = function(me) {
        return(me.tipo == 'STF');
    };
    
    var doIHaveContexts = function() {
        var user = $localstorage.getObject('user', {});
        if($.isEmptyObject(user)) {
            return false;
        }
        return (!$.isEmptyObject(user.contextos));
    };
    
    var getContextsByKey = function(key) {
        var user = $localstorage.getObject('user', {});
        if($.isEmptyObject(user)) {
            return [];
        }
        
        if(!$.isEmptyObject(user.contextos) && user.contextos.hasOwnProperty(key))
        {
            return user.contextos[key];
        }
        
        return [];
    };
    
    var getContextByKeyAndId = function(key, id) {
        var contexts = getContextsByKey(key);
        if(contexts.length > 0) {
            for(var k in contexts) {
                if(contexts[k].id == id) {
                    return contexts[k];
                }
            }
        }
        
        return {};
    };
    
    var getMembresiaData = function() {
        var user = $localstorage.getObject('user', {});
        if($.isEmptyObject(user)) {
            throw new Error('sem dados de membresia');
        }
        return user.membresia;
    };
    
    var getUserHeader = function() {
        var token = $localstorage.get('token');
        var header = {
            "Authorization": "token=" + token
        };
        return header;
    };
    
    var getOficialId = function(tipo, ref) {
        var user = $localstorage.getObject('user', {});
        if($.isEmptyObject(user)) {
            return '';
        }
        
        if(user.oficialatos.length == 0) {
            return '';
        }
        
        if(!user.oficialatos.hasOwnProperty(tipo)) {
            return '';
        }
        
        for(var k in user.oficialatos[tipo]) {
            if(user.oficialatos[tipo][k].ref == ref) {
                return user.oficialatos[tipo][k].id;
            }
        }
        
        return '';
    };
    
    var getSociedadeData = function() {
        var user = $localstorage.getObject('user', {});
        if($.isEmptyObject(user)) {
            throw new Error('sem dados de sociedade');
        }
        
        if($.isEmptyObject(user.sociedade)) {
            throw new Error('sem dados de sociedade');
        }
        
        return user.sociedade;
    };
    
    return {
        logout: logout,
        updateMe: updateMe,
        getTokenForInstancia: getTokenForInstancia,
        checkByNome: checkByNome,
        get: get,
        isLogged: isLogged,
        canIAccessThis: canIAccessThis,
        doIHavePermission: doIHavePermission,
        doIHaveAccess: doIHaveAccess,
        doIHaveFullAccess: doIHaveFullAccess,
        doIHaveMod: doIHaveMod,
        amIAdmin: amIAdmin,
        doIHaveContexts: doIHaveContexts,
        getContextsByKey: getContextsByKey,
        getContextByKeyAndId: getContextByKeyAndId,
        getMembresiaData: getMembresiaData,
        getUserHeader: getUserHeader,
        getOficialId: getOficialId,
        getSociedadeData: getSociedadeData
    };
});


