<?php
	class UserKernelTest extends PHPUnit_Framework_TestCase{
		public function testHashPassword(){			
			$this->assertTrue(false,"HashPassword() can not be tested");
		}		
		public function testRegisterUserNotPreviousExisting(){
			$loginName = "testUser";
			$displayName = "testUser";
			$mailAddress = "mail@localhost.lan";
			$password = "test";
			$got = $value = $GLOBALS["Kernel"]->UserKernel->RegisterUser($loginName,$displayName,$mailAddress,$password);			
			$this->assertTrue(\Redundancy\Classes\Errors::UserOrEmailAlreadyGiven!=$got);
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
			$got = $value = $GLOBALS["Kernel"]->UserKernel->RegisterUser($loginName,$displayName,$mailAddress,$password);		
			$this->assertTrue(\Redundancy\Classes\Errors::UserOrEmailAlreadyGiven==$got);	
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
	}
?>
