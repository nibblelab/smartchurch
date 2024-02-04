angular.module('SmartChurchPanel').controller('SmartChurchPanelCtrl', function($scope, $context, $cache, $state, $rootScope, $http, $notifications, 
            ApiEndpoint, User, Versions) {

    if (!User.isLogged()) {
        $state.go('login');
        return;
    }

    /* versões */
    $scope.versions = Versions;
    $scope.menu_html = 'templates/menu.html?v=' + $scope.versions.html;
    $scope.top_menu_html = 'templates/top-menu.html?v=' + $scope.versions.html;
    
    /* dados comuns */
//    $scope.web_logo = ApiEndpoint.base + '/img/web_logo.png';
    $scope.user = User.get();
    $scope.USER = User;
    
    $rootScope.USER = $scope.USER;
    $rootScope.user = $scope.user;
    
//    if (VarTester.isVoidStr($scope.user.avatar)) {
//        $scope.user['avatar_img'] = ApiEndpoint.base + '/img/avatar.png';
//    }
//    else {
//        $scope.user['avatar_img'] = ApiEndpoint.rc + '/usuarios/' + $scope.user.avatar;
//    }
    
    /* menu */
    $scope.menus = {
        dashboard: {
            nome: 'SmartChurchPanel',
            titulo: 'Painel',
            icon: 'fa fa-th-large',
            modulos: ['MOD_BASE'], 
            is_active: ($state.current.name == 'SmartChurchPanel.painel'),
            is_open: false,
            is_dropdown: false,
            states: ['SmartChurchPanel.painel'],
            setted: []
        }
    };
    $scope.addMenus = function(menus) {
        for(var k in menus) {
                if(!$scope.menus.hasOwnProperty(menus[k].nome)) {
                    $scope.menus[menus[k].nome] = {
                        nome: menus[k].nome,
                        titulo: menus[k].titulo,
                        icon: menus[k].icon,
                        modulos: menus[k].modulos,
                        is_active: false,
                        is_open: false,
                        is_dropdown: true,
                        sref: '',
                        states: [],
                        perms: [],
                        setted: menus[k].setted,
                        submenus: {}
                    };

                    if(menus[k].submenus.length > 0) {
                        for(var j in menus[k].submenus) {
                            if(!$scope.menus[menus[k].nome].submenus.hasOwnProperty(menus[k].submenus[j].nome)) {
                                $scope.menus[menus[k].nome].submenus[menus[k].submenus[j].nome] = {
                                    is_active: false,
                                    titulo: menus[k].submenus[j].titulo,
                                    sref: menus[k].submenus[j].sref,
                                    states: menus[k].submenus[j].states,
                                    modulos: menus[k].submenus[j].modulos,
                                    perms: menus[k].submenus[j].perms,
                                    setted: menus[k].submenus[j].setted
                                };

                                $scope.menus[menus[k].nome].states = $scope.menus[menus[k].nome].states.concat(menus[k].submenus[j].states);
                                $scope.menus[menus[k].nome].perms = $scope.menus[menus[k].nome].perms.concat(menus[k].submenus[j].perms);

                                if($scope.menus[menus[k].nome].submenus[menus[k].submenus[j].nome].states.indexOf($state.current.name) > -1) {
                                    $scope.menus[menus[k].nome].submenus[menus[k].submenus[j].nome].is_active = true;
                                }
                            }
                        }
                    }
                    else {
                        $scope.menus[menus[k].nome].is_dropdown = false;
                        $scope.menus[menus[k].nome].sref = menus[k].sref;
                        $scope.menus[menus[k].nome].states = menus[k].states;
                        $scope.menus[menus[k].nome].perms = menus[k].perms;
                    }

                    if($scope.menus[menus[k].nome].states.indexOf($state.current.name) > -1) {
                        $scope.menus[menus[k].nome].is_active = true;
                        $scope.menus[menus[k].nome].is_open = true;
                    }
                }
            }
    };
    $scope.loadMenu = function(menu) {
        var url = '';
        if(menu.length > 0) {
            url = ApiEndpoint.base + '/data/'+menu+'/menus.json?v=' + $scope.versions.data;
        }
        
        if(url.length > 0) {
            $http.get(url).then(function(r) {
                if(r.hasOwnProperty('data')) {
                    if(r.data.hasOwnProperty('menus')) {
                        $scope.addMenus(r.data.menus);
                    }
                }
            }, function() {
                $notifications.err('Erro ao carregar os menus');
            });
        }
    };
    $rootScope.loadMenu = $scope.loadMenu;
    
    if($rootScope.USER.amIAdmin($rootScope.user)) {
        $scope.loadMenu(Menus.STAFF);
    }
    else {
        $scope.loadMenu(Menus.SMARTAPP);
        var menus = $context.getContextMenus().split(',');
        if(menus.length > 0) {
            for(var k in menus) {
                $scope.loadMenu(menus[k]);
            }
        }
    }
    
    $scope.getMenubyState = function(state) {
        for (var k in $scope.menus) {
            if ($scope.menus[k].states.indexOf(state) > -1) {
                return k;
            }
        }
        return null;
    };
    $scope.getSubMenubyState = function(menu, state) {
        for (var k in menu.submenus) {
            if (menu.submenus[k].states.indexOf(state) > -1) {
                return k;
            }
        }
        return null;
    };
    $scope.deActiveSubMenubyExcept = function(menu, except) {
        for (var k in menu.submenus) {
            if (k != except) {
                menu.submenus[k].is_active = false;
            }
        }
    };
    $scope.deActiveAllSubMenus = function(menu) {
        for (var k in menu.submenus) {
            menu.submenus[k].is_active = false;
        }
    };
    $scope.deActiveMenubyExcept = function(except) {
        for (var k in $scope.menus) {
            if (k != except) {
                $scope.menus[k].is_active = false;
                if ($scope.menus[k].is_dropdown) {
                    $scope.menus[k].is_open = false;
                }
            }
        }
    };
    $scope.activeMenubyState = function(state) {
        var menu = $scope.getMenubyState(state);
        if (menu != null) {
            $scope.menus[menu].is_active = true;
            if ($scope.menus[menu].is_dropdown) {
                $scope.menus[menu].is_open = true;
                var submenu = $scope.getSubMenubyState($scope.menus[menu], state);
                if (submenu != null) {
                    $scope.menus[menu].submenus[submenu].is_active = true;
                    $scope.deActiveSubMenubyExcept($scope.menus[menu], submenu);
                }
            }
        }
        $scope.deActiveMenubyExcept(menu);
    };
    $scope.deActivePreviousSubMenus = function(state) {
        var menu = $scope.getMenubyState(state);
        if (menu != null) {
            for (var k in $scope.menus) {
                if (k != menu) {
                    if ($scope.menus[k].is_dropdown) {
                        $scope.deActiveAllSubMenus($scope.menus[k]);
                    }
                }
            }
        }
    };
    $rootScope.$on('$stateChangeStart', function(event, toState, toParams, fromState, fromParams) {
        $scope.activeMenubyState(toState.name);
        $scope.deActivePreviousSubMenus(toState.name);
    });
    $scope.activeMenuDropDown = function(menu) {
        $scope.menus[menu].is_open = !$scope.menus[menu].is_open;
    };
//    $scope.sidebar = {
//        show: false
//    };
//    $scope.showSidebar = function() {
//        $scope.sidebar.show = !$scope.sidebar.show;
//    };

    $scope.logout = function() {
        User.logout().then(function() {
            $cache.end();
            $state.go('login');
        }, function() {});
    };

    $scope.markList = [];
    $scope.markThis = function(d) {
        var pos = -1;
        for (var k in $scope.markList) {
            if ($scope.markList[k].id == d.id) {
                pos = k;
                break;
            }
        }
        
        if (pos == -1) {
            $scope.markList.push(d);
            d.selected = true;
        } else {
            $scope.markList.splice(pos, 1);
            d.selected = false;
        }
    };

    $scope.clearMarkList = function() {
        for (var k in $rootScope.list) {
            $rootScope.list[k].selected = false;
        }
        $scope.markList = [];
    };
    
    $rootScope.list = [];
    $scope.allSelected = false;
    $scope.selectAll = function() {
        $scope.allSelected = !$scope.allSelected;
        if($scope.allSelected) {
            for (var k in $rootScope.list) {
                if($rootScope.list[k].hasOwnProperty('show')) {
                    // trate os casos em que a lista tem filtros dinâmicos aplicados
                    if(!$rootScope.list[k].show) {
                        // esse item não está visível na lista então ignore-o
                        continue;
                    }
                }
                $scope.markThis($rootScope.list[k]);
            }
        }
        else {
            $scope.clearMarkList();
        }
    };
    
    $scope.testError = function(e) {
        if(e == 'Token inválido') {
            $scope.logout();
        }
    };
    
    $scope.now = moment();
    
    $scope.toBack = function(state, params) {
        $state.go(state, params);
    };
    
    $scope.toAdd = function(state, params) {
        $state.go(state, params);
    };
    
    $scope.toEdit = function(state, id, params, id_param) {
        if(id_param == undefined) {
            id_param = 'id';
        }
        params[id_param] = id;
        $state.go(state, params);
    };
    
    $scope.toSubState = function(state, param_name, param_value) {
        var params = {};
        params[param_name] = param_value;
        $state.go(state, params);
    };
});