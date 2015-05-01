<?php
	class FileSystemKernelTest extends PHPUnit_Framework_TestCase{
		//helper method
		protected static function getMethod($name) 
		{
		  //Thanks to https://stackoverflow.com/questions/249664/best-practices-to-test-protected-methods-with-phpunit/2798203#2798203
		  $class  = new ReflectionClass("\Redundancy\Kernel\FileSystemKernel");
		  $method = $class->getMethod($name);
		  $method->setAccessible(true);
		  return $method;
		}
		//***********************Tests GetSystemDir()***********************
		public function testGetSystemDir01(){	
			//Test the case that a wrong value is given by parameter				 
			$foo = self::getMethod('GetSystemDir');
		  	$obj = $GLOBALS["Kernel"]->FileSystemKernel;	  
			$got = $foo->invokeArgs($obj, array(5));			
		  	$this->assertTrue($got == \Redundancy\Classes\Errors::SystemDirectoryNotExisting);
		}
		public function testGetSystemDir02(){
			//Test the case to get the temp dir
			$foo = self::getMethod('GetSystemDir');
		  	$obj = $GLOBALS["Kernel"]->FileSystemKernel;	  
			$got = $foo->invokeArgs($obj, array(1));			
		  	$this->assertTrue($got != \Redundancy\Classes\Errors::SystemDirectoryNotExisting);
			$this->assertTrue(strpos($got,"Temp") !== false);
		}
		public function testGetSystemDir03(){
			//Test the case to get the snapshots dir
			$foo = self::getMethod('GetSystemDir');
		  	$obj = $GLOBALS["Kernel"]->FileSystemKernel;	  
			$got = $foo->invokeArgs($obj, array(2));			
		  	$this->assertTrue($got != \Redundancy\Classes\Errors::SystemDirectoryNotExisting);
			$this->assertTrue(strpos($got,"Snapshots") !== false);
		}		
		public function testGetSystemDir04(){
			//Test the case to get the storage dir
			$foo = self::getMethod('GetSystemDir');
		  	$obj = $GLOBALS["Kernel"]->FileSystemKernel;	  
			$got = $foo->invokeArgs($obj, array(0));			
		  	$this->assertTrue($got != \Redundancy\Classes\Errors::SystemDirectoryNotExisting);
			$this->assertTrue(strpos($got,"Storage") !== false);
		}
		//***********************Tests CreateDirectory()***********************
		public function testCreateDirectory01(){
			$GLOBALS["Kernel"]->UserKernel->RegisterUser("testFS","FileSystemTestUser","test@fs.local","testFS");
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);			
			$got = $GLOBALS["Kernel"]->FileSystemKernel->CreateDirectory("testDirectory01",-1,$token);
			$this->assertTrue($got);
		}
		public function testCreateDirectory02(){
			//This check should fail..
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);
			$got = $GLOBALS["Kernel"]->FileSystemKernel->CreateDirectory("testDirectory01",-1,$token);
			$this->assertTrue($got == \Redundancy\Classes\Errors::EntryExisting);
		}
		public function testCreateDirectory03(){
			//This check should fail..
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);
			$got = $GLOBALS["Kernel"]->FileSystemKernel->CreateDirectory("testDirectory01/",-1,$token);
			$this->assertTrue($got == \Redundancy\Classes\Errors::DisplayNameNotAllowed);
		}
		
		//***********************Tests GetAbsolutePathById()***********************
		public function GetAbsolutePathById01(){
			//This check shoudl fail..
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);
			$got = $GLOBALS["Kernel"]->FileSystemKernel->GetAbsolutePathById(-1,$token);
			$this->assertTrue($got == "/");
		}
		public function GetAbsolutePathById02(){
			//This check shoudl fail..
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);
			$GLOBALS["Kernel"]->FileSystemKernel->CreateDirectory("testDirectory012/",-1,$token);
			$id = $GLOBALS["Kernel"]->FileSystemKernel->GetEntryByAbsolutePath("/testDirectory012",$token)->Id;
			$got = $GLOBALS["Kernel"]->FileSystemKernel->GetAbsolutePathById($id,$token);
			$GLOBALS["Kernel"]->FileSystemKernel->DeleteDirectory("/testDirectory012/",$token);
			$this->assertTrue($got == "/testDirectory012/");
		}
		//***********************Tests GetEntryById()***********************
		public function testGetEntryById01(){
			//This check shoudl fail..
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);
			$got = $GLOBALS["Kernel"]->FileSystemKernel->GetEntryById(-1,$token);
			$this->assertTrue($got->DisplayName == "Rootnode");
		}
		//***********************Tests GetAbsolutePath()***********************
		public function testGetEntryByAbsolutePath01(){
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);
			$got = $GLOBALS["Kernel"]->FileSystemKernel->GetEntryByAbsolutePath("/testDirectory01",$token);
			$this->assertTrue($got->DisplayName == "testDirectory01");
		}
		public function testGetEntryByAbsolutePath02(){
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);
			$got = $GLOBALS["Kernel"]->FileSystemKernel->GetEntryByAbsolutePath("/testDirectory01NOTEXISTING",$token);
			$this->assertTrue(is_null($got));
		}
		//***********************Tests GetEntryByHash()***********************
		public function testGetEntryByHash01(){
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);
			$got = $GLOBALS["Kernel"]->FileSystemKernel->GetEntryByAbsolutePath("/testDirectory01",$token);
			$got2 = $GLOBALS["Kernel"]->FileSystemKernel->GetEntryByHash($got->Hash,$token);
			$this->assertTrue($got2->DisplayName == $got->DisplayName);
			$this->assertTrue($got2->DisplayName == "testDirectory01");
		}


		//***********************Tests IsEntryExisting()***********************
		public function testIsEntryExisting01(){
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);
			$got = $GLOBALS["Kernel"]->FileSystemKernel->IsEntryExisting("testDirectory01",-1,$token);
			$this->assertTrue($got);
		}
		public function testIsEntryExisting02(){
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);
			$got = $GLOBALS["Kernel"]->FileSystemKernel->IsEntryExisting("thiscantbeexisting",-1,$token);
			$this->assertFalse($got);
		}
		//***********************Tests DeleteDirectory()***********************
		public function testDeleteDirectory01(){
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);
			$got = $GLOBALS["Kernel"]->FileSystemKernel->DeleteDirectory("/testDirectory01/",$token);
			$this->assertTrue($got);
		}
		public function testDeleteDirectory02(){
			//This one should fail
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);
			$got = $GLOBALS["Kernel"]->FileSystemKernel->DeleteDirectory("/testDirectory01NOTEXISTING/",$token);
			$this->assertFalse($got);
		}
		public function testDeleteDirectory03(){
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);
			$GLOBALS["Kernel"]->FileSystemKernel->CreateDirectory("testdirroot",-1,$token);
			$parent = $GLOBALS["Kernel"]->FileSystemKernel->GetEntryByAbsolutePath("/testdirroot/",$token);			
			$GLOBALS["Kernel"]->FileSystemKernel->CreateDirectory("testdirsub",$parent->Id,$token);
			$got = $GLOBALS["Kernel"]->FileSystemKernel->DeleteDirectory("/testdirroot/",$token);
			$this->AssertFalse($GLOBALS["Kernel"]->FileSystemKernel->IsEntryExisting("testdirroot",-1,$token));
			$this->assertTrue($got);
		}
		public function testDeleteDirectory04(){
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);
			$GLOBALS["Kernel"]->FileSystemKernel->CreateDirectory("testdirroot",-1,$token);
			$parent = $GLOBALS["Kernel"]->FileSystemKernel->GetEntryByAbsolutePath("/testdirroot/",$token);			
			$GLOBALS["Kernel"]->FileSystemKernel->CreateDirectory("testdirsub",$parent->Id,$token);
			$got = $GLOBALS["Kernel"]->FileSystemKernel->DeleteDirectory("/testdirroot/testdirsub/",$token);
			$this->AssertTrue($GLOBALS["Kernel"]->FileSystemKernel->IsEntryExisting("testdirroot",-1,$token));
			$got2 = $GLOBALS["Kernel"]->FileSystemKernel->DeleteDirectory("/testdirroot/",$token);
			$this->AssertTrue($got2);
			$this->AssertFalse($GLOBALS["Kernel"]->FileSystemKernel->IsEntryExisting("testdirroot",-1,$token));
			$this->assertTrue($got);
		}
		public function testDeleteSharedDirectory01(){					
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);
			$GLOBALS["Kernel"]->FileSystemKernel->CreateDirectory("testdirrootToShare",-1,$token);
			$share = $GLOBALS["Kernel"]->SharingKernel->ShareByCode("/testdirrootToShare",$token);
			$got =$GLOBALS["Kernel"]->FileSystemKernel->DeleteDirectory("/testdirrootToShare/",$token);
			$this->assertTrue($share != "" && $share != false);
			$this->AssertTrue($got);
			$this->AssertFalse($GLOBALS["Kernel"]->FileSystemKernel->IsEntryExisting("testdirrootToShare",-1,$token));
		}
		//***********************Tests RenameEntry()***********************
		public function testRenameEntry01(){
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);
			$GLOBALS["Kernel"]->FileSystemKernel->CreateDirectory("anotherdir",-1,$token);
			$id = $GLOBALS["Kernel"]->FileSystemKernel->GetEntryByAbsolutePath("/anotherdir/",$token)->Id;					
			$this->assertTrue($GLOBALS["Kernel"]->FileSystemKernel->RenameEntry($id,"test1",$token));
			$this->assertTrue($GLOBALS["Kernel"]->FileSystemKernel->IsEntryExisting("test1",-1,$token));
			$GLOBALS["Kernel"]->FileSystemKernel->DeleteDirectory("/test1/",$token);
		}		
		public function testRenameEntry02(){
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);
			$GLOBALS["Kernel"]->FileSystemKernel->CreateDirectory("anotherdir",-1,$token);	
			$GLOBALS["Kernel"]->FileSystemKernel->CreateDirectory("anotherdir2",-1,$token);			
			$id = $GLOBALS["Kernel"]->FileSystemKernel->GetEntryByAbsolutePath("/anotherdir/",$token)->Id;
			$this->assertFalse($GLOBALS["Kernel"]->FileSystemKernel->RenameEntry($id,"anotherdir2",$token));				
			$this->assertTrue($GLOBALS["Kernel"]->FileSystemKernel->IsEntryExisting("anotherdir",-1,$token));
			$this->assertTrue($GLOBALS["Kernel"]->FileSystemKernel->IsEntryExisting("anotherdir2",-1,$token));
			$GLOBALS["Kernel"]->FileSystemKernel->DeleteDirectory("/anotherdir/",$token);
			$GLOBALS["Kernel"]->FileSystemKernel->DeleteDirectory("/anotherdir2/",$token);
		}
		public function testRenameEntry03(){
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);
			$GLOBALS["Kernel"]->FileSystemKernel->CreateDirectory("anotherdir",-1,$token);
			$id = $GLOBALS["Kernel"]->FileSystemKernel->GetEntryByAbsolutePath("/anotherdir/",$token)->Id;					
			$this->assertTrue($GLOBALS["Kernel"]->FileSystemKernel->RenameEntry($id,"test1/",$token) == \Redundancy\Classes\Errors::DisplayNameNotAllowed);
			$this->assertTrue($GLOBALS["Kernel"]->FileSystemKernel->IsEntryExisting("anotherdir",-1,$token));
			$GLOBALS["Kernel"]->FileSystemKernel->DeleteDirectory("/anotherdir/",$token);
		}
		//***********************Tests GetStorage()***********************
		public function testGetStorage01(){
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);	
			$expected = 	$GLOBALS["Kernel"]->UserKernel->GetUser($token)	;
			$got = $GLOBALS["Kernel"]->FileSystemKernel->GetStorage($token);											
			$this->assertTrue($got->sizeInByte == $expected->ContingentInByte);
			$this->assertTrue($got->usedStorageInByte == 0);
		}
		//***********************Tests GetCorrectedUnit()***********************
		public function testGetCorrectedUnit01(){
			$expected = "5 B";									
			$value = 5;
			$got = $GLOBALS["Kernel"]->FileSystemKernel->GetCorrectedUnit($value);			
			$this->assertTrue($expected == $got);			
		}
		public function testGetCorrectedUnit02(){
			$expected = "5 KB";									
			$value = 5*1024;
			$got = $GLOBALS["Kernel"]->FileSystemKernel->GetCorrectedUnit($value);			
			$this->assertTrue($expected == $got);
		}	
		public function testGetCorrectedUnit03(){
			$expected = "5 MB";									
			$value = 5*1024*1024;
			$got = $GLOBALS["Kernel"]->FileSystemKernel->GetCorrectedUnit($value);
			$this->assertTrue($expected == $got);
		}
		public function testGetCorrectedUnit04(){
			$expected = "5 GB";									
			$value = 5*1024*1024*1024;
			$got = $GLOBALS["Kernel"]->FileSystemKernel->GetCorrectedUnit($value);
			$this->assertTrue($expected == $got);
		}
		public function testGetCorrectedUnit05(){
			$expected = "5 TB";									
			$value = 5*1024*1024*1024*1024;
			$got = $GLOBALS["Kernel"]->FileSystemKernel->GetCorrectedUnit($value);			
			$this->assertTrue($expected == $got);
		}
		//***********************Test UploadFile()***********************		
		public function testUploadFile01(){
			 $_FILES = array(
			    'file' => array(
				'name' => 'testUpload',
				'type' => 'text/plain',
				'size' => 16,
				'tmp_name' => __REDUNDANCY_ROOT__."test",
				'error' => 0
			    )
			);			
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);
			$got = $GLOBALS["Kernel"]->FileSystemKernel->UploadFile(-1,$token);		
			$this->AssertTrue($got);
			$this->AssertTrue($GLOBALS["Kernel"]->FileSystemKernel->IsEntryExisting("testUpload",-1,$token));
			
		}
		public function testUploadFile02(){
			//Should fail
			 $_FILES = array(
			    'file' => array(
				'name' => 'testFileUpload',
				'type' => 'text/plain',
				'size' => 16,
				'tmp_name' => __REDUNDANCY_ROOT__."doesnotexists",
				'error' => 0
			    )
			);			
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);
			$got = $GLOBALS["Kernel"]->FileSystemKernel->UploadFile(-1,$token);		
			$this->AssertTrue($got == \Redundancy\Classes\Errors::TempFileCouldNotBeMoved);
		}
		public function testUploadFile03(){
			//Should fail
			 $_FILES = array(
			    'file' => array(
				'name' => 'testFileUpload/',
				'type' => 'text/plain',
				'size' => 16,
				'tmp_name' => __REDUNDANCY_ROOT__."test",
				'error' => 0
			    )
			);			
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);
			$got = $GLOBALS["Kernel"]->FileSystemKernel->UploadFile(-1,$token);		
			$this->AssertTrue($got == \Redundancy\Classes\Errors::DisplayNameNotAllowed);
		}
		//***********************Test DeleteFile()***********************	
		public function testDeleteFile01(){
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);
			$got =$GLOBALS["Kernel"]->FileSystemKernel->DeleteFile("/testUpload",$token);
			$this->AssertTrue($got);
			$this->AssertFalse($GLOBALS["Kernel"]->FileSystemKernel->IsEntryExisting("testUpload",-1,$token));
		}
		public function testDeleteFile02(){
			//Should fail
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);
			$got =$GLOBALS["Kernel"]->FileSystemKernel->DeleteFile("/testUploadX",$token);
			$this->AssertTrue($got == \Redundancy\Classes\Errors::EntryNotExisting);			
		}
		public function testDeleteSharedFile01(){
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);
			 $_FILES = array(
			    'file' => array(
				'name' => 'testTestDeleteSharedFile',
				'type' => 'text/plain',
				'size' => 16,
				'tmp_name' => __REDUNDANCY_ROOT__."test",
				'error' => 0
			    )
			);			
			$GLOBALS["Kernel"]->FileSystemKernel->UploadFile(-1,$token);		
			$share = $GLOBALS["Kernel"]->SharingKernel->ShareByCode("/testTestDeleteSharedFile",$token);
			$got =$GLOBALS["Kernel"]->FileSystemKernel->DeleteFile("/testTestDeleteSharedFile",$token);
			$this->assertTrue($share != "" && $share != false);
			$this->AssertTrue($got);
			$this->AssertFalse($GLOBALS["Kernel"]->FileSystemKernel->IsEntryExisting("testTestDeleteSharedFile",-1,$token));
		}		
		//***********************Test TestCopyEntry()***********************
		//Implement me!	
		//***********************Test MoveEntry()***********************	
		public function testMoveEntry01(){
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);
			$GLOBALS["Kernel"]->FileSystemKernel->CreateDirectory("testMoving",-1,$token);	
			$GLOBALS["Kernel"]->FileSystemKernel->CreateDirectory("target4Moving",-1,$token);	
			$got =$GLOBALS["Kernel"]->FileSystemKernel->MoveEntry("/testMoving/","/target4Moving/",$token);
			$targetID = $GLOBALS["Kernel"]->FileSystemKernel->GetEntryByAbsolutePath("/target4Moving/",$token)->Id;			
			$this->AssertTrue($got);				
			$this->AssertTrue($GLOBALS["Kernel"]->FileSystemKernel->IsEntryExisting("testMoving",$targetID,$token));
			$this->AssertFalse($GLOBALS["Kernel"]->FileSystemKernel->IsEntryExisting("testMoving",-1,$token));
			$GLOBALS["Kernel"]->FileSystemKernel->DeleteDirectory("/target4Moving/",$token);
		}
		public function testMoveEntry02(){
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);
			$GLOBALS["Kernel"]->FileSystemKernel->CreateDirectory("testMoving",-1,$token);	
			$GLOBALS["Kernel"]->FileSystemKernel->CreateDirectory("target4Moving",-1,$token);	
			$GLOBALS["Kernel"]->FileSystemKernel->CreateDirectory("targetRecursive",-1,$token);
			$GLOBALS["Kernel"]->FileSystemKernel->MoveEntry("/testMoving/","/target4Moving/",$token);
			$got = $GLOBALS["Kernel"]->FileSystemKernel->MoveEntry("/targetRecursive/","/target4Moving/testMoving/",$token);
			$targetID = $GLOBALS["Kernel"]->FileSystemKernel->GetEntryByAbsolutePath("/target4Moving/testMoving/",$token)->Id;					
			$this->AssertTrue($GLOBALS["Kernel"]->FileSystemKernel->IsEntryExisting("targetRecursive",$targetID,$token));
			$this->AssertFalse($GLOBALS["Kernel"]->FileSystemKernel->IsEntryExisting("testMoving",-1,$token));
			$GLOBALS["Kernel"]->FileSystemKernel->DeleteDirectory("/target4Moving/",$token);
		}
		public function testMoveEntry03(){
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);
			$GLOBALS["Kernel"]->FileSystemKernel->CreateDirectory("testMoving",-1,$token);	
			$GLOBALS["Kernel"]->FileSystemKernel->CreateDirectory("target4Moving",-1,$token);	
			$GLOBALS["Kernel"]->FileSystemKernel->CreateDirectory("targetRecursive",-1,$token);
			$GLOBALS["Kernel"]->FileSystemKernel->MoveEntry("/testMoving/","/target4Moving/",$token);
			$GLOBALS["Kernel"]->FileSystemKernel->MoveEntry("/targetRecursive/","/target4Moving/testMoving/",$token);
			$got = $GLOBALS["Kernel"]->FileSystemKernel->MoveEntry("/target4Moving/","/target4Moving/testMoving/",$token);
			$targetID = $GLOBALS["Kernel"]->FileSystemKernel->GetEntryByAbsolutePath("/target4Moving/testMoving/",$token)->Id;					
			$this->AssertTrue($got == \Redundancy\Classes\Errors::CanNotPasteIntoItself);
			$this->AssertTrue($GLOBALS["Kernel"]->FileSystemKernel->IsEntryExisting("target4Moving",-1,$token));
			$GLOBALS["Kernel"]->FileSystemKernel->DeleteDirectory("/target4Moving/",$token);
		}
		//***********************Test IsDisplayNameAllowed()***********************	
		public function testIsDisplayNameAllowed01(){					
			$got =$GLOBALS["Kernel"]->FileSystemKernel->IsDisplayNameAllowed("test");
			$this->AssertTrue($got);			
		}
		public function testIsDisplayNameAllowed02(){
			//Should fail			
			$got =$GLOBALS["Kernel"]->FileSystemKernel->IsDisplayNameAllowed("test/");
			$this->AssertFalse($got);			
		}
		//***********************Test GetContent()***********************	
		public function testGetContent01(){					
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);
			$GLOBALS["Kernel"]->FileSystemKernel->CreateDirectory("listTest1",-1,$token);	
			$GLOBALS["Kernel"]->FileSystemKernel->CreateDirectory("listTest2",-1,$token);	
			$GLOBALS["Kernel"]->FileSystemKernel->CreateDirectory("listTest3",-1,$token);
			$got = $GLOBALS["Kernel"]->FileSystemKernel->GetContent("/",$token);			
			$this->AssertTrue(count($got) == 3);	
			$this->AssertTrue($got[0]->DisplayName == "listTest1");
			$this->AssertTrue($got[1]->DisplayName == "listTest2");
			$this->AssertTrue($got[2]->DisplayName == "listTest3");
			$GLOBALS["Kernel"]->FileSystemKernel->DeleteDirectory("/listTest1/",$token);
			$GLOBALS["Kernel"]->FileSystemKernel->DeleteDirectory("/listTest2/",$token);
			$GLOBALS["Kernel"]->FileSystemKernel->DeleteDirectory("/listTest3/",$token);	
		}
		public function testGetContent02(){					
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);			
			$got = $GLOBALS["Kernel"]->FileSystemKernel->GetContent("/",$token);			
			$this->AssertTrue(count($got) == 0);				
		}
		//***********************Test CalculateFolderSize()***********************	
		public function testCalculateFolderSize01(){	
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);	
			 $_FILES = array(
			    'file' => array(
				'name' => 'testUpload',
				'type' => 'text/plain',
				'size' => 16,
				'tmp_name' => __REDUNDANCY_ROOT__."test",
				'error' => 0
			    )
			);			
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);
			$GLOBALS["Kernel"]->FileSystemKernel->UploadFile(-1,$token);	
			$GLOBALS["Kernel"]->FileSystemKernel->CreateDirectory("mySizeDir",-1,$token);	
			$GLOBALS["Kernel"]->FileSystemKernel->MoveEntry("/testUpload","/mySizeDir/",$token);					
			$got =$GLOBALS["Kernel"]->FileSystemKernel->CalculateFolderSize("/mySizeDir/",$token);			
			$this->AssertTrue($got == 16);					
		}
		public function testCalculateFolderSize02(){	
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);	
			 $_FILES = array(
			    'file' => array(
				'name' => 'testUpload2',
				'type' => 'text/plain',
				'size' => 16,
				'tmp_name' => __REDUNDANCY_ROOT__."test",
				'error' => 0
			    )
			);			
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);
			$GLOBALS["Kernel"]->FileSystemKernel->UploadFile(-1,$token);		
			$GLOBALS["Kernel"]->FileSystemKernel->MoveEntry("/testUpload2","/mySizeDir/",$token);					
			$got =$GLOBALS["Kernel"]->FileSystemKernel->CalculateFolderSize("/",$token);			
			$this->AssertTrue($got == 32);	
			$GLOBALS["Kernel"]->FileSystemKernel->DeleteDirectory("/mySizeDir/",$token);			
		}
		public function testCalculateFolderSize03(){						
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);				
			$got =$GLOBALS["Kernel"]->FileSystemKernel->CalculateFolderSize("/thisisnothere",$token);			
			$this->AssertTrue($got == -1);			
		}
		//***********************Test GetContentOfFile()***********************	
		public function testGetContentOfFile01(){
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);	
			$_FILES = array(
			    'file' => array(
				'name' => 'testUpload2',
				'type' => 'text/plain',
				'size' => 16,	
				'tmp_name' => __REDUNDANCY_ROOT__."test",
				'error' => 0
			    )
			);	
			$GLOBALS["Kernel"]->FileSystemKernel->UploadFile(-1,$token);	
			$hash = $GLOBALS["Kernel"]->FileSystemKernel->GetEntryByAbsolutePath("/testUpload2",$token)->Hash;
			$got =$GLOBALS["Kernel"]->FileSystemKernel->GetContentOfFile($hash,$token);			
			$this->AssertTrue($got =="döner.");		
			$got =$GLOBALS["Kernel"]->FileSystemKernel->DeleteFile("/testUpload2",$token);	
		}
		public function testGetContentOfFile02(){
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);	
			$hash ="notexisting.";
			$got =$GLOBALS["Kernel"]->FileSystemKernel->GetContentOfFile($hash,$token);			
			$this->AssertTrue($got == \Redundancy\Classes\Errors::EntryNotExisting);		
		}
		//***********************Test GetLastChangesOfFileSystem()***********************	
		public function testGetLastChangesOfFileSystem01(){
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);				
			$got =$GLOBALS["Kernel"]->FileSystemKernel->GetLastChangesOfFileSystem($token);			
			$this->AssertTrue(count($got) == 0);			
		}
		public function testGetLastChangesOfFileSystem02(){
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);	
			$GLOBALS["Kernel"]->FileSystemKernel->CreateDirectory("testDir",-1,$token);				
			$got =$GLOBALS["Kernel"]->FileSystemKernel->GetLastChangesOfFileSystem($token);			
			$this->AssertTrue(count($got) == 2);	
			$GLOBALS["Kernel"]->FileSystemKernel->DeleteDirectory("/testDir/",$token);		
		}
		//***********************Test StartZipCreation()***********************	
		public function testStartZipCreation01(){
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);		
			$_FILES = array(
			    'file' => array(
				'name' => 'testUpload2',
				'type' => 'text/plain',
				'size' => 16,	
				'tmp_name' => __REDUNDANCY_ROOT__."test",
				'error' => 0
			    )
			);	
			$GLOBALS["Kernel"]->FileSystemKernel->UploadFile(-1,$token);	
			$got =$GLOBALS["Kernel"]->FileSystemKernel->StartZipCreation(-1,$token,-1);
			$this->AssertTrue($got != "");	
			//TODO: Look into the file.
			$GLOBALS["Kernel"]->FileSystemKernel->DeleteFile("/testUpload2",$token);	
		}
		public function testStartZipCreation02(){
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);		
			$_FILES = array(
			    'file' => array(
				'name' => 'testUpload2',
				'type' => 'text/plain',
				'size' => 16,	
				'tmp_name' => __REDUNDANCY_ROOT__."test",
				'error' => 0
			    )
			);	
			$GLOBALS["Kernel"]->FileSystemKernel->UploadFile(-1,$token);	
			$got =$GLOBALS["Kernel"]->FileSystemKernel->StartZipCreation(-1,$token,-1);
			$this->AssertTrue($got == \Redundancy\Classes\Errors::ZipFileExisting);	
			$GLOBALS["Kernel"]->FileSystemKernel->DeleteFile("/testUpload2",$token);	
			$tempPath = $GLOBALS["Kernel"]->FileSystemKernel->GetSystemDir(1);
			unlink($tempPath."testFS.zip");
		}
		//***********************Test GetSearchTerms()***********************	
		public function testGetSearchTerms01(){
			$got = $GLOBALS["Kernel"]->FileSystemKernel->GetSearchTerms("file=value");
			$expected = "Select Id from FileSystem where file = 'value'";
			$this->AssertTrue($got == $expected);
		}
		public function testGetSearchTerms02(){
			$got = $GLOBALS["Kernel"]->FileSystemKernel->GetSearchTerms("file = value");
			$expected = "Select Id from FileSystem where file = 'value'";
			$this->AssertTrue($got == $expected);
		}
		public function testGetSearchTerms03(){
			$got = $GLOBALS["Kernel"]->FileSystemKernel->GetSearchTerms("file <> value");
			$expected = "Select Id from FileSystem where file <> 'value'";
			$this->AssertTrue($got == $expected);
		}
		public function testGetSearchTerms04(){
			$got = $GLOBALS["Kernel"]->FileSystemKernel->GetSearchTerms("file <> value");
			$expected = "Select Id from FileSystem where file <> 'value'";
			$this->AssertTrue($got == $expected);
		}
		public function testGetSearchTerms05(){
			$got = $GLOBALS["Kernel"]->FileSystemKernel->GetSearchTerms("file > value");
			$expected = "Select Id from FileSystem where file > 'value'";
			$this->AssertTrue($got == $expected);
		}
		public function testGetSearchTerms06(){
			$got = $GLOBALS["Kernel"]->FileSystemKernel->GetSearchTerms("file < value");
			$expected = "Select Id from FileSystem where file < 'value'";
			$this->AssertTrue($got == $expected);
		}
		public function testGetSearchTerms07(){
			$got = $GLOBALS["Kernel"]->FileSystemKernel->GetSearchTerms("file <= value");
			$expected = "Select Id from FileSystem where file <= 'value'";
			$this->AssertTrue($got == $expected);
		}
		public function testGetSearchTerms08(){
			$got = $GLOBALS["Kernel"]->FileSystemKernel->GetSearchTerms("file >= value");
			$expected = "Select Id from FileSystem where file >= 'value'";
			$this->AssertTrue($got == $expected);
		}
		public function testGetSearchTerms09(){
			$got = $GLOBALS["Kernel"]->FileSystemKernel->GetSearchTerms("file % value");
			$expected = "Select Id from FileSystem where file  like  '%value%'";
			$this->AssertTrue($got == $expected);
		}
		public function testGetSearchTerms10(){
			$got = $GLOBALS["Kernel"]->FileSystemKernel->GetSearchTerms("file fjafjajfl value");
			$expected = "";
			$this->AssertTrue($got == $expected);
		}
		//***********************Test SearchFileSystem()***********************	
		public function testSearchFileSystem01(){
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);	
			$got = $GLOBALS["Kernel"]->FileSystemKernel->SearchFileSystem("notexisting",$token);
			$this->AssertTrue($got == \Redundancy\Classes\Errors::NoSearchResults);
		}
		public function testSearchFileSystem02(){
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);	
			$GLOBALS["Kernel"]->FileSystemKernel->CreateDirectory("searchTest",-1,$token);
			$got = $GLOBALS["Kernel"]->FileSystemKernel->SearchFileSystem("searchTest",$token);
			$GLOBALS["Kernel"]->FileSystemKernel->DeleteDirectory("/searchTest/",$token);
			$this->AssertTrue(count($got) == 1);
		}
		public function testSearchFileSystem03(){
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);	
			$GLOBALS["Kernel"]->FileSystemKernel->CreateDirectory("searchTest",-1,$token);
			$got = $GLOBALS["Kernel"]->FileSystemKernel->SearchFileSystem("displayName=searchTest",$token);
			$GLOBALS["Kernel"]->FileSystemKernel->DeleteDirectory("/searchTest/",$token);
			$this->AssertTrue(count($got) == 1);
		}
		public function testSearchFileSystem04(){
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);	
			$GLOBALS["Kernel"]->FileSystemKernel->CreateDirectory("searchTest",-1,$token);
			$got = $GLOBALS["Kernel"]->FileSystemKernel->SearchFileSystem("displayName%Test",$token);
			$GLOBALS["Kernel"]->FileSystemKernel->DeleteDirectory("/searchTest/",$token);
			$this->AssertTrue(count($got) == 1);
		}
		public function testSearchFileSystem05(){
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);	
			$got = $GLOBALS["Kernel"]->FileSystemKernel->SearchFileSystem("displayName/Test;",$token);
			$this->AssertTrue($got == \Redundancy\Classes\Errors::NoSearchResults);
		}
	}
?>
