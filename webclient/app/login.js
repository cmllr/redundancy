(function(){
	var loginController = function($scope, $log){

		console.log("test");

		$scope.login = function(){
			$log.log("login()");
		};
	};

	var userService = function($http){
		//TODO
	};

	var app = angular.module('redundancy');
	app.controller('loginController', ['$scope', '$log', loginController]);
}());