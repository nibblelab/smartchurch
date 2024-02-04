
(function() {
    'use strict';

    angular
        .module('SmartChurchMural', [
            'SmartChurchMural.config',
            'ngSanitize',
            'nblutils'
        ]);
})();

(function() {
    'use strict';

    angular
        .module('SmartChurchMural.config', []);
})();

(function() {
    'use strict';

    angular
        .module('SmartChurchMural.config', [])
        .constant('ApiEndpoint', {
                url: Endpoint.URL,
                rc: Endpoint.RC,
                base: Endpoint.BASE
        })
        
        .constant('Versions', {
            html: '0.0.1'
        })
        
        .factory('loadingIntercept', ['$q', function ($q) {
            return {
                response: function (response) {
                    angular.element('.pace').addClass('pace-inactive');
                    return response;
                },
                responseError: function (response) {
                    angular.element('.pace').addClass('pace-inactive');
                    return $q.reject(response);
                }
            };
        }])
        
        .config(['$httpProvider', function($httpProvider) {
                $httpProvider.defaults.headers.common = {};
                $httpProvider.defaults.headers.post = {};
                $httpProvider.defaults.headers.get = {};
                $httpProvider.defaults.headers.put = {};
                $httpProvider.defaults.headers.patch = {};
                $httpProvider.interceptors.push('loadingIntercept');
                var spinnerFunction = function (data, headersGetter) {
                    if(data != undefined) {
                        var d = $.parseJSON(data);
                        if(d.hasOwnProperty('ignoreLoading')) {
                            if(d.ignoreLoading) {
                                return data;
                            }
                        }
                    }
                    angular.element('.pace').removeClass('pace-inactive');
                    return data;
                };
                $httpProvider.defaults.transformRequest.push(spinnerFunction);
            }
        ])
        
        .config(['$locationProvider', function($locationProvider) {
                $locationProvider.html5Mode(true);
                $locationProvider.hashPrefix('');
        }])
        
        .run(function() {
            moment.locale('pt-BR');
        })
        
    ;
})();

(function() {
    'use strict';

    angular
        .module('SmartChurchMural')
        .factory('Mural', function ($q, $http, ApiEndpoint) {
            
            var byId = function(id) {
                var deferred = $q.defer();
                $http
                    .get(ApiEndpoint.url+'/mural/byId?id='+id)
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
                byId: byId
            };
        })
        ;
})();

(function() {
    'use strict';

    angular
        .module('SmartChurchMural')
        .controller('MuralCtrl', function($location, $sce, ApiEndpoint, Mural) {
            var vm = this;
    
            vm.loaded = false;
            vm.data = {
                titulo: '',
                conteudo: '',
                img_url: '',
                videoData: {
                    url: '',
                    type: ''
                },
                audioData: {
                    url: ''
                },
                mural_content: {
                    hasOne: false,
                    text: false,
                    img: false,
                    video: false,
                    audio: false
                }
            };
            
            vm.procVideo = function(video) {
                var videoData = {
                    url: '',
                    type: ''
                };

                // veja se é youtube ou vimeo
                if(video.includes('vimeo')) {
                    var found  = video.match(/vimeo\.com\/([\da-zA-Z_\-]*)/i);
                    if(found != null && found.length > 1) {
                        videoData.url = $sce.trustAsResourceUrl('https://player.vimeo.com/video/' + found[1] + '?title=0&byline=0');
                        videoData.type = 'Vimeo';
                    }
                }
                else if(video.includes('youtube')) {
                    var found = video.match(/youtube\.com\/watch\?v=([\da-zA-Z_\-]*)/i);
                    if(found != null && found.length > 1) {
                        videoData.url = $sce.trustAsResourceUrl('https://www.youtube.com/embed/' + found[1]);
                        videoData.type = 'Youtube';
                    }
                }
                else if(video.includes('youtu.be')) {
                    var found  = video.match(/youtu\.be\/([\da-zA-Z_\-]*)/i);
                    if(found != null && found.length > 1) {
                        videoData.url = $sce.trustAsResourceUrl('https://www.youtube.com/embed/' + found[1]);
                        videoData.type = 'Youtube';
                    }
                }

                return videoData;
            };

            vm.procAudio = function(audio) {
                var audioData = {
                    url: ''
                };

                var found  = audio.match(/tracks\/([\da-zA-Z_\-]*)/i);
                if(found != null && found.length > 1) {
                    audioData.url = $sce.trustAsResourceUrl("https://w.soundcloud.com/player/?url="+
                            "https%3A//api.soundcloud.com/tracks/"+ found[1] +
                            "&color=%23ff5500&auto_play=false&hide_related=false"+
                            "&show_comments=false&show_user=true&show_reposts=false"+
                            "&show_teaser=true&visual=false");
                }

                return audioData;
            };
            
            try
            {
                var param = atob($location.path().replace('/', ''));
                var match = [...param.matchAll(/([\w]+)|([0-9]+)|([0-9]+)/g)];
                var id = match[0][0];
                Mural.byId(id).then(function(r) {
                    vm.data = $.extend(vm.data, r);

                    if(vm.data.img != '') {
                        vm.data.img_url = ApiEndpoint.rc + '/' + vm.data.img;
                        vm.data.mural_content.img = true;
                        vm.data.mural_content.hasOne = true;
                    }

                    if(vm.data.video != '' && !vm.data.mural_content.hasOne) {
                        vm.data.videoData = vm.procVideo(vm.data.video);
                        vm.data.mural_content.video = true;
                        vm.data.mural_content.hasOne = true;
                    }

                    if(vm.data.audio != '' && !vm.data.mural_content.hasOne) {
                        vm.data.audioData = vm.procAudio(vm.data.audio);
                        vm.data.mural_content.audio = true;
                        vm.data.mural_content.hasOne = true;
                    }

                    if(!vm.data.mural_content.hasOne) {
                        vm.data.mural_content.text = true;
                    }
                    
                    $('title').html(vm.data.titulo);


                    vm.loaded = true;
                }, function(e) { console.log(e); })
            } catch (e) { console.log(e); }
            
        });
})();
