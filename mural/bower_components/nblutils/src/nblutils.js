
var nblutilsdata = {
    provinces: {
        'br': [
            { code: "AC", name: "Acre" },
            { code: "AL", name: "Alagoas" },
            { code: "AM", name: "Amazonas" },
            { code: "AP", name: "Amapá" },
            { code: "BA", name: "Bahia" },
            { code: "CE", name: "Ceará" },
            { code: "DF", name: "Distrito Federal" },
            { code: "ES", name: "Espírito Santo" },
            { code: "GO", name: "Goiás" },
            { code: "MA", name: "Maranhão" },
            { code: "MG", name: "Minas Gerais" },
            { code: "MS", name: "Mato Grosso do Sul" },
            { code: "MT", name: "Mato Grosso" },
            { code: "PA", name: "Pará" },
            { code: "PB", name: "Paraíba" },
            { code: "PE", name: "Pernambuco" },
            { code: "PI", name: "Piauí" },
            { code: "PR", name: "Paraná" },
            { code: "RJ", name: "Rio de Janeiro" },
            { code: "RN", name: "Rio Grande do Norte" },
            { code: "RO", name: "Rondônia" },
            { code: "RR", name: "Roraima" },
            { code: "RS", name: "Rio Grande do Sul" },
            { code: "SC", name: "Santa Catarina" },
            { code: "SE", name: "Sergipe" },
            { code: "SP", name: "São Paulo" },
            { code: "TO", name: "Tocantins" }
        ]
    },
    days: {
        'br': [
            { code: "Dom", num: '0', name: "Domingo" },
            { code: "Seg", num: '1', name: "Segunda" },
            { code: "Ter", num: '2', name: "Terça" },
            { code: "Qua", num: '3', name: "Quarta" },
            { code: "Qui", num: '4', name: "Quinta" },
            { code: "Sex", num: '5', name: "Sexta" },
            { code: "Sáb", num: '6', name: "Sábado" }
        ]
    },
    months: {
        'br': [
            { code: "01", short: "Jan", name: "Janeiro" },
            { code: "02", short: "Fev", name: "Fevereiro" },
            { code: "03", short: "Mar", name: "Março" },
            { code: "04", short: "Abr", name: "Abril" },
            { code: "05", short: "Mai", name: "Maio" },
            { code: "06", short: "Jun", name: "Junho" },
            { code: "07", short: "Jul", name: "Julho" },
            { code: "08", short: "Ago", name: "Agosto" },
            { code: "09", short: "Set", name: "Setembro" },
            { code: "10", short: "Out", name: "Outubro" },
            { code: "11", short: "Nov", name: "Novembro" },
            { code: "12", short: "Dez", name: "Dezembro" }
        ]
    }
};


angular.module('nblutils', [])
        .factory('$localstorage', ['$window', function ($window) {
            return {
                set: function (key, value) {
                    $window.localStorage[key] = value;
                },
                get: function (key, defaultValue) {
                    return $window.localStorage[key] || defaultValue;
                },
                setObject: function (key, value) {
                    $window.localStorage[key] = JSON.stringify(value);
                },
                getObject: function (key, defaultValue) {
                    return JSON.parse($window.localStorage[key] || '{}') || defaultValue;
                }
            };
        }])
        .factory('$provinces', function () {
            
            var provinces = nblutilsdata.provinces;
            
            return {
                get: function(country) {
                    if(provinces.hasOwnProperty(country)) {
                        return provinces[country];
                    }
                    return [];
                },
                getBy: function(country, search_by, data) {
                    if(provinces[country][0].hasOwnProperty(search_by))
                    {
                        for(var k in provinces[country]) {
                            if(provinces[country][k][search_by] == data) {
                                return provinces[country][k];
                            }
                        }
                    }
                    
                    return {};
                }
            };
        })
        .factory('$days', function () {
            
            var days = nblutilsdata.days;
    
            return {
                get: function(country) {
                    if(days.hasOwnProperty(country)) {
                        return days[country];
                    }
                    return [];
                },
                getBy: function(country, search_by, data) {
                    if(days[country][0].hasOwnProperty(search_by))
                    {
                        for(var k in days[country]) {
                            if(days[country][k][search_by] == data) {
                                return days[country][k];
                            }
                        }
                    }
                    
                    return {};
                }
            };
        })
        .factory('$months', function () {
            
            var months = nblutilsdata.months;
    
            return {
                get: function(country) {
                    if(months.hasOwnProperty(country)) {
                        return months[country];
                    }
                    return [];
                },
                getBy: function(country, search_by, data) {
                    if(months[country][0].hasOwnProperty(search_by))
                    {
                        for(var k in months[country]) {
                            if(months[country][k][search_by] == data) {
                                return months[country][k];
                            }
                        }
                    }
                    
                    return {};
                }
            };
        })
        .factory('$cep', ['$q', '$http', function ($q, $http) {
            return {
                getAddress: function(cep, use_https) {
                    var p_cep = cep;
                    p_cep = p_cep.split('.').join('');
                    p_cep = p_cep.split('-').join('');
                    
                    var url = 'http://viacep.com.br';
                    var _https = (use_https == undefined) ? true : use_https;
                    if(_https) {
                        url = 'https://viacep.com.br';
                    }
                    
                    var deferred = $q.defer();
                    $http
                        .get(url+'/ws/'+p_cep+'/json/')
                        .then(function(r) {
                            deferred.resolve(r.data);
                        }, 
                        function(r) {
                            deferred.reject(r.status);
                        });

                    return deferred.promise;

                }
            };
        }])
        .factory('loadingInterceptor', ['$q', function ($q) {
            return {
                response: function (response) {
                    angular.element('#loadingdiv').hide();
                    return response;
                },
                responseError: function (response) {
                    angular.element('#loadingdiv').hide();
                    return $q.reject(response);
                }
            };
        }])
        ;

