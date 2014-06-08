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
		
	}
?>
