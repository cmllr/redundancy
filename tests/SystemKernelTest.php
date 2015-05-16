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
		public function testIsAffectedByXSS05(){
			$got = $GLOBALS["Kernel"]->SystemKernel->IsAffectedByXSS(array("<a href='test'>Test</a>"));	 			
		  	$this->assertTrue($got);
		}
		public function testIsAffectedByXSS06(){
			$got = $GLOBALS["Kernel"]->SystemKernel->IsAffectedByXSS(array('<script>window.onload = function() {var link=document.getElementsByTagName("a");link[0].href="http://;}</script>'));	 			
		  	$this->assertTrue($got);
		}
		public function testIsAffectedByXSS07(){
			$got = $GLOBALS["Kernel"]->SystemKernel->IsAffectedByXSS(array('<a href=# onclick=\"document.location=\'http://=\'+escape\(document.cookie\)\;\">My Name</a>'));	 			
		  	$this->assertTrue($got);
		}
		public function testIsAffectedByXSS08(){
			$got = $GLOBALS["Kernel"]->SystemKernel->IsAffectedByXSS(array("üäößst bla"));	 			
		  	$this->assertFalse($got);
		}	
		public function testIsAffectedByXSS09(){
			$got = $GLOBALS["Kernel"]->SystemKernel->IsAffectedByXSS(array('{"name":"0002.gif","type":"image\/gif","tmp_name":"\/tmp\/jfkalw","error":0,"size":33067}'));	 			
		  	$this->assertFalse($got);
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
		//***********************Tests IsMyIPBanned***********************
		public function testIsMyIPBanned01(){
			$got = $GLOBALS["Kernel"]->SystemKernel->IsMyIPBanned();
			$this->assertFalse($got);
		}
		public function testIsMyIPBanned02(){
			$token = $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);	
			$ip = $GLOBALS["Kernel"]->UserKernel->GetIP();
			$GLOBALS["Kernel"]->SystemKernel->BanUser($ip,"test");
			$got = $GLOBALS["Kernel"]->SystemKernel->IsMyIPBanned();
			$this->assertTrue($got);
			$GLOBALS["Kernel"]->SystemKernel->UnBan($ip,$token);
			$got = $GLOBALS["Kernel"]->SystemKernel->IsMyIPBanned();
			$this->assertFalse($got);
		}
		public function testGetMaxUploadSize01(){
			$got = $GLOBALS["Kernel"]->SystemKernel->GetMaxUploadSize(1,2);
			$this->assertTrue($got == 2);
		}
		public function testGetMaxUploadSize02(){
			$got = $GLOBALS["Kernel"]->SystemKernel->GetMaxUploadSize(4,2);
			$this->assertTrue($got == 4);
		}
		public function testGetMaxUploadSize03(){
			$got = $GLOBALS["Kernel"]->SystemKernel->GetMaxUploadSize(2,2);
			$this->assertTrue($got == 2);
		}
		public function testGetMaxUploadSize04(){
			$got = $GLOBALS["Kernel"]->SystemKernel->GetMaxUploadSize("1m","2m");
			$this->assertTrue($got == 2*1024*1024);
		}
		public function testGetMaxUploadSize05(){
			$got = $GLOBALS["Kernel"]->SystemKernel->GetMaxUploadSize("4m","2m");
			$this->assertTrue($got == 4*1024*1024);
		}
		public function testGetMaxUploadSize06(){
			$got = $GLOBALS["Kernel"]->SystemKernel->GetMaxUploadSize("2m","2m");
			$this->assertTrue($got == 2*1024*1024);
		}
		public function testGetMaxUploadSize07(){
			$got = $GLOBALS["Kernel"]->SystemKernel->GetMaxUploadSize("4","2m");
			$this->assertTrue($got == 2*1024*1024);
		}
		public function testExtractBytesFromDisplayString01(){
			$got = $GLOBALS["Kernel"]->SystemKernel->ExtractBytesFromDisplayString("81k");			
			$this->assertTrue($got == 81*1024);
		}
		public function testExtractBytesFromDisplayString02(){
			$got = $GLOBALS["Kernel"]->SystemKernel->ExtractBytesFromDisplayString("81m");			
			$this->assertTrue($got == 81*1024*1024);
		}
		public function testExtractBytesFromDisplayString03(){
			$got = $GLOBALS["Kernel"]->SystemKernel->ExtractBytesFromDisplayString("81g");			
			$this->assertTrue($got == 81*1024*1024*1024);
		}
		public function testExtractBytesFromDisplayString04(){
			$got = $GLOBALS["Kernel"]->SystemKernel->ExtractBytesFromDisplayString("81K");			
			$this->assertTrue($got == 81*1024);
		}
		public function testExtractBytesFromDisplayString05(){
			$got = $GLOBALS["Kernel"]->SystemKernel->ExtractBytesFromDisplayString("81M");			
			$this->assertTrue($got == 81*1024*1024);
		}
		public function testExtractBytesFromDisplayString06(){
			$got = $GLOBALS["Kernel"]->SystemKernel->ExtractBytesFromDisplayString("81G");			
			$this->assertTrue($got == 81*1024*1024*1024);
		}
		public function testExtractBytesFromDisplayString07(){
			$got = $GLOBALS["Kernel"]->SystemKernel->ExtractBytesFromDisplayString("81");		
			$this->assertTrue($got == 81);
		}
		public function testExtractBytesFromDisplayString08(){
			$got = $GLOBALS["Kernel"]->SystemKernel->ExtractBytesFromDisplayString("814231421");		
			$this->assertTrue($got == 814231421);
		}
	}
?>