Number.prototype.formatMoney = function(c, d, t) {
    if(this == undefined || this == null) return '0,00';
    var n = this, 
    c = isNaN(c = Math.abs(c)) ? 2 : c, 
    d = d == undefined ? "," : d, 
    t = t == undefined ? "." : t, 
    s = n < 0 ? "-" : "", 
    i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", 
    j = (j = i.length) > 3 ? j % 3 : 0;
    return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
};

String.prototype.replaceLast = function (what, replacement) {
    return this.split(' ').reverse().join(' ').replace(new RegExp(what), replacement).split(' ').reverse().join(' ');
};

String.prototype.replaceAll = function (target, replacement) {
    return this.split(target).join(replacement);
};

String.prototype.formatCNPJ = function () {
    return this.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, "$1.$2.$3/$4-$5");
};

String.prototype.formatTime = function () {
    return this.replace(/^(\d{2})(\d{2})(\d{2})/, "$1:$2:$3");
};

String.prototype.formatCEP = function () {
    return this.replace(/^(\d{5})(\d{3})/, "$1-$2");
};

String.prototype.capitalizeFirstLetter = function () {
    return this.charAt(0).toUpperCase() + this.slice(1);
};

String.prototype.formatPhone = function () {
    var n = this;
    if (n.indexOf("(") > -1) {
        return n.replace(/^(\d*)/, "$1");
    }
    else if (n.length == 3) {
        return n.replace(/^(\d{3})/, "$1");
    }
    else if (n.length == 8) {
        return n.replace(/^(\d{4})(\d{4})/, "$1-$2");
    }
    else if (n.length == 10 && !(n.indexOf('0800') == 0)) {
        return n.replace(/^(\d{2})(\d{4})(\d{4})/, "($1) $2-$3");
    }
    else if (n.length == 11 && !(n.indexOf('0800') == 0)) {
        return n.replace(/^(\d{2})(\d{1})(\d{4})(\d{4})/, "($1) $2 $3-$4");
    }
    else if (n.length == 10 && (n.indexOf('0800') == 0)) {
        return n.replace(/^(\d{4})(\d{3})(\d{3})/, "$1 $2-$3");
    }
    else if (n.length == 11 && (n.indexOf('0800') == 0)) {
        return n.replace(/^(\d{4})(\d{3})(\d{4})/, "$1 $2-$3");
    }
    return n.replace(/^(\d*)/, "+$1");
};

String.prototype.cleanMoney = function () {
    if(this.includes(',')) {
        return this.replaceAll('.', '').replaceAll(',', '.');
    }
    return this;
};

String.prototype.toMoneyFloat = function () {
    var n = this;
    n = n.replaceAll('.', '');
    n = n.replaceAll(',', '.');
    return parseFloat(n);
};

String.prototype.isNumeric = function () {
    var str = this;
    return !isNaN(parseFloat(str)) && isFinite(str);
};

String.prototype.validCPF = function () {
    var cpf_in = this;
    if(cpf_in.length <= 0)
        return false;
    
    var numeros, digitos, soma, i, resultado, digitos_iguais;
    var cpf = '';
    
    if(!cpf_in.isNumeric()) {
        for(i = 0; i < cpf_in.length; i++) {
            if(!isNaN(cpf_in.charAt(i)) && cpf_in.charAt(i) != ' ') {
                cpf += cpf_in.charAt(i);
            }
        }
    }
    else {
        cpf = cpf_in;
    }
    
    digitos_iguais = 1;
    
    if (cpf.length < 11)
        return false;
    

    for (i = 0; i < cpf.length - 1; i++) {
        if (cpf.charAt(i) != cpf.charAt(i + 1)) {
            digitos_iguais = 0;
            break;
        }
    }

    if (!digitos_iguais) {
        numeros = cpf.substring(0,9);
        digitos = cpf.substring(9);
        soma = 0;
        for (i = 10; i > 1; i--) {
            soma += numeros.charAt(10 - i) * i;
        }

        resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
        if (resultado != digitos.charAt(0)) {
            return false;
        }

        numeros = cpf.substring(0,10);
        soma = 0;
        for (i = 11; i > 1; i--) {
            soma += numeros.charAt(11 - i) * i;
        }
        resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
        if (resultado != digitos.charAt(1)) {
            return false;
        }

        return true;
    }
    else {
        return false;
    }
};

