angular.module('SmartChurchPanel').controller('SinodalHomeDireCtrl', function($scope, $attrs, User, Home) {
    
    var vm = this;
    
    vm.USER = User;
    vm.user = User.get();
    vm.sinodal = '';
    
    vm.setSinodal = function(obj) {
        if(obj != undefined) {
            vm.sinodal = obj;
            vm.loadGraphs();
        }
    };
    
    vm.loadFederacoesAtivasGraph = function() {
        if(vm.USER.doIHavePermission(vm.user, 'RelSinFederacoes')) {
            Home.getRelatorioFederacoesAtivasSinodal(vm.sinodal).then(function(r) {
                var data = [
                    { label: "Ativas", value: r.ativas},
                    { label: "Não Ativas", value: r.nao_ativas}
                ];
                var colors = ["#07ab0a", "#db0000"];
                
                try {
                    Morris.Donut({
                        element: 'federacoes_ativa',
                        data: data,
                        formatter: function (y) { return y },
                        resize: true,
                        colors: colors
                    });
                } catch(e) {
                    console.log(e);
                }
                
            }, function() {});
        }
    };
    
    vm.loadSociedadesAtivasGraph = function() {
        if(vm.USER.doIHavePermission(vm.user, 'RelSinSociedades')) {
            Home.getRelatorioSociedadesAtivasSinodal(vm.sinodal).then(function(r) {
                var data = [
                    { label: "Ativas", value: r.ativas},
                    { label: "Não Ativas", value: r.nao_ativas}
                ];
                var colors = ["#07ab0a", "#db0000"];
                
                try {
                    Morris.Donut({
                        element: 'sociedades_ativa',
                        data: data,
                        formatter: function (y) { return y },
                        resize: true,
                        colors: colors
                    });
                } catch(e) {
                    console.log(e);
                }
                
            }, function() {});
        }
    };
    
    vm.loadSocioPorAnoGraph = function() {
        if(vm.USER.doIHavePermission(vm.user, 'RelSinSocioAno')) {
            Home.getRelatorioSocioPorIdadeSinodal(vm.sinodal).then(function(r) {
                var data = [];
                var ano = parseInt(moment().format('YYYY'));
                var mes = parseInt(moment().format('MM'));
                for(var k in r) {
                    var idade = ano - r[k].ano;
                    if(mes <  r[k].mes) {
                        idade--;
                    }
                    var indx = data.findIndex(x => x.idade == idade);
                    if(indx < 0) {
                        data.push({ label: idade + ' anos', value: r[k].total, idade: idade });
                    }
                    else {
                        data[indx].value += r[k].total;
                    }
                }
                
                try {
                    if(data.length > 0) {
                        data.reverse();
                        Morris.Line({
                            element: 'socios_idade',
                            data: data,
                            xkey: 'label',
                            ykeys: ['value'],
                            labels: ['Quantidade'],
                            resize: true,
                            parseTime: false
                        });
                    }
                } catch(e) {
                    console.log(e);
                }
                
            }, function() {});
        }
    };
    
    vm.loadSocioPorSexoGraph = function() {
        if(vm.USER.doIHavePermission(vm.user, 'RelSinSocioSexo')) {
            Home.getRelatorioSocioPorSexoSinodal(vm.sinodal).then(function(r) {
                var data = [];
                var colors = [];
                for(var k in r) {
                    var sexo = (r[k].sexo != '') ? r[k].sexo : 'Não informado';
                    data.push({ label: sexo, value: r[k].total });
                    if(k == 'M') {
                        colors.push('#348fe2');
                    }
                    else if(k == 'F') {
                        colors.push('#fb5597');
                    }
                    else {
                        colors.push('#ccc');
                    }
                }
                
                try {
                    Morris.Donut({
                        element: 'socios_sexo',
                        data: data,
                        formatter: function (y) { return y },
                        resize: true,
                        colors: colors
                    });
                } catch(e) {
                    console.log(e);
                }
                
            }, function() {});
        }
    };
    
    vm.loadSocioPorEstadoCivilGraph = function() {
        if(vm.USER.doIHavePermission(vm.user, 'RelSinSocioEstadoCivil')) {
            Home.getRelatorioSocioPorEstadoCivilSinodal(vm.sinodal).then(function(r) {
                var data = [];
                var colors = [];
                for(var k in r) {
                    if(r[k].total > 0) {
                        var estado_civil = (r[k].estado_civil != '') ? r[k].estado_civil : 'Não informado';
                        data.push({ label: estado_civil, value: r[k].total });
                        colors.push(randomColor());
                    }
                }
                
                try {
                    Morris.Donut({
                        element: 'socios_estado_civil',
                        data: data,
                        formatter: function (y) { return y },
                        resize: true,
                        colors: colors
                    });
                } catch(e) {
                    console.log(e);
                }
                
            }, function() {});
        }
    };

    vm.loadSocioPorEscolaridadeGraph = function() {
        if(vm.USER.doIHavePermission(vm.user, 'RelSinSocioEscolaridade')) {
            Home.getRelatorioSocioPorEscolaridadeSinodal(vm.sinodal).then(function(r) {
                var data = [];
                for(var k in r) {
                    var escolaridade = (r[k].escolaridade != '') ? r[k].escolaridade : 'Não informado';
                    data.push({ label: escolaridade, value: r[k].total });
                }
                
                try {
                    Morris.Area({
                        element: 'socios_escolaridade',
                        data: data,
                        xkey: 'label',
                        ykeys: ['value'],
                        labels: ['Quantidade'],
                        pointSize: 2.5,
                        resize: true,
                        parseTime: false,
                        lineColors: ['#8753de']
                    });
                } catch(e) {
                    console.log(e);
                }

            }, function() {});
        }
    };

    vm.loadSocioPorProfissaoFeGraph = function() {
        if(vm.USER.doIHavePermission(vm.user, 'RelSinSocioComungante')) {
            Home.getRelatorioSocioPorProfissaoDeFeSinodal(vm.sinodal).then(function(r) {
                var data = [
                    { label: 'Comungante', value: r.S.total },
                    { label: 'Não Comungante', value: r.N.total }
                ];
                var colors = ['#348fe2','#90ca4b'];
                
                try {
                    Morris.Donut({
                        element: 'socios_comungante',
                        data: data,
                        formatter: function (y) { return y },
                        resize: true,
                        colors: colors
                    });
                } catch(e) {
                    console.log(e);
                }
                
            }, function() {});
        }
    };

    vm.loadSocioPorFilhoGraph = function() {
        if(vm.USER.doIHavePermission(vm.user, 'RelSinSocioFilho')) {
            Home.getRelatorioSocioPorTerFilhoSinodal(vm.sinodal).then(function(r) {
                var data = [
                    { label: 'Tem Filhos', value: r.S.total },
                    { label: 'Não Tem Filhos', value: r.N.total }
                ];
                var colors = ['#348fe2','#90ca4b'];
                
                try {
                    Morris.Donut({
                        element: 'socios_filho',
                        data: data,
                        formatter: function (y) { return y },
                        resize: true,
                        colors: colors
                    });
                } catch(e) {
                    console.log(e);
                }
                
            }, function() {});
        }
    };

    vm.loadSocioPorFilhoPorSexoGraph = function() {
        if(vm.USER.doIHavePermission(vm.user, 'RelSinSocioFilhoSexo')) {
            Home.getRelatorioSocioPorTerFilhoPorSexoSinodal(vm.sinodal).then(function(r) {
                var data = [
                    { label: 'Mulheres com Filhos', value: r.S_F.total },
                    { label: 'Mulheres sem Filhos', value: r.N_F.total },
                    { label: 'Homens com Filhos', value: r.S_M.total },
                    { label: 'Homens sem Filhos', value: r.N_M.total }
                ];
                var colors = ['#7d006c','#ff7dfd', '#0267ab', '#00c6e0'];
                
                try {
                    Morris.Donut({
                        element: 'socios_filho_por_sexo',
                        data: data,
                        formatter: function (y) { return y },
                        resize: true,
                        colors: colors
                    });
                } catch(e) {
                    console.log(e);
                }
                
            }, function() {});
        }
    };
    
    vm.loadSocioPorNecessidadeGraph = function() {
        if(vm.USER.doIHavePermission(vm.user, 'RelSinSocioNecessidade')) {
            Home.getRelatorioSocioPorNecessidadesSinodal(vm.sinodal).then(function(r) {
                var data = [];
                var colors = [];
                for(var k in r) {
                    data.push({ label: r[k].necessidade, value: r[k].total });
                    colors.push(randomColor());
                }
                
                try {
                    Morris.Bar({
                        element: 'socios_necessidade',
                        data: data,
                        xkey: 'label',
                        ykeys: ['value'],
                        labels: ['Quantidade'],
                        barRatio: 0.4,
                        xLabelAngle: 35,
                        resize: true,
                        barColors: function (row, series, type) {
                            return colors[row.x];
                        }
                    });
                } catch(e) {
                    console.log(e);
                }
            }, function() {});
        }
    };
    
    vm.loadSocioPorDoacaoGraph = function() {
        if(vm.USER.doIHavePermission(vm.user, 'RelSinSocioDoacao')) {
            Home.getRelatorioSocioPorDoacaoSinodal(vm.sinodal).then(function(r) {
                var data = [];
                var colors = [];
                for(var k in r) {
                    data.push({ label: r[k].doacao, value: r[k].total });
                    colors.push(randomColor());
                }
                
                try {
                    Morris.Bar({
                        element: 'socios_doacao',
                        data: data,
                        xkey: 'label',
                        ykeys: ['value'],
                        labels: ['Quantidade'],
                        barRatio: 0.4,
                        xLabelAngle: 35,
                        resize: true,
                        barColors: function (row, series, type) {
                            return colors[row.x];
                        }
                    });
                } catch(e) {
                    console.log(e);
                }
            }, function() {});
        }
    };
    
    vm.loadSocioPorArrolamentoGraph = function() {
        if(vm.USER.doIHavePermission(vm.user, 'RelSinSocioArrolamento')) {
            Home.getRelatorioSocioPorArrolamentoSinodal(vm.sinodal).then(function(r) {
                var data = [
                    { label: 'Cooperador', value: r.S.total },
                    { label: 'Sócio', value: r.N.total }
                ];
                var colors = ['#348fe2','#90ca4b'];
                
                try {
                    Morris.Donut({
                        element: 'socios_cooperador',
                        data: data,
                        formatter: function (y) { return y },
                        resize: true,
                        colors: colors
                    });
                } catch(e) {
                    console.log(e);
                }
            }, function() {});
        }
    };
    
    vm.loadGraphs = function() {
        vm.loadFederacoesAtivasGraph();
        vm.loadSociedadesAtivasGraph();
        vm.loadSocioPorAnoGraph();
        vm.loadSocioPorSexoGraph();
        vm.loadSocioPorEstadoCivilGraph();
        vm.loadSocioPorEscolaridadeGraph();
        vm.loadSocioPorProfissaoFeGraph();
        vm.loadSocioPorFilhoGraph();
        vm.loadSocioPorFilhoPorSexoGraph();
        vm.loadSocioPorNecessidadeGraph();
        vm.loadSocioPorDoacaoGraph();
        vm.loadSocioPorArrolamentoGraph();
    };
    
});