(function() {
    'use strict';

    var principal = function(user, $q) {
        var _identity;
        var _authenticated = false;
        /*
        var data = {
            loginName: '',
            displayName: '',
            token: '',
            roles: ''
        };*/

        var getIdentity = function() {
            return _identity;
        };

        var isIdentityResolved = function() {
            return angular.isDefined(_identity);
        };

        var isAuthenticated = function() {
            //return _authenticated;
            return _identity != null;
        };

        var isInRole = function(role) {
            if (!_authenticated || !_identity.roles)
                return false;

            return _identity.roles.indexOf(role) > -1;
        };

        var isInAnyRole = function(roles) {
            if (!_authenticated || !_identity.roles)
                return false;

            for (var i = 0; i < roles.length; i++)
                if (isInRole(roles[i]))
                    return true;

            return false;
        };

        var authenticate = function(identity) {
            _identity = identity;
            _authenticated = _identity != null;

            console.log(identity);

            if (identity) {
                localStorage.setItem('r2.identity', angular.toJson(identity));
            } else {
                localStorage.removeItem('r2.identity');
                console.log("storage cleared");
            }
        };

        var identity = function(identity) {
            var deferred = $q.defer();

            _identity = angular.fromJson(localStorage.getItem('r2.identity'));
            authenticate(_identity);
            deferred.resolve(_identity);

            return deferred.promise;
        };

        return {
            getIdentity: getIdentity,
            isIdentityResolved: isIdentityResolved,
            identity: identity,
            isAuthenticated: isAuthenticated,
            isInRole: isInRole,
            isInAnyRole: isInAnyRole,
            authenticate: authenticate
        };
    };

    var app = angular.module('redundancy');
    app.factory('principal', ['user', '$q', principal]);
}());