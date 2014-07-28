(function() {
    var mainController = function($scope, principal, user) {
        $scope.principal = principal;
        console.log($scope.principal);

        var getUser = function() {
            var token = $scope.principal.token;
            user.getUser(token).success(onGetUserSuccess);
        };

        var onGetUserSuccess = function(response) {
            $scope.principal.displayName = response.DisplayName;
        };

        getUser();
    };

    var app = angular.module('redundancy');
    app.controller('mainController', ['$scope', 'principal', 'user', mainController]);
}());