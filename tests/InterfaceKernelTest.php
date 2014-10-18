<?php
	class InteraceKernelTest extends PHPUnit_Framework_TestCase{
		//helper method
		protected static function getMethod($name) 
		{
		  //Thanks to https://stackoverflow.com/questions/249664/best-practices-to-test-protected-methods-with-phpunit/2798203#2798203
		  $class  = new ReflectionClass("\Redundancy\Kernel\UserKernel");
		  $method = $class->getMethod($name);
		  $method->setAccessible(true);
		  return $method;
		}
		//***********************Tests SplitFileNameAndExtension()***********************
		public function testSplitFileNameAndExtension01(){				
		  	$got = $GLOBALS["Kernel"]->InterfaceKernel->SplitFileNameAndExtension("test.png");	 			
		  	$this->assertTrue($got[0] == "test");
		  	$this->assertTrue($got[1] == ".png");
		}		
		public function testSplitFileNameAndExtension02(){				
		  	$got = $GLOBALS["Kernel"]->InterfaceKernel->SplitFileNameAndExtension("test");	 			
		  	$this->assertTrue($got[0] == "test");
		  	$this->assertTrue($got[1] == "");
		}
		public function testSplitFileNameAndExtension03(){				
		  	$got = $GLOBALS["Kernel"]->InterfaceKernel->SplitFileNameAndExtension("test..png");	 			
		  	$this->assertTrue($got[0] == "test.");
		  	$this->assertTrue($got[1] == ".png");
		}
		public function testSplitFileNameAndExtension04(){				
		  	$got = $GLOBALS["Kernel"]->InterfaceKernel->SplitFileNameAndExtension("test.tar.gz");	 			
		  	$this->assertTrue($got[0] == "test.tar");
		  	$this->assertTrue($got[1] == ".gz");
		}
	}
?>
