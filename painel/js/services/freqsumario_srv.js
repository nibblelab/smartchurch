angular.module('SmartChurchPanel').factory('SumarioEBD', function ($localstorage, $q, $http, ApiEndpoint) {
    
    var getAll = function(page, pagesize, searchBy, orderBy, groupBy, sala, dia) {
        
        var token = $localstorage.get('token');
        var deferred = $q.defer();
        
        var pg = (page == undefined) ? '' : page;
        var ps = (pagesize == undefined) ? '' : pagesize;
        var sb = (searchBy == undefined) ? '' : searchBy;
        var ob = (orderBy == undefined) ? '' : orderBy;
        var gb = (groupBy == undefined) ? '' : groupBy;
        var sc = (sala == undefined) ? '' : sala;
        var dy = (dia == undefined) ? '' : dia;
        
        $http
            .get(ApiEndpoint.url+'/freqsumarios/all?page='+pg+'&pageSize='+ps+'&searchBy='+sb+
                        '&orderBy='+ob+'&groupBy='+gb+'&sala='+sc+'&dia='+dy, {
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


