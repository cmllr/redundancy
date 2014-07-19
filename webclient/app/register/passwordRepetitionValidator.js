//original code from: http://www.yearofmoo.com/2014/05/how-to-use-ngmessages-in-angularjs.html#angular-1-3-and-above
(function() {
    var directive = function(user) {
        return {
            require: 'ngModel',
            link: function(scope, element, attrs, ngModel) {
                var password = attrs.passwordRepetitionValidator;

                var setAsPasswordIsRepeated = function(bool) {
                    ngModel.$setValidity('passwordIsRepeated', bool);
                };

                ngModel.$parsers.push(function(value) {
                    console.log(value);
                    if (!value | value.length === 0) return;

                    console.log(password);
                    setAsPasswordIsRepeated(password === value);

                    return value;
                });
            }
        }
    };

    angular.module('redundancy').directive('passwordRepetitionValidator', directive);
}());