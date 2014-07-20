<?php
	class SharingKernelTest extends PHPUnit_Framework_TestCase{
		
		protected static function getMethod($name) 
		{
		  //Thanks to https://stackoverflow.com/questions/249664/best-practices-to-test-protected-methods-with-phpunit/2798203#2798203
		  $class  = new ReflectionClass("\Redundancy\Kernel\SharingKernel");
		  $method = $class->getMethod($name);
		  $method->setAccessible(true);
		  return $method;
		}
		//***********************Tests GetFreeShareLink()***********************
		public function testGetFreeShareLink(){	
			//Test the case that a wrong value is given by parameter				 
			$foo = self::getMethod('GetFreeShareLink');
		  	$obj = $GLOBALS["Kernel"]->SharingKernel;	  
			$got = $foo->invokeArgs($obj, array());			
		  	$this->assertTrue($got != "");
		}
		//***********************Tests GetRandomString()***********************
		public function testGetRandomString01(){
			$got = $GLOBALS["Kernel"]->SharingKernel->GetRandomString(5);
			$this->assertTrue($got != "");
	 		$this->assertTrue(strlen($got) == 5);
		}
		public function testGetRandomString02(){
			$got = $GLOBALS["Kernel"]->SharingKernel->GetRandomString(5);
			$got2 = $GLOBALS["Kernel"]->SharingKernel->GetRandomString(5);			
			$this->assertTrue($got != "");
			$this->assertTrue($got2 != "");
			$this->assertTrue($got2 != $got);
	 		$this->assertTrue(strlen($got) == 5);
			$this->assertTrue(strlen($got2) == 5);
		}
		//***********************Tests ShareByCode()***********************
		private function prepareUpload(){
			 $_FILES = array(
			    'file' => array(
				'name' => 'testUpload4Share',
				'type' => 'text/plain',
				'size' => 16,
				'tmp_name' => __REDUNDANCY_ROOT__."test",
				'error' => 0
			    )
			);			
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);
			$got = $GLOBALS["Kernel"]->FileSystemKernel->UploadFile(-1,$token);			
		}
		public function testShareByCode01(){
			$this->prepareUpload();
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);
			$code = $GLOBALS["Kernel"]->SharingKernel->ShareByCode("/testUpload4Share",$token);
			$this->assertTrue($code != false);			
			$got =$GLOBALS["Kernel"]->SharingKernel->DeleteCodeShare($code,$token);
			$this->assertTrue($got);
			$got =$GLOBALS["Kernel"]->FileSystemKernel->DeleteFile("/testUpload4Share",$token);
			$this->assertTrue($got);
		}
		//***********************Tests IsEntryShared()***********************
		public function testIsEntryShared01(){	
			$this->prepareUpload();		
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);
			$code = $GLOBALS["Kernel"]->SharingKernel->ShareByCode("/testUpload4Share",$token);
			$this->assertTrue($code != false);			
			$got = $GLOBALS["Kernel"]->SharingKernel->IsEntryShared("/testUpload4Share",$token,\Redundancy\Classes\ShareMode::ByCode);
			$this->assertTrue($got);
			$got =$GLOBALS["Kernel"]->SharingKernel->DeleteCodeShare($code,$token);
			$this->assertTrue($got);
			$got =$GLOBALS["Kernel"]->FileSystemKernel->DeleteFile("/testUpload4Share",$token);
			$this->assertTrue($got);		
		}
		public function testIsEntryShared02(){	
			$this->prepareUpload();		
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);					
			$got = $GLOBALS["Kernel"]->SharingKernel->IsEntryShared("/testUpload4Share",$token,\Redundancy\Classes\ShareMode::ByCode);
			$this->assertFalse($got);		
			$got =$GLOBALS["Kernel"]->FileSystemKernel->DeleteFile("/testUpload4Share",$token);
			$this->assertTrue($got);		
		}
		//***********************Tests GetEntryByShareCode()***********************
		public function testGetEntryByShareCode01(){	
			$this->prepareUpload();		
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);
			$code = $GLOBALS["Kernel"]->SharingKernel->ShareByCode("/testUpload4Share",$token);
			$this->assertTrue($code != false);			
			$got = $GLOBALS["Kernel"]->SharingKernel->GetEntryByShareCode($code);
			$this->assertTrue(!is_null($got));			
			$this->assertTrue($got->DisplayName == "testUpload4Share");
			$got =$GLOBALS["Kernel"]->SharingKernel->DeleteCodeShare($code,$token);
			$this->assertTrue($got);
			$got =$GLOBALS["Kernel"]->FileSystemKernel->DeleteFile("/testUpload4Share",$token);
			$this->assertTrue($got);		
		}
		public function testGetEntryByShareCode02(){					
			$got = $GLOBALS["Kernel"]->SharingKernel->GetEntryByShareCode("fkjawfööaklfklaklöwfjkölawklf");			
			$this->assertTrue($got == \Redundancy\Classes\Errors::EntryNotExisting);				
		}
		//***********************Tests DeleteUserShare()***********************
		public function testDeleteUserShare01(){	
			$this->prepareUpload();		
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);
			$code = $GLOBALS["Kernel"]->SharingKernel->ShareByCode("/testUpload4Share",$token);
			$this->assertTrue($code != false);			
			$got = $GLOBALS["Kernel"]->SharingKernel->IsEntryShared("/testUpload4Share",$token,\Redundancy\Classes\ShareMode::ByCode);
			$this->assertTrue($got);
			$got =$GLOBALS["Kernel"]->SharingKernel->DeleteCodeShare($code,$token);
			$this->assertTrue($got);
			$got =$GLOBALS["Kernel"]->FileSystemKernel->DeleteFile("/testUpload4Share",$token);
			$this->assertTrue($got);		
		}
		public function testDeleteUserShare02(){	
			$this->prepareUpload();		
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);		
			$got =$GLOBALS["Kernel"]->SharingKernel->DeleteCodeShare("xyznotexisting",$token);
			$this->assertTrue($got == \Redundancy\Classes\Errors::EntryNotExisting);				
		}
		public function testDeleteUserShare03(){	
			//Create another user if the system can detect when a foreign user wants to delete an other users share			
			$loginName = "testUser2";
			$displayName = "testUser2";
			$mailAddress = "mail2@localhost.lan";
			$password = "test2";
			$got = $value = $GLOBALS["Kernel"]->UserKernel->RegisterUser($loginName,$displayName,$mailAddress,$password);		
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testUser2","test2",true);	
			//Upload the file and share it
			$this->prepareUpload();		
			$token2 =  $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);
			$code = $GLOBALS["Kernel"]->SharingKernel->ShareByCode("/testUpload4Share",$token);
			$this->assertTrue($code == \Redundancy\Classes\Errors::EntryNotExisting);					
			$got =$GLOBALS["Kernel"]->SharingKernel->DeleteCodeShare("xyznotexisting",$token2);
			$this->assertTrue($got == \Redundancy\Classes\Errors::EntryNotExisting);
			$got =$GLOBALS["Kernel"]->FileSystemKernel->DeleteFile("/testUpload4Share",$token2);
			$this->assertTrue($got);	
			$got = $GLOBALS["Kernel"]->UserKernel->DeleteUser("testUser2","test2");		
			$this->assertTrue($got);			
		}
					
	}
?>
