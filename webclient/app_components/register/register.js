(function() {
    'use strict';

    var registerController = function(principal, user) {
        var vm = this;

        vm.principal = principal;
        console.log(principal);

        vm.register = function() {
            console.log(vm.user);
            user.registerUser(vm.user.loginName, vm.user.displayName, vm.user.mailAddress, vm.user.password)
                .success(onRegisterSuccess).error(onRegisterError);
        };

        var onRegisterSuccess = function(response) {

            //Save principal for authentification
            vm.principal.displayName = response.DisplayName;
            vm.principal.loginName = response.LoginName;
        };

        var onRegisterError = function(response) {

            console.log(response);
        };

        vm.interacted = function(field) {
            return vm.submitted || field.$dirty;
        };
    };

    angular.module('redundancy')
        .controller('registerController', ['principal', 'user', registerController]);
}());