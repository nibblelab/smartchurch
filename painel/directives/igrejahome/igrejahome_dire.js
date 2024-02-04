angular.module('SmartChurchPanel').controller('IgrejaHomeDireCtrl', function($scope, $attrs, User, Home) {
    
    var vm = this;
    
    vm.USER = User;
    vm.user = User.get();
    vm.igreja = '';
    
    vm.setIgreja = function(obj) {
        if(obj != undefined) {
            vm.igreja = obj;
            vm.loadGraphs();
        }
    };
    
    vm.loadMembroPorAnoGraph = function() {
        if(vm.USER.doIHavePermission(vm.user, 'RelMembroAno')) {
            Home.getRelatorioMembroPorIdade(vm.igreja).then(function(r) {
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

                if(data.length > 0) {
                    data.reverse();
                    Morris.Line({
                        element: 'membros_idade',
                        data: data,
                        xkey: 'label',
                        ykeys: ['value'],
                        labels: ['Quantidade'],
                        resize: true,
                        parseTime: false
                    });
                }

            }, function() {});
        }
    };

    vm.loadMembroPorGrupoDeIdadeGraph = function() {
        if(vm.USER.doIHavePermission(vm.user, 'RelMembroGrpIdade')) {
            Home.getRelatorioMembroPorGrupoDeIdade(vm.igreja).then(function(r) {
                var data = [];
                var colors = ['#90ca4b','#ffd900','#32a932','#00acac','#8753de'];
                data.push({ label: 'Crianças', value: r.criancas });
                data.push({ label: 'Adolescentes', value: r.adolescentes });
                data.push({ label: 'Jovens', value: r.jovens });
                data.push({ label: 'Adultos', value: r.adultos });
                data.push({ label: 'Idosos', value: r.idosos });
                Morris.Bar({
                    element: 'membros_grupo_idade',
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
            }, function() {});
        }
    };

    vm.loadMembroPorSexoGraph = function() {
        if(vm.USER.doIHavePermission(vm.user, 'RelMembroSexo')) {
            Home.getRelatorioMembroPorSexo(vm.igreja).then(function(r) {
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

                if(data.length > 0) { 
                    Morris.Donut({
                        element: 'membros_sexo',
                        data: data,
                        formatter: function (y) { return y },
                        resize: true,
                        colors: colors
                    });
                }
            }, function() {});
        }
    };

    vm.loadMembroPorEstadoCivilGraph = function() {
        if(vm.USER.doIHavePermission(vm.user, 'RelMembroEstadoCivil')) {
            Home.getRelatorioMembroPorEstadoCivil(vm.igreja).then(function(r) {
                var data = [];
                var colors = [];
                for(var k in r) {
                    if(r[k].total > 0) {
                        var estado_civil = (r[k].estado_civil != '') ? r[k].estado_civil : 'Não informado';
                        data.push({ label: estado_civil, value: r[k].total });
                        colors.push((r[k].estado_civil != '') ? randomColor() : '#ccc');
                    }
                }

                if(data.length > 0) {
                    Morris.Donut({
                        element: 'membros_estado_civil',
                        data: data,
                        formatter: function (y) { return y },
                        resize: true,
                        colors: colors
                    });
                }
            }, function() {});
        }
    };

    vm.loadMembroPorEscolaridadeGraph = function() {
        if(vm.USER.doIHavePermission(vm.user, 'RelMembroEscolaridade')) {
            Home.getRelatorioMembroPorEscolaridade(vm.igreja).then(function(r) {
                var data = [];
                for(var k in r) {
                    var escolaridade = (r[k].escolaridade != '') ? r[k].escolaridade : 'Não informado';
                    data.push({ label: escolaridade, value: r[k].total });
                }

                if(data.length > 0) {
                    Morris.Area({
                        element: 'membros_escolaridade',
                        data: data,
                        xkey: 'label',
                        ykeys: ['value'],
                        labels: ['Quantidade'],
                        pointSize: 2.5,
                        resize: true,
                        parseTime: false,
                        lineColors: ['#8753de']
                    });
                }

            }, function() {});
        }
    };

    vm.loadMembroPorProfissaoFeGraph = function() {
        if(vm.USER.doIHavePermission(vm.user, 'RelMembroComungante')) {
            Home.getRelatorioMembroPorProfissaoDeFe(vm.igreja).then(function(r) {
                var data = [
                    { label: 'Comungante', value: r.S.total },
                    { label: 'Não Comungante', value: r.N.total }
                ];
                var colors = ['#348fe2','#90ca4b'];
                Morris.Donut({
                    element: 'membros_comungante',
                    data: data,
                    formatter: function (y) { return y },
                    resize: true,
                    colors: colors
                });

            }, function() {});
        }
    };
    
    vm.loadMembroPorBairroGraph = function() {
        if(vm.USER.doIHavePermission(vm.user, 'RelMembroBairro')) {
            Home.getRelatorioMembroPorBairro(vm.igreja).then(function(r) {
                var data = [];
                var colors = [];
                for(var k in r) {
                    if(k == '') {
                        data.push({ label: 'Não informado', value: r[k] });
                        colors.push('#ccc');
                    }
                    else {
                        data.push({ label: k, value: r[k] });
                        colors.push(randomColor());
                    }
                }
                Morris.Donut({
                    element: 'membros_bairro',
                    data: data,
                    formatter: function (y) { return y },
                    resize: true,
                    colors: colors
                });

            }, function() {});
        }
    };
    
    vm.loadMembrosEVisitantesGraph = function() {
        if(vm.USER.doIHavePermission(vm.user, 'RelMembroVisitante')) {
            Home.getRelatorioMembroEVisitantes(vm.igreja).then(function(r) {
                var data = [
                    { label: 'Arrolado', value: r.S.total },
                    { label: 'Visitante', value: r.N.total }
                ];
                var colors = ['#0b9e1f','#f2db07'];
                Morris.Donut({
                    element: 'membros_visitantes',
                    data: data,
                    formatter: function (y) { return y },
                    resize: true,
                    colors: colors
                });

            }, function() {});
        }
    };
    
    vm.loadMembroPorRedesSociaisGraph = function() {
        if(vm.USER.doIHavePermission(vm.user, 'RelMembroSocialNet')) {
            Home.getRelatorioMembroPorRedesSociais(vm.igreja).then(function(r) {
                var data = [];
                var colors = [];
                for(var k in r) {
                    if(r[k] > 0) {
                        var l = (k == 'no') ? 'Não informado' : k;
                        data.push({ label: l, value: r[k] });
                        colors.push((k == 'no') ? '#ccc' : randomColor());
                    }
                }
                Morris.Donut({
                    element: 'membros_socialnet',
                    data: data,
                    formatter: function (y) { return y },
                    resize: true,
                    colors: colors
                });
            }, function() {});
        }
    };
    
    vm.loadMembroPorNecessidadesEspeciaisGraph = function() {
        if(vm.USER.doIHavePermission(vm.user, 'RelMembroNecessidade')) {
            Home.getRelatorioMembroPorNecessidadesEspeciais(vm.igreja).then(function(r) {
                var data = [];
                var colors = [];
                for(var k in r) {
                    data.push({ label: r[k].necessidade, value: r[k].total });
                    colors.push(randomColor());
                }
                
                try {
                    Morris.Bar({
                        element: 'membros_necessidades',
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
    
    vm.loadMembroPorDoacoesGraph = function() {
        if(vm.USER.doIHavePermission(vm.user, 'RelMembroDoacao')) {
            Home.getRelatorioMembroPorDoacoes(vm.igreja).then(function(r) {
                var data = [];
                var colors = [];
                for(var k in r) {
                    data.push({ label: r[k].doacao, value: r[k].total });
                    colors.push(randomColor());
                }
                
                try {
                    Morris.Bar({
                        element: 'membros_doacao',
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
    
    vm.loadMembroEEspeciaisGraph = function() {
        if(vm.USER.doIHavePermission(vm.user, 'RelMembroEspecial')) {
            Home.getRelatorioMembroEEspeciais(vm.igreja).then(function(r) {
                var data = [
                    { label: 'Especial', value: r.S.total },
                    { label: 'Normal', value: r.N.total }
                ];
                var colors = ['#0b9e1f','#ccc'];
                Morris.Donut({
                    element: 'membros_especial',
                    data: data,
                    formatter: function (y) { return y },
                    resize: true,
                    colors: colors
                });

            }, function() {});
        }
    };
    
    vm.loadMembroPorAdmissaoGraph = function() {
        if(vm.USER.doIHavePermission(vm.user, 'RelMembroAdmissao')) {
            Home.getRelatorioMembroPorAdmissao(vm.igreja).then(function(r) {
                var data = [];
                for(var k in r) {
                    data.push({ label: r[k].ano, value: r[k].total });
                }

                if(data.length > 0) {
                    Morris.Line({
                        element: 'membros_admissao',
                        data: data,
                        xkey: 'label',
                        ykeys: ['value'],
                        labels: ['Quantidade'],
                        resize: true,
                        parseTime: false
                    });
                }

            }, function() {});
        }
    };
    
    vm.loadMembroPorDemissaoGraph = function() {
        if(vm.USER.doIHavePermission(vm.user, 'RelMembroDemissao')) {
            Home.getRelatorioMembroPorDemissao(vm.igreja).then(function(r) {
                var data = [];
                for(var k in r) {
                    data.push({ label: r[k].ano, value: r[k].total });
                }

                if(data.length > 0) {
                    Morris.Line({
                        element: 'membros_demissao',
                        data: data,
                        xkey: 'label',
                        ykeys: ['value'],
                        labels: ['Quantidade'],
                        resize: true,
                        parseTime: false
                    });
                }

            }, function() {});
        }
    };
    
    vm.loadMembroPorPreenchimentoDoPerfilGraph = function() {
        if(vm.USER.doIHavePermission(vm.user, 'RelMembroPreenchimento')) {
            Home.getRelatorioMembroPorPreenchimentoDoPerfil(vm.igreja).then(function(r) {
                var data = [
                    { label: 'Básico', value: r.basico },
                    { label: 'Mínimo', value: r.minimo },
                    { label: 'Médio', value: r.medio },
                    { label: 'Total', value: r.total }
                ];
                var colors = ['#f28007','#f2db07','#0b9e1f','#09a9de'];
                Morris.Donut({
                    element: 'membros_preenchimento',
                    data: data,
                    formatter: function (y) { return y },
                    resize: true,
                    colors: colors
                });

            }, function() {});
        }
    };
    
    vm.loadMembroPorTerFilhoGraph = function() {
        if(vm.USER.doIHavePermission(vm.user, 'RelMembroFilhos')) {
            Home.getRelatorioMembroPorTerFilho(vm.igreja).then(function(r) {
                var data = [
                    { label: 'Tem Filhos', value: r.S.total },
                    { label: 'Não Tem Filhos', value: r.N.total }
                ];
                var colors = ['#348fe2','#90ca4b'];
                
                try {
                    Morris.Donut({
                        element: 'membros_filhos',
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
        vm.loadMembroPorAnoGraph();
        vm.loadMembroPorGrupoDeIdadeGraph();
        vm.loadMembroPorSexoGraph();
        vm.loadMembroPorEstadoCivilGraph();
        vm.loadMembroPorEscolaridadeGraph();
        vm.loadMembroPorProfissaoFeGraph();
        if(vm.USER.doIHaveMod(vm.user, 'MOD_IGREJA')) {
            vm.loadMembroPorBairroGraph();
            vm.loadMembrosEVisitantesGraph();
            vm.loadMembroPorRedesSociaisGraph();
            vm.loadMembroPorNecessidadesEspeciaisGraph();
            vm.loadMembroPorDoacoesGraph();
            vm.loadMembroEEspeciaisGraph();
            vm.loadMembroPorAdmissaoGraph();
            vm.loadMembroPorDemissaoGraph();
            vm.loadMembroPorPreenchimentoDoPerfilGraph();
            vm.loadMembroPorTerFilhoGraph();
        }
    };
    
    
    
});

