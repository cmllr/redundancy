(function() {
    'use strict';

    var mainController = function(principal, user) {
        var vm = this;
        vm.principal = principal;

        var getUser = function() {
            var token = vm.principal.token;
            user.getUser(token).success(onGetUserSuccess);
        };

        var onGetUserSuccess = function(response) {
            console.log(response);
            vm.principal.displayName = response.DisplayName;
        };

        getUser();
    };

    angular.module('redundancy')
        .controller('mainController', ['principal', 'user', mainController]);
}());