String.prototype.validCNPJ = function () {
    var cnpj_in = this;
    if(cnpj_in.length <= 0)
        return false;
    
    var numeros, digitos, soma, i, resultado, pos, tamanho, digitos_iguais;
    var cnpj = '';
    
    if(!cnpj_in.isNumeric()) {
        for(i = 0; i < cnpj_in.length; i++) {
            if(!isNaN(cnpj_in.charAt(i)) && cnpj_in.charAt(i) != ' ') {
                cnpj += cnpj_in.charAt(i);
            }
        }
    }
    else {
        cnpj = cnpj_in;
    }
    
    digitos_iguais = 1;
    if (cnpj.length < 14 && cnpj.length < 15) {
        return false;
    }

    for (i = 0; i < cnpj.length - 1; i++) {
        if (cnpj.charAt(i) != cnpj.charAt(i + 1)) {
            digitos_iguais = 0;
            break;
        }
    }

    if (!digitos_iguais) {
        tamanho = cnpj.length - 2
        numeros = cnpj.substring(0,tamanho);
        digitos = cnpj.substring(tamanho);
        soma = 0;
        pos = tamanho - 7;
        for (i = tamanho; i >= 1; i--) {
            soma += numeros.charAt(tamanho - i) * pos--;
            if (pos < 2) {
                pos = 9;
            }
        }

        resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
        if (resultado != digitos.charAt(0)) {
            return false;
        }

        tamanho = tamanho + 1;
        numeros = cnpj.substring(0,tamanho);
        soma = 0;
        pos = tamanho - 7;
        for (i = tamanho; i >= 1; i--) {
            soma += numeros.charAt(tamanho - i) * pos--;
            if (pos < 2) {
                pos = 9;
            }
        }

        resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
        if (resultado != digitos.charAt(1)) {
            return false;
        }

        return true;
    }
    else {
        return false;
    }
};

String.prototype.validEmail = function() {
    var email = this;
    var reg = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return (reg.test(email));
};

String.prototype.removeAccentedLetters = function () {
    var str = this;
    var map = {
        a : /[\xE0-\xE6]/g,
        A : /[\xC0-\xC6]/g,
        e : /[\xE8-\xEB]/g,
        E : /[\xC8-\xCB]/g,
        i : /[\xEC-\xEF]/g,
        I : /[\xCC-\xCF]/g,
        o : /[\xF2-\xF6]/g,
        O : /[\xD2-\xD6]/g,
        u : /[\xF9-\xFC]/g,
        U : /[\xD9-\xDC]/g,
        c : /\xE7/g,
        C : /\xC7/g,
        n : /\xF1/g,
        N : /\xD1/g
    };

    for (var letter in map) {
        var expReg = map[letter];
        str = str.replace(expReg,letter);
    }

    return str;
};

function shuffleArray(array) {
    for (var i = array.length - 1; i > 0; i--) {
        var j = Math.floor(Math.random() * (i + 1));
        var temp = array[i];
        array[i] = array[j];
        array[j] = temp;
    }
}

function getRandomSequence(size) {
    var arr = ['0','1','2','3','4','5','6','7','8','9'];
    var seq = '';
    for(var i = 0; i < size; i++)
    {
        seq += arr[Math.floor(Math.random() * 10)];
    }
    
    return seq;
}

function getRandomId() {
    return (moment().format('YYYYMMDDHHMMSS') + getRandomSequence(10));
}

var UtilImage = {
    whiteOne: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAAN1wAADdcBQiibeAAAABl0RVh0U29mdHdhcmUAd3d3Lmlua3NjYXBlLm9yZ5vuPBoAAABWSURBVGiB7c+BCQAhEMCw9/ff+VxCMEgzQbtmZr4H/LcDTmlE04imEU0jmkY0jWga0TSiaUTTiKYRTSOaRjSNaBrRNKJpRNOIphFNI5pGNI1oGtE0otk8xARgkNjnHAAAAABJRU5ErkJggg=='
};

function isURLAnImage(url) {
    return(url.toLowerCase().match(/\.(jpeg|jpg|gif|png)$/) != null);
}

var VarTester = {
    isUndefined: function(v) {
        if(v == undefined)
            return true;
        
        if(typeof(v) == 'undefined')
            return true;
        
        return false;
    },
    isEmpty: function(v) {
        return (v == '');
    },
    isNull: function(v) {
        return (v == null);
    },
    isZeroInt: function(v) {
        return (v == 0);
    },
    isZeroFloat: function(v) {
        return (v == 0.0);
    },
    isEmptyObject: function(v) {
        return $.isEmptyObject(v);
    },
    isVoidStr: function(v) {
        return (VarTester.isUndefined(v) || VarTester.isNull(v) || VarTester.isEmpty(v));
    }
};