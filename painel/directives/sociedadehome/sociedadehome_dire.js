angular.module('SmartChurchPanel').controller('SociedadeHomeDireCtrl', function($scope, $attrs, User, Home) {
    
    var vm = this;
    
    vm.USER = User;
    vm.user = User.get();
    vm.sociedade = '';
    
    vm.setSociedade = function(obj) {
        if(obj != undefined) {
            vm.sociedade = obj;
            vm.loadGraphs();
        }
    };
    
    vm.loadSocioPorAnoGraph = function() {
        if(vm.USER.doIHavePermission(vm.user, 'RelSocioAno')) {
            Home.getRelatorioSocioPorIdade(vm.sociedade).then(function(r) {
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
        if(vm.USER.doIHavePermission(vm.user, 'RelSocioSexo')) {
            Home.getRelatorioSocioPorSexo(vm.sociedade).then(function(r) {
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
        if(vm.USER.doIHavePermission(vm.user, 'RelSocioEstadoCivil')) {
            Home.getRelatorioSocioPorEstadoCivil(vm.sociedade).then(function(r) {
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
        if(vm.USER.doIHavePermission(vm.user, 'RelSocioEscolaridade')) {
            Home.getRelatorioSocioPorEscolaridade(vm.sociedade).then(function(r) {
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
        if(vm.USER.doIHavePermission(vm.user, 'RelSocioComungante')) {
            Home.getRelatorioSocioPorProfissaoDeFe(vm.sociedade).then(function(r) {
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
        if(vm.USER.doIHavePermission(vm.user, 'RelSocioFilho')) {
            Home.getRelatorioSocioPorTerFilho(vm.sociedade).then(function(r) {
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
        if(vm.USER.doIHavePermission(vm.user, 'RelSocioFilhoSexo')) {
            Home.getRelatorioSocioPorTerFilhoPorSexo(vm.sociedade).then(function(r) {
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
        if(vm.USER.doIHavePermission(vm.user, 'RelSocioNecessidade')) {
            Home.getRelatorioSocioPorNecessidades(vm.sociedade).then(function(r) {
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
        if(vm.USER.doIHavePermission(vm.user, 'RelSocioDoacao')) {
            Home.getRelatorioSocioPorDoacao(vm.sociedade).then(function(r) {
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
        if(vm.USER.doIHavePermission(vm.user, 'RelSocioArrolamento')) {
            Home.getRelatorioSocioPorArrolamento(vm.sociedade).then(function(r) {
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