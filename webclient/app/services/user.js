(function(){
	var user = function($http){
		var apiUrl = 'http://localhost/redundancy/Includes/api.inc.php';
		var module = 'Kernel.UserKernel';

		var post = function(method, args){
			var params = {
				module: module,
				method: method
			};

			//arguments are optional
			if(args)
				params.args = args;
			return $http.post(apiUrl, params);
		};

		//API functions
		var registerUser = function(loginName, displayName, mailAddress, password){
			var args = [
				loginName,
				displayName, 
				mailAddress,
				password
			];
			return post('RegisterUser', args);
		};

		var deleteUser = function(loginName, password){
			var args = [
				loginName,
				password
			];
			return post('DeleteUser', args);
		};

		var changePassword = function(token, oldPassword, newPassword){
			var args = [
				token,
				oldPassword,
				newPassword
			];
			return post('ChangePassword', args);
		};

		var generatePassword = function(length){
			return post('GeneratePassword', [length])
		};

		var resetPasswordByMail = function(mailAddress){
			return post('ResetPasswordByMail', [mailAddress]);
		};

		var getInstalledRoles = function(){
			return post('GetInstalledRoles');
		};

		var getUser = function(){
			return post('GetUser', [token]);
		};

		var authentificate = function(loginName, password){
			var args = [
				loginName,
				password
			];
			return post('Authentificate', args);
		};

		var login = function(loginName, password, stayLoggedIn){
			var args = [
				loginName,
				password, 
				stayLoggedIn
			];
			return post('LogIn', args);
		};

		var getSessionByCookie = function(){
			return post('GetSessionByCookie');
		};

		var killSessionByToken = function(token){
			return post('KillSessionByToken', [token]);
		};

		var isSessionExisting = function(token){
			return post('IsSessionExisting', [token]);
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