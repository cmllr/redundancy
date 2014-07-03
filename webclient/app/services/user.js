(function(){
	var user = function($http){
		var apiUrl = 'http://localhost/redundancy/Includes/api.inc.php';
		var module = 'Kernel.UserKernel';

		var post = function(method, args){
			var params = {
				module: module,
				method: method
			};
			if(args) //arguments are optional
				params.args = args;
			return $http.post(apiUrl, params);
		};

		//API functions
		var registerUser = function(loginName, displayName, mailAddress, password){
			var args = {
				loginName: loginName,
				displayName: displayName, 
				mailAddress: mailAddress,
				password: password
			};
			return post('RegisterUser', args);
		};

		var deleteUser = function(loginName, password){
			var args = {
				loginName: loginName,
				password: password
			};
			return post('DeleteUser', args);
		};

		var changePassword = function(token, oldPassword, newPassword){
			var args = {
				token: token,
				oldPassword: oldPassword,
				newPassword: newPassword
			};
			return post('ChangePassword', args);
		};

		var generatePassword = function(length){
			var args = {
				length: length
			};
			return post('GeneratePassword', args)
		};

		var resetPasswordByMail = function(mailAddress){
			var args = {
				mailAddress: mailAddress
			};
			return post('ResetPasswordByMail', args);
		};

		var getInstalledRoles = function(){
			return post('GetInstalledRoles');
		};

		var authentificate = function(loginName, password){
			var args = {
				loginName: loginName,
				password: password
			};
			return post('Authentificate', args);
		};

		var login = function(loginName, password, stayLoggedIn){
			var args = {
				loginName: loginName,
				password: password, 
				stayLoggedIn: stayLoggedIn
			};
			return post('Login', args);
		};

		var getSessionByCookie = function(){
			return post('GetSessionByCookie');
		};

		var killSessionByToken = function(token){
			var args = {
				token: token
			};
			return post('KillSessionByToken', args);
		};

		var isSessionExisting = function(token){
			var args = {
				token: token
			};
			return post('IsSessionExisting', args);
		};
		//end API functions

		return {
			registerUser: registerUser,
			deleteUser: deleteUser,
			changePassword: changePassword,
			generatePassword: generatePassword,
			resetPasswordByMail: resetPasswordByMail,
			getInstalledRoles: getInstalledRoles,
			getUser: getUser,
			authentificate: authentificate,
			login: login,
			getSessionByCookie: getSessionByCookie,
			killSessionByToken: killSessionByToken,
			isSessionExisting: isSessionExisting
		};
	};

	angular.module('redundancy').factory('user', ['$http', user]);
}());