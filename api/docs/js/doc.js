
var Endpoint = {
    URL: 'https://www.smartchurch.software/api',
    init: function() {
        
        if(window.location.href.includes("smartchurch.local"))
        {
            Endpoint.URL = 'http://smartchurch.local/api';
        }
        
    }
};
Endpoint.init();

(function() {
    'use strict';

    angular
        .module('Docs', [
            'Docs.config'
        ]);
})();


(function() {
    'use strict';

    angular
        .module('Docs.config', [])
        .constant('ApiEndpoint', {
                url: Endpoint.URL
        })
        
        .constant('Versions', {
            html: '0.0.1',
            data: '0.0.4'
        })
    ;
})();

(function() {
    'use strict';

    angular
        .module('Docs')
        .controller('DocsCtrl', function($http, ApiEndpoint, Versions) {
            
            var vm = this;
    
            vm.doc_base_url = ApiEndpoint.url + '/docs/content';
            vm.doc_ws_url = ApiEndpoint.url + '/docs/content/ws';
    
            vm.webservices = [];
            $http.get(vm.doc_base_url + '/nblfram-doc.json?v=' + Versions.data).then(function(r) {
                vm.webservices = r.data.webservices.sort(function(a,b) {return (a > b) ? 1 : ((b > a) ? -1 : 0);} );
            });
            
            vm.webservice = {is_loaded: false, data: {}};
            vm.showWsDoc = function(ws) {
                $http.get(vm.doc_ws_url + '/'+ ws +'.json?v=' + Versions.data).then(function(r) {
                    vm.webservice.data = r.data;
                    vm.webservice.is_loaded = true;
                });
            };
            
        });
})();
