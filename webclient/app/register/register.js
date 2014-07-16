(function() {
    var registerController = function($scope, principal, user) {
        $scope.principal = principal;

        $scope.register = function() {
            console.log('Try registering...');
            user.registerUser($scope.user.userName, $scope.user.displayName, $scope.user.mailAddress, $scope.user.password)
                .success(onRegisterSuccess).error(onRegisterError);
        };

        var onRegisterSuccess = function(response) {
            console.log('Registered.');
            console.log(response);
            $scope.principal.displayName = response.DisplayName;
            $scope.principal.loginName = response.LoginName;
        };

        var onRegisterError = function(response) {
            console.log('Failed.');
            console.log(response);
        };

        $scope.arePasswordsIdentical = function() {
            return $scope.user.password === $scope.user.passwordRepetition;
        };
    };

    var app = angular.module('redundancy');
    app.controller('registerController', ['$scope', 'principal', 'user', registerController]);
}());