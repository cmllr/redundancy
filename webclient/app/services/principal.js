(function() {
    var principal = function() {
        return {};
    }

    var app = angular.module('redundancy');
    app.service('principal', principal);
}());