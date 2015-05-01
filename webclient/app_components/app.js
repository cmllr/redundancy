angular.module('redundancy', ['ui.router', 'ngMessages', 'pascalprecht.translate']);

var run = function($rootScope, $state, $stateParams, authorization, principal) {
    /* $rootScope.safeApply = function(fn) {
        var phase = $rootScope.$$phase;
        if (phase === '$apply' || phase === '$digest') {
            if (fn && (typeof(fn) === 'function')) {
                fn();
            }
        } else {
            this.$apply(fn);
        }
    };*/

    $rootScope.$on('$stateChangeStart', function(event, toState, toStateParams) {
        $rootScope.toState = toState;
        $rootScope.toStateParams = toStateParams;

        //  if (principal.isIdentityResolved())
        authorization.authorize();

        if (principal.isAuthenticated() && toState.name === 'login')
            $state.go('main.start');
    });
};

angular.module('redundancy')
    .run(['$rootScope', '$state', '$stateParams', 'authorization', 'principal', run]);