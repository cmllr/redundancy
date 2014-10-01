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
	}
?>
