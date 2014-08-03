(function() {
    'use strict';

    var principal = function() {
        return {
            loginName: '',
            displayName: '',
            token: ''
        };
    };

    var app = angular.module('redundancy');
    app.service('principal', principal);
}());