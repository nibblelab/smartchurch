angular.module('SmartChurchPanel').factory('Home', function ($localstorage, $q, $http, ApiEndpoint) {
    
    var getRelatorioMembroPorIdade = function(igreja) {
        
        var token = $localstorage.get('token');
        var deferred = $q.defer();
        
        $http
            .get(ApiEndpoint.url+'/home/relMembroPorIdade?igreja='+igreja, {
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
    
    var getRelatorioMembroPorGrupoDeIdade = function(igreja) {
        
        var token = $localstorage.get('token');
        var deferred = $q.defer();
        
        $http
            .get(ApiEndpoint.url+'/home/relMembroPorGrupoIdade?igreja='+igreja, {
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
    
    var getRelatorioMembroPorSexo = function(igreja) {
        
        var token = $localstorage.get('token');
        var deferred = $q.defer();
        
        $http
            .get(ApiEndpoint.url+'/home/relMembroPorSexo?igreja='+igreja, {
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
    
    var getRelatorioMembroPorEstadoCivil = function(igreja) {
        
        var token = $localstorage.get('token');
        var deferred = $q.defer();
        
        $http
            .get(ApiEndpoint.url+'/home/relMembroPorEstadoCivil?igreja='+igreja, {
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
    
    var getRelatorioMembroPorEscolaridade = function(igreja) {
        
        var token = $localstorage.get('token');
        var deferred = $q.defer();
        
        $http
            .get(ApiEndpoint.url+'/home/relMembroPorEscolaridade?igreja='+igreja, {
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
    
    var getRelatorioMembroPorProfissaoDeFe = function(igreja) {
        
        var token = $localstorage.get('token');
        var deferred = $q.defer();
        
        $http
            .get(ApiEndpoint.url+'/home/relMembroPorProfissaoFe?igreja='+igreja, {
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
    
    var getRelatorioMembroPorBairro = function(igreja) {
        
        var token = $localstorage.get('token');
        var deferred = $q.defer();
        
        $http
            .get(ApiEndpoint.url+'/home/relMembroPorBairro?igreja='+igreja, {
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
    
    var getRelatorioMembroEVisitantes = function(igreja) {
        
        var token = $localstorage.get('token');
        var deferred = $q.defer();
        
        $http
            .get(ApiEndpoint.url+'/home/relMembrosEVisitantes?igreja='+igreja, {
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
    
    var getRelatorioMembroPorRedesSociais = function(igreja) {
        
        var token = $localstorage.get('token');
        var deferred = $q.defer();
        
        $http
            .get(ApiEndpoint.url+'/home/relMembrosPorRedeSocial?igreja='+igreja, {
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
    
    var getRelatorioMembroPorNecessidadesEspeciais = function(igreja) {
        
        var token = $localstorage.get('token');
        var deferred = $q.defer();
        
        $http
            .get(ApiEndpoint.url+'/home/relMembrosPorNecessidadesEspeciais?igreja='+igreja, {
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
    
    var getRelatorioMembroPorDoacoes = function(igreja) {
        
        var token = $localstorage.get('token');
        var deferred = $q.defer();
        
        $http
            .get(ApiEndpoint.url+'/home/relMembrosPorDoacao?igreja='+igreja, {
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
    
    var getRelatorioMembroEEspeciais = function(igreja) {
        
        var token = $localstorage.get('token');
        var deferred = $q.defer();
        
        $http
            .get(ApiEndpoint.url+'/home/relMembrosPorEspecial?igreja='+igreja, {
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
    
    var getRelatorioMembroPorAdmissao = function(igreja) {
        
        var token = $localstorage.get('token');
        var deferred = $q.defer();
        
        $http
            .get(ApiEndpoint.url+'/home/relMembrosPorAnoDeAdmissao?igreja='+igreja, {
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
    
    var getRelatorioMembroPorDemissao = function(igreja) {
        
        var token = $localstorage.get('token');
        var deferred = $q.defer();
        
        $http
            .get(ApiEndpoint.url+'/home/relMembrosPorAnoDeDemissao?igreja='+igreja, {
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
    
    var getRelatorioMembroPorPreenchimentoDoPerfil = function(igreja) {
        
        var token = $localstorage.get('token');
        var deferred = $q.defer();
        
        $http
            .get(ApiEndpoint.url+'/home/relMembrosPorPreenchimentoPerfil?igreja='+igreja, {
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
    
    var getRelatorioMembroPorTerFilho = function(igreja) {
        
        var token = $localstorage.get('token');
        var deferred = $q.defer();
        
        $http
            .get(ApiEndpoint.url+'/home/relMembrosPorTemFilho?igreja='+igreja, {
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
    
    var getRelatorioSocioPorIdade = function(sociedade) {
        
        var token = $localstorage.get('token');
        var deferred = $q.defer();
        
        $http
            .get(ApiEndpoint.url+'/home/relSocioPorIdade?sociedade='+sociedade, {
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
    
    var getRelatorioSocioPorSexo = function(sociedade) {
        
        var token = $localstorage.get('token');
        var deferred = $q.defer();
        
        $http
            .get(ApiEndpoint.url+'/home/relSocioPorSexo?sociedade='+sociedade, {
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
    
    var getRelatorioSocioPorEstadoCivil = function(sociedade) {
        
        var token = $localstorage.get('token');
        var deferred = $q.defer();
        
        $http
            .get(ApiEndpoint.url+'/home/relSocioPorEstadoCivil?sociedade='+sociedade, {
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
    
    var getRelatorioSocioPorEscolaridade = function(sociedade) {
        
        var token = $localstorage.get('token');
        var deferred = $q.defer();
        
        $http
            .get(ApiEndpoint.url+'/home/relSocioPorEscolaridade?sociedade='+sociedade, {
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
    
    var getRelatorioSocioPorProfissaoDeFe = function(sociedade) {
        
        var token = $localstorage.get('token');
        var deferred = $q.defer();
        
        $http
            .get(ApiEndpoint.url+'/home/relSocioPorProfissaoFe?sociedade='+sociedade, {
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
    
    var getRelatorioSocioPorTerFilho = function(sociedade) {
        
        var token = $localstorage.get('token');
        var deferred = $q.defer();
        
        $http
            .get(ApiEndpoint.url+'/home/relSocioPorTemFilho?sociedade='+sociedade, {
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
    
    var getRelatorioSocioPorTerFilhoPorSexo = function(sociedade) {
        
        var token = $localstorage.get('token');
        var deferred = $q.defer();
        
        $http
            .get(ApiEndpoint.url+'/home/relSocioPorTemFilhoPorSexo?sociedade='+sociedade, {
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
    
    var getRelatorioSocioPorNecessidades = function(sociedade) {
        
        var token = $localstorage.get('token');
        var deferred = $q.defer();
        
        $http
            .get(ApiEndpoint.url+'/home/relSocioPorNecessidade?sociedade='+sociedade, {
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
    
    var getRelatorioSocioPorDoacao = function(sociedade) {
        
        var token = $localstorage.get('token');
        var deferred = $q.defer();
        
        $http
            .get(ApiEndpoint.url+'/home/relSocioPorDoacao?sociedade='+sociedade, {
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
    
    var getRelatorioSocioPorArrolamento = function(sociedade) {
        
        var token = $localstorage.get('token');
        var deferred = $q.defer();
        
        $http
            .get(ApiEndpoint.url+'/home/relSocioPorArrolamento?sociedade='+sociedade, {
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
    
    var getRelatorioSociedadesAtivasFederacao = function(federacao) {
        
        var token = $localstorage.get('token');
        var deferred = $q.defer();
        
        $http
            .get(ApiEndpoint.url+'/home/relSociedadesAtivasForFederacao?federacao='+federacao, {
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
    
    var getRelatorioSocioPorIdadeFederacao = function(federacao) {
        
        var token = $localstorage.get('token');
        var deferred = $q.defer();
        
        $http
            .get(ApiEndpoint.url+'/home/relSocioPorIdadeForFederacao?federacao='+federacao, {
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
    
    var getRelatorioSocioPorSexoFederacao = function(federacao) {
        
        var token = $localstorage.get('token');
        var deferred = $q.defer();
        
        $http
            .get(ApiEndpoint.url+'/home/relSocioPorSexoForFederacao?federacao='+federacao, {
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
    
    var getRelatorioSocioPorEstadoCivilFederacao = function(federacao) {
        
        var token = $localstorage.get('token');
        var deferred = $q.defer();
        
        $http
            .get(ApiEndpoint.url+'/home/relSocioPorEstadoCivilForFederacao?federacao='+federacao, {
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
    
    var getRelatorioSocioPorEscolaridadeFederacao = function(federacao) {
        
        var token = $localstorage.get('token');
        var deferred = $q.defer();
        
        $http
            .get(ApiEndpoint.url+'/home/relSocioPorEscolaridadeForFederacao?federacao='+federacao, {
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
    
    var getRelatorioSocioPorProfissaoDeFeFederacao = function(federacao) {
        
        var token = $localstorage.get('token');
        var deferred = $q.defer();
        
        $http
            .get(ApiEndpoint.url+'/home/relSocioPorProfissaoFeForFederacao?federacao='+federacao, {
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
    
    var getRelatorioSocioPorTerFilhoFederacao = function(federacao) {
        
        var token = $localstorage.get('token');
        var deferred = $q.defer();
        
        $http
            .get(ApiEndpoint.url+'/home/relSocioPorTemFilhoForFederacao?federacao='+federacao, {
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
    
    var getRelatorioSocioPorTerFilhoPorSexoFederacao = function(federacao) {
        
        var token = $localstorage.get('token');
        var deferred = $q.defer();
        
        $http
            .get(ApiEndpoint.url+'/home/relSocioPorTemFilhoPorSexoForFederacao?federacao='+federacao, {
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
    
    var getRelatorioSocioPorNecessidadesFederacao = function(federacao) {
        
        var token = $localstorage.get('token');
        var deferred = $q.defer();
        
        $http
            .get(ApiEndpoint.url+'/home/relSocioPorNecessidadeForFederacao?federacao='+federacao, {
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
    
    var getRelatorioSocioPorDoacaoFederacao = function(federacao) {
        
        var token = $localstorage.get('token');
        var deferred = $q.defer();
        
        $http
            .get(ApiEndpoint.url+'/home/relSocioPorDoacaoForFederacao?federacao='+federacao, {
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
    
    var getRelatorioSocioPorArrolamentoFederacao = function(federacao) {
        
        var token = $localstorage.get('token');
        var deferred = $q.defer();
        
        $http
            .get(ApiEndpoint.url+'/home/relSocioPorArrolamentoForFederacao?federacao='+federacao, {
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
    
    var getRelatorioFederacoesAtivasSinodal = function(sinodal) {
        
        var token = $localstorage.get('token');
        var deferred = $q.defer();
        
        $http
            .get(ApiEndpoint.url+'/home/relFederacoesAtivasForSinodal?sinodal='+sinodal, {
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
    
    var getRelatorioSociedadesAtivasSinodal = function(sinodal) {
        
        var token = $localstorage.get('token');
        var deferred = $q.defer();
        
        $http
            .get(ApiEndpoint.url+'/home/relSociedadesAtivasForSinodal?sinodal='+sinodal, {
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
    
    var getRelatorioSocioPorIdadeSinodal = function(sinodal) {
        
        var token = $localstorage.get('token');
        var deferred = $q.defer();
        
        $http
            .get(ApiEndpoint.url+'/home/relSocioPorIdadeForSinodal?sinodal='+sinodal, {
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
    
    var getRelatorioSocioPorSexoSinodal = function(sinodal) {
        
        var token = $localstorage.get('token');
        var deferred = $q.defer();
        
        $http
            .get(ApiEndpoint.url+'/home/relSocioPorSexoForSinodal?sinodal='+sinodal, {
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
    
    var getRelatorioSocioPorEstadoCivilSinodal = function(sinodal) {
        
        var token = $localstorage.get('token');
        var deferred = $q.defer();
        
        $http
            .get(ApiEndpoint.url+'/home/relSocioPorEstadoCivilForSinodal?sinodal='+sinodal, {
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
    
    var getRelatorioSocioPorEscolaridadeSinodal = function(sinodal) {
        
        var token = $localstorage.get('token');
        var deferred = $q.defer();
        
        $http
            .get(ApiEndpoint.url+'/home/relSocioPorEscolaridadeForSinodal?sinodal='+sinodal, {
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
    
    var getRelatorioSocioPorProfissaoDeFeSinodal = function(sinodal) {
        
        var token = $localstorage.get('token');
        var deferred = $q.defer();
        
        $http
            .get(ApiEndpoint.url+'/home/relSocioPorProfissaoFeForSinodal?sinodal='+sinodal, {
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
    
    var getRelatorioSocioPorTerFilhoSinodal = function(sinodal) {
        
        var token = $localstorage.get('token');
        var deferred = $q.defer();
        
        $http
            .get(ApiEndpoint.url+'/home/relSocioPorTemFilhoForSinodal?sinodal='+sinodal, {
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
    
    var getRelatorioSocioPorTerFilhoPorSexoSinodal = function(sinodal) {
        
        var token = $localstorage.get('token');
        var deferred = $q.defer();
        
        $http
            .get(ApiEndpoint.url+'/home/relSocioPorTemFilhoPorSexoForSinodal?sinodal='+sinodal, {
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
    
    var getRelatorioSocioPorNecessidadesSinodal = function(sinodal) {
        
        var token = $localstorage.get('token');
        var deferred = $q.defer();
        
        $http
            .get(ApiEndpoint.url+'/home/relSocioPorNecessidadeForSinodal?sinodal='+sinodal, {
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
    
    var getRelatorioSocioPorDoacaoSinodal = function(sinodal) {
        
        var token = $localstorage.get('token');
        var deferred = $q.defer();
        
        $http
            .get(ApiEndpoint.url+'/home/relSocioPorDoacaoForSinodal?sinodal='+sinodal, {
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
    
    var getRelatorioSocioPorArrolamentoSinodal = function(sinodal) {
        
        var token = $localstorage.get('token');
        var deferred = $q.defer();
        
        $http
            .get(ApiEndpoint.url+'/home/relSocioPorArrolamentoForSinodal?sinodal='+sinodal, {
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
    
    return {
        getRelatorioMembroPorIdade: getRelatorioMembroPorIdade,
        getRelatorioMembroPorGrupoDeIdade: getRelatorioMembroPorGrupoDeIdade,
        getRelatorioMembroPorSexo: getRelatorioMembroPorSexo,
        getRelatorioMembroPorEstadoCivil: getRelatorioMembroPorEstadoCivil,
        getRelatorioMembroPorEscolaridade: getRelatorioMembroPorEscolaridade,
        getRelatorioMembroPorProfissaoDeFe: getRelatorioMembroPorProfissaoDeFe,
        getRelatorioMembroPorBairro: getRelatorioMembroPorBairro,
        getRelatorioMembroEVisitantes: getRelatorioMembroEVisitantes,
        getRelatorioMembroPorRedesSociais: getRelatorioMembroPorRedesSociais,
        getRelatorioMembroPorNecessidadesEspeciais: getRelatorioMembroPorNecessidadesEspeciais,
        getRelatorioMembroPorDoacoes: getRelatorioMembroPorDoacoes,
        getRelatorioMembroEEspeciais: getRelatorioMembroEEspeciais,
        getRelatorioMembroPorAdmissao: getRelatorioMembroPorAdmissao,
        getRelatorioMembroPorDemissao: getRelatorioMembroPorDemissao,
        getRelatorioMembroPorPreenchimentoDoPerfil: getRelatorioMembroPorPreenchimentoDoPerfil,
        getRelatorioMembroPorTerFilho: getRelatorioMembroPorTerFilho,
        getRelatorioSocioPorIdade: getRelatorioSocioPorIdade,
        getRelatorioSocioPorSexo: getRelatorioSocioPorSexo,
        getRelatorioSocioPorEstadoCivil: getRelatorioSocioPorEstadoCivil,
        getRelatorioSocioPorEscolaridade: getRelatorioSocioPorEscolaridade,
        getRelatorioSocioPorProfissaoDeFe: getRelatorioSocioPorProfissaoDeFe,
        getRelatorioSocioPorTerFilho: getRelatorioSocioPorTerFilho,
        getRelatorioSocioPorTerFilhoPorSexo: getRelatorioSocioPorTerFilhoPorSexo,
        getRelatorioSocioPorNecessidades: getRelatorioSocioPorNecessidades,
        getRelatorioSocioPorDoacao: getRelatorioSocioPorDoacao,
        getRelatorioSocioPorArrolamento: getRelatorioSocioPorArrolamento,
        getRelatorioSociedadesAtivasFederacao: getRelatorioSociedadesAtivasFederacao,
        getRelatorioSocioPorIdadeFederacao: getRelatorioSocioPorIdadeFederacao,
        getRelatorioSocioPorSexoFederacao: getRelatorioSocioPorSexoFederacao,
        getRelatorioSocioPorEstadoCivilFederacao: getRelatorioSocioPorEstadoCivilFederacao,
        getRelatorioSocioPorEscolaridadeFederacao: getRelatorioSocioPorEscolaridadeFederacao,
        getRelatorioSocioPorProfissaoDeFeFederacao: getRelatorioSocioPorProfissaoDeFeFederacao,
        getRelatorioSocioPorTerFilhoFederacao: getRelatorioSocioPorTerFilhoFederacao,
        getRelatorioSocioPorTerFilhoPorSexoFederacao: getRelatorioSocioPorTerFilhoPorSexoFederacao,
        getRelatorioSocioPorNecessidadesFederacao: getRelatorioSocioPorNecessidadesFederacao,
        getRelatorioSocioPorDoacaoFederacao: getRelatorioSocioPorDoacaoFederacao,
        getRelatorioSocioPorArrolamentoFederacao: getRelatorioSocioPorArrolamentoFederacao,
        getRelatorioFederacoesAtivasSinodal: getRelatorioFederacoesAtivasSinodal,
        getRelatorioSociedadesAtivasSinodal: getRelatorioSociedadesAtivasSinodal,
        getRelatorioSocioPorIdadeSinodal: getRelatorioSocioPorIdadeSinodal,
        getRelatorioSocioPorSexoSinodal: getRelatorioSocioPorSexoSinodal,
        getRelatorioSocioPorEstadoCivilSinodal: getRelatorioSocioPorEstadoCivilSinodal,
        getRelatorioSocioPorEscolaridadeSinodal: getRelatorioSocioPorEscolaridadeSinodal,
        getRelatorioSocioPorProfissaoDeFeSinodal: getRelatorioSocioPorProfissaoDeFeSinodal,
        getRelatorioSocioPorTerFilhoSinodal: getRelatorioSocioPorTerFilhoSinodal,
        getRelatorioSocioPorTerFilhoPorSexoSinodal: getRelatorioSocioPorTerFilhoPorSexoSinodal,
        getRelatorioSocioPorNecessidadesSinodal: getRelatorioSocioPorNecessidadesSinodal,
        getRelatorioSocioPorDoacaoSinodal: getRelatorioSocioPorDoacaoSinodal,
        getRelatorioSocioPorArrolamentoSinodal: getRelatorioSocioPorArrolamentoSinodal
    };
});


