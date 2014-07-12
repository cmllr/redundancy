(function() {
    var registerController = function($scope, principal) {
        $scope.principal = principal;
    };

    var app = angular.module('redundancy');
    app.controller('registerController', ['$scope', 'principal', registerController]);
}());