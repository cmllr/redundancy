(function() {
    'use strict';

    var mainController = function(principal, user, $state) {
        var vm = this;
        vm.principal = principal.getIdentity();

        var getUser = function() {
            var token = vm.principal.token;
            user.getUser(token).success(onGetUserSuccess);
        };

        var onGetUserSuccess = function(response) {
            console.log(response);
            vm.principal.displayName = response.DisplayName;
        };

        vm.logout = function() {
            principal.authenticate(null);
            $state.go('login');
        };

        getUser();
    };

    angular.module('redundancy')
        .controller('mainController', ['principal', 'user', '$state', mainController]);
}());