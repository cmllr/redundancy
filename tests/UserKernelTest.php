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
			$got= $GLOBALS["Kernel"]->UserKernel->RegisterUser($loginName,$displayName,$mailAddress,$password);		
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
		public function testRegisterUserMissingArg(){
			$loginName = "testUser";
			$displayName = "";
			$mailAddress = "mail@localhost.lan";
			$password = "test";
			$got = $GLOBALS["Kernel"]->UserKernel->RegisterUser($loginName,$displayName,$mailAddress,$password);		
			$this->assertTrue(\Redundancy\Classes\Errors::ArgumentMissing==$got);	
		}
		public function testRegisterUserDisabledBySystem(){
			$loginName = "testUser";
			$displayName = "testUser";
			$mailAddress = "mail@localhost.lan";
			$password = "test";
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",false);	
			$GLOBALS["Kernel"]->SystemKernel->SetSetting($token,"Enable_Register","false");
			$got = $GLOBALS["Kernel"]->UserKernel->RegisterUser($loginName,$displayName,$mailAddress,$password);		
			$this->assertTrue(\Redundancy\Classes\Errors::RegistrationNotEnabled==$got);	
			$GLOBALS["Kernel"]->SystemKernel->SetSetting($token,"Enable_Register","true");
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
			$expected[0]->IsDefault = true;
			$expected[0]->Permissions = array();
			$expected[0]->Permissions[] = "1";
			$expected[0]->Permissions[] = "1";
			$expected[0]->Permissions[] = "1";
			$expected[0]->Permissions[] = "1";
			$expected[0]->Permissions[] = "1";
			$expected[0]->Permissions[] = "1";
			$expected[0]->Permissions[] = "1";	
			$expected[0]->Permissions[] = "1";
			$expected[0]->Permissions[] = "1";
			$expected[0]->Permissions[] = "1";
			$expected[0]->Permissions[] = "1";
			$this->assertEquals($value[0],$expected[0]);		
		}
		//***********************Tests GetPermissionSet()***********************
		function testGetPermissionSet01(){
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",false);	
			$got = $GLOBALS["Kernel"]->UserKernel->GetPermissionSet($token);
			$expected = array();
			$expected[] = "1";
			$expected[] = "1";
			$expected[] = "1";
			$expected[] = "1";
			$expected[] = "1";
			$expected[] = "1";
			$expected[] = "1";
			$expected[] = "1";
			$expected[] = "1";
			$expected[] = "1";
			$expected[] = "1";
			$this->assertEquals($expected,$got);
		}
		function testGetPermissionSet02(){
			$got = $GLOBALS["Kernel"]->UserKernel->GetPermissionSet("fail");
			$this->assertTrue(\Redundancy\Classes\Errors::TokenNotValid == $got);
		}
		//***********************Tests GetPermissionValues()***********************
		function testGetPermissionValues01(){
			$got = $GLOBALS["Kernel"]->UserKernel->GetPermissionValues();
			$this->assertTrue(count($got) == 11);
		}
		//***********************Tests GetRoleByName()***********************
		function testGetRoleByName01(){
			$got = $GLOBALS["Kernel"]->UserKernel->GetRoleByName("Root");
			$this->assertTrue(!is_numeric($got));
			$this->assertTrue($got->Description == "Root");
		}
		function testGetRoleByName02(){
			$got = $GLOBALS["Kernel"]->UserKernel->GetRoleByName("RootDoesNotExist");
			$this->assertTrue($got == \Redundancy\Classes\Errors::PermissionNotFound);
		}
		//***********************Tests GetPermission()***********************
		function testGetPermission01(){
			$got = $GLOBALS["Kernel"]->UserKernel->GetPermission("Root","AllowUpload");
			$this->assertTrue($got == 1);
		}
		function testGetPermission02(){
			$got = $GLOBALS["Kernel"]->UserKernel->GetPermission("Root","AllowUpload2");
			$this->assertTrue($got == \Redundancy\Classes\Errors::PermissionNotFound);
		}
		function testGetPermission03(){
			$got = $GLOBALS["Kernel"]->UserKernel->GetPermission("Root2","AllowUpload");
			$this->assertTrue($got == \Redundancy\Classes\Errors::PermissionNotFound);
		}
		//***********************Tests ChangePassword()***********************
		public function testChangePassword01(){
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
			$expected->Permissions = array();
			$expected->Permissions[] = "1";
			$expected->Permissions[] = "1";
			$expected->Permissions[] = "1";
			$expected->Permissions[] = "1";
			$expected->Permissions[] = "1";
			$expected->Permissions[] = "1";
			$expected->Permissions[] = "1";
			$expected->Permissions[] = "1";
			$expected->Permissions[] = "1";
			$expected->Permissions[] = "1";
			$expected->Permissions[] = "1";
			$expected->IsDefault = true;
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
		//***********************Tests IsActionAllowed()***********************
		public function testIsActionAllowed01(){
			$loginName = "testUser";		
			$token = $GLOBALS["Kernel"]->UserKernel->LogIn($loginName,"test1",true);	
			$got = $GLOBALS["Kernel"]->UserKernel->IsActionAllowed($token,\Redundancy\Classes\PermissionSet::AllowUpload);
			$this->assertTrue($got);	
		}
		public function testIsActionAllowed02(){
			$loginName = "testUser";		
			$token = $GLOBALS["Kernel"]->UserKernel->LogIn($loginName,"test1",true);	
			$got = $GLOBALS["Kernel"]->UserKernel->IsActionAllowed($token,\Redundancy\Classes\PermissionSet::AllowCreatingFolder);
			$this->assertTrue($got);	
		}
		public function testIsActionAllowed03(){
			$loginName = "testUser";		
			$token = $GLOBALS["Kernel"]->UserKernel->LogIn($loginName,"test1",true);	
			$got = $GLOBALS["Kernel"]->UserKernel->IsActionAllowed($token,300); //This permission can't be existing!
			$this->assertFalse($got);	
		}	
		
		//***********************Tests KillSessionByToken()***********************
		public function testKillSessionByToken(){
			$loginName = "testUser";		
			$token = $GLOBALS["Kernel"]->UserKernel->LogIn($loginName,"test1",true);	
			$GLOBALS["Kernel"]->UserKernel->KillSessionByToken($token)	;		
			$got = $GLOBALS["Kernel"]->UserKernel->IsSessionExisting($token);		
			$this->assertFalse($got);	
		}
		//***********************Tests IsLoginNameFree()***********************
		public function testIsLoginOrMailFree01(){					
			$got = $GLOBALS["Kernel"]->UserKernel->IsLoginOrMailFree("test123");		
			$this->assertTrue($got);	
		}
		public function testIsLoginOrMailFree02(){					
			$got = $GLOBALS["Kernel"]->UserKernel->IsLoginOrMailFree("testUser");		
			$this->assertFalse($got);	
		}
		//***********************Tests UpdateOrCreateGroup()***********************
		public function testUpdateOrCreateGroup01(){
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testUser","test1",true);	
			$got =  $GLOBALS["Kernel"]->UserKernel->UpdateOrCreateGroup("sauerkraut","-1","000000000",$token);
			$this->assertTrue($got);
		}
		public function testUpdateOrCreateGroup02(){
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testUser","test1",true);	
			$got =  $GLOBALS["Kernel"]->UserKernel->UpdateOrCreateGroup("sauerkraut","-1","000000000",$token);
			$this->assertTrue(is_numeric($got));
		}
		public function testUpdateOrCreateGroup03(){
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testUser","test1",true);	
			$groupTotest = $GLOBALS["Kernel"]->UserKernel->GetRoleByName("sauerkraut");
			$got =  $GLOBALS["Kernel"]->UserKernel->UpdateOrCreateGroup("sauerkrautRenamed",$groupTotest->Id,"000000001",$token);
			$groupTotest = $GLOBALS["Kernel"]->UserKernel->GetRoleByName("sauerkraut");
			//Test old name
			$this->assertTrue(is_numeric($groupTotest));
			$groupTotest = $GLOBALS["Kernel"]->UserKernel->GetRoleByName("sauerkrautRenamed");
			$this->assertTrue($groupTotest->Description == "sauerkrautRenamed");
		}
		//***********************Tests SetAsDefaultGroup()***********************
		public function SetAsDefaultGroup01(){
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testUser","test1",true);	
			$got =  $GLOBALS["Kernel"]->UserKernel->SetAsDefaultGroup("sauerkrautRenamed",$token);
			$this->assertTrue($got);
			$groupTotest = $GLOBALS["Kernel"]->UserKernel->GetRoleByName("sauerkrautRenamed");
			$this->assertTrue($groupTotest->IsDefault);
			//Reset the group permission
			$GLOBALS["Kernel"]->UserKernel->SetAsDefaultGroup("Root",$token);
		}
		//***********************Tests DeleteGroup()***********************
		public function testDeleteGroup01(){
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testUser","test1",true);	
			$GLOBALS["Kernel"]->UserKernel->SetAsDefaultGroup("sauerkrautRenamed",$token);
			$got =  $GLOBALS["Kernel"]->UserKernel->DeleteGroup("sauerkrautRenamed",$token);
			$this->assertTrue($got == 43);
			$groupTotest = $GLOBALS["Kernel"]->UserKernel->GetRoleByName("sauerkrautRenamed");
			$this->assertTrue(!is_numeric($groupTotest));
			//Reset the group permission
			$GLOBALS["Kernel"]->UserKernel->SetAsDefaultGroup("Root",$token);
		}

		public function testDeleteGroup02(){
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testUser","test1",true);	
			$got =  $GLOBALS["Kernel"]->UserKernel->DeleteGroup("sauerkrautRenamed",$token);
			$this->assertTrue($got);
			$groupTotest = $GLOBALS["Kernel"]->UserKernel->GetRoleByName("sauerkrautRenamed");
			$this->assertTrue(is_numeric($groupTotest));
		}
		public function testDeleteGroup03(){
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testUser","test1",true);	
			$got =  $GLOBALS["Kernel"]->UserKernel->DeleteGroup("sauerkrautRenamed",$token);
			$this->assertFalse($got);
			$groupTotest = $GLOBALS["Kernel"]->UserKernel->GetRoleByName("sauerkrautRenamed");
			$this->assertTrue(is_numeric($groupTotest));
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
			$this->assertFalse($got);	
		}
		public function testDeleteUserShouldFail2(){
			$loginName = "testUser2";		
			$got = $GLOBALS["Kernel"]->UserKernel->DeleteUser($loginName,"test1");		
			$this->assertTrue(!$got);	
		}
		//************************GetUserByAdminPanel**********************
		public function testGetUserByAdminPanel01(){
			$token = $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);	
			$got = $GLOBALS["Kernel"]->UserKernel->GetUserByAdminPanel("testFS",$token);		
			$this->assertTrue(!is_null($got));
			$this->assertTrue($got->DisplayName == "testFS");
			$this->assertTrue($got->LoginName == "testFS");			
		}
		public function testGetUserByAdminPanel02(){
			$token = $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);	
			$got = $GLOBALS["Kernel"]->UserKernel->GetUserByAdminPanel("sauerkraut",$token);		
			$this->assertTrue(is_null($got));	
		}
		//************************DeleteUserByAdminPanel**********************
		public function testDeleteUserByAdminPanel01(){
			$token = $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);	
			$got = $GLOBALS["Kernel"]->UserKernel->DeleteUserByAdminPanel("testFS",$token);		
			$this->assertTrue($got == \Redundancy\Classes\Errors::SystemAdminAccountNotAllowedToModify);
			$token = $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);	
			$this->assertTrue($token != \Redundancy\Classes\Errors::PasswordOrUserNameWrong);
		}
		public function testDeleteUserByAdminPanel02(){
			$token = $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);	
			$got = $GLOBALS["Kernel"]->UserKernel->DeleteUserByAdminPanel("sauerkraut",$token);		
			$this->assertTrue($got == \Redundancy\Classes\Errors::UserNotExisting);
		}
		public function testDeleteUserByAdminPanel03(){			
			$GLOBALS["Kernel"]->UserKernel->RegisterUser("tdubap3","tdubap3","tdubap3@localhost","tdubap3");		
			 $_FILES = array(
			    'file' => array(
				'name' => 'testFileUpload',
				'type' => 'text/plain',
				'size' => 16,
				'tmp_name' => __REDUNDANCY_ROOT__."test",
				'error' => 0
			    )
			);			
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("tdubap3","tdubap3",true);
			$got = $GLOBALS["Kernel"]->FileSystemKernel->UploadFile(-1,$token);		
			$token = $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);	
			$got = $GLOBALS["Kernel"]->UserKernel->DeleteUserByAdminPanel("tdubap3",$token);		
			$this->assertTrue($got);
			$got = $GLOBALS["Kernel"]->UserKernel->DeleteUserByAdminPanel("tdubap3",$token);		
			$this->assertTrue($got == \Redundancy\Classes\Errors::UserNotExisting);
		}		
		//************************SetUserByAdminPanel**********************
		public function testSetUserByAdminPanel01(){
			$token = $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);	
			$GLOBALS["Kernel"]->UserKernel->RegisterUser("tdubap3","tdubap3","tdubap3@localhost","tdubap3");		
			$testFS =  $GLOBALS["Kernel"]->UserKernel->GetUser($token);	
			$got = $GLOBALS["Kernel"]->UserKernel->SetUserByAdminPanel($token,"tdubap3","RENAMEDtdubap3","on",$testFS->ContingentInByte +1,"sauerkraut",1);	
			$this->assertTrue($got);			
			$got = $GLOBALS["Kernel"]->UserKernel->DeleteUserByAdminPanel("tdubap3",$token);		
			$this->assertTrue($got);
			$got = $GLOBALS["Kernel"]->UserKernel->DeleteUserByAdminPanel("tdubap3",$token);		
			$this->assertTrue($got == \Redundancy\Classes\Errors::UserNotExisting);
		}		
		public function testSetUserByAdminPanel02(){
			$token = $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);	
			$GLOBALS["Kernel"]->UserKernel->RegisterUser("tdubap3","tdubap3","tdubap3@localhost","tdubap3");		
			$testFS =  $GLOBALS["Kernel"]->UserKernel->GetUser($token);	
			$got = $GLOBALS["Kernel"]->UserKernel->SetUserByAdminPanel($token,"tdubap3","RENAMEDtdubap3","on",$testFS->ContingentInByte * -1 ,"sauerkraut",1);	
			$this->assertFalse($got);			
			$got = $GLOBALS["Kernel"]->UserKernel->DeleteUserByAdminPanel("tdubap3",$token);		
			$this->assertTrue($got);
			$got = $GLOBALS["Kernel"]->UserKernel->DeleteUserByAdminPanel("tdubap3",$token);		
			$this->assertTrue($got == \Redundancy\Classes\Errors::UserNotExisting);
		}	
		public function testSetUserByAdminPanel03(){
			$token = $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);					
			$testFS =  $GLOBALS["Kernel"]->UserKernel->GetUser($token);	
			$got = $GLOBALS["Kernel"]->UserKernel->SetUserByAdminPanel($token,"testFS","testFS","on",$testFS->ContingentInByte,"",1);	
			$this->assertTrue($got == \Redundancy\Classes\Errors::SystemAdminAccountNotAllowedToModify);		
		}
		public function testSetUserByAdminPanel04(){
			$token = $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);	
			$GLOBALS["Kernel"]->UserKernel->RegisterUser("tdubap4","tdubap4","tdubap4@localhost","tdubap3");		
			$testFS =  $GLOBALS["Kernel"]->UserKernel->GetUser($token);	
			$got = $GLOBALS["Kernel"]->UserKernel->SetUserByAdminPanel($token,"tdubap4","RENAMEDtdubap3","off",$testFS->ContingentInByte,"sauerkraut",2);	
			$this->assertTrue($got);			
			$got = $GLOBALS["Kernel"]->UserKernel->DeleteUserByAdminPanel("tdubap4",$token);		
			$this->assertTrue($got);
			$got = $GLOBALS["Kernel"]->UserKernel->DeleteUserByAdminPanel("tdubap4",$token);		
			$this->assertTrue($got == \Redundancy\Classes\Errors::UserNotExisting);
		}

	}
?>
