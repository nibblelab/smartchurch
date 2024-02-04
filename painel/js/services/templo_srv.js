angular.module('SmartChurchPanel').factory('Templo', function ($localstorage, $q, $http, ApiEndpoint) {
    
    var getAll = function(page, pagesize, searchBy, orderBy, groupBy, stat, sinodo, presbiterio, igreja) {
        
        var token = $localstorage.get('token');
        var deferred = $q.defer();
        
        var pg = (page == undefined) ? '' : page;
        var ps = (pagesize == undefined) ? '' : pagesize;
        var sb = (searchBy == undefined) ? '' : searchBy;
        var ob = (orderBy == undefined) ? '' : orderBy;
        var gb = (groupBy == undefined) ? '' : groupBy;
        var st = (stat == undefined) ? '' : stat;
        var sn = (sinodo == undefined) ? '' : sinodo;
        var pb = (presbiterio == undefined) ? '' : presbiterio;
        var ig = (igreja == undefined) ? '' : igreja;
        
        $http
            .get(ApiEndpoint.url+'/templos/all?page='+pg+'&pageSize='+ps+'&searchBy='+sb+
                        '&orderBy='+ob+'&groupBy='+gb+'&stat='+st+'&sinodo='+sn+
                        '&presbiterio='+pb+'&igreja='+ig, {
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
    
    return {
        getAll: getAll
    };
});


