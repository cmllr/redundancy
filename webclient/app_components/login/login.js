(function() {
    'use strict';

    var loginController = function($scope, $log, user, principal, $state) {
        var vm = this;
        vm.principal = principal;
        vm.loginErrors = {};

        vm.user = {
            loginName: vm.principal.loginName
        };

        vm.login = function() {
            //TODO: implement stayLoggedIn parameter Checkbox
            login(vm.user.loginName, vm.user.password, true);
        };

        var login = function(loginName, password, stayLoggedIn) {
            user.login(loginName, password, stayLoggedIn)
                .then(onLoginSuccess, onLoginError);
        };

        var validateErrors = function(errorcode) {
            switch (errorcode) {
                case '7':
                    vm.loginErrors.wrongPasswordOrLoginName = true;
                    break;

                    //if there are no errors, reset all errors
                default:
                    for (var prop in vm.loginErrors)
                        if (vm.loginErrors.hasOwnProperty(prop))
                            vm.loginErrors[prop] = false;
            }
        };

        var onLoginSuccess = function(response) {
            var token = response.data.substring(1, response.data.length - 1);

            principal.authenticate({
                loginName: vm.user.loginName,
                token: token,
                roles: ['user'] //FOR TESTS HARDCODED!!!
            });
            /*
            principal.loginName = vm.user.loginName;
            principal.token = token;*/
            console.log($scope.returnToState);

            //reset errors
            validateErrors();

            if ($scope.returnToState)
                $state.go($scope.returnToState.name, $scope.returnToStateParams);
            else
                $state.go('main.start');
        };

        var onLoginError = function(response) {
            validateErrors(response.data);
        };
    };

    angular.module('redundancy')
        .controller('loginController', ['$scope', '$log', 'user', 'principal', '$state', loginController]);
}());