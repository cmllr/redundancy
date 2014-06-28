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
		//***********************Tests HashPassword()***********************
		public function testHashPassword(){	
			$foo = self::getMethod('HashPassword');
		  	$obj = $GLOBALS["Kernel"]->UserKernel;	  
			$got = $foo->invokeArgs($obj, array("test"));
		  	$this->assertTrue($got != "" && !empty($got) && !is_null($got));
		}		
		//***********************Tests RegisterUser()***********************
		public function testRegisterUserNotPreviousExisting(){
			$loginName = "testUser";
			$displayName = "testUser";
			$mailAddress = "mail@localhost.lan";
			$password = "test";
			$got = $value = $GLOBALS["Kernel"]->UserKernel->RegisterUser($loginName,$displayName,$mailAddress,$password);			
			$this->assertTrue(!is_null($got));
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
		//***********************Tests LogIn()***********************
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
		//***********************Tests GetInstalledRoles()***********************
		public function testGetInstalledRoles(){
			$value = $GLOBALS["Kernel"]->UserKernel->GetInstalledRoles();
			$expected = array();
			$expected[0] = new \Redundancy\Classes\Role();
			$expected[0]->Id = 1;
			$expected[0]->Description = "Root";
			$expected[0]->Permissions = "1";
			$this->assertEquals($value,$expected);		
		}
		//***********************Tests ChangePassword()***********************
		public function testChangePassword(){
			// ChangePassword($token,$oldPassword,$newPassword){
			$oldPassword = "test";
			$newPassword = "test1";
			$user = "testUser";
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn($user,$oldPassword,false);	
			$got =  $GLOBALS["Kernel"]->UserKernel->ChangePassword($token,$oldPassword,$newPassword);
			$testNewPassword = $GLOBALS["Kernel"]->UserKernel->Authentificate($user,$newPassword);			
			$this->assertTrue($got && $testNewPassword);
		}
		//***********************Tests GetUserRole()***********************
		public function testGetUserRoleShouldSucceed(){		
			$expected = new \Redundancy\Classes\Role();
			$expected->Id = 1;
			$expected->Description = "Root";
			$expected->Permissions = "1";
			$foo = self::getMethod('GetUserRole');
		  	$obj = $GLOBALS["Kernel"]->UserKernel;	  
			$got = $foo->invokeArgs($obj, array("testUser"));			
			$this->assertEquals($expected,$got);
		}
		public function testGetUserRoleShouldFail(){			
			$foo = self::getMethod('GetUserRole');
		  	$obj = $GLOBALS["Kernel"]->UserKernel;	  
			$got = $foo->invokeArgs($obj, array("testUserNotExisting"));			
			$this->assertTrue(is_null($got));
		}
		//***********************Tests Authentificate()***********************
		public function testAuthentificateShouldSucceed(){			
			$got =  $GLOBALS["Kernel"]->UserKernel->Authentificate("testUser","test1");
			$got2 = $GLOBALS["Kernel"]->UserKernel->Authentificate("testUser","test1");
			$this->assertTrue($got);
			$this->assertEquals($got,$got2);
		}
		public function testAuthentificateShouldFail1(){			
			$got =  $GLOBALS["Kernel"]->UserKernel->Authentificate("testUser1","test1");
			$this->assertFalse($got);
		}
		public function testAuthentificateShouldFail2(){			
			$got =  $GLOBALS["Kernel"]->UserKernel->Authentificate("testUser","test");
			$this->assertFalse($got);
		}
		//***********************Tests GetUser()***********************
		public function testGetUserShouldSucceed(){			
			$loginName = "testUser";
			$password = "test1";
			$stayLoggedIn = "true";
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn($loginName,$password,$stayLoggedIn);		
			$got =  $GLOBALS["Kernel"]->UserKernel->GetUser($token);			
			$this->assertTrue($got->LoginName == $loginName);			
			$this->assertTrue(!is_null($got));
		}
		public function testGetUserShouldFail1(){			
			$loginName = "testUser";
			$password = "test";
			$stayLoggedIn = "true";
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn($loginName,$password,$stayLoggedIn);		
			$got =  $GLOBALS["Kernel"]->UserKernel->GetUser($token);			
			$this->assertTrue(is_null($got));
		}
		public function testGetUserShouldFail2(){			
			$loginName = "testUser1";
			$password = "test";
			$stayLoggedIn = "true";
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn($loginName,$password,$stayLoggedIn);		
			$got =  $GLOBALS["Kernel"]->UserKernel->GetUser($token);			
			$this->assertTrue(is_null($got));
		}
		//***********************Tests GeneratePassword()***********************
		public function testGeneratePassword(){
			$got1 = $GLOBALS["Kernel"]->UserKernel->GeneratePassword();
			$got2 = $GLOBALS["Kernel"]->UserKernel->GeneratePassword();
			$this->assertTrue(strlen($got1) != 0);
			$this->assertTrue(strlen($got2) != 0);
			$this->assertTrue($got1 != $got2);
		}		
		//***********************TestsIsSessionExisting()***********************
		public function testIsSessionExistingShouldSucceed(){
			$loginName = "testUser";
			$password = "test1";
			$stayLoggedIn = "true";
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn($loginName,$password,$stayLoggedIn);		
			$got = $GLOBALS["Kernel"]->UserKernel->IsSessionExisting($token);			
			$this->assertTrue($got);
		}
		public function testIsSessionExistingShouldFail1(){
			$loginName = "testUser";
			$password = "test";
			$stayLoggedIn = "true";
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn($loginName,$password,$stayLoggedIn);		
			$got = $GLOBALS["Kernel"]->UserKernel->IsSessionExisting($token);			
			$this->assertFalse($got);
		}
		public function testIsSessionExistingShouldFail2(){
			$loginName = "testUser1";
			$password = "test";
			$stayLoggedIn = "true";
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn($loginName,$password,$stayLoggedIn);		
			$got = $GLOBALS["Kernel"]->UserKernel->IsSessionExisting($token);			
			$this->assertFalse($got);
		}
		//***********************TestsGenerateToken()***********************
		public function testGenerateToken(){
			$foo = self::getMethod('GenerateToken');
		  	$obj = $GLOBALS["Kernel"]->UserKernel;	  
			$token1 = $foo->invokeArgs($obj, array("testUser",date("Y-m-d H:i:s",time()),"127.0.0.1"));
			$token2 = $foo->invokeArgs($obj, array("testUser",date("Y-m-d H:i:s",time()),"127.0.0.1"));
			$this->assertTrue($token1 == $token2);
		}
		//***********************TestsIsNewSessionNeeded()***********************
		public function testIsNewSessionNeededShouldSucceed1(){
			$foo = self::getMethod('IsNewSessionNeeded');
		  	$obj = $GLOBALS["Kernel"]->UserKernel;	  
			$got = $foo->invokeArgs($obj, array("testUser"));
			$expected = $GLOBALS["Kernel"]->UserKernel->LogIn("testUser","test1",true);
			$this->assertTrue($got == $expected);
		}		
		public function testIsNewSessionNeededShouldSucceed2(){
			$foo = self::getMethod('IsNewSessionNeeded');
		  	$obj = $GLOBALS["Kernel"]->UserKernel;	  
			$got = $foo->invokeArgs($obj, array("testUserX"));		
			$this->assertTrue($got);
		}
		//***********************Tests GetFailedLoginsCounter()***********************
		public function testGetFailedLoginsCounter1(){
			$foo = self::getMethod('GetFailedLoginsCounter');
		  	$obj = $GLOBALS["Kernel"]->UserKernel;	  
			$got = $foo->invokeArgs($obj, array("testUser"));		
			$this->assertTrue($got == 0);
		}
		public function testGetFailedLoginsCounter2(){
			$GLOBALS["Kernel"]->UserKernel->Authentificate("testUser","wrongpassword");
			$foo = self::getMethod('GetFailedLoginsCounter');
		  	$obj = $GLOBALS["Kernel"]->UserKernel;	  
			$got = $foo->invokeArgs($obj, array("testUser"));			
			$this->assertTrue($got > 0);
		}
		public function testGetFailedLoginsCounter3(){
			$GLOBALS["Kernel"]->UserKernel->Authentificate("testUser","test1");
			$foo = self::getMethod('GetFailedLoginsCounter');
		  	$obj = $GLOBALS["Kernel"]->UserKernel;	  
			$got = $foo->invokeArgs($obj, array("testUser"));				
			$this->assertTrue($got ==  0);
		}		
		//***********************Tests KillSessionByToken()***********************
		public function testKillSessionByToken(){
			$loginName = "testUser";		
			$token = $GLOBALS["Kernel"]->UserKernel->LogIn($loginName,"test1",true);	
			$GLOBALS["Kernel"]->UserKernel->KillSessionByToken($token)	;		
			$got = $GLOBALS["Kernel"]->UserKernel->IsSessionExisting($token);		
			$this->assertFalse($got);	
		}		
		//***********************Tests DeleteUser()***********************
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