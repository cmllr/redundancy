(function() {
    var config = function($httpProvider, $stateProvider, $urlRouterProvider) {

        //code from: http://victorblog.com/2012/12/20/make-angularjs-http-service-behave-like-jquery-ajax/
        //necessary to make angular requests work like jquery ajax requests
        // Use x-www-form-urlencoded Content-Type
        $httpProvider.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';

        /**
         * The workhorse; converts an object to x-www-form-urlencoded serialization.
         * @param {Object} obj
         * @return {String}
         */
        var param = function(obj) {
            var query = '',
                name, value, fullSubName, subName, subValue, innerObj, i;

            for (name in obj) {
                value = obj[name];

                if (value instanceof Array) {
                    for (i = 0; i < value.length; ++i) {
                        subValue = value[i];
                        fullSubName = name + '[' + i + ']';
                        innerObj = {};
                        innerObj[fullSubName] = subValue;
                        query += param(innerObj) + '&';
                    }
                } else if (value instanceof Object) {
                    for (subName in value) {
                        subValue = value[subName];
                        fullSubName = name + '[' + subName + ']';
                        innerObj = {};
                        innerObj[fullSubName] = subValue;
                        query += param(innerObj) + '&';
                    }
                } else if (value !== undefined && value !== null)
                    query += encodeURIComponent(name) + '=' + encodeURIComponent(value) + '&';
            }

            return query.length ? query.substr(0, query.length - 1) : query;
        };

        // Override $http service's default transformRequest
        $httpProvider.defaults.transformRequest = [

            function(data) {
                return angular.isObject(data) && String(data) !== '[object File]' ? param(data) : data;
            }
        ];

        //routing

        //for any unmatched url, redirect to /login
        $urlRouterProvider.otherwise('/login');
        var register = {
                name: 'register',
                url: '/register',
                controller: 'registerController as register',
                templateUrl: 'app/templates/register.tpl.html'
            },
            login = {
                name: 'login',
                url: '/login',
                controller: 'loginController as login',
                templateUrl: 'app/templates/login.tpl.html'
            },
            main = {
                name: 'main',
                url: '/main',
                controller: 'mainController as main',
                templateUrl: 'app/templates/main.tpl.html'
            };
        $stateProvider.state(register);
        $stateProvider.state(login);
        $stateProvider.state(main);

        //TODO: IMPORTANT: Use of abstract controller
    };

    angular.module('redundancy').config(['$httpProvider', '$stateProvider', '$urlRouterProvider', config]);
}());