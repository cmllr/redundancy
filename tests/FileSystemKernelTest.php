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
			//This check shoudl fail..
			$token =  $GLOBALS["Kernel"]->UserKernel->LogIn("testFS","testFS",true);
			$got = $GLOBALS["Kernel"]->FileSystemKernel->CreateDirectory("testDirectory01",-1,$token);
			$this->assertTrue($got == \Redundancy\Classes\Errors::EntryExisting);
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
	}
?>
