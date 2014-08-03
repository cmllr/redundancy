//original code from: http://www.yearofmoo.com/2014/05/how-to-use-ngmessages-in-angularjs.html#angular-1-3-and-above
(function() {
    var directive = function(user) {
        return {
            require: 'ngModel',
            link: function(scope, element, attrs, ngModel) {
                var setAsLoading = function(bool) {
                    ngModel.$setValidity('usernameOrMailAddressLoading', !bool);
                };
                var setAsAvailable = function(bool) {
                    ngModel.$setValidity('usernameOrMailAddressAvailable', bool);
                };

                ngModel.$parsers.push(function(value) {
                    if (!value | value.length === 0) return;

                    // setAsLoading(true);
                    // setAsAvailable(false);

                    var onCheckingError = function() {
                        setAsLoading(false);
                        setAsAvailable(false);
                    };

                    var onCheckingSuccess = function(response) {
                        if (response === 'true') {
                            setAsLoading(false);
                            setAsAvailable(true);
                        } else
                            onCheckingError();
                    };

                    user.isLoginOrMailFree(value).success(onCheckingSuccess).error(onCheckingError);
                    return value;
                });
            }
        };
    };

    angular.module('redundancy').directive('usernameOrMailAddressAvailabilityValidator', ['user', directive]);
}());