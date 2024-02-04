
var Endpoint = {
    URL: 'https://www.smartchurch.software/api',
    RC: 'https://www.smartchurch.software/api/rc',
    BASE: 'https://www.smartchurch.software/mural',
    init: function() {
        
        if(window.location.href.includes("smartchurch.local"))
        {
            Endpoint.URL = 'http://smartchurch.local/api';
            Endpoint.RC = 'http://smartchurch.local/api/rc';
            Endpoint.BASE = 'http://smartchurch.local/mural';
        }
        
    }
};
Endpoint.init();
