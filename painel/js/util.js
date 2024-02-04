angular.module('SmartChurchPanel.utils', [])
        .factory('$dialogs', function ($q, $window) {
            return {
                onSave: function() {
                    var deferred = $q.defer();
                    swal({
                        title: "Salvo!",
                        text: "Alterações salvas com sucesso!",
                        type: "success",
                        confirmButtonClass: "btn-success",
                        confirmButtonText: "Ok"
                    },
                    function(){
                        deferred.resolve();
                    });
                    return deferred.promise;
                },
                beforeRemove: function () {
                    var deferred = $q.defer();
                    swal({
                        title: "Tem certeza que deseja apagar?",
                        text: "O processo de remoção é irreversível!",
                        type: "warning",
                        showCancelButton: true,
                        cancelButtonClass: "btn-default",
                        confirmButtonClass: "btn-warning",
                        confirmButtonText: "Apagar!",
                        cancelButtonText: "Cancelar",
                        closeOnConfirm: true
                    },
                    function(){
                        deferred.resolve();
                    });
                    return deferred.promise;
                },
                onRemove: function () {
                    var deferred = $q.defer();
                    swal({
                        title: "Removido!",
                        text: "Remoção realizada com sucesso",
                        type: "success",
                        confirmButtonClass: "btn-success"
                    }, function() {
                        deferred.resolve();
                    });
                    deferred.resolve();
                    return deferred.promise;
                },
                beforeChange: function () {
                    var deferred = $q.defer();
                    swal({
                        title: "Tem certeza que deseja alterar?",
                        text: "",
                        type: "info",
                        showCancelButton: true,
                        cancelButtonClass: "btn-default",
                        confirmButtonClass: "btn-info",
                        confirmButtonText: "Alterar!",
                        cancelButtonText: "Cancelar",
                        closeOnConfirm: false
                    },
                    function(){
                        deferred.resolve();
                    });
                    return deferred.promise;
                },
                onChange: function () {
                    var deferred = $q.defer();
                    swal({
                        title: "Alterado!",
                        text: "Alteração realizada com sucesso",
                        type: "success",
                        confirmButtonClass: "btn-success"
                    }, function() {
                        deferred.resolve();
                    });
                    return deferred.promise;
                },
                onSended: function () {
                    var deferred = $q.defer();
                    swal({
                        title: "Enviado!",
                        text: "Mensagem enviada com sucesso",
                        type: "success",
                        confirmButtonClass: "btn-success"
                    }, function() {
                        deferred.resolve();
                    });
                    return deferred.promise;
                },
                onRequested: function (title, msg) {
                    var deferred = $q.defer();
                    swal({
                        title: title,
                        text: msg,
                        type: "success",
                        confirmButtonClass: "btn-success"
                    }, function() {
                        deferred.resolve();
                    });
                    return deferred.promise;
                },
                beforeNotify: function(title, msg) {
                    var deferred = $q.defer();
                    swal({
                        title: title,
                        text: msg
                    },
                    function(){
                        deferred.resolve();
                    });
                    return deferred.promise;
                }
            };
        })
        .factory('$uploadProgress', function ($window) {
            var toastr = {};
            return {
                show: function(progress) {
                    if(progress < 100) {
                        if($.isEmptyObject(toastr)) {
                            toastr = $.toast({ 
                                text : 'Envio em ' + progress + '', 
                                showHideTransition : 'plain',  
                                allowToastClose : false,       
                                hideAfter : false,             
                                stack : 5,                     
                                textAlign : 'left',           
                                position : 'bottom-right'      
                            });
                        }
                        else {
                            toastr.update({text : 'Envio em ' + progress + '%'});
                        }
                    }
                    else {
                        if(!$.isEmptyObject(toastr)) {
                            toastr.reset();
                        }
                    }
                }
            };
        })
        .factory('$notifications', function ($q, $window) {
            return {
                err: function(msg) {
                    $.gritter.add({
                        title: 'Erro!',
                        class_name: 'ntf-err',
                        text: msg,
                        sticky: false
                    });
                },
                info: function(title, msg) {
                    $.gritter.add({
                        title: title,
                        text: msg,
                        sticky: false
                    });
                }
            };
        })
        .factory('$ibge', function ($q, $http) {
            return {
                getUFs: function() {
                    var deferred = $q.defer();
                    $http
                        .get('https://servicodados.ibge.gov.br/api/v1/localidades/estados')
                        .then(function(r) {
                            deferred.resolve(r.data);
                        }, 
                        function(r) {
                            deferred.reject('Não foi possível realizar a operação :( erro #'+r.status);
                        });

                    return deferred.promise;
                },
                getMunicipios: function(uf) {
                    var deferred = $q.defer();
                    $http
                        .get('https://servicodados.ibge.gov.br/api/v1/localidades/estados/'+uf+'/municipios')
                        .then(function(r) {
                            deferred.resolve(r.data);
                        }, 
                        function(r) {
                            deferred.reject('Não foi possível realizar a operação :( erro #'+r.status);
                        });

                    return deferred.promise;
                }
            };
        })
        .factory('$context', function ($q, $window, $localstorage) {
            return {
                setIgrejaContext: function(id) {
                    $localstorage.set('manage_igreja', id);
                    $localstorage.set('active_context', Status.ATIVO);
                },
                getIgrejaContext: function() {
                    return $localstorage.get('manage_igreja', '');
                },
                setPastorContext: function(id) {
                    $localstorage.set('manage_pastor', id);
                    $localstorage.set('active_context', Status.ATIVO);
                },
                getPastorContext: function() {
                    return $localstorage.get('manage_pastor', '');
                },
                setEvangelistaContext: function(id) {
                    $localstorage.set('manage_evangelista', id);
                    $localstorage.set('active_context', Status.ATIVO);
                },
                getEvangelistaContext: function() {
                    return $localstorage.get('manage_evangelista', '');
                },
                setConselhoContext: function(id) {
                    $localstorage.set('manage_conselho', id);
                    $localstorage.set('active_context', Status.ATIVO);
                },
                getConselhoContext: function() {
                    return $localstorage.get('manage_conselho', '');
                },
                setJuntaContext: function(id) {
                    $localstorage.set('manage_junta', id);
                    $localstorage.set('active_context', Status.ATIVO);
                },
                getJuntaContext: function() {
                    return $localstorage.get('manage_junta', '');
                },
                setEventoContext: function(id) {
                    $localstorage.set('manage_evento', id);
                    $localstorage.set('active_context', Status.ATIVO);
                },
                getEventoContext: function() {
                    return $localstorage.get('manage_evento', '');
                },
                setEleicaoContext: function(id) {
                    $localstorage.set('manage_eleicao', id);
                    $localstorage.set('active_context', Status.ATIVO);
                },
                getEleicaoContext: function() {
                    return $localstorage.get('manage_eleicao', '');
                },
                setSociedadeContext: function(id) {
                    $localstorage.set('manage_sociedade', id);
                    $localstorage.set('active_context', Status.ATIVO);
                },
                getSociedadeContext: function() {
                    return $localstorage.get('manage_sociedade', '');
                },
                setFederacaoContext: function(id) {
                    $localstorage.set('manage_federacao', id);
                    $localstorage.set('active_context', Status.ATIVO);
                },
                getFederacaoContext: function() {
                    return $localstorage.get('manage_federacao', '');
                },
                setSinodalContext: function(id) {
                    $localstorage.set('manage_sinodal', id);
                    $localstorage.set('active_context', Status.ATIVO);
                },
                getSinodalContext: function() {
                    return $localstorage.get('manage_sinodal', '');
                },
                setSecretariaContext: function(id) {
                    $localstorage.set('manage_secretaria', id);
                    $localstorage.set('active_context', Status.ATIVO);
                },
                getSecretariaContext: function() {
                    return $localstorage.get('manage_secretaria', '');
                },
                setMinisterioContext: function(id) {
                    $localstorage.set('manage_ministerio', id);
                    $localstorage.set('active_context', Status.ATIVO);
                },
                getMinisterioContext: function() {
                    return $localstorage.get('manage_ministerio', '');
                },
                setEbdContext: function(id) {
                    $localstorage.set('manage_ebd', id);
                    $localstorage.set('active_context', Status.ATIVO);
                },
                getEbdContext: function() {
                    return $localstorage.get('manage_ebd', '');
                },
                addContextMenu(menu) {
                    var m = $localstorage.get('context_menu', Menus.NONE);
                    if(m.length > 0) {
                        m += ',';
                    }
                    m += menu;
                    $localstorage.set('context_menu', m);
                },
                getContextMenus: function() {
                    return $localstorage.get('context_menu', Menus.NONE);
                },
                getActiveContext: function() {
                    if($localstorage.get('manage_igreja', '') != '') { return {id: $localstorage.get('manage_igreja', ''), key: Contexts.IGREJAS}; }
                    if($localstorage.get('manage_pastor', '') != '') { return {id: $localstorage.get('manage_pastor', ''), key: Contexts.PASTORES}; }
                    if($localstorage.get('manage_evangelista', '') != '') { return {id: $localstorage.get('manage_evangelista', ''), key: Contexts.EVANGELISTAS}; }
                    if($localstorage.get('manage_conselho', '') != '') { return {id: $localstorage.get('manage_conselho', ''), key: Contexts.CONSELHOS}; }
                    if($localstorage.get('manage_junta', '') != '') { return {id: $localstorage.get('manage_junta', ''), key: Contexts.JUNTAS}; }
                    if($localstorage.get('manage_evento', '') != '') { return {id: $localstorage.get('manage_evento', ''), key: Contexts.EVENTOS}; }
                    if($localstorage.get('manage_eleicao', '') != '') { return {id: $localstorage.get('manage_eleicao', ''), key: Contexts.ELEICOES}; }
                    if($localstorage.get('manage_sociedade', '') != '') { return {id: $localstorage.get('manage_sociedade', ''), key: Contexts.SOCIEDADES}; }
                    if($localstorage.get('manage_federacao', '') != '') { return {id: $localstorage.get('manage_federacao', ''), key: Contexts.FEDERACOES}; }
                    if($localstorage.get('manage_sinodal', '') != '') { return {id: $localstorage.get('manage_sinodal', ''), key: Contexts.SINODAIS}; }
                    if($localstorage.get('manage_secretaria', '') != '') { return {id: $localstorage.get('manage_secretaria', ''), key: Contexts.SECRETARIAS}; }
                    if($localstorage.get('manage_ministerio', '') != '') { return {id: $localstorage.get('manage_ministerio', ''), key: Contexts.MINISTERIOS}; }
                    if($localstorage.get('manage_ebd', '') != '') { return {id: $localstorage.get('manage_ebd', ''), key: Contexts.EBDS}; }
                },
                hasActive() {
                    return ($localstorage.get('active_context') == Status.ATIVO);
                },
                end: function() {
                    $localstorage.set('manage_igreja', '');
                    $localstorage.set('manage_pastor', '');
                    $localstorage.set('manage_evangelista', '');
                    $localstorage.set('manage_conselho', '');
                    $localstorage.set('manage_junta', '');
                    $localstorage.set('manage_evento', '');
                    $localstorage.set('manage_eleicao', '');
                    $localstorage.set('manage_sociedade', '');
                    $localstorage.set('manage_federacao', '');
                    $localstorage.set('manage_sinodal', '');
                    $localstorage.set('manage_secretaria', '');
                    $localstorage.set('manage_ministerio', '');
                    $localstorage.set('manage_ebd', '');
                    $localstorage.set('context_menu', Menus.NONE);
                    $localstorage.set('active_context', Status.NAO_ATIVO);
                }
            };
        })
        .factory('$contextosAgendaForm', function () { 
            
            var contextos = [
                    { label: 'Todos', value: 'geral', checked: false },
                    { label: 'Pastor', value: 'pastor', checked: false },
                    { label: 'Evangelista', value: 'evangelista', checked: false },
                    { label: 'Conselho', value: 'conselho', checked: false },
                    { label: 'Junta Diaconal', value: 'diacono', checked: false },
                    { label: 'Igreja', value: 'igreja', checked: false }
                ];
            
            return {
                get: function() {
                    return contextos;
                }
            };
        })
        .factory('$smartapp', function ($q, $window, $localstorage) { 
            var views = {
                igreja: false,
                sociedade: false
            };
            return {
                setIgrejaView: function(igreja) {
                    $localstorage.set('ver_igreja', igreja);
                },
                getIgrejaView: function() {
                    return $localstorage.get('ver_igreja', '');
                },
                setSociedadeView: function(sociedade) {
                    $localstorage.set('ver_sociedade', sociedade);
                },
                getSociedadeView: function() {
                    return $localstorage.get('ver_sociedade', '');
                },
                checkViews: function() {
                    var igreja = $localstorage.set('ver_igreja', '');
                    var sociedade = $localstorage.set('ver_sociedade', '');
                    
                    views.igreja = (igreja != '');
                    views.sociedade = (sociedade != '');
                    
                    return (views.igreja || views.sociedade);
                },
                end: function() {
                    $localstorage.set('ver_igreja', '');
                    $localstorage.set('ver_sociedade', '');
                }
            };
        })
        .factory('$modulos', function () {
            
            var modulos = {
                MOD_BASE: {
                    id: 'MOD_BASE', 
                    label: 'Básico', 
                    desc: 'gestão de usuários e dados básicos do sistema'
                },
                MOD_B_IGREJA: {
                    id: 'MOD_B_IGREJA', 
                    label: 'Igreja (base)', 
                    desc: 'gestão básica de igreja: membresia, sociedade interna, obreiros'
                },
                MOD_IGREJA: {
                    id: 'MOD_IGREJA', 
                    label: 'Igreja (avançado)', 
                    desc: 'gestão avançada de igreja: congregações, pontos de pregação, ministérios, pequenos grupos, secretarias, EBD, sermões'
                },
                MOD_EBD: {
                    id: 'MOD_EBD', 
                    label: 'Escola Bíblica Dominical', 
                    desc: 'gestão da ebd: superintendentes, salas, alunos, frequências, conteúdo'
                },
                MOD_AGENDA: {
                    id: 'MOD_AGENDA', 
                    label: 'Agenda', 
                    desc: 'gestão de agenda: eventos, atividades'
                },
                MOD_DOCUMENTOS: {
                   id: 'MOD_DOCUMENTOS', 
                    label: 'Documentos', 
                    desc: 'gestão de documentos: atas, relatórios, aprovações de documentos'
                },
                MOD_EVENTOS: {
                   id: 'MOD_EVENTOS', 
                    label: 'Eventos', 
                    desc: 'gestão de eventos: eventos, check-in, comissões'
                },
                MOD_ELEICOES: {
                   id: 'MOD_ELEICOES', 
                    label: 'Eleições', 
                    desc: 'gestão de eleições'
                },
                MOD_FINANCAS: {
                   id: 'MOD_FINANCAS', 
                    label: 'Finanças', 
                    desc: 'gestão financeira'
                },
                MOD_CONFEDERACAO: {
                   id: 'MOD_CONFEDERACAO', 
                    label: 'Confederações', 
                    desc: 'gestão de presbitério/federação, sínodo/sinodal'
                }
            };
            
            return {
                get: function() {
                    return modulos;
                },
                getForm: function() {
                    return [
                        { id: modulos.MOD_BASE.id, label: modulos.MOD_BASE.label, desc: modulos.MOD_BASE.desc, checked: true, required: true },
                        { id: modulos.MOD_B_IGREJA.id, label: modulos.MOD_B_IGREJA.label, desc: modulos.MOD_B_IGREJA.desc, checked: true, required: true },
                        { id: modulos.MOD_IGREJA.id, label: modulos.MOD_IGREJA.label, desc: modulos.MOD_IGREJA.desc, checked: false, required: false },
                        { id: modulos.MOD_EBD.id, label: modulos.MOD_EBD.label, desc: modulos.MOD_EBD.desc, checked: false, required: false },
                        { id: modulos.MOD_AGENDA.id, label: modulos.MOD_AGENDA.label, desc: modulos.MOD_AGENDA.desc, checked: false, required: false },
                        { id: modulos.MOD_DOCUMENTOS.id, label: modulos.MOD_DOCUMENTOS.label, desc: modulos.MOD_DOCUMENTOS.desc, checked: false, required: false },
                        { id: modulos.MOD_EVENTOS.id, label: modulos.MOD_EVENTOS.label, desc: modulos.MOD_EVENTOS.desc, checked: false, required: false },
                        { id: modulos.MOD_ELEICOES.id, label: modulos.MOD_ELEICOES.label, desc: modulos.MOD_ELEICOES.desc, checked: false, required: false },
                        { id: modulos.MOD_FINANCAS.id, label: modulos.MOD_FINANCAS.label, desc: modulos.MOD_FINANCAS.desc, checked: false, required: false },
                        { id: modulos.MOD_CONFEDERACAO.id, label: modulos.MOD_CONFEDERACAO.label, desc: modulos.MOD_CONFEDERACAO.desc, checked: false, required: false }
                    ];
                }
            };
        })
        .factory('$generator', function () {
            return {
                randomPwd: function(plength) {
                    var keylistalpha = "abcdefghijklmnopqrstuvwxyz";
                    var keylistalphaUpper = keylistalpha.toUpperCase();
                    var keylistint = "0123456789";
                    var keylistspec = "!@_";
                    var temp = '' ;
                    var len = plength/2;
                    var len = len - 1;
                    var lenspec = plength-len-len;

                    var i;
                    for (i = 0; i < len; i++) {
                        temp += keylistalpha.charAt(Math.floor(Math.random()*keylistalpha.length));
                    }

                    for (i = 0; i < len; i++) {
                        temp += keylistalphaUpper.charAt(Math.floor(Math.random()*keylistalphaUpper.length));
                    }

                    for (i = 0; i < lenspec; i++) {
                        temp += keylistspec.charAt(Math.floor(Math.random()*keylistspec.length));
                    }

                    for (i = 0; i < len; i++) {
                        temp += keylistint.charAt(Math.floor(Math.random()*keylistint.length));
                    }

                    temp = temp.split('').sort(function(){return 0.5-Math.random()}).join('');
                    return temp;
                },
                randomEmail: function(l) {
                    var keylistalpha = "abcdefghijklmnopqrstuvwxyz";
                    var keylistint = "0123456789";
                    var temp = '';
                    var len = l - 1;
                    var len_i = len / 2;

                    var i;
                    for (i = 0; i < len; i++) {
                        temp += keylistalpha.charAt(Math.floor(Math.random()*keylistalpha.length));
                    }

                    for (i = 0; i < len_i; i++) {
                        temp += keylistint.charAt(Math.floor(Math.random()*keylistint.length));
                    }

                    temp = temp.split('').sort(function(){return 0.5-Math.random()}).join('');
                    return temp;
                },
                randomId: function(l) {
                    var keylistint = "0123456789";
                    var temp = '';
                    
                    var i;
                    for (i = 0; i < l; i++) {
                        temp += keylistint.charAt(Math.floor(Math.random()*keylistint.length));
                    }
                    
                    temp = temp.split('').sort(function(){return 0.5-Math.random()}).join('');
                    return temp;
                }
            };
        })
        .factory('$cache', function ($localstorage) { 
            var local_cache = {};
            return {
                set: function(r) {
                    $localstorage.setObject('cache', r);
                },
                get: function() {
                    if($.isEmptyObject(local_cache)) {
                        local_cache = $localstorage.getObject('cache', {});
                    }
                    return local_cache;
                },
                end: function() {
                    $localstorage.setObject('cache', {});
                    local_cache = {};
                }
            };
        })
