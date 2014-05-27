<?php
	class UserKernelTest extends PHPUnit_Framework_TestCase{
		//helper method
		protected static function getMethod($name) 
		{
		  //Thanks to https://stackoverflow.com/questions/249664/best-practices-to-test-protected-methods-with-phpunit/2798203#2798203
		  $class  = new ReflectionClass("\Redundancy\Kernel\UserKernel");
		  $method = $class->getMethod($name);
		  $method->setAccessible(true);
		  return $method;
		}
		public function testHashPassword(){	
			$foo = self::getMethod('HashPassword');
		  	$obj = $GLOBALS["Kernel"]->UserKernel;	  
			$got = $foo->invokeArgs($obj, array("test"));
		  	$this->assertTrue($got != "" && !empty($got) && !is_null($got));
		}		
		public function testRegisterUserNotPreviousExisting(){
			$loginName = "testUser";
			$displayName = "testUser";
			$mailAddress = "mail@localhost.lan";
			$password = "test";
			$got = $value = $GLOBALS["Kernel"]->UserKernel->RegisterUser($loginName,$displayName,$mailAddress,$password);			
			$this->assertTrue(!is_null($got) && \Redundancy\Classes\Errors::UserOrEmailAlreadyGiven!=$got);
			$this->assertTrue(!is_null($got),"Got no new User object");
			$this->assertEquals($loginName,$got->LoginName);
			$this->assertEquals($displayName,$got->DisplayName);
			$this->assertEquals($mailAddress,$got->MailAddress);			
		}
		public function testRegisterUserPreviousExisting(){
			$loginName = "testUser";
			$displayName = "testUser";
			$mailAddress = "mail@localhost.lan";
			$password = "test";
			$got = $GLOBALS["Kernel"]->UserKernel->RegisterUser($loginName,$displayName,$mailAddress,$password);		
			$this->assertTrue(\Redundancy\Classes\Errors::UserOrEmailAlreadyGiven==$got);	
		}
		public function testLogInShouldSucceed(){
			$loginName = "testUser";
			$password = "test";
			$stayLoggedIn = "true";
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn($loginName,$password,$stayLoggedIn);		
			$this->assertTrue($token != \Redundancy\Classes\Errors::PasswordOrUserNameWrong);
		}
		public function testLogInShouldFail(){
			$loginName = "testUser";
			$password = "test1";
			$stayLoggedIn = "true";
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn($loginName,$password,$stayLoggedIn);		
			$this->assertTrue($token == \Redundancy\Classes\Errors::PasswordOrUserNameWrong);
		}
		public function testLogInShouldFail2(){
			$loginName = "testUser1";
			$password = "test";
			$stayLoggedIn = "true";
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn($loginName,$password,$stayLoggedIn);		
			$this->assertTrue($token == \Redundancy\Classes\Errors::PasswordOrUserNameWrong);
		}

		public function testGetInstalledRoles(){
			$value = $GLOBALS["Kernel"]->UserKernel->GetInstalledRoles();
			$expected = array();
			$expected[0] = new \Redundancy\Classes\Role();
			$expected[0]->Id = 1;
			$expected[0]->Description = "Root";
			$expected[0]->Permissions = "1";
			$this->assertEquals($value,$expected);		
		}
		public function testChangePassword(){
			// ChangePassword($token,$oldPassword,$newPassword){
			$oldPassword = "test";
			$newPassword = "test1";
			$user = "testUser";
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn($user,$oldPassword,false);	
			$got =  $GLOBALS["Kernel"]->UserKernel->ChangePassword($token,$oldPassword,$newPassword);
			$this->assertTrue($got);
		}
		public function testDeleteUser(){
			$loginName = "testUser";		
			$got = $GLOBALS["Kernel"]->UserKernel->DeleteUser($loginName,"test1");		
			$this->assertTrue($got);	
		}
		public function testDeleteUserShouldFail1(){
			$loginName = "testUser";		
			$got = $GLOBALS["Kernel"]->UserKernel->DeleteUser($loginName,"test");		
			$this->assertTrue(!$got);	
		}
		public function testDeleteUserShouldFail2(){
			$loginName = "testUser2";		
			$got = $GLOBALS["Kernel"]->UserKernel->DeleteUser($loginName,"test1");		
			$this->assertTrue(!$got);	
		}
	}
?>
