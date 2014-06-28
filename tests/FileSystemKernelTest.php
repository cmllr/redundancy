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
	}
?>
