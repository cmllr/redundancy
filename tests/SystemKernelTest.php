<?php
	class SystemKernelTest extends PHPUnit_Framework_TestCase{
		//helper method
		protected static function getMethod($name) 
		{
		  //Thanks to https://stackoverflow.com/questions/249664/best-practices-to-test-protected-methods-with-phpunit/2798203#2798203
		  $class  = new ReflectionClass("\Redundancy\Kernel\UserKernel");
		  $method = $class->getMethod($name);
		  $method->setAccessible(true);
		  return $method;
		}
		//***********************Tests IsInTestEnvironment()***********************
		public function testIsInTestEnvironment(){				
		  	$got = $GLOBALS["Kernel"]->SystemKernel->IsInTestEnvironment();	 			
		  	$this->assertTrue($got);
		}		
		//***********************Tests IsAffectedByXSS()***********************
		public function testIsAffectedByXSS01(){
			$got = $GLOBALS["Kernel"]->SystemKernel->IsAffectedByXSS("data");	 			
		  	$this->assertFalse($got);
		}
		public function testIsAffectedByXSS02(){
			$got = $GLOBALS["Kernel"]->SystemKernel->IsAffectedByXSS("<script type=\"text/javascript\">alert(\"XSS\");</script>");	 			
		  	$this->assertTrue($got);
		}
		public function testIsAffectedByXSS03(){
			$got = $GLOBALS["Kernel"]->SystemKernel->IsAffectedByXSS(array("data"));	 			
		  	$this->assertFalse($got);
		}
		public function testIsAffectedByXSS04(){
			$got = $GLOBALS["Kernel"]->SystemKernel->IsAffectedByXSS(array("<script type=\"text/javascript\">alert(\"XSS\");</script>","data"));	 			
		  	$this->assertTrue($got);
		}
		//***********************Tests BanUser()***********************
		public function testBanUser(){				
		  	$GLOBALS["Kernel"]->SystemKernel->BanUser("127.0.0.1","Test");	 			
		  	$got = $GLOBALS["Kernel"]->SystemKernel->IsBanned("127.0.0.1");	
		  	$this->assertTrue($got);
		}
		//***********************Tests IsBanned()***********************
		public function testIsBanned01(){				
		  	$GLOBALS["Kernel"]->SystemKernel->BanUser("127.0.0.1","Test");	 			
		  	$got = $GLOBALS["Kernel"]->SystemKernel->IsBanned("127.0.0.1");	
		  	$this->assertTrue($got);
		}
		public function testIsBanned02(){				 			
		  	$got = $GLOBALS["Kernel"]->SystemKernel->IsBanned("127.0.0.2");	
		  	$this->assertFalse($got);
		}
		//***********************Tests GetBannedIPs()***********************
		public function testGetBannedIPs01(){
			$got =  $GLOBALS["Kernel"]->UserKernel->RegisterUser("testUser2","testuser","lba@jlbajlkfjfj","test2");		
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testUser2","test2",true);			
		  	$got = $GLOBALS["Kernel"]->SystemKernel->GetBannedIPs($token);	
		  	$this->assertTrue(count($got) == 1);
			$GLOBALS["Kernel"]->UserKernel->DeleteUser("testUser2","test2");	
		}
		public function testGetBannedIPs02(){				
		  	$GLOBALS["Kernel"]->SystemKernel->BanUser("127.0.0.1","Test");	 			
		  	$got = $GLOBALS["Kernel"]->SystemKernel->GetBannedIPs("fjjfwlajlfajklwfjklawf");	
		  	$this->assertTrue($got == \Redundancy\Classes\Errors::NotAllowed);
		}
		//***********************Tests UnBan()***********************
		public function testUnBan01(){
			$got =  $GLOBALS["Kernel"]->UserKernel->RegisterUser("testUser2","testuser","lba@jlbajlkfjfj","test2");		
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testUser2","test2",true);	
			$GLOBALS["Kernel"]->SystemKernel->UnBan("127.0.0.1",$token);	
			$got = $GLOBALS["Kernel"]->SystemKernel->IsBanned("127.0.0.1");
			$GLOBALS["Kernel"]->UserKernel->DeleteUser("testUser2","test2");	
			$this->assertFalse($got);
		}
		//***********************Tests GetSystemSettings***********************
		public function testGetSettings01(){
			$got = $GLOBALS["Kernel"]->SystemKernel->GetSettings(); 
			$er = $got[0];			
			$this->assertTrue(!is_null($got));
			$this->assertTrue($er->Name=="Enable_Register");
			$this->assertTrue($er->Type=="Boolean");
		}
		//***********************Tests GetSetting***********************
		public function testGetSetting01(){
			$got = $GLOBALS["Kernel"]->SystemKernel->GetSetting("Enable_Register"); 				
			$this->assertTrue(!is_null($got));
			$this->assertTrue($got->Name=="Enable_Register");
			$this->assertTrue($got->Type=="Boolean");
		}
		public function testGetSetting02(){
			$got = $GLOBALS["Kernel"]->SystemKernel->GetSetting("Notexisting"); 				
			$this->assertTrue(is_null($got));
		}
		//***********************Tests SetSetting***********************
		public function testSetSetting01(){			
			$old = $GLOBALS["Kernel"]->SystemKernel->GetSetting("Enable_Register"); 

			$got =  $GLOBALS["Kernel"]->UserKernel->RegisterUser("testUser2","testuser","lba@jlbajlkfjfj","test2");		
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testUser2","test2",true);	

			$GLOBALS["Kernel"]->SystemKernel->SetSetting($token,"Enable_Register","false");	

			$got =  $GLOBALS["Kernel"]->SystemKernel->GetSetting("Enable_Register");
			$GLOBALS["Kernel"]->SystemKernel->SetSetting($token,"Enable_Register","true");	
			$GLOBALS["Kernel"]->UserKernel->DeleteUser("testUser2","test2");				
			$this->assertTrue($got->Value == false);
			$this->assertTrue($got->Name=="Enable_Register");
			$this->assertTrue($got->Type=="Boolean");
		}
	}
?>