;

var Endpoint = {
    URL: 'https://www.smartchurch.software/api',
    RC: 'https://www.smartchurch.software/api/rc',
    BASE: 'https://www.smartchurch.software/painel',
    INSCRICOES: 'https://www.smartchurch.software/inscricoes',
    MURAL: 'https://www.smartchurch.software/mural',
    VALIDACAO: 'https://www.smartchurch.software/painel/validacao',
    init: function() {
        
        if(window.location.href.includes("smartchurch.local"))
        {
            Endpoint.URL = 'http://smartchurch.local/api';
            Endpoint.RC = 'http://smartchurch.local/api/rc';
            Endpoint.BASE = 'http://smartchurch.local/painel';
            Endpoint.INSCRICOES = 'http://smartchurch.local/inscricoes';
            Endpoint.MURAL = 'http://smartchurch.local/mural';
            Endpoint.VALIDACAO = 'http://smartchurch.local/painel/validacao';
        }
        
    }
};
Endpoint.init();

/**
 * Converte string para objeto
 * 
 * @returns {object}
 */
String.prototype.toObj = function() {
    var properties = this.split(',');
    var obj = {};
    var re = /([a-zA-Z0-9]+)\s*:\s*"([a-zA-Z0-9]+)"/;
    properties.forEach(function(property) {
        var match = re.exec(property);
        if(match != null) {
            obj[match[1]] = match[2];
        }
    });
    return obj;
};

/**
 * Processa uma string de state (angular) para objeto
 * 
 * @returns {object}
 */
String.prototype.parseState = function() {
    var re = /([a-zA-Z0-9\.]+)(\(([a-zA-Z0-9"'\\:{}\s,]*)\))*/;
    var match = re.exec(this);
    if(match != null) {
        return {
            state: match[1],
            params: (match[3] != undefined) ? match[3].toObj() : {}
        };
    }
    
    return { state: '', params: {} };
};

