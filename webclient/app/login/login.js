(function() {
    var loginController = function($scope, $log, user, principal) {
        $scope.principal = principal;
        $scope.loginErrors = {};

        $scope.login = function() {
            //TODO: implement stayLoggedIn parameter Checkbox
            login($scope.user.loginName, $scope.user.password, true);
        };

        var login = function(loginName, password, stayLoggedIn) {
            user.login(loginName, password, stayLoggedIn)
                .then(onLoginSuccess, onLoginError);
        };

        var validateErrors = function(errorcode) {
            switch (errorcode) {
                case '7':
                    console.log("true");
                    $scope.loginErrors.wrongPasswordOrLoginName = true;
                    break;

                    //if there are no errors, reset all errors
                default:
                    console.log('reset');
                    for (var prop in $scope.loginErrors)
                        if ($scope.loginErrors.hasOwnProperty(prop))
                            $scope.loginErrors[prop] = false;
            }
        };

        var onLoginSuccess = function(response) {
            $log.info(response.data);
            if (response.data && response.data.length > 2) {
                var token = response.data.substring(1, response.data.length - 1);

                principal.loginName = $scope.user.loginName;
                principal.token = token;
                validateErrors();
            } else
                validateErrors(response.data);
        };

        var onLoginError = function(response) {
            $log.info(response.data);
        };
    };

    var app = angular.module('redundancy');
    app.controller('loginController', ['$scope', '$log', 'user', 'principal', loginController]);
}());