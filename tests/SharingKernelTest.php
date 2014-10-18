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
		//***********************Tests DeleteAllSharesOfEntry()***********************
		public function testDeleteAllSharesOfEntry01(){	
			$this->prepareUpload();		
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);	
			$code = $GLOBALS["Kernel"]->SharingKernel->ShareByCode("/testUpload4Share",$token);						
			$hash = $GLOBALS["Kernel"]->SharingKernel->GetEntryByShareCode($code)->Hash;	
			$GLOBALS["Kernel"]->SharingKernel->DeleteAllSharesOfEntry($hash,$token);
			$got = $GLOBALS["Kernel"]->SharingKernel->IsEntryShared("/testUpload4Share",$token,\Redundancy\Classes\ShareMode::ByCode);
			$GLOBALS["Kernel"]->FileSystemKernel->DeleteFile("/testUpload4Share",$token);
			$this->assertFalse($got);		
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
			$got =  $GLOBALS["Kernel"]->UserKernel->RegisterUser($loginName,$displayName,$mailAddress,$password);		
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
		//***********************Tests GetSharesOfUser()***********************
		public function testGetSharesOfUser01(){	
			$this->prepareUpload();		
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);			
			$code = $GLOBALS["Kernel"]->SharingKernel->ShareByCode("/testUpload4Share",$token);
			$this->assertTrue($code != false);			
			$got = $GLOBALS["Kernel"]->SharingKernel->IsEntryShared("/testUpload4Share",$token,\Redundancy\Classes\ShareMode::ByCode);
			$this->assertTrue($got);
			$shares = $GLOBALS["Kernel"]->SharingKernel->GetSharesOfUser($token,\Redundancy\Classes\ShareDirection::ByMe);			
			$got =$GLOBALS["Kernel"]->FileSystemKernel->DeleteFile("/testUpload4Share",$token);
			$this->assertTrue($got);		
			$this->assertTrue(count($shares) == 1);
		}
		public function testGetSharesOfUser02(){	
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);		
			$shares = $GLOBALS["Kernel"]->SharingKernel->GetSharesOfUser($token,\Redundancy\Classes\ShareDirection::ByMe);					
			$this->assertTrue(count($shares) == 0);
		}	
		public function testGetSharesOfUser03(){
			$this->prepareUpload();		
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);	
			//ShareToUser($absolutePath,$to,$token)		
			//create a target user...to share it to the user..
			$GLOBALS["Kernel"]->UserKernel->RegisterUser("target","target","target@localhost.lan","target");		
			$targetToken =  $GLOBALS["Kernel"]->UserKernel->LogIn("target","target",true);
			$targetID = $owner = $GLOBALS["Kernel"]->UserKernel->GetUser($targetToken)->ID;
			$code = $GLOBALS["Kernel"]->SharingKernel->ShareToUser("/testUpload4Share",$targetID,$token);
			$this->assertTrue($code != false);			
			$got = $GLOBALS["Kernel"]->SharingKernel->IsEntryShared("/testUpload4Share",$token,\Redundancy\Classes\ShareMode::ToUser);
			$this->assertTrue($got);
			$shares = $GLOBALS["Kernel"]->SharingKernel->GetSharesOfUser($token,\Redundancy\Classes\ShareDirection::ByMe);			
			$sharesToTarget = $GLOBALS["Kernel"]->SharingKernel->GetSharesOfUser($targetToken,\Redundancy\Classes\ShareDirection::ToMe);	
			$got =$GLOBALS["Kernel"]->FileSystemKernel->DeleteFile("/testUpload4Share",$token);
			$GLOBALS["Kernel"]->UserKernel->DeleteUser("target","target");		
			$this->assertTrue($got);		
			$this->assertTrue(count($shares) == 1);
			$this->assertTrue(count($sharesToTarget) == 1);
		}
		//***********************Tests RefreshShareLink()***********************
		public function testRefreshShareLink01(){	
			$this->prepareUpload();		
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);			
			$code = $GLOBALS["Kernel"]->SharingKernel->ShareByCode("/testUpload4Share",$token);
			$this->assertTrue($code != false);			
			$got = $GLOBALS["Kernel"]->SharingKernel->IsEntryShared("/testUpload4Share",$token,\Redundancy\Classes\ShareMode::ByCode);
			$this->assertTrue($got);
			$newCode = $GLOBALS["Kernel"]->SharingKernel->RefreshShareLink($code,$token);	

			$shares = $GLOBALS["Kernel"]->SharingKernel->GetSharesOfUser($token,\Redundancy\Classes\ShareDirection::ByMe);		
			$shareFound = $shares[0];
			$this->assertTrue($code != $newCode);
			$this->assertTrue($shareFound->Entry->DisplayName == "testUpload4Share");
			$GLOBALS["Kernel"]->FileSystemKernel->DeleteFile("/testUpload4Share",$token);
			$this->assertTrue($got);		
			$this->assertTrue(count($shares) == 1);
		}
		//***********************Tests ShareToUser()***********************
		public function testShareToUser01(){
			$this->prepareUpload();		
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);	
			//ShareToUser($absolutePath,$to,$token)		
			//create a target user...to share it to the user..
			$GLOBALS["Kernel"]->UserKernel->RegisterUser("target","target","target@localhost.lan","target");		
			$targetToken =  $GLOBALS["Kernel"]->UserKernel->LogIn("target","target",true);
			$targetID = $owner = $GLOBALS["Kernel"]->UserKernel->GetUser($targetToken)->ID;
			$code = $GLOBALS["Kernel"]->SharingKernel->ShareToUser("/testUpload4Share",$targetID,$token);
			$this->assertTrue($code != false);			
			$got = $GLOBALS["Kernel"]->SharingKernel->IsEntryShared("/testUpload4Share",$token,\Redundancy\Classes\ShareMode::ToUser);
			$this->assertTrue($got);
			$shares = $GLOBALS["Kernel"]->SharingKernel->GetSharesOfUser($token,\Redundancy\Classes\ShareDirection::ByMe);			
			$sharesToTarget = $GLOBALS["Kernel"]->SharingKernel->GetSharesOfUser($targetToken,\Redundancy\Classes\ShareDirection::ToMe);	
			$got =$GLOBALS["Kernel"]->FileSystemKernel->DeleteFile("/testUpload4Share",$token);
			$GLOBALS["Kernel"]->UserKernel->DeleteUser("target","target");		
			$this->assertTrue($got);		
			$this->assertTrue(count($shares) == 1);
			$this->assertTrue(count($sharesToTarget) == 1);
		}
		public function testShareToUser02(){
			$this->prepareUpload();		
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);	
			//ShareToUser($absolutePath,$to,$token)		
			//create a target user...to share it to the user..
			$GLOBALS["Kernel"]->UserKernel->RegisterUser("target","target","target@localhost.lan","target");		
			$targetToken =  $GLOBALS["Kernel"]->UserKernel->LogIn("target","target",true);
			$targetID = $owner = $GLOBALS["Kernel"]->UserKernel->GetUser($targetToken)->ID;
			$code = $GLOBALS["Kernel"]->SharingKernel->ShareToUser("/testUpload4ShareDoesNOtExists",$targetID,$token);
			$this->assertTrue($code == \Redundancy\Classes\Errors::EntryNotExisting);			
			$got = $GLOBALS["Kernel"]->SharingKernel->IsEntryShared("/testUpload4Share",$token,\Redundancy\Classes\ShareMode::ToUser);
			$this->assertFalse($got);
			$shares = $GLOBALS["Kernel"]->SharingKernel->GetSharesOfUser($token,\Redundancy\Classes\ShareDirection::ByMe);			
			$sharesToTarget = $GLOBALS["Kernel"]->SharingKernel->GetSharesOfUser($targetToken,\Redundancy\Classes\ShareDirection::ToMe);	
			$got =$GLOBALS["Kernel"]->FileSystemKernel->DeleteFile("/testUpload4Share",$token);
			$GLOBALS["Kernel"]->UserKernel->DeleteUser("target","target");		
			$this->assertTrue($got);		
			$this->assertTrue(count($shares) == 0);
			$this->assertTrue(count($sharesToTarget) == 0);
		}
		//***********************Tests IsEntrySharedByHash()***********************	
		public function testIsEntrySharedByHash01(){
			$this->prepareUpload();
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);
			$code = $GLOBALS["Kernel"]->SharingKernel->ShareByCode("/testUpload4Share",$token);
			$this->assertTrue($code != false);	
			$hash = $GLOBALS["Kernel"]->SharingKernel->GetEntryByShareCode($code)->Hash;	
			$isShared = $GLOBALS["Kernel"]->SharingKernel->IsEntrySharedByHash($hash,$token,\Redundancy\Classes\ShareMode::ByCode);
			$this->assertTrue($isShared);
			$got =$GLOBALS["Kernel"]->SharingKernel->DeleteCodeShare($code,$token);
			$this->assertTrue($got);
			$got =$GLOBALS["Kernel"]->FileSystemKernel->DeleteFile("/testUpload4Share",$token);
			$this->assertTrue($got);
		}
		public function testIsEntrySharedByHash02(){
			$this->prepareUpload();
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);
			$code = $GLOBALS["Kernel"]->SharingKernel->ShareByCode("/testUpload4Share",$token);
			$this->assertTrue($code != false);			
			$hash = $GLOBALS["Kernel"]->SharingKernel->GetEntryByShareCode($code)->Hash;
			$got =$GLOBALS["Kernel"]->SharingKernel->DeleteCodeShare($code,$token);
			$this->assertTrue($got);	
			$isShared = $GLOBALS["Kernel"]->SharingKernel->IsEntrySharedByHash($hash,$token,\Redundancy\Classes\ShareMode::ByCode);
			$this->assertFalse($isShared);			
			$got =$GLOBALS["Kernel"]->FileSystemKernel->DeleteFile("/testUpload4Share",$token);
			$this->assertTrue($got);
		}
		public function testIsEntrySharedByHash03(){
			$this->prepareUpload();
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);
			$code = $GLOBALS["Kernel"]->SharingKernel->ShareByCode("/testUpload4Share",$token);
			$this->assertTrue($code != false);	
			$hash = $GLOBALS["Kernel"]->SharingKernel->GetEntryByShareCode($code)->Hash;	
			$isShared = $GLOBALS["Kernel"]->SharingKernel->IsEntrySharedByHash($hash,$token,\Redundancy\Classes\ShareMode::ByCode);
			$isSharedToUser = $GLOBALS["Kernel"]->SharingKernel->IsEntrySharedByHash($hash,$token,\Redundancy\Classes\ShareMode::ToUser);
			$this->assertTrue($isShared);
			$this->assertFalse($isSharedToUser);
			$got =$GLOBALS["Kernel"]->SharingKernel->DeleteCodeShare($code,$token);
			$this->assertTrue($got);
			$got =$GLOBALS["Kernel"]->FileSystemKernel->DeleteFile("/testUpload4Share",$token);
			$this->assertTrue($got);
		}		
	}
?>
