(function() {
    var loginController = function($scope, $log, user) {
        $scope.user = {};

        $scope.login = function() {
            //TODO: implement stayLoggedIn parameter Checkbox
            login($scope.user.username, $scope.user.password, true);
        };

        var login = function(username, password, stayLoggedIn) {
            user.login(username, password, stayLoggedIn)
                .then(onLoginSuccess, onLoginError);
        };

        var onLoginSuccess = function(response) {
            $log.info(response.data);
        };

        var onLoginError = function(response) {
            $log.info(response.data);
        };
    };

    var app = angular.module('redundancy');
    app.controller('loginController', ['$scope', '$log', 'user', loginController]);
}());