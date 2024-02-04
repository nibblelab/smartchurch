angular.module('SmartChurchPanel').controller('ValidacaoCtrl', function ($location, $notifications, $scope, $dialogs, Credencial) { 
    
    $scope.data = {
        evento: '',
        credenciais: []
    };
    try
    {
        var query = $location.search();
        if(!$.isEmptyObject(query)) {
            if(query.hasOwnProperty('key')) {                
                Credencial.getByChave(query.key).then(function(r) {
                    console.log(r);
                    $scope.data.evento = r.evento;
                    for (var k in r.datas) {
                        r.datas[k]['time_cad_str'] = moment(r.datas[k].time_cad).format('DD/MM/YYYY HH:mm');
                        r.datas[k]['last_mod_str'] = moment(r.datas[k].last_mod).format('DD/MM/YYYY HH:mm');
                        
                        r.datas[k]['cargo_str'] = '';
                        if(r.datas[k].cargo != null) {
                            var c = r.cargos.find(x => x.id == r.datas[k].cargo);
                            if(c != undefined) {
                                r.datas[k]['cargo_str'] = c.nome;
                            }
                            var i = r.instancias.find(x => x.value == r.datas[k].cargo_ref);
                            if(i != undefined) {
                                r.datas[k]['cargo_str'] += ' na ' + i.label;
                            }
                        }
                    }
                    
                    $scope.data.credenciais = r.datas;
                }, function(e) {
                    $notifications.err(e);
                });
            }
        }
    } catch (e) { $notifications.err(e); }
    
    $scope.sign = function(d) {
        Credencial.signResponsavel(d.id, d).then(function(r) {
            $dialogs.onChange().then(function() {
                for (var k in $scope.data.credenciais) {
                    if ($scope.data.credenciais[k].id == d.id) {
                        $scope.data.credenciais[k].assinatura_responsavel = r.assinatura_responsavel;
                    }
                }
            }, function() {});
        }, function() {
            
        });
    };
    
    $scope.approve = function(d) {
        $dialogs.beforeChange().then(function() {
            $scope.sign(d);
        }, function() {});
    };
    
    
    
});


